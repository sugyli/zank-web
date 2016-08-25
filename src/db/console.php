<?php

$settings = require dirname(__DIR__).'/settings.php';

try {

    $settings = $settings['settings']['db'];
    $settings = $settings['connections'][$settings['default']];

    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($settings);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();
} catch (Exception $e) {
    echo $e->getMassage(), "\n";
    exit;
}