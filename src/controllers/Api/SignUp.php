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
class SignUp extends Controller
{
    public function __invoke(Request $request, Response $response)
    {
        $phone = $request->getParsedBodyParam('phone');
        $password = $request->getParsedBodyParam('password');
        $captcha = $request->getParsedBodyParam('captcha');
        $invite_code = $request->getParsedBodyParam('invite_code');

        // if (!$phone) {
        // }

        $user = \Zank\Model\User::byPhone($phone)->first();
        var_dump($user);
    }
} // END class SignUp
