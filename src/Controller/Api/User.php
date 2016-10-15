<?php

namespace Zank\Controller\Api;

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
            '/api/user/change' => '修改用户资料',
        ]);

        return $response;
    }

    public function changeDate(Request $request, Response $response)
    {
        $user = $this->ci->get('user');
        $user->save();

        return with(new \Zank\Common\Message($response, true, '修改用户资料成功'))
            ->withJson();
    }
}
