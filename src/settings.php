<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Monolog settings
        'logger' => [
            'name' => 'zank',
            'level' => Monolog\Logger::DEBUG,
            'padt' => __DIR__.'/../logs/app.log',
        ],
        // Eloquent settings
        'db' => [
            'driver' => 'pgsql',
            'host' => '127.0.0.1',
            'database' => 'zank',
            'username' => 'zank',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => 'medz_',
            'port' => 3432
        ],
    ],
];
