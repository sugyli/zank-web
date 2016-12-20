<?php

// Application middleware
// e.g: app()->add(new \Slim\Csrf\Guard);
//

// Trailing slash
\Zank\Application::add(Zank\Middleware\TrailingSlash::class);
\Zank\Application::add(new RKA\Middleware\IpAddress(true));//第2个参数设置信任IP 就是不详细查了
