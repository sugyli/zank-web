<?php

namespace Zank\Console;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public static $name = 'Zank Command Tool';
    public static $version = '1.0.0';

    public function __construct()
    {
        error_reporting(-1);

        parent::__construct(self::$name, self::$version);

        $this->add(new Command\TableImportCommand());
        $this->add(new Command\TableOutCommand());
        $this->setDefaultCommand('list');
    }
}
