<?php

namespace Zank\Controller\Api\User;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zank\Controller;

class User extends Controller
{
    /**
     * 索引方法，返回api列表.
     *
     * @param Request  $request  请求对象
     * @param Response $response 返回资源
     *
     * @return Response
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function __invoke(Request $request, Response $response)
    {
        $response->withJson([
        ]);

        return $response;
    }

    public function changeDate(Request $request, Response $response)
    {
        $username = $request->getParsedBodyParam('username');
        $age = $request->getParsedBodyParam('age');
        $height = $request->getParsedBodyParam('height');
        $kg = $request->getParsedBodyParam('kg');
        $role = $request->getParsedBodyParam('role');
        $area_id = $request->getParsedBodyParam('area_id');
    }
}
