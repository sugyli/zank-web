<?php

/* 开启强类型模式 */
declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

$settings = require __DIR__.'/src/settings.php';
$app = new \Zank\Application($settings);

// Set up dependencies
require __DIR__.'/src/dependencies.php';

// Register middleware
require __DIR__.'/src/middleware.php';

// Register routes
require __DIR__.'/src/routes.php';

// Run app
return $app->run();
