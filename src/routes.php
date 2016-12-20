<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zank\Application;

Application::any('/', \Zank\Controller\Novel\Wap\Control::class.':home')
->add(\Zank\Middleware\InitDb::class)
->add(\Zank\Middleware\ExceptionHandle2API::class);
//->add(new \Slim\HttpCache\Cache('public', 86400));


Application::any('/sort-{sortid:[1-9]\d*}-{page:[1-9]\d*}[/]', \Zank\Controller\Novel\Wap\Control::class.':home')
->add(\Zank\Middleware\InitDb::class)
->add(\Zank\Middleware\ExceptionHandle2API::class)
->setName('novelsort');

Application::any('/sort-{sortid:[1-9]\d*}-{page:[1-9]\d*}/index.html', \Zank\Controller\Novel\Wap\Control::class.':home')
->add(\Zank\Middleware\InitDb::class)
->add(\Zank\Middleware\ExceptionHandle2API::class);

/*
Application::post('/novel/sort/mindexpost', \Zank\Controller\Novel\Wap\Control::class.':mIndexPost')
->add(\Zank\Middleware\InitDb::class)
->add(\Zank\Middleware\ExceptionHandle2API::class)
->setName('mindexpost');
*/
//介绍
Application::any('/info-{bookid:[1-9]\d*}[/]', \Zank\Controller\Novel\Wap\Control::class.':info')
->add(\Zank\Middleware\InitDb::class)
->add(\Zank\Middleware\ExceptionHandle2API::class)
->setName('novelinfo');

Application::any('/info-{bookid:[1-9]\d*}/index.html', \Zank\Controller\Novel\Wap\Control::class.':info')
->add(\Zank\Middleware\InitDb::class)
->add(\Zank\Middleware\ExceptionHandle2API::class);

//目录
//http://www.sugyli.com/wapbook-53530  
//http://www.sugyli.com/wapbook-53530_1
//http://www.sugyli.com/wapbook-53530_1/

Application::any('/wapbook-{bookid:[1-9]\d*}[_{page:[1-9]\d*}[/]]', \Zank\Controller\Novel\Wap\Control::class.':mulu')
->add(\Zank\Middleware\InitDb::class)
->add(\Zank\Middleware\ExceptionHandle2API::class)
->setName('novelmulu1');

//http://www.sugyli.com/wapbook-53530/index.html
Application::any('/wapbook-{bookid:[1-9]\d*}/index.html', \Zank\Controller\Novel\Wap\Control::class.':mulu')
->add(\Zank\Middleware\InitDb::class)
->add(\Zank\Middleware\ExceptionHandle2API::class);

//http://www.sugyli.com/wapbook-53530_1/index.html
Application::any('/wapbook-{bookid:[1-9]\d*}_{page:[1-9]\d*}/index.html', \Zank\Controller\Novel\Wap\Control::class.':mulu')
->add(\Zank\Middleware\InitDb::class)
->add(\Zank\Middleware\ExceptionHandle2API::class);


//目录倒序
//http://www.sugyli.com/wapbook-53530_1_1/  
//http://www.sugyli.com/wapbook-53530_1_1
Application::any('/wapbook-{bookid:[1-9]\d*}_{page:[1-9]\d*}_{sort:[1-9]\d*}[/]', \Zank\Controller\Novel\Wap\Control::class.':mulu')
->add(\Zank\Middleware\InitDb::class)
->add(\Zank\Middleware\ExceptionHandle2API::class)
->setName('novelmulu2');

//http://www.sugyli.com/wapbook-53530_1_1/index.html
Application::any('/wapbook-{bookid:[1-9]\d*}_{page:[1-9]\d*}_{sort:[1-9]\d*}/index.html', \Zank\Controller\Novel\Wap\Control::class.':mulu')
->add(\Zank\Middleware\InitDb::class)
->add(\Zank\Middleware\ExceptionHandle2API::class);

//内容
Application::any('/wapbook-{bid:[1-9]\d*}-{cid:[1-9]\d*}[/]', \Zank\Controller\Novel\Wap\Control::class.':content')
->add(\Zank\Middleware\InitDb::class)
->add(\Zank\Middleware\ExceptionHandle2API::class)
->setName('novelcontent');

Application::any('/wapbook-{bid:[1-9]\d*}-{cid:[1-9]\d*}/index.html', \Zank\Controller\Novel\Wap\Control::class.':content')
->add(\Zank\Middleware\InitDb::class)
->add(\Zank\Middleware\ExceptionHandle2API::class);


