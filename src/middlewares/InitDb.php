<?php

namespace Zank\Middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Interop\Container\ContainerInterface;

/**
 * 初始化数据库连接
 *
 * @package default
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class InitDb
{
    protected $ci;

    public function __construct(ContainerInterface $ci) {
        $this->ci = $ci;
    }

    /**
     * Init database middleware invokable class.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param  \callable                           $next     Next middleware
     * @return \Psr\Http\Message\ResponseInterface
     * @author Seven Du <lovevipdsw@outlook.com>
     **/
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $this->ci->get('db');

        return $next($request, $response);
    }
} // END class TrailingSlash
