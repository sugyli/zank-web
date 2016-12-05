<?php

namespace Zank\Middleware\User\Change;

use Psr\Http\RequestInterface as Request;
use Psr\Http\RequestInterface as Response;
use Zank\Model;
use Zank\Traits\Container;

/**
 * 修改用户信息，地区修改中间件。
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class Area
{
    use Container;

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $user = $this->ci->get('user');
        $area_id = $request->getParsedBodyParam('area_id');

        if ($area_id && ($area = Model\Area::find($area_id))) {
            // code...
        }
    }
} // END class Area
