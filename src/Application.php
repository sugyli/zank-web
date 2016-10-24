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

    private static $requireFiles = [];

    public function __construct($container)
    {
        if (!self::$application instanceof App) {
            self::$application = new App($container);
        }
    }

    public function run(array $requireFiles = [])
    {
        self::requires($requireFiles);

        $slient = strtolower(PHP_SAPI) === 'cli';
        $client = [
            'web' => self::$application->run($slient),
        ];

        if ($slient === true) {
            $client['cli'] = new Console\Application();
            $client['cli-return'] = $client['cli']->run();
        }

        return self::$client = $client;
    }

    public static function getClient()
    {
        return self::$client;
    }

    public static function requires(array $requireFiles = [], bool $notOne = false)
    {
        $rets = [];

        foreach ($requireFiles as $key => $file) {
            if (!isset(self::$requireFiles[$file]) || $notOne === true) {
                self::$requireFiles[$file] = $rets[$key] = require $file;
            } else {
                $rets[$key] = self::$requireFiles[$file];
            }
        }

        return $rets;
    }

    public static function __callStatic($funcname, $arguments)
    {
        if (self::$application instanceof App) {
            return call_user_func_array([self::$application, $funcname], $arguments);
        }

        throw new \Exception(sprintf('Error: Not new the %s class.', __CLASS__));
    }
} // END class Application
