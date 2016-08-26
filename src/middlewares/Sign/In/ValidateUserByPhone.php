<?php

namespace Zank\Middleware\Sign\In;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

/**
 * 验证注册的用户手机号码是否正确
 *
 * @author Seven Du <lovevipdsw@outlook.com> 
 **/
class ValidateUserByPhone
{
    protected $ci;

    public function __construct(ContainerInterface $ci) {
        $this->ci = $ci;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $phone = $request->getParsedBodyParam('phone');

        if (!$phone) {
            $response = new \Zank\Common\Message($response, false, '手机号码不正确。');

            return $response->withJson();
        }

        // 检查是否存在注入的用户信息，如果有，获取
        if ($this->ci->has('user')) {
            $user = $this->ci->get('user');

        // 不存在注入信息，查询信息，并注入
        } else {
            $user = \Zank\Model\User::withTrashed()
                ->byPhone($phone)
                ->first()
            ;
            $this->ci->offsetSet('user', $user);
        }

        // 如果用户不存在
        if (!$user) {
            $response = new \Zank\Common\Message($response, false, '该手机用户不存在。');

            return $response->withJson();
        }

        return $next($request, $response);
    }
} // END class ValidateUserByPhone