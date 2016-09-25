<?php

namespace Zank;

use Slim\App;

/**
 * Zank application.
 *
 * @author Seven Du <lovevipdsw@outlook.com> 
 **/
class Application
{
    protected static $application;

    private static $client;

    public function __construct($container)
    {
        if (!self::$application instanceof App) {
            self::$application = new App($container);
        }
    }

    public function run()
    {
        $slient = strtolower(PHP_SAPI) === 'cli';
        $client = [
            'web' => self::$application->run($slient),
        ];

        if ($slient) {
            $client['cli'] = new Console\Application();
            $client['cli-return'] = $client['cli']->run();
        }

        return self::$client = $client;
    }

    public static function __callStatic($funcname, $arguments)
    {
        if (self::$application instanceof app) {
            return call_user_func_array([self::$application, $funcname], $arguments);
        }

        throw new \Exception(sprintf('Error: Not new the %s class.', __CLASS__));
    }
} // END class Application
