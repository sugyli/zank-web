<?php

namespace Zank\Middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * 错误消息收集处理 针对API.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class ExceptionHandle2API
{
    /**
     * ExceptionHandle middleware invokable class.
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
        $message = null;
        try {
            $message = $next($request, $response);
        } catch (\Exception $e) {
            $message = $e;
        }

        if ($message instanceof \Exception) {
            return with(new \Zank\Common\Message($response, false, $message->getMessage()))
                ->withJson();
        }

        return $message;
    }
} // END class ExceptionHandle
