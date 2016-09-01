<?php

namespace Zank\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 用户认证模型.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class SignToken extends Model
{
    use SoftDeletes;

    protected $table = 'sign_tokens';

    protected $primaryKey = 'sign_tokens_id';

    public function scopeByToken(Builder $query, $token): Builder
    {
        return $query
            ->where('token', $token);
    }

    public function scopeByRefreshToken(Builder $query, $refreshToken): Builder
    {
        return $query
            ->where('refresh_token', $refreshToken);
    }

    public static function createToken()
    {
        $token = str_random(64);
        $tokens = self::byToken($token)->count();

        if ($tokens) {
            return self::createToken();
        }

        return $token;
    }

    public static function createRefreshToken()
    {
        $token = str_random(64);
        $tokens = self::byRefreshToken($token)->count();

        if ($tokens) {
            return self::createToken();
        }

        return $token;
    }

    public function user()
    {
        return $this->hasOne(\Zank\Model\User::class, 'user_id', 'user_id');
    }
} // END class SignToken extends Model
