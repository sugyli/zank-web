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
        $password = $request->getParsedBodyParam('password');
        $captcha = $request->getParsedBodyParam('captcha');
        $invite_code = $request->getParsedBodyParam('invite_code');

        $user = \Zank\Model\User::byPhone($phone)->first();
        var_dump($user);
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
        // var_dump($this->ci->demo);exit;
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
