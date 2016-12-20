<?php

namespace Zank\Model\Novel\Wap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemUsers extends Model
{
    use SoftDeletes;

    protected $table = 'system_users';

    protected $primaryKey = 'uid';
    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    //public $timestamps = false;
    public function bookcase()
    {
        return $this->hasMany(\Zank\Model\Novel\Wap\ArticleBookcase::class, 'userid', 'uid');//第一个参数关联的 第2个是自己的
    }


    public function messages($field)
    {
        return $this->hasMany(\Zank\Model\Novel\Wap\SystemMessage::class, $field, 'uid');//第一个参数关联的 第2个是自己的
    }
}
