<?php

namespace Zank\Middleware\User\Change;

use Psr\Http\RequestInterface as Request;
use Psr\Http\RequestInterface as Response;
use Zank\Traits\Container;

class Role
{
    use Container;

    /**
     * 修改用户角色中间件.
     *
     * @param Psr\Http\RequestInterface $request 请求注入容器
     * @param Psr\Http\ResponseInterface $response 返回的资源体
     *
     * @return callable $next
     * 
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $role = $request->getParsedBodyParam('role');
        $user = $this->ci->get('user');

        if ($role !== null) {
            if (!in_array($role, ['1', '0.5', '0', '-1'])) {
                return with(new \Zank\Common\Message($response, false, '设置的角色范围有误'))
                    ->withJson();
            }

            $user->role = $role;
        }

        return $next($request, $response);
    }
}
