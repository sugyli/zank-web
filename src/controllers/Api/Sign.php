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
} // END class Sign
