<?php

namespace Zank\Util;
use Illuminate\Support\Collection;
/**
 * 资源模型 - 业务逻辑模型
 * @example
 * 根据表名及资源ID，获取对应的资源信息
 * @author jason <yangjs17@yeah.net>
 * @version TS3.0
 */
class SourceUtil
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
            self::$_instance = new self('Zank\Util\SourceUtil');
        }
        return call_user_func_array(array(self::$_instance, $name), $arguments);
    }

    //处理介绍数据兼容批量循环

    public static function formatNoveInfoData($_datas){

      foreach ($_datas as $key => $_data) 
      {
        //$_datas = deepslashes($_datas);
        if ($_data['imgflag']) {
            $newCover = FENMIAN . floor($_data['articleid'] /1000) . '/' . $_data['articleid'] . '/' . $_data['articleid'] . 's.jpg';

            $_datas[$key]['cover'] = $newCover;
        }else{
            $_datas[$key]['cover'] = NOFENMIAN;
        }
        /*
        $coverUrl = FENMIAN . floor($_data['articleid'] /1000) . '/' . $_data['articleid'] . '/' . $_data['articleid'] . 's.jpg';
        my_file_exists($coverUrl) ? $_datas[$key]['cover'] = $coverUrl : $_datas[$key]['cover'] = NOFENMIAN;
        */
        $_datas[$key]['shortid'] = intval($_data['articleid']/1000);
        $_datas[$key]['sortname'] = \Zank\Util\NovelFunction::getSortName($_data['sortid']);
        //$_datas[$key]['time'] = tranTime($_data['lastupdate']);
        $_datas[$key]['time'] =  date("Y-m-d H:i:s", $_data['lastupdate']);
      }
      return $_datas;
    }
    //颠倒数组
    public static function upsideDown(array $_datas)
    {

        $collection = collect($_datas);

        $reversed = $collection->reverse();

        return $reversed->all();

    }
    //切片
    public static function sliceArray(array $_datas,int $intend , int $intfirst = 0)
    {

        $collection = collect($_datas);
        $slice = $collection->slice($intfirst , $intend);

        return $slice->all();

    }

    public static function sortByArray(array $_datas ,$field)
    {

        $collection = collect($_datas);
        $sorted = $collection->sortBy($field);

        return $sorted->values()->all();

    }
   
    public static function findForTwoArry(array $_datas ,int $articleid ,$field)
    {

      $collection = collect($_datas);
      $collection = $collection
                      ->map(function ($item, $key) use ($articleid ,$field){
                            if ($item[$field] == $articleid) {
                              return ($item);
                            }
                          })
                      ->reject(function ($item) {
                          return empty($item);
                      });

      if ($collection && !$collection->isEmpty()) {

          return $collection->first();
      }

      return null;
    }


} // END class Message