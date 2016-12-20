<?php

namespace Zank\Util;
use Zank\Application;
use Illuminate\Support\Collection;
use Zank\Util\SourceUtil;
/**
 * 资源模型 - 业务逻辑模型
 * @example
 * 根据表名及资源ID，获取对应的资源信息
 * @author jason <yangjs17@yeah.net>
 * @version TS3.0
 */
class NovelFunction
{
     
    /**
     * 储存单例的静态成员
     *
     * @var object
     **/
    protected static $_instance;

    /**
     * 获取单例对象
     *
     * @return object
     * @author Seven Du <lovevipdsw@vip.qq.com>
     **/
    

    public static function __callStatic($name, array $arguments)
    {
        
        if (!self::$_instance instanceof self) {
            self::$_instance = new self('Zank\Util\NovelFunction');
        }
        return call_user_func_array(array(self::$_instance, $name), $arguments);
    }


    

    public static function getNovelSort()
    {
      
        $jieqiSort[] = array('sortid' => 1, 'caption' => '玄幻魔法', 'shortname' => '玄幻', 'description'=>'', 'imgurl' => '', 'publish' => '1');
        $jieqiSort[] = array('sortid' => 2, 'caption' => '武侠修真', 'shortname' => '武侠', 'description'=>'', 'imgurl' => '', 'publish' => '1');
        $jieqiSort[] = array('sortid' => 3, 'caption' => '都市言情', 'shortname' => '都市', 'description'=>'', 'imgurl' => '', 'publish' => '1');
        $jieqiSort[] = array('sortid' => 4, 'caption' => '历史穿越', 'shortname' => '历史', 'description'=>'', 'imgurl' => '', 'publish' => '1');
        $jieqiSort[] = array('sortid' => 5, 'caption' => '恐怖悬疑', 'shortname' => '恐怖', 'description'=>'', 'imgurl' => '', 'publish' => '1');
        $jieqiSort[] = array('sortid' => 6, 'caption' => '游戏竞技', 'shortname' => '游戏', 'description'=>'', 'imgurl' => '', 'publish' => '1');
        $jieqiSort[] = array('sortid' => 7, 'caption' => '军事科幻', 'shortname' => '军事', 'description'=>'', 'imgurl' => '', 'publish' => '1');
        $jieqiSort[] = array('sortid' => 8, 'caption' => '综合类型', 'shortname' => '综合', 'description'=>'', 'imgurl' => '', 'publish' => '1');
        $jieqiSort[] = array('sortid' => 9, 'caption' => '名家作品', 'shortname' => '名家', 'description'=>'', 'imgurl' => '', 'publish' => '1');
        $jieqiSort[] = array('sortid' => 10, 'caption' => '网友更新', 'shortname' => '网友', 'description'=>'', 'imgurl' => '', 'publish' => '1');
        $jieqiSort[] = array('sortid' => 11, 'caption' => '商战职场', 'shortname' => '职场', 'description'=>'', 'imgurl' => '', 'publish' => '1');
        $jieqiSort[] = array('sortid' => 12, 'caption' => '同人小说', 'shortname' => '同人', 'description'=>'', 'imgurl' => '', 'publish' => '1');
        return $jieqiSort;   
    }

     //根据id查找所属分类
    public static function findNovelSortById($id)
    {
      
      $sorts = self::getNovelSort();
      $collection = collect($sorts);
      $collection = $collection
                      ->map(function ($item, $key) use ($id){
                            if ($item['sortid'] == $id) {
                              return ($item);
                            }
                          })
                      ->reject(function ($item) {
                          return empty($item);
                      });

      if (!$collection->isEmpty()) {

          return $collection->first();
      }

      return null;
    }
   
    /*
    //根据拼音查找所属分类
    public function findNovelSort($sort)
    {
      
      $sorts = self::$_instance->getNovelSort();
      $collection = collect($sorts);
      $collection = $collection
                      ->map(function ($item, $key) use ($sort){
                            if ($item['code'] == $sort) {
                              return ($item);
                            }
                          })
                      ->reject(function ($item) {
                          return empty($item);
                      });

      if (!$collection->isEmpty()) {

          return $collection->first();
      }

      return null;
    }
    
    
    */
   //转换分类名
    public static function getSortName($id)
    {
      $sort = self::findNovelSortById($id);

      return !empty($sort) ? $sort['caption'] : DFSORT;
    }


