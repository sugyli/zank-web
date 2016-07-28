<?php

namespace Zank\Model;

use Illuminate\Database\Eloquent\Model;
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

    protected $table = 'user';

    protected $primaryKey = 'user_id';
} // END class User extends Eloquent