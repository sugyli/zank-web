<?php

namespace Zank\Model\Novel\Wap;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleArticle extends Model
{
   // use SoftDeletes;

    protected $table = 'article_article';

    protected $primaryKey = 'articleid';

//最基础的
    public function scopeBaseBook(Builder $query): Builder
    {

        return $query
                ->where('display',0)
                ->where('lastchapterid','>' ,0);

    }

    /**
     * 关联章节
     */
    public function ArticleChapter()
    {
        return $this->hasMany(\Zank\Model\Novel\Wap\ArticleChapter::class ,'articleid','articleid');
    }

    public function ArticleBookcase()
    {
        return $this->hasMany(\Zank\Model\Novel\Wap\ArticleBookcase::class ,'articleid','articleid');
    }
    //获取一本书
    public static function getOneBook($id)
    {
        return
                self::BaseBook()
                    ->where('articleid',$id)
                    ->first();

    }


    public static function pageData(int $page = 1 ,int $pageSize = 15,$sortid = 0)
    {
        if ($sortid>0) {
            $total = self::BaseBook()
                        ->where('sortid' , $sortid)
                        ->count();

        }else{

            $total = self::BaseBook()->count();
        }


        if($total >0)
        {

            if ($sortid>0) {

                return  self::BaseBook()
                            ->where('sortid',$sortid)
                            ->orderBy('lastupdate','desc')  
                            ->take($pageSize)   //限制(Limit)                                                   
                            ->get();

            }else{

                return  self::BaseBook()
                            ->orderBy('lastupdate','desc')
                            ->take($pageSize)   //限制(Limit)                         
                            ->get();

            } 

        }

        return null;

    }
    public static function pageAppData(int $bookid ,int $pageSize = 15,$sortid = 0 ,$isdow=0)
    {

        $t = $isdow >0 ? "<" : ">";


        if ($sortid>0) {
            $total = self::BaseBook()
                       ->where('sortid' , $sortid) 
                       ->where('articleid', $t ,$bookid)
                       ->count();

        }else{

            $total = self::BaseBook()
                        ->where('articleid', $t ,$bookid)
                        ->count();
        }

        if($total >0)
        {

            if ($sortid>0) {

                return  self::BaseBook()
                            ->where('sortid',$sortid)
                            ->where('articleid', $t ,$bookid)
                            ->orderBy('articleid','desc')
                            ->take($pageSize)   //限制(Limit)                                                   
                            ->get();
            }else{

                return  self::BaseBook()
                            ->where('articleid', $t ,$bookid)
                            ->orderBy('articleid','desc')
                            ->take($pageSize)   //限制(Limit)                         
                            ->get();

            }

        }
        return null;

    }

    public static function pageAjaxData(int $tm = 1 ,int $pageSize = 15,$sortid = 0)
    {
        if ($sortid>0) {
            $total = self::BaseBook()
                       ->where('sortid' , $sortid) 
                       ->where('lastupdate','<',$tm)
                       ->count();

        }else{

            $total = self::BaseBook()
                        ->where('lastupdate','<',$tm)
                        ->count();
        }


        if($total >0)
        {
            if ($sortid>0) {

                return  self::BaseBook()
                            ->where('sortid',$sortid)
                            ->where('lastupdate','<',$tm)
                            ->orderBy('lastupdate','desc')
                            ->take($pageSize)   //限制(Limit)                                                   
                            ->get();
            }else{

                return  self::BaseBook()
                            ->where('lastupdate','<',$tm)
                            ->orderBy('lastupdate','desc')
                            ->take($pageSize)   //限制(Limit)                         
                            ->get();

            } 

        }

        return null;

    }


}
