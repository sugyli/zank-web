<?php

namespace Zank\Middleware\Sign\Up;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * 验证注册是使用的邀请码
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class ValidateUserInviteCode
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $inviteCode = $request->getParsedBodyParam('invite_code');

        if ($inviteCode) {
            $invite = \Zank\Model\UserInvite::byInviteCode($inviteCode)
                ->first();

            if (!$invite) {
                $response = new \Zank\Common\Message($response, false, '该邀请码不存。');

                return $response->withJson();
            }
        }

        return $next($request, $response);
    }
} // END class ValidateInviteCode
