<?php

namespace Zank\Traits;

use Interop\Container\ContainerInterface;

trait Container
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }
}
