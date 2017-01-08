<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zank\Application;

Application::any('/test', function (Request $request, Response $response) {
    $response = $response->withJson([1, 2, 3]);

    return $response;
})
->add(\Zank\Middleware\InitDb::class);

Application::group('/api', function () {
    // index
    $this->any('', function (Request $request, Response $response): Response {
        $apiList = [
            '/api/sign'    => '用户注册｜登陆',
            '/api/captcha' => '验证码',
            '/api/upload'  => '上传相关',
            '/api/user'    => '用户相关',
        ];

        return $response->withJson($apiList);
    });

    // 用户注册｜登陆
    $this->group('/sign', function () {
        // 索引
        $this->any('', \Zank\Controller\Api\Sign::class);

        // 基本信息注册
        $this
            ->post('/up/base', \Zank\Controller\Api\Sign::class.':stepRegisterBase')
            ->add(\Zank\Middleware\Sign\Up\ValidateUserInviteCode::class)
            ->add(\Zank\Middleware\Captcha\ValidateByPhoneCaptcha::class)
            ->add(\Zank\Middleware\Sign\Up\ValidateUserByPhone::class)
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

    // 上传附件相关
    $this
        ->group('/upload', function () {
            // 索引
            $this->any('', function (Request $request, Response $response) {
                $apiList = [
                    '/api/upload/attach' => '上传附件',
                    '/api/uplaod/avatar' => '上传头像',
                ];

                return $response->withJson($apiList);
            });

            // 上传附件
            $this
                ->post('/attach', \Zank\Controller\Api\Upload::class.':attach')
                ->add(\Zank\Middleware\AttachUpload::class);

            // 上传头像
            $this
                ->post('/avatar', \Zank\Controller\Api\Upload::class.':avatar')
                ->add(\Zank\Middleware\AttachUpload::class);
        })
        ->add(\Zank\Middleware\InitAliyunOss::class)
        ->add(\Zank\Middleware\AuthenticationUserToken::class)
        ->add(\Zank\Middleware\InitDb::class);

    // 用户相关
    $this
        ->group('/user', function () {
            // api 索引
            $this->any('', \Zank\Controller\Api\User::class);

            // change data
            $this
                ->post('/change', \Zank\Controller\Api\User::class.':changeDate')
                ->add(\Zank\Middleware\User\Change\Love::class)
                ->add(\Zank\Middleware\User\Change\Shape::class)
                ->add(\Zank\Middleware\User\Change\Role::class)
                ->add(\Zank\Middleware\User\Change\Kg::class)
                ->add(\Zank\Middleware\User\Change\Height::class)
                ->add(\Zank\Middleware\User\Change\Age::class)
                ->add(\Zank\Middleware\User\Change\Username::class);

            //  搜索用户接口
            $this->post('/search', \Zank\Controller\Api\User::class.':search');
        })
        ->add(\Zank\Middleware\AuthenticationUserToken::class)
        ->add(\Zank\Middleware\InitDb::class);

    // 首页用户
    $this->post('/users', \Zank\Controller\Api\User::class.':gets')
        ->add(\Zank\Middleware\AuthenticationUserToken::class)
        ->add(\Zank\Middleware\InitDb::class);
})
->add(\Zank\Middleware\ExceptionHandle2API::class);

// 附件相关
Application::get('/attach/{id:\d+}[/{type:[0|1]}]', function (Request $request, Response $response, $args) {
    $attach = \Zank\Model\Attach::find($args['id']);

    // 先不用判断是非存在oss中，如果是迁移，可能也有可能回源的附件。
    if (!$attach/* || file_exists(($ossPath = 'oss://'.$attach->path)) === false*/) {
        return $response
            ->withStatus(404)
            ->write('Page not found.');
    }

    $url = attach_url($attach->path);

    if ((bool) $request->getAttribute('type') === true) {
        return with(new \Zank\Common\Message($response, true, '', $url))
            ->withJson();
    }

    return $response
        ->withStatus(307)
        ->withRedirect($url);
})
->add(\Zank\Middleware\InitAliyunOss::class)
->add(\Zank\Middleware\InitDb::class);
