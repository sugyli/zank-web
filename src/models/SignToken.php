<?php

namespace Zank\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 用户认证模型
 *
 * @author Seven Du <lovevipdsw@outlook.com> 
 **/
class SignToken extends Model
{
    use SoftDeletes;

    protected $table = 'sign_tokens';

    protected $primaryKey = 'sign_tokens_id';

    public function scopeByToken(Builder $query, $token)
    {
        return $query
            ->where('token', $token)
        ;
    }

    public function scopeByRefreshToken(Builder $query, $refreshToken)
    {
        return $query
            ->where('refresh_token', $refreshToken)
        ;
    }
} // END class SignToken extends Model
