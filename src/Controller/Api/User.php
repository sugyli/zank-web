<?php

namespace Zank\Controller\Api;

use Geohash\Geohash;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zank\Controller;
use Zank\Model;

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
        return $response->withJson([
            '/api/user/change' => '修改用户资料',
        ]);
    }

    public function changeDate(Request $request, Response $response)
    {
        $user = $this->ci->get('user');
        $user->save();

        return with(new \Zank\Common\Message($response, true, '修改用户资料成功'))
            ->withJson();
    }

    public function search(Request $request, Response $response)
    {
        $key = $request->getParsedBodyParam('key');

        if (!$key) {
            return with(new \Zank\Common\Message($response, false, '请输入搜索关键词'))
                ->withJson();
        }

        $users = Model\User::where('username', 'like', '%'.$key.'%')
            ->get();

        return with(new \Zank\Common\Message($response, true, '', $users->toArray()))
            ->withJson();
    }

    public function gets(Request $request, Response $response)
    {
        $latitude = $request->getParsedBodyParam('latitude');
        $longitude = $request->getParsedBodyParam('longitude');

        if (!$latitude || !$longitude) {
            return with(new \Zank\Common\Message($response, false, '请设置当前经纬度'))
                ->withJson();
        }

        $geohash = Geohash::encode($latitude, $longitude);
        $geohash = substr($geohash, 0, 4); // 约20km

        $users = Model\User::where('geohash', 'like', $geohash.'%');

        // 加入角色筛选
        $role = $request->getParsedBodyParam('role');
        if ($role) {
            if (!is_array($role)) {
                $role = explode(',', $role);
            }
            $roles = (array) $role;
            $users->where(function (Builder $query) use ($roles) {
                foreach ($roles as $role) {
                    $query->orWhere('role', $role);
                }
            });
        }

        // 体型筛选
        $shapes = $request->getParsedBodyParam('shape');
        if ($shapes) {
            if (!is_array($shapes)) {
                $shapes = explode(',', $shapes);
            }
            $shapes = (array) $shapes;
            $users->where(function (Builder $query) use ($shapes) {
                foreach ($shapes as $shape) {
                    $query->orWhere('shape', $shape);
                }
            });
        }

        // 年龄筛选
        $ages = $request->getParsedBodyParam('age');
        if ($ages) {
            [$minAge, $maxAge] = explode(',', $ages);
            $minAge = max(0, $minAge);
            $maxAge = min(160, $maxAge);
            $users->where(function (Builder $query) use ($minAge, $maxAge) {
                $query->where('age', '>', $minAge)
                    ->orWhere('age', '<', $maxAge);
            });
        }

        // 体重筛选
        $kg = $request->getParsedBodyParam('kg');
        if ($kg) {
            [$minKg, $maxKg] = explode(',', $kg);
            $minKg = max(0, $minKg);
            $maxKg = min(300, $maxKg);
            $users->where(function (Builder $query) use ($minKg, $maxKg) {
                $query->where('kg', '>', $minKg);
                $query->orWhere('kg', '<', $maxKg);
            });
        }

        // 身高筛选
        $height = $request->getParsedBodyParam('height');
        if ($height) {
            [$minHeight, $maxHeight] = explode(',', $height);
            $minHeight = max(0, $minHeight);
            $maxHeight = min(250, $maxHeight);
            $users->where(function (Builder $query) use ($minHeight, $maxHeight) {
                $query->where('height', '>', $minHeight);
                $query->orWhere('height', '<', $maxHeight);
            });
        }

        $users = $users->toSql();

        if (!$users->count()) {
            return with(new \Zank\Common\Message($response, false, '没有用户'))->withJson();
        }

        return with(new \Zank\Common\Message($response, true, '', $users->toArray()))->withJson();
    }
}
