<?php

namespace Zank\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 用户模型
 *
 * @package default
 * @author Seven Du<lovevipdsw@outlook.com>
 **/
class User extends Model
{
    use SoftDeletes;

    protected $table = 'users';

    protected $primaryKey = 'users_id';

    /**
     * 用于设置查询条件为phone的快捷方法
     * 
     * @param    \Illuminate\Database\Eloquent\Builder $query 查询构造器
     * @param    string|int                            $phone 条件字符串
     * @return   \Illuminate\Database\Eloquent\Builder
     * 
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function scopeByPhone(Builder $query, $phone): Builder
    {
        return $query
            ->where('phone', $phone)
        ;
    }

    public function scopeByUserName(Builder $query, $username): Builder
    {
        return $query->where('username', $username);
    }
} // END class User extends Eloquent