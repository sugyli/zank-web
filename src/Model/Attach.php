<?php

namespace Zank\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 附件模型.
 *
 * @author Seven Du<lovevipdsw@outlook.com>
 **/
class Attach extends Model
{
    use SoftDeletes;

    protected $table = 'attachs';

    protected $primaryKey = 'attach_id';

    public function scopeByMd5(Builder $query, $md5)
    {
        return $query->where('md5', $md5);
    }

    public function links()
    {
        return $this->hasMany(AttachLink::class);
    }
} // END class Attach extends Eloquent
