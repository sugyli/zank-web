<?php

namespace Zank\Middleware;

use Carbon\Carbon;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * 用户token认证（用于API请求）.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class AuthenticationUserToken
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $token = $request->getHeaderLine('zank-token');
        $token = \Zank\Model\SignToken::byToken($token)->first();

        if (!$token) {
            return with(new \Zank\Common\Message($response, false, '认证失败或者认证信息不存在。', -1))
                ->withJson();

        // 是否过期
        } elseif ($token->updated_at->diffInSeconds(Carbon::now()) >= (60 * 60 * 24 * 7)) {
            return with(new \Zank\Common\Message($response, false, '登陆过期', -2))
                ->withJson();

        // 查询注入的用户是否存在
        } elseif (!$token->user) {
            return with(new \Zank\Common\Message($response, false, '认证用户不存在！', -3))
                ->withJson();
        }

        $this->ci->offsetSet('user', $token->user);

        return $next($request, $response);
    }
} // END class AuthenticationUserToken
