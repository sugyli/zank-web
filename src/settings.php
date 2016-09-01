<?php

return [
    'settings' => [
        'displayErrorDetails'    => env('DISPLAY_ERROR_DETAILS', true), // set to false in production
        'addContentLengthHeader' => env('ADD_CONTENT_LENGTH_HEADER', false), // Allow the web server to send the content-length header

        // Monolog settings
        'logger' => [
            'name'  => env('LOGGER_NAME', 'zank'),
            'level' => env('LOGGER_LEVEL', Monolog\Logger::DEBUG),
            'path'  => dirname(__DIR__).env('LOGGER_PATH', '/logs/app.log'),
        ],

        // Eloquent settings
        'db' => [
            'default' => env('DB_DRIVER', 'pgsql'),

            'connections' => [

                'sqlite' => [
                    'driver'   => 'sqlite',
                    'database' => env('DB_DATABASE', 'database.sqlite'),
                    'prefix'   => env('DB_PREFIX', 'medz_'),
                ],

                'mysql' => [
                    'driver'    => 'mysql',
                    'host'      => env('DB_HOST', 'localhost'),
                    'port'      => env('DB_PORT', 3306),
                    'database'  => env('DB_DATABASE', 'zank'),
                    'username'  => env('DB_USERNAME', 'root'),
                    'password'  => env('DB_PASSWORD', ''),
                    'charset'   => env('DB_CHARSET', 'utf8'),
                    'collation' => env('DB_COLLATION', 'utf8_unicode_ci'),
                    'prefix'    => env('DB_PREFIX', 'medz_'),
                    'strict'    => env('DB_STRICT', false),
                    'engine'    => env('DB_ENGINE', null),
                ],

                'pgsql' => [
                    'driver'   => 'pgsql',
                    'host'     => env('DB_HOST', 'localhost'),
                    'port'     => env('DB_PORT', 5432),
                    'database' => env('DB_DATABASE', 'zank'),
                    'username' => env('DB_USERNAME', 'root'),
                    'password' => env('DB_PASSWORD', ''),
                    'charset'  => env('DB_CHARSET', 'utf8'),
                    'prefix'   => env('DB_PREFIX', 'medz_'),
                    'schema'   => env('DB_SCHEMA', 'public'),
                ],

            ],

        ],

        'send:phone:captcha:space' => env('SEND_PHONE_CAPTCHA_SPACE', 300), // 发送验证码间隔时间 s

        // Aliyun OSS settings
        'oss' => [
            'accessKeyId' => env('OSS_ACCESS_KEY_ID', ''),
            'accessKeySecret' => env('OSS_ACCESS_KEY_SECRET', ''),
            'endpoint' => env('OSS_ENDPOINT', 'oss-cn-hangzhou.aliyuncs.com'),
            'bucket' => env('OSS_BUCKET', '')
        ],
    ],
];
