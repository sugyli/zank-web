<?php

namespace Zank\Util;

use Closure;
use Symfony\Component\Finder\Finder;

class DatabaseTablesIterator
{
    private $finder;

    public function __construct(array $tables = [])
    {
        $tablesDir = database_source_dir().'/tables';

        $this->finder = new Finder();

        $this->finder
            ->files()
            ->in($tablesDir);

        if (count($tables) > 0) {
            foreach ($tables as $table) {
                $filename = sprintf('%s.php', $table);
                $finder = Finder::create()->files()->in($tablesDir)->name($filename);
                if ($finder->count() <= 0) {
                    $filename = $tablesDir.'/'.$filename;
                    $filename = str_replace('/', DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $filename));
                    throw new \Exception(sprintf('Not find the "%s" table blueprint in "%s".', $table, $filename));
                }

                $this->finder->name($filename);
            }
        } else {
            $this->finder->name('*.php');
        }
    }

    public function forEach(Closure $callable)
    {
        foreach ($this->finder as $file) {
            $callable($file);
        }
    }

    public function count()
    {
        return $this->finder->count();
    }
}
