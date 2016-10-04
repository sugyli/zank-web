<?php

namespace Zank\Middleware\User\Change;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zank\Traits\Container;

class Height
{
    use Container;

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $height = (int) $request->getParsedBodyParam('height');
        $user = $this->ci->get('user');

        if ($height) {
            if ($height < 20 || $height > 320) {
                return with(new \Zank\Common\Message($response, false, '不合法的身高范围'))
                    ->withJson();
            }

            $user->height = $height;
        }

        return $next($request, $response);
    }
}
