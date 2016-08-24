<?php

use Slim\Container;

// DIC configuration
$container = $app->getContainer();

// monolog
$container['logger'] = function (Container $c): \Monolog\Logger
{
    $settings = $c->get('settings')['logger'];
    $logger = new \Monolog\Logger($settings['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

// Service factory for the Eloquent ORM.
$container['db'] = function (Container $c): \Illuminate\Database\Capsule\Manager
{
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($c->get('settings')['db']);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};
