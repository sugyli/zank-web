<?php

/* 开启强类型模式 */
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/src/main.php';

$settings = require __DIR__.'/src/settings.php';
$app = new \Zank\Application($settings);

/*
 *  运行程序，并返回程序储存的资源数组。
 */
return $app->run([
    // Set up dependencies
    __DIR__.'/src/dependencies.php',

    // Register middleware
    __DIR__.'/src/middleware.php',

    // Register routes
    __DIR__.'/src/routes.php',
]);
