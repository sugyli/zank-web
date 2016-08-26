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
            ->post('/up/setp/base', \Zank\Controller\Api\Sign::class.':setpResisterBase')
            ->add(\Zank\Middleware\Sign\Up\ValidateUserInviteCode::class)
            ->add(\Zank\Middleware\Captcha\ValidateByPhoneCaptcha::class)
            ->add(\Zank\Middleware\Sign\Up\ValidateUserByPhone::class)
            ->add(\Zank\Middleware\InitDb::class)
        ;

        // 登陆
        $this
            ->any('/in', \Zank\Controller\Api\Sign::class.':in')
            ->add(\Zank\Middleware\AuthenticationUserToken::class)
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
                '/api/captcha/phone/get' => '获取手机号码验证码',
                '/api/captcha/phone/has' => '验证手机号码验证码',
            ];

            return $response->withJson($apiList);
        });

        // 获取手机号码验证码
        $this
            ->post('/phone/get', \Zank\Controller\Api\Captcha\Phone::class.':get')
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
