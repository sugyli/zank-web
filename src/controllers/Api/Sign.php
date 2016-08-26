<?php

namespace Zank\Controller\Api;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zank\Controller;

/**
 * 注册控制器
 *
 * @package default
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class Sign extends Controller
{

    /**
     * 注册控制器
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function up(Request $request, Response $response)
    {
        $phone = $request->getParsedBodyParam('phone');
        $username = $request->getParsedBodyParam('username');
        $password = $request->getParsedBodyParam('password');
        $invite_code = $request->getParsedBodyParam('invite_code');

        $user = new \Zank\Model\User;
        $user->phone = $phone;
        $user->username = $username;
        $user->hash = str_random(64);
        $user->password = md5($user->hash.$password);

        if (!$user->save()) {
            $response = new \Zank\Common\Message($response, false, '创建用户失败！');

            return $response->withJson();
        }

        var_dump($user);
        exit;

    }

    /**
     * 登陆控制器
     *
     * @param Request $request
     */
    public function in(Request $request, Response $response)
    {
        $user = $request->getParsedBodyParam('user');
        $password = $request->getParsedBodyParam('password');
        var_dump(11);exit;
        return 123;
        register
        // var_dump($this->ci->demo);exit;
    }

    public function setpResisterBase(Request $request, Response $response)
    {
        $phone = $request->getParsedBodyParam('phone');
        $password = $request->getParsedBodyParam('password');
        $invite_code = $request->getParsedBodyParam('invite_code');

        $user = new \Zank\Model\User;
        $user->phone = $phone;
        $user->username = sprintf('手机用户_%s', $phone);
        $user->hash = str_random(64);
        $user->password = md5($user->hash.$password);

        var_dump($user);
        exit;
    }

    /**
     * 索引方法，返回api列表
     *
     * @param Request $request 请求对象
     * @param Response $response 返回资源
     * @return Response
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function __invoke(Request $request, Response $response)
    {
        $response->withJson([
            '/api/sign/up' => '用户注册',
        ]);

        return $response;
    }
} // END class Sign
