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
//app
    public function appIndex(Request $request, Response $response,$args)
    {
        $bookid =  $request->getParsedBodyParam('bookid');
        $sortid =  $request->getParsedBodyParam('sortid'); 
        $isdow =  $request->getParsedBodyParam('isdow'); 
        $bookid =  intval($bookid);
        $sortid =  intval($sortid);
        $isdow =  intval($isdow);

        if ($sortid > 0) {
            $sort = NovelFunction::findNovelSortById($sortid);
        }
        $sortid = isset($sort['sortid']) ? $sort['sortid']:0;
        if ($bookid > 0) {
            
            $articleArticle = ArticleArticle::pageAppData($bookid,$this->pageSize,$sortid , $isdow);
            

        }else{

            if ($sortid>0) {

                $articleArticle =  ArticleArticle::BaseBook()
                                            ->where('sortid',$sortid)
                                            ->orderBy('articleid','desc')
                                            ->take($this->pageSize)   //限制(Limit)                                                   
                                            ->get();
            }else{

                $articleArticle =  ArticleArticle::BaseBook()
                                            ->orderBy('articleid','desc')
                                            ->take($this->pageSize)   //限制(Limit)                         
                                            ->get();

            }



        }

        if ($articleArticle && !$articleArticle->isEmpty()) {

            $articleDatas = $articleArticle->toArray();
            $articleDatas = SourceUtil::formatNoveInfoData($articleDatas);

            $outdata['islast'] = false;
            $outdata['items'] = $articleDatas;
            if(count($articleDatas) < $this->pageSize)
            {
                $outdata['islast'] = true;
            }
            return with(new \Zank\Common\Message($response, true, '请求成功！', $outdata))
                    ->withJson();

        }
        return with(new \Zank\Common\Message($response, false, '没有获取到数据'))
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
                            'bookid' => $bookid,
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
        $page > 0 or  $page = 1;
        $sort  = isset($args['sort']) ? 'desc' : null ; //false正序
        $pagenum = PAGENUM ;//默认多少数量一页
        $router = $this->ci->get('router');
        if ($bookid > 0) 
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
                            'bookid' => $bookid,
                            'webconfig' => $this->webconfig,
                            'jumppage' => $pageset,
                            'sort' => $sort,
                            'page' => $page,
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
        //$txtfind = 0;//判断是否抓取到了内容
        $isimg = 0;//判断是否附件
        if ($bid >0 && $cid>0) {
                           
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
                            break;
                        }
                    }               
                }
                //在章节存在的情况下再去查内容
                if (isset($contentData['chapter'])) {
                    
                    $puDIR = $shortid .'/'.$bid;
                    $txtDir = TXTDIR . $puDIR ."/{$cid}.txt";
                    
                    //做了附件区别
                    $mContentKey = 'nr_'. $bid ."_". $cid ."_". $contentData['chapter']['lastupdate'];
                    $txt = $this->ci->fcache->get($mContentKey);
                    if (!$txt) {
                        $curl = new \Curl\Curl();
                        $curl->setOpt(CURLOPT_TIMEOUT, 5);                           
                        $curl->get($txtDir);
                        
                        if ($curl->http_status_code == '200') {
                            $txt = $curl->response;
                            $txt = trim($txt);
                            
                            if (!empty($contentData['chapter']['attachment']) && getstrlength(t($txt))<=300) 
                            {
                                $imgobj = unserialize($contentData['chapter']['attachment']);
                                $imghtml = "<myimgs class='contentText'>";
                                foreach ($imgobj as  $item) {
                                    $img = IMAGEDIR . $puDIR ."/". $cid . "/" .$item['name'];
                                    $imghtml .= "<img src='{$img}' />";
                                }
                                $imghtml .= '</myimgs>';
                                //$txtfind = 1;
                                $isimg = 1;
                                $contentData['content'] = $imghtml;    
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

                            /*
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
                                
                            }else{

                                if (!empty($contentData['chapter']['attachment'])) {
                                    $imgobj = unserialize($contentData['chapter']['attachment']);
                                    $imghtml = "<myimgs class='contentText'>";
                                    foreach ($imgobj as  $item) {
                                        $img = IMAGEDIR . $puDIR ."/". $cid . "/" .$item['name'];
                                        $imghtml .= "<img src='{$img}' />";
                                    }
                                    $imghtml .= '</myimgs>';
                                    //$txtfind = 1;
                                    $isimg = 1;
                                    $contentData['content'] = $imghtml;    
                                }

                            }
                            */
                        }  
                    }else{
                        
                        $contentData['content'] =  $txt;  
                    }
                    
                    $contentData['preview'] = isset($bookData['chapter'][$key-1]) ? $bookData['chapter'][$key-1] : "";
                    $contentData['next'] = isset($bookData['chapter'][$key+1]) ? $bookData['chapter'][$key+1] : "";
                    //计算所目录
                    
                    $contentData['page'] =ceil( ($key+1)/PAGENUM );
                    $contentData['sortname'] = $bookData['bookInfo']['sortname'] ;
                    $contentData['author'] = $bookData['bookInfo']['author'] ;
                    $contentData['isimg'] = $isimg;
                    $contentData['cover'] = $bookData['bookInfo']['cover'];
                    //判断属于哪个分类
                    $selectjs= $bookData['bookInfo']['sortname'] != DFSORT ? '$(".item_'. $bookData['bookInfo']['sortid'] .'").addClass("cur");' : "";
                    unset($bookData);
                    $jieqiSorts = NovelFunction::getNovelSort();//获取所有小说分类
                    return $this->ci->view
                        ->render($response, $this->mbPath.'chapter.html.twig', [
                            'jieqiSorts' => $jieqiSorts,
                            'selectjs' => $selectjs,
                            'contentData' => $contentData,
                            'bookid' => $bid,
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
              
            //书不存在也跳首页

        }
        //全不对跳首页
        return $response->withRedirect((string) "/", 302);

    }

    public function baidusearch(Request $request, Response $response,$args)
    {
        $jieqiSorts = NovelFunction::getNovelSort();
        return $this->ci->view
                            ->render($response, $this->mbPath.'baidusearch.html.twig', [
                                'jieqiSorts' => $jieqiSorts,
                                'webconfig' => $this->webconfig,
                                'title' =>"站内搜索"
                            ]);

    }

    public function linshishujia(Request $request, Response $response,$args)
    {
        $jieqiSorts = NovelFunction::getNovelSort();
        return $this->ci->view
                            ->render($response, $this->mbPath.'hislogs.html.twig', [
                                'jieqiSorts' => $jieqiSorts,
                                'webconfig' => $this->webconfig,
                                'title' =>"临时书架"
                            ]);

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
            $search_key = "没有输入查询条件将会是";
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

    //判断是否更新
    public function upsqldata(Request $request, Response $response,$args)
    {

        $bookid  = isset($args['bookid']) ? intval($args['bookid']) : 0;
        if ($bookid>0) {
            NovelFunction::checkUpSql($bookid);
        }
        //return $response->write('console.log("1");');
    }


    public function mSiteMap(Request $request, Response $response,$args)
    {
        $newResponse = $response->withHeader('Content-type', 'text/xml');
        $uri = $request->getUri();
        $host = $uri->getHost();
        $scheme = $uri->getScheme();
        $pagesize = 300; //每页输出几条记录
        $router = $this->ci->get('router');
        $url = $scheme . "://".$host;
        $page =  isset($args['page']) ? intval($args['page']) : 0;
        if ($page > 0) {

            $total = ArticleArticle::BaseBook()->count();//总数
            //计算总页数
            $pagenum = ceil( $total / $pagesize );//当没有数据的时候 计算出来为0
            if ($page > $pagenum)
            {
                $page = $pagenum;//分页越界

            } 
            //下一页开始的ID (0)开始
            $offset = ($page - 1) * $pagesize;
            $articleArticle = 
                            ArticleArticle::BaseBook()
                                    ->orderBy('articleid','desc')
                                    ->skip($offset)   //偏移(Offset)
                                    ->take($pagesize)   //限制(Limit)
                                    ->get();
            if ($articleArticle && !$articleArticle->isEmpty()) {
                $articleArticle = $articleArticle->toArray(); 
                $articleArticle = SourceUtil::formatNoveInfoData($articleArticle);
                $xml = 
                    '<?xml version="1.0"  encoding="UTF-8" ?>
                        <urlset>
                        ';
                foreach ($articleArticle as $key => $row) {
                    $row['intro'] = preg_replace('/[\x00-\x08\x0b-\x0c\x0e-\x1f\x7f]/', '', $row['intro']);
                    $row['articlename'] = preg_replace('/[\x00-\x08\x0b-\x0c\x0e-\x1f\x7f]/', '', $row['articlename']);
                    $row['author'] = preg_replace('/[\x00-\x08\x0b-\x0c\x0e-\x1f\x7f]/', '', $row['author']);
                    $row['lastchapter'] = preg_replace('/[\x00-\x08\x0b-\x0c\x0e-\x1f\x7f]/', '', $row['lastchapter']);
                    $xml .= 
                         '<url>
                            <loc><![CDATA[' . $url . $router->pathFor('novelinfo', ['bookid'=>$row['articleid']]).']]></loc>
                            <lastmod>'. date('Y-m-d', $row['lastupdate']) . 'T' . date('H:i:s', $row['lastupdate']) . '</lastmod>
                            <changefreq>always</changefreq>
                            <priority>1.0</priority>
                            <data>
                                <name>'. htmlspecialchars($row['articlename']) .'</name>
                                <author>
                                    <name>'. htmlspecialchars($row['author']) .'</name>
                                </author>    
                                <image><![CDATA['. $row['cover'] .']]></image>
                                <description><![CDATA['. htmlspecialchars($row['intro']) .']]></description>
                                <alternativeHeadline/>
                                <genre>'.$row['sortname'].'</genre>
                                <url><![CDATA[' . $url . $router->pathFor('novelinfo', ['bookid'=>$row['articleid']]).']]></url>
                                <updateStatus>更新中</updateStatus>
                                <trialStatus>免费</trialStatus>
                                <keywords/>
                                <newestChapter>
                                    <articleSection>'. htmlspecialchars($row['articlename']) .'</articleSection>
                                    <headline>'.htmlspecialchars($row['lastchapter']).'</headline>
                                    <url>
                                        <![CDATA['. $url . $router->pathFor('novelcontent', ['bid'=>$row['articleid'] , 'cid'=>$row['lastchapterid']]).']]>
                                    </url>
                                    <dateModified>'.date('Y-m-d', $row['lastupdate']) .'</dateModified>
                                </newestChapter>    
                                <dateModified>'.date('Y-m-d', $row['lastupdate']) .'</dateModified>
                                <listPage>
                                    <headline>'. htmlspecialchars($row['articlename']) .'</headline>
                                    <url>
                                        <![CDATA[' . $url . $router->pathFor('novelinfo', ['bookid'=>$row['articleid']]).']]>
                                    </url>
                                    <itemCount>1</itemCount>
                                </listPage>
                            </data>
                          </url>';
                            
                            
                }
                $xml .= '</urlset>';
                return $newResponse->write($xml);
            }             
        }else{

            $total = ArticleArticle::BaseBook()->count();//总数

            if($total >0){
                //计算总页数
                $pagenum = ceil( $total / $pagesize );//当没有数据的时候 计算出来为0
                $xml = 
                    '<?xml version="1.0"  encoding="UTF-8" ?>
                        <sitemapindex>
                        ';
                for($p = 1; $p <= $pagenum; $p++)
                {
                    $url1 = $url . $router->pathFor('mbookmap', ['page'=>$p]);
                    $xml .=
                            '<sitemap>
                            <loc>' . $url1 . '</loc>
                            </sitemap>
                            ';
                }        
                $xml .= '</sitemapindex>';
                return $newResponse->write($xml);

            }
        }

        $this->comlog($request ,'搜索地图出现BUG');

    }

    
} // END class Sign
