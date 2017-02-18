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
  	

  	public function upList(Request $request, Response $response,$args)
    {
        $lastupdate = $request->getParsedBodyParam('lastupdate') !== null ? 
        								intval($request->getParsedBodyParam('lastupdate')) : "";

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

    
    public function downList(Request $request, Response $response,$args)
    {
        $sortid = $request->getParsedBodyParam('sortid');
 
        if ($sortid > 0) {
            $sort = NovelFunction::findNovelSortById($sortid);
        }

        $sortid = isset($sort['sortid']) ? $sort['sortid']:0;
        $articleArticle = ArticleArticle::pageData(1,PAGESIZE,$sortid);
        if ($articleArticle  && !$articleArticle->isEmpty()) {
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

        return with(new \Zank\Common\Message($response, false, '未获取到数据'))
                        ->withJson();
    }
    
    
} // END class Sign
