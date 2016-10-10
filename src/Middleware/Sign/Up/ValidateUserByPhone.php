<?php

namespace Zank\Middleware\Sign\Up;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * 验证注册的用户手机号码是否正确.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class ValidateUserByPhone
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $phone = $request->getParsedBodyParam('phone');

        if (!$phone) {
            return with(new \Zank\Common\Message($response, false, '手机号码不正确。'))
                ->withJson();
        }

        // 检查是否存在注入的用户信息，如果有，获取
        if ($this->ci->has('user')) {
            $user = $this->ci->get('user');

        // 不存在注入信息，查询信息，并注入
        } else {
            $user = \Zank\Model\User::withTrashed()
                ->byPhone($phone)
                ->first();
        }

        // 如果用户存在
        if ($user) {
            $this->ci->offsetSet('user', $user);

            // 手机号等于注入的用户手机号
            if ($user->phone == $phone) {
                $response = new \Zank\Common\Message($response, false, '手机号已经被使用。');

            // 只要用户存在就不允许
            } else {
                $response = new \Zank\Common\Message($response, false, '当前用户名或者手机号码不能注册。');
            }

            return $response->withJson();
        }

        return $next($request, $response);
    }
} // END class ValidateUserByPhone
