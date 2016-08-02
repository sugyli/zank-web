<?php

$settings = require dirname(__DIR__).'/settings.php';

try {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($settings['settings']['db']);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();
} catch (Exception $e) {
    echo $e->getMassage(), "\n";
    exit;
}