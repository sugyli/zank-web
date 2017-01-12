<?php

namespace Zank\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 用户模型.
 *
 * @author Seven Du<lovevipdsw@outlook.com>
 **/
class User extends Model
{
    use SoftDeletes;

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    protected $hidden = ['password', 'hash'];

    /**
     * 用于设置查询条件为phone的快捷方法.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query 查询构造器
     * @param string|int                            $phone 条件字符串
     *
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function scopeByPhone(Builder $query, $phone): Builder
    {
        return $query
            ->where('phone', $phone);
    }

    /**
     * 用于查询username字段.
     *
     * @param Builder $query    查询构造器
     * @param string  $username 用户名
     *
     * @return Buolder
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function scopeByUserName(Builder $query, $username): Builder
    {
        return $query->where('username', $username);
    }

    /**
     * 用户下的附件关联模型.
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function attachs()
    {
        return $this->belongsToMany(Attach::class, 'attach_links', 'user_id', 'attach_id');
    }
} // END class User extends Eloquent
