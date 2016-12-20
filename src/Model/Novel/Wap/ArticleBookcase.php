<?php

namespace Zank\Model\Novel\Wap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleBookcase extends Model
{
    //use SoftDeletes;

    protected $table = 'article_bookcase';

    protected $primaryKey = 'caseid';
    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;


}
