<?php

namespace Zank\Model\Novel\Wap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleChapter extends Model
{
   // use SoftDeletes;

    protected $table = 'article_chapter';

    protected $primaryKey = 'chapterid';

    //章节基础
    public function scopeBaseChapter(Builder $query): Builder
    {

        return $query
                ->where('chaptertype',0)
                ->where('display',0);

    }

}
