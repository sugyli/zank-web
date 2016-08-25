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

        // 注册
        $this
            ->post('/up', \Zank\Controller\Api\Sign::class.':up')
            ->add(\Zank\Middleware\Sign\Up\ValidateUserByUserName::class)
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
});
