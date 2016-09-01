<?php

namespace Zank\Console;

use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class TableImportCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('db:import')
            ->setDescription('Import tables to database.')
            ->setHelp('This command allows you to import tables to database.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Import tables to database.',
            '======================',
            '',
        ]);

        $tablesDir = dirname(__DIR__).'/db/tables';
        $finder = new Finder();
        $finder
            ->files()
            ->in($tablesDir)
            ->name('*.php')
        ;

        $i = 0;
        foreach ($finder as $file) {
            $tableName = $file->getBasename('.php');
            Capsule::Schema()->dropIfExists($tableName); // 删除数据库的表
            $handle = require $tablesDir.'/'.$tableName.'.php';
            Capsule::Schema()->create($tableName, $handle);
            $output->writeln('Create table:'.$tableName.' <fg=green>OK.</>');
            $i++;
        }

        $output->writeln(sprintf('<info>Import table num:%d</info>', $i));
    }
}
