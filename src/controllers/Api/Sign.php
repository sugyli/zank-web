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
        $token->user_id = $user->user_id;
        $token->expires = 60 * 60 * 24; // 24小时过期

        // 清除token
        \Zank\Model\SignToken::where('token', $token->token)
            ->orWhere('refresh_token', $token->refresh_token)
            ->orWhere('user_id', $user->user_id)
            ->delete()
        ;

        if (!$token->save()) {
            $response = new \Zank\Common\Message($response, false, '登陆失败！');

            return $response->withJson();

        // 判断是否注入了验证码，删除验证码
        } elseif ($this->ci->has('phone_captcha')) {
            $this->ci->get('phone_captcha')->delete();
        }

        return with(new \Zank\Common\Message($response, true, '登陆成功！', $token))
            ->withJson()
        ;

    }

    /**
     * 刷新token接口
     *
     * @param Request $request 请求对象
     * @param Response $response 响应对象
     * @return Response 请求对象
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function refreshToken(Request $request, Response $response): Response
    {
        $refreshToken = $request->getParsedBodyParam('refresh_token');

        // 刷新token的值为空
        if (!$refreshToken) {

            return with(new \Zank\Common\Message($response, false, '请传递正确的参数'))
                ->withJson()
            ;
        }

        $token = \Zank\Model\SignToken::byRefreshToken($refreshToken)->first();

        if (!$token) {

            return with(new \Zank\Common\Message($response, false, '刷新token的参数不存在。'))
                ->withJson()
            ;
        }

        $token->token = \Zank\Model\SignToken::createToken();
        $token->refresh_token = \Zank\Model\SignToken::createRefreshToken();
        

        if (!$token->save()) {

            return with(new \Zank\Common\Message($response, false, '刷新token失败，请重新登陆。'))
                ->withJson()
            ;
        }

        return with(new \Zank\Common\Message($response, true, '刷新token成功！', $token))
            ->withJson()
        ;
    }

    /**
     * 注册步骤第一步，手机号，密码注册
     *
     * @param Request $request 请求对象
     * @param Response $response 响应对象
     * @return Response 请求对象
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function stepRegisterBase(Request $request, Response $response): Response
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

            $this->ci->offsetSet('user', $user);

            return $this->in($request, $response);
        }

        return with(new \Zank\Common\Message($response, false, '注册失败！'))
            ->withJson()
        ;
    }

    /**
     * 完善其他信息步骤
     *
     * @param Request $request 请求对象
     * @param Response $response 响应对象
     * @return Response 请求对象
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function stepRegisterInfo(Request $request, Response $response): Response
    {
        $username = $request->getParsedBodyParam('username');
        $age = $request->getParsedBodyParam('age');
        $height = $request->getParsedBodyParam('height');
        $kg = $request->getParsedBodyParam('kg');
        $areas_id = $request->getParsedBodyParam('areas_id');
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
