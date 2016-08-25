<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Monolog settings
        'logger' => [
            'name' => 'zank',
            'level' => Monolog\Logger::DEBUG,
            'path' => __DIR__.'/../logs/app.log',
        ],
        // Eloquent settings
        'db' => [
            'default' => 'mysql',

            'connections' => [

                'sqlite' => [
                    'driver' => 'sqlite',
                    'database' => 'database.sqlite',
                    'prefix' => 'medz',
                ],

                'mysql' => [
                    'driver' => 'mysql',
                    'host' => 'localhost',
                    'port' => '3306',
                    'database' => 'zank',
                    'username' => 'root',
                    'password' => '',
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix' => 'medz_',
                    'strict' => false,
                    'engine' => null,
                ],

                'pgsql' => [
                    'driver' => 'pgsql',
                    'host' => 'localhost',
                    'port' => '5432',
                    'database' => 'zank',
                    'username' => 'zank',
                    'password' => '',
                    'charset' => 'utf8',
                    'prefix' => 'medz_',
                    'schema' => 'public',
                ],

            ],

        ],
    ],
];
