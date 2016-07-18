<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
    ],

    // Monolog settings
    'logger' => [
        'name' => 'zank',
        'padt' => __DIR__.'/../logs/app.log',
    ],

    // Eloquent settings
    'db' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'zank',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ],
];