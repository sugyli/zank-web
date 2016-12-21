<?php

namespace Zank\Controller\Novel\Wap;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\Cookies as Cookies;

use Zank\PublicController;
use Slim\Http\MobileRequest;
use Zank\Util\NovelFunction;
use Zank\Model\Novel\Wap\ArticleArticle;
use Zank\Util\SourceUtil;
/**
 * 认证控制器.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class Control extends PublicController
{
    /**
     * 小说手机
     *
     * @param Request $request
     */
    protected $pageSize = PAGESIZE;
    protected $mbPath = NOVELMB ; //默认模板前缀
    protected $title = WEBNAME.'小说网手机版';
    protected $keywords = WEBNAME.'小说网,小说,手机阅读网';
    protected $description = WEBNAME.'小说网收录了当前最好看的小说,是广大小说阅读爱好者必备的手机阅读小说网。';
    protected $webname = WEBNAME;//手机用
    protected $webconfig = WEBCONFIG;
    public function home(Request $request, Response $response,$args)
    {
        
        $request  = new MobileRequest($request);
        if ($request->isMobile()) //如果是手机
        {
            return $this->mIndex($request, $response,$args);
        }else{//不是手机
            return $this->mIndex($request, $response,$args);
            
        } 
    }


    public function mIndex(Request $request, Response $response,$args)
    {   

        $sortid = isset($args['sortid']) ? intval($args['sortid']) : 0;
        if ($sortid > 0) {
            $sort = NovelFunction::findNovelSortById($sortid);
        }

        $sortid = isset($sort['sortid']) ? $sort['sortid']:0;
        $articleArticle = ArticleArticle::pageData(1,$this->pageSize,$sortid);
        
        if ($articleArticle  && !$articleArticle->isEmpty()) {

            $jieqiSorts = NovelFunction::getNovelSort();//获取所有小说分类
            $articleDatas = $articleArticle->toArray();
                  
            $articleDatas = SourceUtil::formatNoveInfoData($articleDatas);
            //封面推荐
            $picTuiJians = NovelFunction::picGoodCache(3);
            
            $lastData = end($articleDatas);
            $router = $this->ci->get('router');
            $ajaxpath = $router->pathFor('mindexpost'); //获取AJAX请求路径
                // daying($articleDatas);
            return $this->ci->view
                    ->render($response, $this->mbPath.'index.html.twig', [
                        'jieqiSorts' => $jieqiSorts,
                        'articleDatas' => $articleDatas,
                        //'pagenum' => $pagenum,//总页数
                        'ajaxpath' => $ajaxpath,
                        'sortname' => isset($sort['caption']) ?  $sort['caption'] : "",
                        'selectjs'=> '$(".item_'. $sortid .'").addClass("cur");',
                        'picTuiJians'=>$picTuiJians,
                        'sortid' => $sortid,//用于判断是否显示友情和AJAX请求分类
                        //'sortajax' => isset($sort['code']) ?  $sort['code'] : "",
                        'ajaxPage' => $lastData['lastupdate'],
                        'title' => $sortid ? $sort['caption']."-".$this->title : $this->title,
                        'keywords' => $sortid ? "{$sort['caption']},{$sort['caption']}推荐,热门{$sort['caption']},{$sort['caption']}分类列表" : $this->keywords,
                        'description' => $sortid ? "{$this->title}分类中{$sort['caption']}的列表" : $this->description,
                        'webname' =>$this->webname,
                        'webconfig' => $this->webconfig
                    ]);

        }
        
        if ($sortid > 0) {

            return $response->withRedirect((string) "/", 302);

        }else{
            return with(new \Zank\Common\Message($response, false, '未获取到数据'))
                        ->withJson();

        }
        
    }


    public function mIndexPost(Request $request, Response $response,$args)
    {       
        $dwtime =  $request->getParsedBodyParam('dwtime');
        $sortid =  $request->getParsedBodyParam('sortajax'); 
        $dwtime =  intval($dwtime);
        $sortid =  intval($sortid);
        if ($sortid > 0) {
            $sort = NovelFunction::findNovelSortById($sortid);
        }
        
        if ($dwtime > 0) {
            $sortid = isset($sort['sortid']) ? $sort['sortid']:0;
            $articleArticle = ArticleArticle::pageAjaxData($dwtime,$this->pageSize,$sortid);
            if ($articleArticle && !$articleArticle->isEmpty()) {
                $articleDatas = $articleArticle->toArray();
                $articleDatas = SourceUtil::formatNoveInfoData($articleDatas);
                $outdata['islast'] = false;
                $outdata['items'] = $articleDatas;
                $lastData = end($articleDatas);
                $outdata['ajaxPage'] = $lastData['lastupdate'];
                if(count($articleDatas) < $this->pageSize)
                {
                    $outdata['islast'] = true;
                }
                return with(new \Zank\Common\Message($response, true, '请求成功！', $outdata))
                        ->withJson();
                
            }

            return with(new \Zank\Common\Message($response, false, '没有数据了'))
                        ->withJson();

        }
        $this->comlog($request ,'时间小于0,非法请求可能大');
        return with(new \Zank\Common\Message($response, false, '时间小于0'))
                        ->withJson();
    }


    public function info(Request $request, Response $response,$args)
    {

        $request  = new MobileRequest($request);
        if ($request->isMobile()) //如果是手机
        {
            return $this->mInfo($request, $response,$args);
        }else{//不是手机
            return $this->mInfo($request, $response,$args);
            
        } 
    }
    //如果章节不存在返回为空
    public function mInfo(Request $request, Response $response,$args)
    {
        $bookid  = isset($args['bookid']) ? intval($args['bookid']) : 0;
        if ($bookid > 0 ) {
           $infoData = NovelFunction::getInfoData($bookid);
           if ($infoData) {
               $jieqiSorts = NovelFunction::getNovelSort();//获取所有小说分类
                return $this->ci->view
                        ->render($response, $this->mbPath.'info.html.twig', [
                            'jieqiSorts' => $jieqiSorts,
                            'infoData' => $infoData,
                            'webconfig' => $this->webconfig,
                            'selectjs'=> $infoData['bookInfo']['sortname'] != DFSORT ? '$(".item_'. $infoData['bookInfo']['sortid'] .'").addClass("cur");' : "",
                        ]);
           }
          
        }
         //书不存在跳转首页
        return $response->withRedirect((string) "/", 302);

    }


    public function mulu(Request $request, Response $response,$args)
    {
        $request  = new MobileRequest($request);
        if ($request->isMobile()) //如果是手机
        {
             return $this->mMulu($request, $response,$args);
        }else{//不是手机
            return $this->mMulu($request, $response,$args);
            
        } 

    }

    public function mMulu(Request $request, Response $response,$args)
    {
        
        $bookid  = isset($args['bookid']) ? intval($args['bookid']) : 0;
        $page  = isset($args['page']) ? intval($args['page']) : 1;
        $page <= 0 or  $pid = 1;
        $sort  = isset($args['sort']) ? 'desc' : null ; //false正序
        $pagenum = PAGENUM ;//默认多少数量一页
        $router = $this->ci->get('router');
        if ($bookid > 0  && $page > 0) 
        {

            $infoData = NovelFunction::getMuluData($bookid ,$page,$sort,$pagenum);
            if ($infoData) {
                $jieqiSorts = NovelFunction::getNovelSort();               
                $thispage = "";
                $pageset = '<div class="showpage r3"><div class="bk">请选择章节</div><ul>';
                
                //分页样式
                for($i = 1 ; $i <= $infoData['pagenum'] ; $i++){
                    if($i == $page){
                        $thispage .= '<a class="xbk this tb">'.(($i-1)*$pagenum+1).' - '.($i*$pagenum).'章</a>';
                        $pageset .= $thispage;
                    }
                    else{
                        $url = empty($sort) ? $router->pathFor('novelmulu1', ['bookid'=>$bookid , 'page'=>$i]) : $router->pathFor('novelmulu2', ['bookid'=>$bookid , 'page'=>$i , 'sort' => 1 ]);

                        $pageset .= '<li><a href=" ' . $url  . '" class="xbk">'.(($i-1)*$pagenum+1).' - '.($i*$pagenum).'章</a><li>';
                    }
                }
                $pageset .= '<li><a class="xbk tb">没有更多分页了！</a></li></ul></div>'."<div id='spagebg'></div>";
                $pageset .= '<div class="spage" class="xbk r3">'.$thispage.'</div>'.$pageset;
                
                return $this->ci->view
                        ->render($response, $this->mbPath.'mulu.html.twig', [
                            'jieqiSorts' => $jieqiSorts,
                            'infoData' => $infoData,
                            'webconfig' => $this->webconfig,
                            'jumppage' => $pageset,
                            'sort' => $sort,
                            'selectjs'=> $infoData['bookInfo']['sortname'] != DFSORT ? '$(".item_'. $infoData['bookInfo']['sortid'] .'").addClass("cur");' : "",
                        ]);

            }

            $jupurl = $router->pathFor('novelinfo', ['bookid'=>$bookid]);
            //没有查到数据就跳转介绍 介绍会判断是否跳转首页
            return $response->withRedirect((string) $jupurl, 302);

        }
         //书不存在跳转首页
        return $response->withRedirect((string) "/", 302);

    }

    public function content(Request $request, Response $response,$args)
    {
        $request  = new MobileRequest($request);
        if ($request->isMobile()) //如果是手机
        {
             return $this->mContent($request, $response,$args);
        }else{//不是手机
            return $this->mContent($request, $response,$args);
            
        } 

    }

