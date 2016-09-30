<?php

return [

    'debug' => cfg('DEBUG', true),

    'settings' => [
        'displayErrorDetails' => cfg('DISPLAY_ERROR_DETAILS', true), // set to false in production
        'addContentLengthHeader' => cfg('ADD_CONTENT_LENGTH_HEADER', false), // Allow the web server to send the content-length header

        // Monolog settings
        'logger' => [
            'name' => cfg('LOGGER_NAME', 'zank'),
            'level' => cfg('LOGGER_LEVEL', Monolog\Logger::DEBUG),
            'path' => dirname(__DIR__).cfg('LOGGER_PATH', '/logs/app.log'),
        ],

        // Eloquent settings
        'db' => [
            'default' => cfg('DB_DRIVER', 'pgsql'),

            'connections' => [

                'sqlite' => [
                    'driver' => 'sqlite',
                    'database' => cfg('DB_DATABASE', 'database.sqlite'),
                    'prefix' => cfg('DB_PREFIX', 'medz_'),
                ],

                'mysql' => [
                    'driver' => 'mysql',
                    'host' => cfg('DB_HOST', 'localhost'),
                    'port' => cfg('DB_PORT', 3306),
                    'database' => cfg('DB_DATABASE', 'zank'),
                    'username' => cfg('DB_USERNAME', 'root'),
                    'password' => cfg('DB_PASSWORD', ''),
                    'charset' => cfg('DB_CHARSET', 'utf8'),
                    'collation' => cfg('DB_COLLATION', 'utf8_unicode_ci'),
                    'prefix' => cfg('DB_PREFIX', 'medz_'),
                    'strict' => cfg('DB_STRICT', false),
                    'engine' => cfg('DB_ENGINE', null),
                ],

                'pgsql' => [
                    'driver' => 'pgsql',
                    'host' => cfg('DB_HOST', 'localhost'),
                    'port' => cfg('DB_PORT', 5432),
                    'database' => cfg('DB_DATABASE', 'zank'),
                    'username' => cfg('DB_USERNAME', 'root'),
                    'password' => cfg('DB_PASSWORD', ''),
                    'charset' => cfg('DB_CHARSET', 'utf8'),
                    'prefix' => cfg('DB_PREFIX', 'medz_'),
                    'schema' => cfg('DB_SCHEMA', 'public'),
                ],

            ],

        ],

        'send:phone:captcha:space' => cfg('SEND_PHONE_CAPTCHA_SPACE', 300), // 发送验证码间隔时间 s

        // Aliyun OSS settings
        'oss' => [
            'accessKeyId' => cfg('OSS_ACCESS_KEY_ID', ''),
            'accessKeySecret' => cfg('OSS_ACCESS_KEY_SECRET', ''),
            'endpoint' => cfg('OSS_ENDPOINT', 'oss-cn-hangzhou.aliyuncs.com'),
            'bucket' => cfg('OSS_BUCKET', ''),
            'source_url' => cfg('OSS_SOURCE_URL', ''),
            'sign' => cfg('OSS_SOURCE_SIGN', false),
            'timeout' => cfg('OSS_SIGN_TIMEOUT', 1800),
        ],
    ],
];
