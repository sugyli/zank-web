<?php

namespace Zank\Model\Novel\Wap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class SystemRight extends Model
{
    //use SoftDeletes;

    protected $table = 'system_right';

    protected $primaryKey = 'rid';
    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;


}
