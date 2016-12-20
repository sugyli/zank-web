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
class ValidateUserByPhoneByZank
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        open_session();
        $phone = $request->getParsedBodyParam('mobile');
        $phone =trim($phone);
        $send_code = $request->getParsedBodyParam('send_code');
        $novel_code = $request->getParsedBodyParam('novel_code');
        //防用户恶意请求
        if(empty($_SESSION['send_code']) or $send_code!= $_SESSION['send_code']){
            return with(new \Zank\Common\Message($response, false, '防恶意请求，请刷新页面后重试'))
                ->withJson();
        }

        if(empty($_SESSION['novel_code']) or strtolower($novel_code) != strtolower($_SESSION['novel_code'])){
            return with(new \Zank\Common\Message($response, false, '图形验证码错误,请从新输入'))
                ->withJson();
        }

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
            $response = new \Zank\Common\Message($response, false, '该手机用户不存在。');

            return $response->withJson();
        }
        $this->ci->offsetSet('user', $user);
        return $next($request, $response);
    }
} // END class ValidateUserByPhone
