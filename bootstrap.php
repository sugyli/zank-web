<?php

/* 开启强类型模式 */
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

$settings = require __DIR__.'/src/settings.php';
$app = new \Zank\Application($settings);

return $app->run([
    // Set up dependencies
    __DIR__.'/src/dependencies.php',

    // Register middleware
    __DIR__.'/src/middleware.php',

    // Register routes
    __DIR__.'/src/routes.php',
]);