Application::group('/novel', function () {
    $this->any('', function (Request $request, Response $response): Response {
        return $response->withRedirect((string) "/", 301);
    });

    $this
        ->post('/sort/mindexpost', \Zank\Controller\Novel\Wap\Control::class.':mIndexPost')
        ->add(\Zank\Middleware\InitDb::class)
        ->setName('mindexpost');

    $this
        ->any('/search', \Zank\Controller\Novel\Wap\Control::class.':search')
        ->add(\Zank\Middleware\InitDb::class)
        ->add(\Zank\Middleware\ExceptionHandle2API::class)
        ->setName('search');

    $this
        ->any('/user/login', \Zank\Controller\Novel\User\Control::class.':login')
        ->setName('loginurl');
    //图形验证
    $this
        ->any('/user/code', \Zank\Controller\Novel\User\Control::class.':code')
        ->setName('usercode');
    // 找回密码
    $this
        ->post('/user/forgetpass', \Zank\Controller\Novel\User\Control::class.':forgetpass')
        ->add(\Zank\Middleware\Captcha\ValidateByPhoneCaptchaByZank::class)
        ->add(\Zank\Middleware\Sign\Up\ValidateUserByPhoneByZank::class)
        ->add(\Zank\Middleware\InitDb::class);

    // 基本信息注册
    $this
        ->post('/user/sign', \Zank\Controller\Novel\User\Control::class.':stepRegisterBase')
        ->add(\Zank\Middleware\Captcha\ValidateByPhoneCaptchaByZank::class)
        ->add(\Zank\Middleware\Sign\Up\ValidateUserByPhoneByZank::class)
        ->add(\Zank\Middleware\InitDb::class);

    // 登陆
    $this
        ->post('/user/in', \Zank\Controller\Novel\User\Control::class.':in')
        ->add(\Zank\Middleware\Sign\In\ValidateUserByPhoneByZank::class)
        ->add(\Zank\Middleware\InitDb::class);
    //用户中心
    $this
        ->get('/user/usercore', \Zank\Controller\Novel\User\Control::class.':usercore')
        ->add(\Zank\Middleware\AuthenticationUserTokenByZank::class)
        ->add(\Zank\Middleware\InitDb::class)
        ->setName('usercore');   
    $this
        ->get('/user/bookcase', \Zank\Controller\Novel\User\Control::class.':bookcase')
        ->add(\Zank\Middleware\AuthenticationUserTokenByZank::class)
        ->add(\Zank\Middleware\InitDb::class)
        ->setName('bookcase');

    $this
        ->get('/user/readbookcase/{bid:[1-9]\d*}/{cid:[1-9]\d*}', \Zank\Controller\Novel\User\Control::class.':readbookcase')
        ->add(\Zank\Middleware\AuthenticationUserTokenByZank::class)
        ->add(\Zank\Middleware\InitDb::class)
        ->setName('readbookcase'); 
    $this
        ->get('/user/mailbox[/{type:[1-9]\d*}]', \Zank\Controller\Novel\User\Control::class.':mailbox')
        ->add(\Zank\Middleware\AuthenticationUserTokenByZank::class)
        ->add(\Zank\Middleware\InitDb::class)
        ->setName('mailbox'); 


    //api接口
    $this->group('/api', function () {

        // 获取手机号码验证码
        $this
            ->post('/phone/get/verify', \Zank\Controller\Api\Captcha\ZankPhone::class.':get')
            ->add(\Zank\Middleware\Sign\Up\ValidateUserByPhoneByZank::class)
            ->add(\Zank\Middleware\InitDb::class);
        $this
            ->post('/post/receivemail', \Zank\Controller\Novel\User\Control::class.':receiveMail')
            ->add(\Zank\Middleware\AuthenticationUserTokenByZank::class)
            ->add(\Zank\Middleware\InitDb::class);
        $this
            ->post('/post/delmail', \Zank\Controller\Novel\User\Control::class.':delMail')
            ->add(\Zank\Middleware\AuthenticationUserTokenByZank::class)
            ->add(\Zank\Middleware\InitDb::class);
        $this
            ->post('/post/delbookcase', \Zank\Controller\Novel\User\Control::class.':delbookcase')
            ->add(\Zank\Middleware\AuthenticationUserTokenByZank::class)
            ->add(\Zank\Middleware\InitDb::class);
        $this
            ->post('/post/addbookcase', \Zank\Controller\Novel\User\Control::class.':addbookcase')
            ->add(\Zank\Middleware\AuthenticationUserTokenByZank::class)
            ->add(\Zank\Middleware\InitDb::class);
        $this
            ->post('/post/clock', \Zank\Controller\Novel\User\Control::class.':clock')
            ->add(\Zank\Middleware\AuthenticationUserTokenByZank::class)
            ->add(\Zank\Middleware\InitDb::class);
        $this
            ->post('/post/exitweb', \Zank\Controller\Novel\User\Control::class.':exitweb')
            ->add(\Zank\Middleware\AuthenticationUserTokenByZank::class)
            ->add(\Zank\Middleware\InitDb::class);
    });

})
->add(\Zank\Middleware\ExceptionHandle2API::class);

