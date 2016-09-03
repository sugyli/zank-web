<?php

namespace Zank\Middleware;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

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
        $file = $request->getUploadedFiles();
        $fileNum = count($file);

        if ($fileNum <= 0) {
            return with(new \Zank\Common\Message($response, false, '没有上传任何文件.'))
                ->withJson();
        // 如果超过一个
        } elseif ($fileNum > 1) {
            return with(new \Zank\Common\Message($response, false, '只允许单个文件上传.'))
                ->withJson();
        }

        $file = current($file);
        if ($file->getError() !== UPLOAD_ERR_OK) {
            try {
                throw new \Zank\Exception\UploadException($file->getError());
            } catch (\Zank\Exception\UploadException $e) {
                return with(new \Zank\Common\Message($response, false, $e->getMessage()))
                    ->withJson();
            }
        }

        $fileExt = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $fileExt = $fileExt ? '.'.$fileExt : '';
        $fileMd5 = md5_file($file->file);

        $attach = \Zank\Model\Attach::byMd5($fileMd5)->first();
        if (!$attach) {
            $path = sprintf('attachs/%s/%s%s', \Carbon\Carbon::now()->format('Y/m/d/H/i/s'), $fileMd5, $fileExt);

            $attach = new \Zank\Model\Attach;
            $attach->path = $path;
            $attach->name = $file->getClientFilename();
            $attach->type = $file->getClientMediaType();
            $attach->size = $file->getSize();
            $attach->md5  = $fileMd5;
            $attach->user_id = $this->ci->get('user')->user_id;

            $message = null;

            $this->ci->get('oss')->multiuploadFile(getAliyunOssBucket(), $path, $file->file);
            $attach->save();
        }

        $this->ci->offsetSet('attach', $attach);

        return $next($request, $response);
    }
} // END class AttachUpload
