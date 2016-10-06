<?php

namespace Zank\Util;

use Closure;
use Symfony\Component\Finder\Finder;

class DatabaseTablesIterator
{
    private $finder;

    public function __construct()
    {
        $tablesDir = database_source_dir().'/tables';

        $this->finder = new Finder();

        $this->finder
            ->files()
            ->in($tablesDir)
            ->name('*.php');
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
