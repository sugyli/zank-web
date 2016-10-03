<?php

namespace Zank\Middleware\User\Change;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zank\Traits\Container;

class Username
{
    use Container;

    public function __invoke(Request $request, Response $response, callable $next)
    {
        var_dump($this->ci);
        exit;
    }
}
