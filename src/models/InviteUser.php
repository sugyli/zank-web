<?php

namespace Zank\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 用户要去码邀请用户纪录模型
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class InviteUser extends Model
{
    use SoftDeletes;

    protected $table = 'invite_users';

    protected $primaryKey = 'invite_users_id';

    public function scopeByInviteCode(Builder $query, $inviteCode): Builder
    {
        return $query->where('invite_code', $inviteCode);
    }

    public function scopeByUsersId(Builder $query, $usersId): Builder
    {
        return $query->where('users_id', $usersId);
    }
} // END class UserInvite extends Model
