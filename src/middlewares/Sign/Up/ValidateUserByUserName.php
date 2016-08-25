<?php

namespace Zank\Middleware\Sign\Up;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

/**
 * 验证注册用户的用户名是否被使用或者用户已经存在
 *
 * @author Seven Du <lovevipdsw@outlook.com> 
 **/
class ValidateUserByUserName
{
    protected $ci;

    public function __construct(ContainerInterface $ci) {
        $this->ci = $ci;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $username = $request->getParsedBodyParam('username');

        // 判断用户名不能为空
        if (!$username) {
            $response = new \Zank\Model\Message($response, false, '用户名不正确');

            return $response->withJson();

        // 判断用户名是否符合规范
        } elseif (!preg_match('/^[\u4E00-\u9FA5A-Za-z][\u4E00-\u9FA5A-Za-z0-9]+$/', $username)) {
            $response = new \Zank\Model\Message($response, false, '用户名不正确，只能非数字和非符号开头。')；

            return $response->withJson();
        }

        $user = $this->ci->get('user');
        if (!$user) {
            $user = \Zank\Model\User::byUserName($username)->frist();
            $this->ci->offsetSet('user', $user);
        }

        if ($user) {
            
            // 判断是否用户名相等于注入的用户名
            if ($user->username == $username) {
                $response = new \Zank\Common\Message($response, false, '用户名已经被使用。');

            // 只要用户存在就不允许
            } else {
                $response = new \Zank\Model\Message($response, false, '当前用户名或者手机号码不能注册。');
            }

            return $response->withJson();
        }

        return $next($request, $response);
    }
} // END class ValidateUserByUserName
