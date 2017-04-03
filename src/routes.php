<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zank\Application;

Application::any('/', \Zank\Controller\Novel\Wap\Control::class.':home')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class);

//兼容总榜
Application::any('/top-{topname}-{id}[/]', \Zank\Controller\Novel\Wap\Control::class.':home')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class);


Application::any('/sort-{sortid:[1-9]\d*}-{page:[1-9]\d*}[/]', \Zank\Controller\Novel\Wap\Control::class.':home')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class)
->setName('novelsort');

Application::any('/sort-{sortid:[1-9]\d*}-{page:[1-9]\d*}/index.html', \Zank\Controller\Novel\Wap\Control::class.':home')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class);


//介绍
Application::any('/info-{bookid:[1-9]\d*}[/]', \Zank\Controller\Novel\Wap\Control::class.':info')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class)
->setName('novelinfo');

Application::any('/info-{bookid:[1-9]\d*}/index.html', \Zank\Controller\Novel\Wap\Control::class.':info')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class);

//目录
//http://www.sugyli.com/wapbook-53530  
//http://www.sugyli.com/wapbook-53530_1
//http://www.sugyli.com/wapbook-53530_1/


Application::any('/wapbook-{bookid:[1-9]\d*}[_{page:[1-9]\d*}[/]]', \Zank\Controller\Novel\Wap\Control::class.':mulu')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class)
->setName('novelmulu1');
//http://www.sugyli.com/wapbook-53530/
Application::any('/wapbook-{bookid:[1-9]\d*}/', \Zank\Controller\Novel\Wap\Control::class.':mulu')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class);

//http://www.sugyli.com/wapbook-53530/index.html
Application::any('/wapbook-{bookid:[1-9]\d*}/index.html', \Zank\Controller\Novel\Wap\Control::class.':mulu')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class);

//http://www.sugyli.com/wapbook-53530_1/index.html
Application::any('/wapbook-{bookid:[1-9]\d*}_{page:[1-9]\d*}/index.html', \Zank\Controller\Novel\Wap\Control::class.':mulu')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class);


//目录倒序
//http://www.sugyli.com/wapbook-53530_1_1/  
//http://www.sugyli.com/wapbook-53530_1_1
Application::any('/wapbook-{bookid:[1-9]\d*}_{page:[1-9]\d*}_{sort:[1-9]\d*}[/]', \Zank\Controller\Novel\Wap\Control::class.':mulu')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class)
->setName('novelmulu2');

//http://www.sugyli.com/wapbook-53530_1_1/index.html
Application::any('/wapbook-{bookid:[1-9]\d*}_{page:[1-9]\d*}_{sort:[1-9]\d*}/index.html', \Zank\Controller\Novel\Wap\Control::class.':mulu')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class);

//内容
Application::any('/wapbook-{bid:[1-9]\d*}-{cid:[1-9]\d*}[/]', \Zank\Controller\Novel\Wap\Control::class.':content')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class)
->setName('novelcontent');

Application::any('/wapbook-{bid:[1-9]\d*}-{cid:[1-9]\d*}/index.html', \Zank\Controller\Novel\Wap\Control::class.':content')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class);
//百度搜索
Application::any('/baidusearch', \Zank\Controller\Novel\Wap\Control::class.':baidusearch')
    ->add(\Zank\Middleware\InitDb::class)
    ->add(new \Slim\HttpCache\Cache('private', WEBCASE))
    ->setName('baidusearch');
Application::any('/linshishujia', \Zank\Controller\Novel\Wap\Control::class.':linshishujia')
    ->add(\Zank\Middleware\InitDb::class)
    ->add(new \Slim\HttpCache\Cache('private', WEBCASE))
    ->setName('linshishujia');
//M搜索地图
Application::any('/map/msitemap', \Zank\Controller\Novel\Wap\Control::class.':mSiteMap')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class)
->setName('msitemap');

Application::any('/map/mbookmap/{page:[1-9]\d*}', \Zank\Controller\Novel\Wap\Control::class.':mSiteMap')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class)
->setName('mbookmap');

