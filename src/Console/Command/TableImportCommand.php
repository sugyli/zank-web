<?php

namespace Zank\Console\Command;

use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
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
        $io = new SymfonyStyle($input, $output);
        $io->title('导入数据表结构到数据库中');

        $confirm = $io->confirm('导入数据表会删除已有的数据表和数据,是否确定导入？', false);
        if ($confirm === false) {
            $output->writeln('<question>已经取消导入数据表结构.</question>');

            return ;
        }

        // init database;
        Web::getContainer()->get('db');

        $tablesDir = database_source_dir().'/tables';
        $finder = new Finder();
        $finder
            ->files()
            ->in($tablesDir)
            ->name('*.php');


        $io->progressStart($finder->count());
        $tables = [];

        foreach ($finder as $file) {
            $tableName = $file->getBasename('.php');
            $filename = $file->getPathname();

            try {
                $handle = require $filename;

                Capsule::Schema()->dropIfExists($tableName); // 删除数据库的表
                Capsule::Schema()->create($tableName, $handle);

                $tables[] = [$tableName, '<info>success</info>'];

            } catch (\Exception $e) {
                $tables[] = [$tableName, '<error>error</error>', $e->getMessage()];
            }

            $io->progressAdvance();
        }

        $io->progressFinish();
        $io->table(
            ['表名 [不含前缀]', '导入状态', 'Note'],
            $tables
        );
    }
}
