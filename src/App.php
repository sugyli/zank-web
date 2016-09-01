<?php

namespace Zank;

use Slim\App as SlimApp;

/**
 * Zank application.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class App extends SlimApp
{
    protected static $application;

    public function setAsGlobal()
    {
        self::$application = &$this;
    }

    public static function getApplication()
    {
        return self::$application;
    }
} // END class App extends SlimApp
