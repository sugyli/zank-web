<?php

namespace Zank;

use Interop\Container\ContainerInterface;

/**
 * Controller 基础类
 *
 * @package default
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
abstract class Controller
{
    protected $ci;

    public function __construct(ContainerInterface $ci) {
        $this->ci = $ci;
    }

} // END class Controller
