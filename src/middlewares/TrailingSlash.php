<?php

namespace Zank\Middleware;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * TrailingSlash 斜线结尾处理
 *
 * @package default
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class TrailingSlash
{
    /**
     * Trailing slash middleware invokable class.
     *
     * @param  \Psr\Http\Message\RequestInterface  $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface $response PSR7 response
     * @param  \callable                           $next     Next middleware
     * @return \Psr\Http\Message\ResponseInterface
     * @author Seven Du <lovevipdsw@outlook.com>
     **/
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if ($path != '/' && substr($path, -1) == '/') {
            // permanently redirect paths with a trailing slash
            // to their non-trailing counterpart
            $uri = $uri->withPath(substr($path, 0, -1));
            return $response->withRedirect((string) $uri, 301);
        }

        return $next($request, $response);
    }
} // END class TrailingSlash
