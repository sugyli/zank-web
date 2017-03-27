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
        $type = $request->getParsedBodyParam('type');
    
        if (!$phone) {
            return with(new \Zank\Common\Message($response, false, '手机号码不不能为空。'))
                ->withJson();
        }

        if (!isPhoneNumber($phone)) {
            return with(new \Zank\Common\Message($response, false, '手机号码非法格式。'))
                ->withJson();   
        }

        // 检查是否存在注入的用户信息，如果有，获取
        if ($this->ci->has('user')) {
            $user = $this->ci->get('user');

        // 不存在注入信息，查询信息，并注入
        } else {
            $user = \Zank\Model\Novel\Wap\SystemUsers::withTrashed()
                    ->where('uname',$phone)
                    ->first();

        }

        if ($type == 3) {//3是找密码
            if (!$user) {
                $response = new \Zank\Common\Message($response, false, '找P啊,你还没有注册呢。');
                return $response->withJson();
            }

            if (!empty($user->deleted_at)) {
                $response = new \Zank\Common\Message($response, false, '手机号码已经被禁用');
                return $response->withJson();
            }
            $this->ci->offsetSet('user', $user);

        }else{//注册
            
            // 如果用户存在
            if ($user) {
                
                // 手机号等于注入的用户手机号
                if ($user->uname == $phone) {
                    $response = new \Zank\Common\Message($response, false, '手机号已经被使用。');

                // 只要用户存在就不允许
                } else {
                    $response = new \Zank\Common\Message($response, false, '当前用户名或者手机号码不能注册。');
                }

                return $response->withJson();
            }

        }

        return $next($request, $response);
    }
} // END class ValidateUserByPhone
