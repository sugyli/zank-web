<?php

namespace Zank\Controller\NovelApp;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Zank\PublicController;
use Zank\Util\NovelFunction;
use Zank\Util\SourceUtil;
use Zank\Model\Novel\Wap\ArticleArticle;
class AppControl extends PublicController
{
  	

  	public function mainList(Request $request, Response $response,$args)
    {
        $lastupdate = $request->getParsedBodyParam('lastupdate') !== null ? 
        								intval($request->getParsedBodyParam('lastupdate')) : 0;

        if ($lastupdate<=0) {
        	$this->comlog($request ,'APP请求时间小于0,非法请求可能大');
	        return with(new \Zank\Common\Message($response, false, '时间小于0'))
                ->withJson();
        }

        $sortid = $request->getParsedBodyParam('sortid');
 
        if ($sortid > 0) {
            $sort = NovelFunction::findNovelSortById($sortid);
        }
        $sortid = isset($sort['sortid']) ? $sort['sortid']:0;
        $articleArticle = ArticleArticle::pageAjaxData($lastupdate,PAGESIZE,$sortid);

    	if ($articleArticle && !$articleArticle->isEmpty()) {
    		$articleDatas = $articleArticle->toArray();
    		$articleDatas = SourceUtil::formatNoveInfoData($articleDatas);
            //封面推荐
            $picTuiJians = NovelFunction::picGoodCache(6);
            $outdata['fentui'] = $picTuiJians;
    		$outdata['islast'] = false;
            $outdata['items'] = $articleDatas;
            $lastData = end($articleDatas);
            $outdata['lastupdate'] = $lastData['lastupdate'];
            if(count($articleDatas) < PAGESIZE)
            {
                $outdata['islast'] = true;
            }
            return with(new \Zank\Common\Message($response, true, '请求成功！', $outdata))
                    ->withJson();
    	}

    	return with(new \Zank\Common\Message($response, false, '没有数据了'))
                        ->withJson();
    }

    public function bookInfoList(Request $request, Response $response,$args)
    {

        $bookid  = $request->getParsedBodyParam('bookid') !== null ? 
                                        intval($request->getParsedBodyParam('bookid')) : 0;
        $message = "没有获取到数据请检查服务端";
        $data = [];
        $state = false;
        if ($bookid > 0) 
        {   
            $infoData = NovelFunction::getInfoData($bookid);

            if ($infoData) {
                $data['chapter'] =  $infoData['chapter'];
                $state = true;
                $message = "请求成功";
            }
        }else{
            $message = "书的ID小于0";       
        }

        return with(new \Zank\Common\Message($response, $state, $message,$data))
                        ->withJson();

    }

    public function bookMuluIndex(Request $request, Response $response,$args)
    {
        $page  = $request->getParsedBodyParam('page') !== null ? 
                                        intval($request->getParsedBodyParam('page')) : 1;
        $page > 0 or  $page = 1;
        $bookid  = $request->getParsedBodyParam('bookid') !== null ? 
                                        intval($request->getParsedBodyParam('bookid')) : 0;
        $message = "没有获取到数据请检查服务端";
        $data = [];
        $state = false;
        $pageNb = 40;                                
        if ($bookid > 0) 
        {
            $infoData = NovelFunction::getMuluData($bookid ,$page,null,$pageNb);

            if ($infoData) {
                for($i = 1 ; $i <= $infoData['pagenum'] ; $i++){
                    $data['muluIndex'][] = (($i-1)*$pageNb+1).' - '.($i*$pageNb).'章';
                }
                $data['chapter'] = $infoData['chapter'];
                $state = true;
                $message = "请求成功";  
            }else{
                $message = "可能PAGE越界了";
            }
            
        }else{

            $message = "书的ID小于0";
        }                               


        return with(new \Zank\Common\Message($response, $state, $message,$data))
                        ->withJson();

    }

    public function bookContent(Request $request, Response $response,$args)
    {
        $bid  = $request->getParsedBodyParam('bid') !== null ? 
                                        intval($request->getParsedBodyParam('bid')) : 0;
        $cid  = $request->getParsedBodyParam('cid') !== null ? 
                                        intval($request->getParsedBodyParam('cid')) : 0;
        
        $message = "没有获取到数据请检查服务端";
        $contentData = [];
        $state = false;      
        if ($bid >0 && $cid>0) {
            $bookData = NovelFunction::getInfoDataBySql($bid);
            if ($bookData) {
                $shortid = intval($bid/1000);
                $key = 0; //章节所在的索引
                Switch($bookData['total']){
                    case 1:                           
                    $contentData['chapter'] = $bookData['chapter'][0];
                    break;
                    default:
                    foreach($bookData['chapter'] as $k=>$v){
                        if ($v['chapterid'] == $cid) {
                            $contentData['chapter'] = $v;
                            $key = $k;
                            break;
                        }
                    }               
                }

                //在章节存在的情况下再去查内容
                if (isset($contentData['chapter'])) {
                    $puDIR = $shortid .'/'.$bid;
                    $txtDir = TXTDIR . $puDIR ."/{$cid}.txt";
                    $contentData['content'] = "章节丢失了,欢迎举报让我们修复,非常感谢！！！";
                    $mContentKey = 'nrapp_'. $bid ."_". $cid ."_". $contentData['chapter']['lastupdate'];
                    $txt = $this->ci->fcache->get($mContentKey);
                    if (!$txt) {
                        $curl = new \Curl\Curl();
                        $curl->setOpt(CURLOPT_TIMEOUT, 5);                           
                        $curl->get($txtDir);
                        if ($curl->http_status_code == '200') {
                            $txt = $curl->response;
                            $txt = trim($txt);
                            if (!empty($contentData['chapter']['attachment']) && getstrlength(t($txt))<=300) {

                                $contentData['content'] = "本章节内容为图片APP不支持";  

                            }else{

                                if (!empty($txt)) {
                                    //$txtfind = 1;
                                    $txt = mb_convert_encoding($txt, 'utf-8', 'GBK,UTF-8,ASCII');

                                    //$txt = @str_replace("\r\n","<br/>",$txt);
                                    $txt = preg_replace('/<br\\s*?\/??>/i',PHP_EOL,$txt);
                                    $txt = preg_replace('/<\/br\\s*?\/??>/i',PHP_EOL,$txt);
                                    $txt = preg_replace('/<p\\s*?\/??>/i',PHP_EOL,$txt);
                                    $txt = preg_replace('/<\/p>/i',PHP_EOL,$txt);
                                    $txt = @str_replace("&nbsp;"," ",$txt); 
                                    //写缓存
                                    $this->ci->fcache->set($mContentKey, $txt ,[                 
                                                            'ttl' => NRCASE,                    
                                                            'compress' => YS,             
                                                        ]);
                                    $contentData['content'] =  $txt;  
                                
                                }
                            }

                        }


                    }else{
                        
                        $contentData['content'] =  $txt;  
                    }
                    $contentData['preview'] = isset($bookData['chapter'][$key-1]) ? $bookData['chapter'][$key-1] : "";
                    $contentData['next'] = isset($bookData['chapter'][$key+1]) ? $bookData['chapter'][$key+1] : "";
                    $contentData['page'] =ceil( ($key+1)/PAGENUM );
                    $message = "请求成功";
                    $state = true;   

                }
            }
        }else{
            $message = "非法请求可能大";

        }

        return with(new \Zank\Common\Message($response, $state, $message,$contentData))
                        ->withJson();                           

    }
    
    
} // END class Sign
