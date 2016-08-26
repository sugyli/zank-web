<?php

namespace Zank\Model;

use Illuminate\Database\Eloquent\{Model, Builder, SoftDeletes};

/**
 * 手机号码验证码模型
 *
 * @author Seven Du <lovevipdsw@outlook.com> 
 **/
class CaptchaPhone extends Model
{
    use SoftDeletes;

    protected $table = 'captcha_phone';

    protected $primaryKey = 'captcha_phone_id';

    public function scopeByPhone(Builder $query, $phone): Builder
    {
        return $query->where('phone', $phone);
    }

    public function scopeByCaptchaCode(Builder $query, $captchaCode): Builder
    {
        return $query->where('captcha_code', $captchaCode);
    }
} // END class SignToken extends Model