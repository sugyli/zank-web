<?php

namespace Zank\Middleware;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 附件上传中间件.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class AttachUpload
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function upload(UploadedFileInterface $file, Response $response)
    {
        $ext = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $ext = $ext ? '.'.$ext : '';
        $md5 = md5_file($file->file);

        $attach = \Zank\Model\Attach::byMd5($md5)->first();
        if (!$attach) {
            try {
                $path = $this->getUploadPath($md5, $ext);
                $this->ci->get('oss')->multiuploadFile(get_oss_bucket_name(), $path, $file->file);
                $attach = $this->savedToDatabase($file, $path, $md5);
            } catch (\Exception $e) {
                return with(new \Zank\Common\Message($response, false, $e->getMessage()));
            }
        }

        return $attach;
    }

    protected function getUploadPath(string $md5, string $ext)
    {
        return sprintf(
            'attachs/%s/%s%s',
            \Carbon\Carbon::now()->format('Y/m/d/H/i/s'),
            $md5,
            $ext
        );
    }

    protected function savedToDatabase(UploadedFileInterface $file, string $path, string $md5)
    {
        $attach = new \Zank\Model\Attach();
        $attach->path = $path;
        $attach->name = $file->getClientFilename();
        $attach->type = $file->getClientMediaType();
        $attach->size = $file->getSize();
        $attach->md5 = $md5;
        $attach->user_id = $this->ci->get('user')->user_id;
        $attach->save();

        return $attach;
    }

    /**
     * Upload attach middleware invokable class.
     *
     * @param \Psr\Http\Message\RequestInterface  $request  PSR7 request
     * @param \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param callable                            $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     **/
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $files = $request->getUploadedFiles();

        // 判断是否只上传了一个文件。
        if (count($files) !== 1) {
            return with(new \Zank\Common\Message($response, false, '只允许单个文件上传'))
                ->withJson();

        // 判断如果上传错误，将返回什么错误消息。
        } elseif (($file = current($files)) && $file->getError() !== UPLOAD_ERR_OK) {
            try {
                throw new \Zank\Exception\UploadException($file->getError());
            } catch (\Zank\Exception\UploadException $e) {
                return with(new \Zank\Common\Message($response, false, $e->getMessage()))
                    ->withJson();
            }

        // 执行上传操作，如果返回的是错误对象，则返回错误，否则，继续执行。
        } elseif (($result = $this->upload($file, $response)) && $result instanceof \Zank\Common\Message) {
            return $result->withJson();
        }

        $this->ci->offsetSet('attach', $result);

        return $next($request, $response);
    }
} // END class AttachUpload
