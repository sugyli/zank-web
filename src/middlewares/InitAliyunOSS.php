<?php

namespace Zank\Middleware;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * 初始化阿里云oss连接.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class InitAliyunOSS
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    /**
     * Init oss middleware invokable class.
     *
     * @param \Psr\Http\Message\RequestInterface  $request  PSR7 request
     * @param \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param callable                            $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     **/
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $this->ci->get('oss');

        return $next($request, $response);
    }
} // END class InitDb
