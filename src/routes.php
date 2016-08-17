<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->group('/api', function () {
    // index
    $this->any('', function(Request $request, Response $response) {
        $apiList = [
            '/api/sign/' => '用户注册｜登陆',
        ];
        $response->withJson($apiList);

        return $response;
    });


    // 用户注册｜登陆
    $this->group('/sign', function () {
        // 索引
        $this->any('', function (Request $request, Response $response) {
            $response->withJson([
                '/api/sign/up' => '用户注册',
            ]);

            return $response;
        });

        // 注册
        $this
            ->any('/up', Zank\Controller\Api\Sign::class.':up')
            ->add(Zank\Middleware\InitDb::class)
        ;
    });
});
