<?php

use Slim\Container;

// DIC configuration
$container = app()->getContainer();

// monolog
$container['logger'] = function (Container $c): \Monolog\Logger {
    $settings = $c->get('settings')['logger'];
    $logger = new \Monolog\Logger($settings['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

    return $logger;
};

// Service factory for the Eloquent ORM.
$container['db'] = function (Container $c): \Illuminate\Database\Capsule\Manager {
    $settings = $c->get('settings')->get('db');
    $settings = $settings['connections'][$settings['default']];

    $capsule = new \Illuminate\Database\Capsule\Manager();
    $capsule->addConnection($settings);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

// Service aliyun oss.
$container['oss'] = function (Container $c) {
    $settings = $c->get('settings')->get('oss');

    $oss = new \Medz\Component\StreamWrapper\AliyunOSS($settings['accessKeyId'], $settings['accessKeySecret'], $settings['endpoint']);
    $oss->setBucket($settings['bucket']);
    $oss->registerStreamWrapper('oss');

    return $oss;
};
