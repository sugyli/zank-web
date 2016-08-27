<?php

namespace Zank\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 用户邀请码数据模型
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class UserInvite extends Model
{
    use SoftDeletes;

    protected $table = 'user_invite';

    protected $primaryKey = 'user_invite_id';

    public function scopeByInviteCode(Builder $query, $inviteCode): Builder
    {
        return $query->where('invite_code', $inviteCode);
    }

    public function scopeByCreateUsersId(Builder $query, $usersId): Builder
    {
        return $query->where('create_user_id', $usersId);
    }

    public function scopeByUseUsersId(Builder $query, $usersId): Builder
    {
        return $query->where('use_user_id', $usersId);
    }
} // END class UserInvite extends Model
