<?php

namespace Zank\Model\Novel\Wap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 用户模型.
 *
 * @author Seven Du<lovevipdsw@outlook.com>
 **/
class Mulu extends Model
{
    use SoftDeletes;

    protected $table = 'mulu';

    protected $primaryKey = 'bid';

    
} // END class User extends Eloquent
