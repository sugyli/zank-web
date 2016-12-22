<?php
namespace Zank\Middleware;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SetNoHttpCache
{

    protected $ci;
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
      $response = $response->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate');

      return $next($request, $response);
    }


}