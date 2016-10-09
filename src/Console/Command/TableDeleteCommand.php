<?php

namespace Zank\Console\Command;

use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zank\Util\DatabaseTablesIterator;
use Zank\Traits\InitDatabaseToConsole;

class TableDeleteCommand extends Command
{
    use InitDatabaseToConsole;

    protected function configure()
    {
        $this
            ->setName('db:delete')
            ->addArgument('table', InputArgument::IS_ARRAY, '需要数据表名称(用空格分隔多个名称).', [])
            ->addOption('y', 'y', InputOption::VALUE_NONE, '忽略删除警告询问.')
            ->addOption('confirm', null, InputOption::VALUE_OPTIONAL, '设置询问消息.', '删除数据库表结构会连同数据一起删除，是否确定删除？')
            ->addOption('confirm-no-message', null, InputOption::VALUE_OPTIONAL, '设置取消命令输出消息.', '已经取消删除数据表结构.')
            ->addOption('no-title', null, InputOption::VALUE_NONE, '不显示命令标题.')
            ->setDescription('Delete all tables.')
            ->setHelp('This command delete all tables to database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $tableNames = $input->getArgument('table');
        $files = new DatabaseTablesIterator($tableNames);

        $input->getOption('no-title') === false && $io->title('删除数据库中所有的表结构和数据');

        $io->note('建议先对数据库进行备份.');
        if ($input->getOption('y') === false) {
            $confirm = (bool) $io->confirm($input->getOption('confirm'), false);
            if ($confirm === false) {
                $output->writeln(
                    sprintf(
                        '<question>%s</question>',
                        $input->getOption('confirm-no-message')
                    )
                );

                return -1;
            }
        }

        $io->progressStart($files->count());
        $tables = [];

        $files->forEach(function ($file) use (&$tables, $io) {
            $tableName = $file->getBasename('.php');

            try {
                Capsule::Schema()->dropIfExists($tableName); // 删除数据库的表
            } catch (\Exception $e) {
                $tables[] = [$tableName, $e->getMessage()];
            }

            $io->progressAdvance();
        });

        $io->progressFinish();
        $this->showTables($tables, $io);
    }

    protected function showTables(array $tables = [], SymfonyStyle $io)
    {
        if (count($tables) > 0) {
            $io->table(
                ['表名 [不含前缀]', '错误消息'],
                $tables
            );
        }
    }
}