//围绕目录数据做计算
    public function mContent(Request $request, Response $response,$args)
    {

        $bid  = isset($args['bid']) ? intval($args['bid']) : 0;
        $cid  = isset($args['cid']) ? intval($args['cid']) : 0;
        $txtfind = 0;//判断是否抓取到了内容
        $isimg = 0;//判断是否附件
        if ($bid >0 && $cid>0) {

            $mContentKey = 'nr_'. $bid . $cid;
            $contentData = $this->ci->fcache->get($mContentKey);
            if (!$contentData) {
                            
                $bookData = NovelFunction::getInfoDataBySql($bid);

                if ($bookData) {
                    $router = $this->ci->get('router');
                    $contentData['content'] = "章节丢失了,欢迎举报让我们修复,非常感谢！！！";
                    $shortid = intval($bid/1000);
                    //上下翻页
                    
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
                                continue;
                            }
                        }               
                    }
                    //在章节存在的情况下再去查内容
                    if (isset($contentData['chapter'])) {
                        
                        $puDIR = $shortid .'/'.$bid;
                        $txtDir = TXTDIR . $puDIR ."/{$cid}.txt";
                        
                        //做了附件区别

                        if (!empty($contentData['chapter']['attachment'])) {
                            $imgobj = unserialize($contentData['chapter']['attachment']);
                            $imghtml = "<myimgs class='contentText'>";
                            foreach ($imgobj as  $item) {
                                $img = IMAGEDIR . $puDIR ."/". $cid . "/" .$item['name'];
                                $imghtml .= "<img src='{$img}' />";
                            }
                            $imghtml .= '</myimgs>';
                            $txtfind = 1;
                            $isimg = 1;
                            $contentData['content'] = $imghtml;    
                        }else{

                            $curl = new \Curl\Curl();
                            $curl->setOpt(CURLOPT_TIMEOUT, 5);
                            $curl->get($txtDir);
                            
                            if ($curl->http_status_code == '200') {
                                $txt = $curl->response;
                                $txt = trim($txt);
                                if (!empty($txt)) {
                                    $txtfind = 1;
                                    $txt = mb_convert_encoding($txt, 'utf-8', 'GBK,UTF-8,ASCII');
                                    //$txt = @str_replace("\r\n","<br/>",$txt);
                                    $txt = @str_replace("&nbsp;"," ",$txt); 
                                    $contentData['content'] =  $txt;
                                }
                            }                     
                        }
                        
                        $contentData['preview'] = isset($bookData['chapter'][$key-1]) ? $bookData['chapter'][$key-1] : "";
                        $contentData['next'] = isset($bookData['chapter'][$key+1]) ? $bookData['chapter'][$key+1] : "";
                        //计算所目录
                        
                        $contentData['page'] =ceil( ($key+1)/PAGENUM );
                        $contentData['sortname'] = $bookData['bookInfo']['sortname'] ;
                        $contentData['author'] = $bookData['bookInfo']['author'] ;
                        $contentData['isimg'] = $isimg;
                        if ($txtfind == 1) {
                            //写缓存
                            $this->ci->fcache->set($mContentKey, $contentData ,[                 
                                                    'ttl' => NRCASE,                    
                                                    'compress' => YS,             
                                                ]);
                        }
                          
                        return $this->ci->view
                            ->render($response, $this->mbPath.'chapter.html.twig', [
                                'contentData' => $contentData,
                                'webconfig' => $this->webconfig,
                                'title' => "{$contentData['chapter']['chaptername']}_{$contentData['chapter']['articlename']}_{$contentData['sortname']}-{$this->title}",
                                'keywords' => "{$contentData['chapter']['chaptername']},{$contentData['chapter']['articlename']}",
                                'description' =>  "{$contentData['chapter']['articlename']}是由{$contentData['author']}所写的{$contentData['sortname']}类小说， {$contentData['chapter']['chaptername']}是小说{$contentData['chapter']['articlename']}的最新章节。",    
                            ]);
                    }

                    //章节不存在
                    $bakurl = $router->pathFor('novelinfo', ['bookid'=>$bid]);
                    return $response->withRedirect($bakurl, 302);
                    
                }

            }else{

                return $this->ci->view
                            ->render($response, $this->mbPath.'chapter.html.twig', [
                                'contentData' => $contentData,
                                'webconfig' => $this->webconfig,
                                'title' => "{$contentData['chapter']['chaptername']}_{$contentData['chapter']['articlename']}_{$contentData['sortname']}-{$this->title}",
                                'keywords' => "{$contentData['chapter']['chaptername']},{$contentData['chapter']['articlename']}",
                                'description' =>  "{$contentData['chapter']['articlename']}是由{$contentData['author']}所写的{$contentData['sortname']}类小说， {$contentData['chapter']['chaptername']}是小说{$contentData['chapter']['articlename']}的最新章节。",    
                            ]);

            }      
            //书不存在也跳首页

        }
        //全不对跳首页
        return $response->withRedirect((string) "/", 302);

    }

    public function search(Request $request, Response $response,$args)
    {
        $request  = new MobileRequest($request);
        if ($request->isMobile()) //如果是手机
        {
             return $this->msearch($request, $response,$args);
        }else{//不是手机
            return $this->msearch($request, $response,$args);
            
        } 

    }
    public function msearch(Request $request, Response $response,$args)
    {
        
        $type =  $request->getParsedBodyParam('type');
        $s =  $request->getParsedBodyParam('s'); 
        $s = trim($s);
        $type = trim($type);
        $res = 0;
        $bookDatas = "";
        $search_key = $s;
        if ($type && $s) {
            if($type == "author"){
                $type = "author";
            }else{

                $type = "articlename";
            }
            $key = 'search_'. $type . $s;
            
            $bookDatas = $this->ci->fcache->get($key);
           
            if (!$bookDatas) {
                if(strlen($s) > 2){
                    $articleArticle =     
                                ArticleArticle::BaseBook()
                                                ->where($type , 'like', "%{$s}%")
                                                ->orderBy('articleid', 'desc')
                                                ->take(50)
                                                ->get();
                                              
                    if ($articleArticle && !$articleArticle->isEmpty()) {
                        $res = 1;
                        $bookDatas = $articleArticle->toArray();
                        $bookDatas = SourceUtil::formatNoveInfoData($bookDatas);
                        //写缓存
                        $this->ci->fcache->set($key, $bookDatas ,[                 
                                                'ttl' => SCASE,                    
                                                'compress' => YS,             
                                            ]);                         
                    }

                }else{

                    $search_key = "搜索词在2个词以上";
                }
                    

            }else{
                $res = 1;
            } 

        }else{
            $search_key = "没有输入查询条件";
        }
        $jieqiSorts = NovelFunction::getNovelSort();  
        return $this->ci->view
                            ->render($response, $this->mbPath.'search.html.twig', [
                                'jieqiSorts' => $jieqiSorts,
                                'search_key'=>$search_key,
                                'res' => $res,
                                'webconfig' => $this->webconfig,
                                'bookDatas' =>$bookDatas,
                                'title' =>"搜索结果"
                            ]);


    }
    
} // END class Sign
