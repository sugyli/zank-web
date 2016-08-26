<?php

namespace Zank\Controller\Api;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zank\Controller;

/**
 * 认证控制器
 *
 * @package default
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class Sign extends Controller
{
    /**
     * 登陆控制器
     *
     * @param Request $request
     */
    public function in(Request $request, Response $response)
    {
        $phone = $request->getParsedBodyParam('phone');
        $password = $request->getParsedBodyParam('password');

        if ($this->ci->has('user')) {
            $user = $this->ci->get('user');
        } else {
            $user = \Zank\Model\User::byPhone($phone)->first();
        }

        if ($user->password != md5($user->hash.$password)) {
            $response = new \Zank\Common\Message($response, false, '该用户密码错误！');

            return $response->withJson();
        }

        $token = new \Zank\Model\SignToken;
        $token->token = \Zank\Model\SignToken::createToken();
        $token->refresh_token = \Zank\Model\SignToken::createRefreshToken();
        $token->users_id = $user->users_id;
        $token->expires = 60 * 60 * 24; // 24小时过期

        // 清除token
        \Zank\Model\SignToken::where('token', $token->token)
            ->orWhere('refresh_token', $token->refresh_token)
            ->orWhere('users_id', $user->users_id)
            ->delete()
        ;

        if (!$token->save()) {
            $response = new \Zank\Common\Message($response, false, '登陆失败！');

            return $response->withJson();
        }

        $response = new \Zank\Common\Message($response, true, '登陆成功！', $token);

        return $response->withJson();

    }

    public function refreshToken(Request $request, Response $response)
    {
        $refreshToken = $request->getParsedBodyParam('refresh_token');

        // 刷新token的值为空
        if (!$refreshToken) {
            $response = new \Zank\Common\Message($response, false, '请传递正确的参数');

            return $response->withJson();
        }

        $token = \Zank\Model\SignToken::byRefreshToken($refreshToken)->first();

        if (!$token) {
            $response = new \Zank\Common\Message($response, false, '刷新token的参数不存在。');

            return $response->withJson();
        }

        $token->token = \Zank\Model\SignToken::createToken();
        $token->refresh_token = \Zank\Model\SignToken::createRefreshToken();
        

        if (!$token->save()) {
            $response = new \Zank\Common\Message($response, false, '刷新token失败，请重新登陆。');

            return $response->withJson();
        }

        return with(new \Zank\Common\Message($response, true, '刷新token成功！', $token))
            ->withJson()
        ;
    }

    public function setpResisterBase(Request $request, Response $response)
    {
        $phone = $request->getParsedBodyParam('phone');
        $password = $request->getParsedBodyParam('password');
        $invite_code = $request->getParsedBodyParam('invite_code');

        $user = new \Zank\Model\User;
        $user->phone = $phone;
        $user->username = sprintf('用户_%s', $phone);
        $user->hash = str_random(64);
        $user->password = md5($user->hash.$password);

        if ($user->save()) {

            return $this->in($request, $response);
        }

        return with(new \Zank\Common\Message($response, false, '注册失败！'))
            ->withJson()
        ;
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
            '/api/sign/in' => '用户登陆',
            '/api/sign/up/setp/base' => '用户基本信息注册',
            '/api/sign/refresh-token' => '刷新token',
        ]);

        return $response;
    }
} // END class Sign
