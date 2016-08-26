<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->group('/api', function (): void
{
    // index
    $this->any('', function (Request $request, Response $response): Response
    {
        $apiList = [
            '/api/sign/' => '用户注册｜登陆',
        ];
        $response->withJson($apiList);

        return $response;
    });

    // 用户注册｜登陆
    $this->group('/sign', function (): void
    {
        // 索引
        $this->any('', \Zank\Controller\Api\Sign::class);

        // 注册第一步信息
        // phone password
        $this
            ->post('/up/setp/base', \Zank\Controller\Api\Sign::class.':setpRegisterBase')
            ->add(\Zank\Middleware\Sign\Up\ValidateUserInviteCode::class)
            ->add(\Zank\Middleware\Captcha\ValidateByPhoneCaptcha::class)
            ->add(\Zank\Middleware\Sign\Up\ValidateUserByPhone::class)
            ->add(\Zank\Middleware\InitDb::class)
        ;

        // 注册第二步信息
        // 基本信息
        $this
            ->post('/up/setp/info', \Zank\Controller\Api\Sign::class.':setpRegisterInfo')
            ->add(\Zank\Middleware\Sign\Up\ValidateUserByUserName::class)
            ->add(\Zank\Middleware\AuthenticationUserToken::class)
            ->add(\Zank\Middleware\InitDb::class);

        // 登陆
        $this
            ->post('/in', \Zank\Controller\Api\Sign::class.':in')
            ->add(\Zank\Middleware\Sign\In\ValidateUserByPhone::class)
            ->add(\Zank\Middleware\InitDb::class)
        ;

        // 刷新token 
        $this
            ->post('/refresh-token', \Zank\Controller\Api\Sign::class.':refreshToken')
            ->add(\Zank\Middleware\InitDb::class)
        ;
    });

    // 验证码相关
    $this->group('/captcha', function ()
    {
        // 索引
        $this->any('', function (Request $request, Response $response): Response
        {
            $apiList = [
                '/api/captcha/phone/get/register' => '获取手机号码验证码',
                '/api/captcha/phone/has' => '验证手机号码验证码',
            ];

            return $response->withJson($apiList);
        });

        // 获取手机号码验证码
        $this
            ->post('/phone/get/register', \Zank\Controller\Api\Captcha\Phone::class.':get')
            ->add(\Zank\Middleware\Sign\Up\ValidateUserByPhone::class)
            ->add(\Zank\Middleware\InitDb::class)
        ;

        // 验证手机号码验证码
        $this
            ->post('/phone/has', \Zank\Controller\Api\Captcha\Phone::class.':has')
            ->add(\Zank\Middleware\Captcha\ValidateByPhoneCaptcha::class)
            ->add(\Zank\Middleware\InitDb::class)
        ;
    });
});
