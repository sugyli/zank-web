<?php

namespace Zank\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * 用户模型
 *
 * @package default
 * @author Seven Du<lovevipdsw@outlook.com>
 **/
class User extends Eloquent
{

    protected $table = 'user';

    protected $primaryKey = 'user_id';
} // END class User extends Eloquent