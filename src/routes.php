<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

app()->any('/oss', function () {
    // var_dump(class_exists(Zank\Interfaces\WrapperInterface::class));
    // var_dump(class_exists(Zank\Streams\AliyunOssStream::class));
    // var_dump(class_exists('streamWrapper'));
    $accessKeyId = 'LTAI8XM6ritGP2po';
    $accessKeySecret = 'GOSeZSsuVjgQdawQChPAy2kg1xbVCa';
    $endpoint = 'oss-cn-hangzhou.aliyuncs.com';
    $oss = new Zank\Services\AliyunOSS($accessKeyId, $accessKeySecret, $endpoint);
    $oss->setBucket('medz');
    $oss->registerStreamWrapper('oss');

    $data = file_get_contents('oss://zank/test.png');

    file_put_contents('oss://zank/1.txt', 'data');

    var_dump($data);

    // var_dump(class_exists(Zank\Streams\AliyunOssStream::class));

    // var_dump($oss->getObjectMeta('medz', 'zank/test.png'));
});

app()->group('/api', function () {
    // index
    $this->any('', function (Request $request, Response $response): Response {
        $apiList = [
            '/api/sign/' => '用户注册｜登陆',
        ];
        $response->withJson($apiList);

        return $response;
    });

    // 用户注册｜登陆
    $this->group('/sign', function () {
        // 索引
        $this->any('', \Zank\Controller\Api\Sign::class);

        // 注册第一步信息
        // phone password
        $this
            ->post('/up/step/base', \Zank\Controller\Api\Sign::class.':stepRegisterBase')
            ->add(\Zank\Middleware\Sign\Up\ValidateUserInviteCode::class)
            ->add(\Zank\Middleware\Captcha\ValidateByPhoneCaptcha::class)
            ->add(\Zank\Middleware\Sign\Up\ValidateUserByPhone::class)
            ->add(\Zank\Middleware\InitDb::class);

        // 注册第二步信息
        // 基本信息
        $this
            ->post('/up/step/info', \Zank\Controller\Api\Sign::class.':stepRegisterInfo')
            ->add(\Zank\Middleware\Sign\Up\ValidateUserByUserName::class)
            ->add(\Zank\Middleware\AuthenticationUserToken::class)
            ->add(\Zank\Middleware\InitDb::class);

        // 登陆
        $this
            ->post('/in', \Zank\Controller\Api\Sign::class.':in')
            ->add(\Zank\Middleware\Sign\In\ValidateUserByPhone::class)
            ->add(\Zank\Middleware\InitDb::class);

        // 刷新token
        $this
            ->post('/refresh-token', \Zank\Controller\Api\Sign::class.':refreshToken')
            ->add(\Zank\Middleware\InitDb::class);
    });

    // 验证码相关
    $this->group('/captcha', function () {
        // 索引
        $this->any('', function (Request $request, Response $response): Response {
            $apiList = [
                '/api/captcha/phone/get/register' => '获取手机号码验证码',
                '/api/captcha/phone/has'          => '验证手机号码验证码',
            ];

            return $response->withJson($apiList);
        });

        // 获取手机号码验证码
        $this
            ->post('/phone/get/register', \Zank\Controller\Api\Captcha\Phone::class.':get')
            ->add(\Zank\Middleware\Sign\Up\ValidateUserByPhone::class)
            ->add(\Zank\Middleware\InitDb::class);

        // 验证手机号码验证码
        $this
            ->post('/phone/has', \Zank\Controller\Api\Captcha\Phone::class.':has')
            ->add(\Zank\Middleware\Captcha\ValidateByPhoneCaptcha::class)
            ->add(\Zank\Middleware\InitDb::class);
    });
});
