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

        $token = $request->getHeaderLine('zank-token');
        $token = \Zank\Model\SignToken::byToken($token)->first();

        var_dump($token);exit;

        if (!$token) {

            return with(new \Zank\Common\Message($response, false, '认证失败或者认证信息不存在。'))
                ->withJson()
            ;
        }

        var_dump($token);
        exit;

        return $next($request, $response);
    }
} // END class AuthenticationUserToken
