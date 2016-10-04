<?php

namespace Zank\Middleware\User\Change;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zank\Traits\Container;
use Zank\Model;

class Age
{
    use Container;

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $age = (int) $request->getParsedBodyParam('age');
        $user = $this->ci->get('user');

        if ($age > 0) {

            if ($age > 120) {
                return with(new \Zank\Common\Message($response, false, '年龄最大不能大于120岁'))
                    ->withJson();
            }

            $user->age = $age;
        }

        return $next($request, $response);
    }
}