    public static function picGoodCache($counts = 5 ,$key="pictuijian")
    {
        //图片推荐
        $ci  = Application::getContainer();
        $pictuijian = $ci->fcache->get($key);
        if (!$pictuijian) 
        {          
            $pictuijian =               
                        \Zank\Model\Novel\Wap\ArticleArticle::BaseBook()
                                        ->where("imgflag",1)
                                        ->orderBy('postdate','desc')
                                        ->take($counts)   //限制(Limit)
                                        ->get();


            if ($pictuijian && !$pictuijian->isEmpty()) {
                $pictuijian = $pictuijian->toArray();
                $pictuijian = \Zank\Util\SourceUtil::formatNoveInfoData($pictuijian);
                //将图片数据写入缓存               
                $ci->fcache->set($key, $pictuijian ,[                 
                                    'ttl' => TJCASE,                    // Time to live 
                                    'compress' => YS,             // Compress data with gzcompress or not
                                ]); 
            }

        }
        return $pictuijian;

    }
//介绍数据
    public static function getInfoData(int $id)
    {
      $bookData = self::getInfoDataBySql($id);

      if ($bookData) 
      {
        //倒序
        $bookData['yuedu'] = $bookData['chapter'][0];
        $bookData['chapter'] =  SourceUtil::upsideDown($bookData['chapter']);
        $bookData['chapter'] = SourceUtil::sliceArray($bookData['chapter'],9);

        return $bookData;
      }
      return null;
    }
//目录数据
    public static function getMuluData(int $bookid ,int $page , $sort, int $pageSize)
    {
      
      $bookData = self::getInfoDataBySql($bookid);

      if ($bookData) {
          $total = $bookData['total'];
        //计算总页数
          $pagenum = ceil( $total / $pageSize );//当没有数据的时候 计算出来为0  
          if ($page > $pagenum)
          {
             // $page = $pagenum;//分页越界
              return null;
          }

          //下一页开始的ID (0)开始
          $offset = ($page - 1) * $pageSize;

          if ($sort == 'desc') {
              $bookData['chapter'] =  SourceUtil::upsideDown($bookData['chapter']);
          }
          $bookData['chapter'] = SourceUtil::sliceArray($bookData['chapter'] , $pageSize,$offset);
          $bookData['pagenum'] = $pagenum;
          return $bookData;

      }

      return null;

    }
    //介绍加目录总数据来源
    public static function getInfoDataBySql(int $id)
    {
      
        $ci  = Application::getContainer();
        $key = 'mulu_'. $id;
        $bookData = $ci->fcache->get($key);

        if (!$bookData) {

            $oneBook = \Zank\Model\Novel\Wap\ArticleArticle::getOneBook($id);
            $bookData = []; 
            $maxCount = NOVELMAX;//章节最多数量    

            if ($oneBook) 
            {               
                $total = $oneBook->ArticleChapter()
                                ->BaseChapter()
                                ->count();

                if ($total>0 && $total <=$maxCount) 
                {
                    $chapter = 
                            $oneBook->ArticleChapter()
                                ->BaseChapter()
                                ->orderBy('chapterorder','asc')
                                ->get();

                    if ($chapter && !$chapter->isEmpty()) 
                    {   
                        //要放最前面
                        $bookData['bookInfo'] =  $oneBook->toArray();
                        $bookData = \Zank\Util\SourceUtil::formatNoveInfoData($bookData);
                        $bookData['chapter'] = $chapter->toArray();
                        $bookData['total'] = $total;
                        //写缓存
                        $ci->fcache->set($key, $bookData ,[                 
                                    'ttl' => MLCASE,                    
                                    'compress' => YS,             
                                ]); 
                    }                  
                      
                }else{

                    $ci  = Application::getContainer();

                    $ci->importantlogger->debug("本书章节小于0或大于了{$maxCount} 一般是大于",['bookid'=>$id]);

                }

            }

            return $bookData;
        }
          
        return $bookData;
    }
    //根据积分查等级
    public static function getNoveTitle($score)
    {
        $systemHonors = \Zank\Model\Novel\Wap\SystemHonors::all();
        if ($systemHonors && !$systemHonors->isEmpty()) {
            $systemHonors = $systemHonors->toArray();

            $collection = collect($systemHonors);
            $collection = $collection
                          ->map(function ($item, $key) use ($score){
                                if ($score > $item['minscore'] && $score<= $item['maxscore']) {
                                  return ($item);
                                }
                              })
                          ->reject(function ($item) {
                              return empty($item);
                          });
            if (!$collection->isEmpty()) {

                return $collection->first();
            }
        }
        

      return null;            
    }
//根据等级查收藏数
    public static function getUserBookCaseCount($honorid)
    {

        $SystemRight = \Zank\Model\Novel\Wap\SystemRight::where('rname','maxbookmarks')->first();

        if ($SystemRight) {
            $SystemRight = $SystemRight->toArray();
            if (!empty($SystemRight['rhonors'])) {
                $bookCase =  unserialize($SystemRight['rhonors']);
                if (isset($bookCase[$honorid])) {
                    return $bookCase[$honorid];
                }
                
            }
            
        }

        return MAXBOOKCASE;
    }
} // END class Message