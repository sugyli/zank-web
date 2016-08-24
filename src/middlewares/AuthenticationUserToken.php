<?php

namespace Zank\Middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

/**
 * 用户token认证（用于API请求）
 *
 * @author Seven Du <lovevipdsw@outlook.com> 
 **/
class AuthenticationUserToken
{
    protected $ci;

    public function __construct(ContainerInterface $ci) {
        $this->ci = $ci;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $token = $request->getParam('token');
        $token = \Zank\Model\SignToken::byToken($token)->first();

        if (!$token) {
            $response = new \Zank\Common\Message(
                $response,
                false, // 错误
                '认证失败或者认证信息不存在。'
            );
            return $response->withJson();
        }

        var_dump($token);

        return $next($request, $response);
    }
} // END class AuthenticationUserToken
