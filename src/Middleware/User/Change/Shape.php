<?php

namespace Zank\Middleware\User\Change;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zank\Traits\Container;

class Shape
{
    use Container;

    protected $shapes = ['壮熊', '狒狒', '肌肉', '普通', '偏瘦'];

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $shape = $request->getParsedBodyParam('shape');

        if ($shape) {
            if (!in_array($shape, $this->shapes)) {
                return with(new \Zank\Common\Message($response, false, '设置的角色非法'))
                    ->withJson();
            }

            $user = $this->ci->get('user');
            $user->shape = $shape;
        }

        return $next($request, $response);
    }
}
