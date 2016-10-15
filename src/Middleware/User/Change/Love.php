<?php

namespace Zank\Middleware\User\Change;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zank\Traits\Container;

class Love
{
    use Container;

    protected $loves = [1, 2];

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $loveStatus = $request->getParsedBodyParam('love');

        if ($loveStatus) {
            if (!in_array($loveStatus, $this->loves)) {
                return with(new \Zank\Common\Message($response, false, '感情状态非法'))
                    ->withJson();
            }

            $user = $this->ci->get('user');
            $user->love = $loveStatus;
        }

        return $next($request, $response);
    }
}
