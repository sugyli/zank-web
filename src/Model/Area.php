<?php

namespace Zank\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use SoftDeletes;

    protected $table = 'areas';

    protected $primaryKey = 'area_id';
}
