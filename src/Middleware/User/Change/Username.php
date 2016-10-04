<?php

namespace Zank\Middleware\User\Change;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zank\Traits\Container;
use Zank\Model;

class Username
{
    use Container;

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $user = $this->ci->get('user');
        $username = $request->getParsedBodyParam('username');

        if ($username) {
            if ($this->chackUsernameForMe($username) === false) {
                return with(new \Zank\Common\Message($response, false, '该用户名不可用！'))
                ->withJson();
            }

            $user->username = $username;
        }

        return $next($request, $response);
    }

    protected function chackUsernameForMe(string $username): bool
    {
        $user_id = $this->ci->get('user')->user_id;
        $old_username = $this->ci->get('user')->username;

        if ($old_username != $username && !($user = Model\User::byUserName($username)->first())) {
            return true;
        }

        return $user_id === $user->user_id;
    }
}
