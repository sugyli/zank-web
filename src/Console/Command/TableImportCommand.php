<?php

namespace Zank\Console\Command;

use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zank\Util\DatabaseTablesIterator;
use Zank\Traits\InitDatabaseToConsole;

class TableImportCommand extends Command
{
    use InitDatabaseToConsole;

    protected function configure()
    {
        $this
            ->setName('db:import')
            ->addArgument('table', InputArgument::IS_ARRAY, '需要导入的表名称(用空格分隔多个名称).', [])
            ->addOption('y', 'y', InputOption::VALUE_NONE, '忽略删除警告询问.')
            ->setDescription('Import tables to database.')
            ->setHelp('This command allows you to import tables to database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('将数据结构导入到数据库.');
        $io->newLine(1);

        $tableNames = $input->getArgument('table');
        $files = new DatabaseTablesIterator($tableNames);

        $io->section('(setp:1) 删除数据库中的表结构和数据');

        $commandName = 'db:delet';
        $command = $this->getApplication()->find($commandName);
        $arguments = [
            'command' => $commandName,
            '--confirm' => '导入数据表会删除已有的数据表和数据,是否确定删除并导入？',
            '--confirm-no-message' => '已经取消导入数据表结构.',
            '--no-title' => true,
            'table' => $tableNames,
        ];

        $input->getOption('y') === true && $arguments['--y'] = true;

        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->run($greetInput, $output);

        if ($returnCode === -1) {
            return 1;
        }

        $io->section('(setp:2) 导入数据表结构到数据库中');

        $tables = [];
        $io->progressStart($files->count());

        $files->forEach(function ($file) use (&$tables, $io) {
            $tableName = $file->getBasename('.php');
            $filename = $file->getPathname();

            try {
                $handle = require $filename;
                Capsule::Schema()->create($tableName, $handle);
                $tables[] = [$tableName, '<info>success</info>'];
            } catch (\Exception $e) {
                $tables[] = [$tableName, '<error>error</error>', $e->getMessage()];
            }

            $io->progressAdvance();
        });

        $io->progressFinish();
        $io->table(
            ['表名 [不含前缀]', '导入状态', 'Note'],
            $tables
        );
    }
}
