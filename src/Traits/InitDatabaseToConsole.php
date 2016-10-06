<?php

namespace Zank\Traits;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zank\Application;

trait InitDatabaseToConsole
{
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        Application::getContainer()->get('db');

        parent::initialize($input, $output);
    }
}