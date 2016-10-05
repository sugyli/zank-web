<?php

namespace Zank\Console\Command;

use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Zank\Application as Web;

class TableImportCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('db:import')
            ->setDescription('Import tables to database.')
            ->setHelp('This command allows you to import tables to database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Import tables to database.',
            '======================',
            '',
        ]);

        // init database;
        Web::getContainer()->get('db');

        $tablesDir = database_source_dir().'/tables';
        $finder = new Finder();
        $finder
            ->files()
            ->in($tablesDir)
            ->name('*.php');


        foreach ($finder as $file) {
            $tableName = $file->getBasename('.php');
            $filename = $file->getPathname();
            $handle = require $filename;

            Capsule::Schema()->dropIfExists($tableName); // 删除数据库的表
            Capsule::Schema()->create($tableName, $handle);

            $output->writeln('Create table:'.$tableName.' <fg=green>OK.</>');
        }

        $output->writeln(sprintf('<info>Import table num:%d</info>', $finder->count()));
    }
}
