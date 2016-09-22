<?php

namespace Zank\Console\Command;

use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class TableOutCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('db:delete')
            ->setDescription('Delete all tables.')
            ->setHelp('This command delete all tables to database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Delete all tables.',
            '======================',
            '',
        ]);

        $tablesDir = dirname(__DIR__).'/db/tables';
        $finder = new Finder();
        $finder
            ->files()
            ->in($tablesDir)
            ->name('*.php');

        $i = 0;
        foreach ($finder as $file) {
            $tableName = $file->getBasename('.php');
            Capsule::Schema()->dropIfExists($tableName); // 删除数据库的表
            $output->writeln('Delete table:'.$tableName.' <fg=green>OK.</>');
            $i++;
        }

        $output->writeln(sprintf('<info>Delete table num:%d</info>', $i));
    }
}
