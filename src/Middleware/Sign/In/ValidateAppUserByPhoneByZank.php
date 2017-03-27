<?php

namespace Zank\Middleware\Sign\In;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * 验证注册的用户手机号码是否正确.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class ValidateAppUserByPhoneByZank
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $phone = $request->getParsedBodyParam('mobile');
        $phone =trim($phone);

        if (strlen($phone) <= 0) {
            return with(new \Zank\Common\Message($response, false, '手机号码或老账户不能为空。'))
            ->withJson();
        }


        // 检查是否存在注入的用户信息，如果有，获取
        if ($this->ci->has('user')) {
            $user = $this->ci->get('user');

        // 不存在注入信息，查询信息，并注入
        } else {
            $user = \Zank\Model\Novel\Wap\SystemUsers::where('uname',$phone)
                    ->first();
        }

        // 如果用户不存在
        if (!$user) {
            $response = new \Zank\Common\Message($response, false, '您输入的账户有误。');

            return $response->withJson();
        }
        $this->ci->offsetSet('user', $user);
        return $next($request, $response);
    }
} // END class ValidateUserByPhone
