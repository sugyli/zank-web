<?php 

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);
//

// Trailing slash
$app->add(Zank\Middleware\TrailingSlash::class);
