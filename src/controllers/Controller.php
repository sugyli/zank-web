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

    /**
     * 构造方法，注入ci
     *
     * @param ContainerInterface $ci 注入器
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function __construct(ContainerInterface $ci) {
        $this->ci = $ci;
    }

} // END class Controller