Application::any('/map/mnewbookmap/{page:[1-9]\d*}', \Zank\Controller\Novel\Wap\Control::class.':mSiteMap')
->add(\Zank\Middleware\InitDb::class)
->add(new \Slim\HttpCache\Cache('private', WEBCASE))
->add(\Zank\Middleware\ExceptionHandle2API::class);

Application::group('/novel', function () {
    $this->any('', function (Request $request, Response $response): Response {
        return $response->withRedirect((string) "/", 302);
    });

    $this
        ->post('/sort/mindexpost', \Zank\Controller\Novel\Wap\Control::class.':mIndexPost')
        ->add(\Zank\Middleware\InitDb::class)
        ->setName('mindexpost');

    $this
        ->get('/checkup/{bookid:[1-9]\d*}', \Zank\Controller\Novel\Wap\Control::class.':upsqldata')
        ->add(\Zank\Middleware\InitDb::class)
        ->setName('checkupsql');

    $this
        ->any('/search', \Zank\Controller\Novel\Wap\Control::class.':search')
        ->add(\Zank\Middleware\InitDb::class)
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
    //用户书架  
    $this
        ->get('/user/bookcase[/{bookcasepage:[1-9]\d*}]', \Zank\Controller\Novel\User\Control::class.':bookcase')
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
->add(\Zank\Middleware\SetNoHttpCache::class)
->add(\Zank\Middleware\ExceptionHandle2API::class);


Application::group('/novelapp', function () {
    $this
        ->post('/mainlist', \Zank\Controller\NovelApp\AppControl::class.':mainList')
        ->add(\Zank\Middleware\InitDb::class);
    $this
        ->post('/bookinfolist', \Zank\Controller\NovelApp\AppControl::class.':bookInfoList')
        ->add(\Zank\Middleware\InitDb::class);
    $this
        ->post('/bookmuluindex', \Zank\Controller\NovelApp\AppControl::class.':bookMuluIndex')
        ->add(\Zank\Middleware\InitDb::class);
    $this
        ->post('/bookcontent', \Zank\Controller\NovelApp\AppControl::class.':bookContent')
        ->add(\Zank\Middleware\InitDb::class);

        // 获取手机号码验证码
    $this
        ->post('/verify', \Zank\Controller\Api\Captcha\ZankPhone::class.':get')
        ->add(\Zank\Middleware\Sign\Up\ValidateAppUserByPhoneByZank::class)
        ->add(\Zank\Middleware\InitDb::class);
        // 登陆
    $this
        ->post('/login', \Zank\Controller\Novel\User\Control::class.':appIn')
        ->add(\Zank\Middleware\Sign\In\ValidateAppUserByPhoneByZank::class)
        ->add(\Zank\Middleware\InitDb::class);
     // 基本信息注册
    $this
        ->post('/sign', \Zank\Controller\Novel\User\Control::class.':stepAppRegisterBase')
        ->add(\Zank\Middleware\Captcha\ValidateByPhoneCaptchaByZank::class)
        ->add(\Zank\Middleware\Sign\Up\ValidateAppUserByPhoneByZank::class)
        ->add(\Zank\Middleware\InitDb::class); 
    // 找回密码
    $this
        ->post('/forgetpass', \Zank\Controller\Novel\User\Control::class.':appForgetpass')
        ->add(\Zank\Middleware\Captcha\ValidateByPhoneCaptchaByZank::class)
        ->add(\Zank\Middleware\Sign\Up\ValidateAppUserByPhoneByZank::class)
        ->add(\Zank\Middleware\InitDb::class);   

    //用户书架  分界线
    $this
        ->post('/bookcase', \Zank\Controller\Novel\User\Control::class.':appBookcase')
        ->add(\Zank\Middleware\AuthenticationUserToken::class)
        ->add(\Zank\Middleware\InitDb::class);

    $this
        ->post('/readbookcase', \Zank\Controller\Novel\User\Control::class.':appReadbookcase')
        ->add(\Zank\Middleware\AuthenticationUserToken::class)
        ->add(\Zank\Middleware\InitDb::class);


})
->add(\Zank\Middleware\ExceptionHandle2API::class);



