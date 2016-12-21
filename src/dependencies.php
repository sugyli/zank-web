<?php
use Ark\Filecache\FileCache; //文件缓存类
use Slim\Container;

// DIC configuration
$container = \Zank\Application::getContainer();

// monolog
$container['commonlogger'] = function (Container $c): \Monolog\Logger {
   // $settings = $c->get('settings')['logger'];
    $logger = new \Monolog\Logger('commonlogger');
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());//添加了唯一标识符
    $logger->pushHandler(new \Monolog\Handler\StreamHandler(ROOT.'/logs/common.log', 100));
    //$logger->pushHandler(new \Monolog\Handler\FirePHPHandler(100));

    return $logger;
};

$container['importantlogger'] = function (Container $c): \Monolog\Logger {
   // $settings = $c->get('settings')['logger'];
    $logger = new \Monolog\Logger('importantlogger');
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());//添加了唯一标识符
    $logger->pushHandler(new \Monolog\Handler\StreamHandler(ROOT.'/logs/important.log', 100));
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

    $oss = new \Medz\Component\StreamWrapper\AliyunOSS\AliyunOSS($settings['accessKeyId'], $settings['accessKeySecret'], $settings['endpoint']);
    $oss->setBucket($settings['bucket']);
    $oss->registerStreamWrapper('oss');

    return $oss;
};

$container['cookies'] = function (Container $c) {
    $cookies = new \Slim\Http\Cookies($c['request']->getCookieParams());
    $cookies->setDefaults([
        'expires' => $c['settings']['cookie']['cookieLifetime'],
        'path' => $c['settings']['cookie']['cookiePath'],
        'domain' => $c['settings']['cookie']['cookieDomain'],
        'secure' => $c['settings']['cookie']['cookieSecure'],
        'httponly' => $c['settings']['cookie']['cookieHttpOnly']
    ]);
    return $cookies;
};

$container['view'] = function (Container $c) :\Slim\Views\Twig {
    //$settings = $c->get('settings')->get('view');
    $view = new \Slim\Views\Twig(ROOT . 'templates', [
        //'cache' => ROOT . $settings['cache']
        'cache' => false
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $c['router'],
        $c['request']->getUri()
    ));

    return $view;
};


$container['fcache'] = function (Container $c) {
  
    $fcache = new \Ark\Filecache\FileCache([
        'root' =>  ROOT . 'storage/fcache', // Cache root
        'ttl' => 60,                    // Time to live 
        'compress' => true,             // Compress data with gzcompress or not
        'serialize' => 'json',          // How to serialize data: json, php, raw
    ]);

    return $fcache;
};

$container['cache'] = function (Container $c) {
    return new \Slim\HttpCache\CacheProvider();
};


