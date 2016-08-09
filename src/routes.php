<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->group('/api', function() {
    // index
    $this->any('', function(Request $request, Response $response, array $args) {
        var_dump($args);
    });

    // 注册
    $this
        ->any('/sign-up', Zank\Controller\Api\SignUp::class)
        ->add(Zank\Middleware\InitDb::class)
    ;
});
