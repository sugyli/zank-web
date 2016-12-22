<?php

namespace Zank\Controller\Novel\User;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Zank\UserController;
use Slim\Http\MobileRequest;
use Zank\Util\NovelFunction;
use Zank\Util\SourceUtil;
/**
 * 认证控制器.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class Control extends UserController
{
    
    public function login(Request $request, Response $response,$args)
    {
        
       $request  = new MobileRequest($request);
        if ($request->isMobile()) //如果是手机
        {
            return $this->mLogin($request, $response,$args);
        }else{//不是手机
            return $this->mLogin($request, $response,$args);
            
        } 
    }

    public function mLogin(Request $request, Response $response,$args)
    {
        open_session();
        $send_code = $_SESSION['send_code'] = random(6,1);

        return $this->ci->view
                ->render($response, NOVELMB.'login.html.twig', [
                    'title' =>'用户登陆',
                    'send_code' => $send_code,
                    'webconfig' => WEBCONFIG,
                ]);
    }

    public function code(Request $request, Response $response)
    {     
        open_session();
        $fontstyle = ROOT.'public/css/novel/wap/font/ARIAL.TTF';
        if (file_exists($fontstyle))
        {
            $im = imagecreate($x=80,$y=25 );
            $bg = imagecolorallocate($im,rand(10,20),rand(130,170),rand(200,255));//背景色
            $fontColor = imageColorAllocate ( $im, 255, 255, 255 );//边框色
            $authcode = "";
            for($i = 0; $i < 4; $i ++) {
                $randAsciiNumArray         = array (rand(48,57),rand(65,90));
                $randAsciiNum                 = $randAsciiNumArray [rand ( 0, 1 )];
                $randStr                         = chr ( $randAsciiNum );
                imagettftext($im,14,rand(0,20)-rand(0,25),5+$i*18,rand(18,22),$fontColor,$fontstyle,$randStr);
                $authcode                        .= $randStr; 
            }
            $_SESSION['novel_code'] = $authcode;
            for ($i=0;$i<5;$i++){
                $lineColor        = imagecolorallocate($im,rand(50,100),rand(120,150),rand(100,200));
                imageline ($im,rand(0,$x),0,rand(0,$x),$y,$lineColor);
            }
            ob_end_clean();
            ob_start();
            imagepng($im);
            $content = ob_get_flush();
            ob_end_clean();
            imagedestroy($im);
            $newResponse = $response->withHeader('Content-type', 'image/png');           
            return $newResponse->write($content);
        }
        $this->errlog($request ,'获取图形验证码出错！');
        
    }


    /**
     * 注册，手机号，密码注册.
     *
     * @param Request  $request  请求对象
     * @param Response $response 响应对象
     *
     * @return Response 请求对象
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function stepRegisterBase(Request $request, Response $response): Response
    {
        $phone = $request->getParsedBodyParam('mobile');
        $password = $request->getParsedBodyParam('userpass');
        $password = trim($password);
        //$invite_code = $request->getParsedBodyParam('invite_code');
        $lengs = strlen($password);
        if ($lengs <= 0  or  $lengs >=16) {
            return with(new \Zank\Common\Message($response, false, '密码长度在1到16位之间'))
            ->withJson();
        }

        $user = new \Zank\Model\Novel\Wap\SystemUsers();
        $user->uname = $phone;
        $user->name = sprintf('用户_%s', $phone);
        $user->pass = md5($password);
        $user->groupid = 3;
        $user->regdate = time();
        $user->viewemail = 1;
        $user->adminemail = 1;
        $user->email = $phone.str_random(10);
        $user->lastlogin = time();

        if ($user->save()) {
            $this->ci->offsetSet('user', $user);
            //注册的时候删除
            //\Zank\Model\UsersRecord::where('user_id',$user->user_id)->delete();
            //$usersRecord = new \Zank\Model\UsersRecord();
            //$usersRecord->integral = 5;
            //$usersRecord->user_id = $user->user_id;
            /*
            if (!$usersRecord->save()) {
                //\Zank\Model\User::destroy($user->user_id);
                $user->forceDelete();//真删除
                return with(new \Zank\Common\Message($response, false, '注册失败_初始化积分出错！'))
                     ->withJson();
                //$user->forceDelete();//删除注册过的组件
                
            }

            $this->ci->offsetSet('usersrecord', $usersRecord);
            */
            return $this->in($request, $response);
        }
        $this->errlog($request ,'注册失败！');
        return with(new \Zank\Common\Message($response, false, '注册失败！'))
            ->withJson();
    }


     /**
     * 忘记密码重置.
     *
     * @param Request  $request  请求对象
     * @param Response $response 响应对象
     *
     * @return Response 请求对象
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function forgetpass(Request $request, Response $response): Response
    {
        
        $phone = $request->getParsedBodyParam('mobile');
        $password = $request->getParsedBodyParam('userpass');
        $password = trim($password);
        $lengs = strlen($password);
        if ($lengs <= 0  or  $lengs >=16) {
            return with(new \Zank\Common\Message($response, false, '密码长度在1到16位之间'))
            ->withJson();
        }
        //$invite_code = $request->getParsedBodyParam('invite_code');
        if ($this->ci->has('user')) {
            $user = $this->ci->get('user');
        } else {
            $user = \Zank\Model\Novel\Wap\SystemUsers::where('uname',$phone)->first();
        }

        if ($user) {
            //$user->hash = str_random(64);
            $user->pass = md5($password);

            if ($user->save()) {
                $this->ci->offsetSet('user', $user);
                return $this->in($request, $response);
            }

        }
        $this->errlog($request ,'修改密码失败！');
        return with(new \Zank\Common\Message($response, false, '修改密码失败！'))
        ->withJson();
    }

    /**
     * 登陆控制器.
     *
     * @param Request $request
     */
    public function in(Request $request, Response $response)
    {
        $phone = $request->getParsedBodyParam('mobile');
        $password = $request->getParsedBodyParam('userpass');
        $password = trim($password);
        if ($this->ci->has('user')) {
            $user = $this->ci->get('user');
        } else {
           $user = \Zank\Model\Novel\Wap\SystemUsers::where('uname',$phone)->first();
        }

        if ($user->pass != md5($password)) {
            $response = new \Zank\Common\Message($response, false, '该用户密码错误！');

            return $response->withJson();
        }

        $token = new \Zank\Model\SignToken();
        $token->token = \Zank\Model\SignToken::createToken();
        $token->refresh_token = \Zank\Model\SignToken::createRefreshToken();
        $token->user_id = $user->uid;
        $token->expires = UEXTIME; // 1个月过期

        // 清除token
        \Zank\Model\SignToken::where('token', $token->token)
            ->orWhere('refresh_token', $token->refresh_token)
            ->orWhere('user_id', $token->user_id)
            ->delete();

        if (!$token->save()) {
            $response = new \Zank\Common\Message($response, false, '登陆失败！');

            return $response->withJson();

        // 判断是否注入了验证码，删除验证码
        } elseif ($this->ci->has('phone_captcha')) {
            $this->ci->get('phone_captcha')->delete();
        }
        $this->ci->cookies->set('zank-token',$token->token);
        $tokenCook = $this->ci->cookies->toHeaders();
        $response = $response->withAddedHeader('Set-Cookie', $tokenCook);

        return with(new \Zank\Common\Message($response, true, '登陆成功！', $token))
            ->withJson();
    }


    public function usercore(Request $request, Response $response,$args)
    {
        
       $request  = new MobileRequest($request);
        if ($request->isMobile()) //如果是手机
        {
            return $this->musercore($request, $response,$args);
        }else{//不是手机
            return $this->musercore($request, $response,$args);
            
        } 
    }


    //用户中心
    public function musercore(Request $request, Response $response,$args)
    {

        if ($this->ci->has('user')) {
            $user = $this->ci->get('user');
            $user = $user->toArray();
            unset($user['pass']);
            $jieqiSorts = NovelFunction::getNovelSort();//获取所有小说分类
            return $this->ci->view
                    ->render($response, NOVELMB.'user.html.twig', [
                        'jieqiSorts' => $jieqiSorts,
                        'user' => $user,
                        'title'=>"用户信息",
                        'webconfig' => WEBCONFIG,
                    ]);

        }
        $this->errlog($request ,'用户验证成功,进入会员中心却没发现用户数据');
        return $response->withRedirect((string) "/", 302);

    }


    public function bookcase(Request $request, Response $response,$args)
    {
        
       $request  = new MobileRequest($request);
        if ($request->isMobile()) //如果是手机
        {
            return $this->mbookcase($request, $response,$args);
        }else{//不是手机
            return $this->mbookcase($request, $response,$args);
            
        } 
    }



    public function mbookcase(Request $request, Response $response,$args)
    {
        if ($this->ci->has('user')){
            $user = $this->ci->get('user');
            $jieqiSorts = NovelFunction::getNovelSort();//获取所有小说分类
            $caseDatas = []; 
            $total = $user->bookcase()
                          ->count();
            if ($total>0) {
                $bookCaseDatas = $user->bookcase()->get();
                
                if ($bookCaseDatas && !$bookCaseDatas->isEmpty()) {

                    $bookCaseDatas = $bookCaseDatas->toArray(); 
                    $ids = array_column($bookCaseDatas, 'articleid');  
                    $articleDatas =
                                \Zank\Model\Novel\Wap\ArticleArticle::BaseBook()
                                                    ->whereIn('articleid', $ids)
                                                    ->orderBy('lastupdate','desc')
                                                    ->get();
                                            
                    if ($articleDatas && !$articleDatas->isEmpty()) {
                        $articleDatas = $articleDatas->toArray(); 
                        foreach ($articleDatas as $key => $itme) {
                            //这个数据是本数据库存在的用户数据
                            $oneBookCaseData = SourceUtil::findForTwoArry($bookCaseDatas,$itme['articleid'] , 'articleid');
                            if ($oneBookCaseData) {                              
                                if ($oneBookCaseData['chapterid'] >0) {
                                    //查询章节可存在了
                                    $chaptercount = 
                                            \Zank\Model\Novel\Wap\ArticleChapter::BaseChapter()
                                                                    ->where('chapterid',$oneBookCaseData['chapterid'])
                                                                    ->count();
                                    if ($chaptercount>0) {
                                        $itme['nochapter'] = null;
                                    }else{
                                        $itme['nochapter'] = "章节已经不存在了";
                                    }
                                }else{

                                    $itme['nochapter'] = "没有添加书签";
                                }           
                                unset($itme['lastvisit']);
                                $result = array_merge($oneBookCaseData, $itme);
                                $caseDatas[] = $result;
                            }

                        }

                    }

                }
            }

            return $this->ci->view
                    ->render($response, NOVELMB.'bookcase.html.twig', [
                        'jieqiSorts' => $jieqiSorts,
                        'caseDatas' => $caseDatas,
                        'title' =>"书架(总容量{$user->bookcount}本)",
                        'webconfig' => WEBCONFIG,
                    ]);

        }
        $this->errlog($request ,'用户验证成功,进入书架却没发现用户数据');
        return $response->withRedirect((string) "/", 302);
        
    }

    public function addbookcase(Request $request, Response $response,$args)
    {
        $bid = $request->getParsedBodyParam('bid');
        $cid = $request->getParsedBodyParam('cid');
        $bid = intval($bid);
        $cid = intval($cid);
        if ($this->ci->has('user') && $bid > 0){
            $user = $this->ci->get('user');
            //检查这本是不是正常
            $articleArticle = \Zank\Model\Novel\Wap\ArticleArticle::BaseBook()
                                    ->where('articleid' , $bid)
                                    ->first();
            
            if ($articleArticle) 
            {
                
                $bookcase = 
                            $articleArticle->ArticleBookcase()
                                                ->where('userid' , $user->uid)
                                                ->first();
                


                if ($bookcase) {//书架有就更新
                    
                    if ($cid > 0) {

                        $articleChapter = 
                                        $articleArticle->ArticleChapter()
                                                        ->BaseChapter()
                                                        ->where('chapterid' ,$cid )
                                                        ->first();
                        if ($articleChapter) {
                            $bookcase->articlename = $articleArticle->articlename;
                            $bookcase->username = $user->uname;
                            $bookcase->chapterid = $articleChapter->chapterid;
                            $bookcase->chaptername = $articleChapter->chaptername;
                            $bookcase->chapterorder = $articleChapter->chapterorder;

                            if ($bookcase->save()) {
                                return with(new \Zank\Common\Message($response, true, '更新书签成功！'))
                                        ->withJson();
                            }else{

                                return with(new \Zank\Common\Message($response, false, '更新书签失败,可能已经存在！'))
                                        ->withJson();

                            }


                        }else{

                            return with(new \Zank\Common\Message($response, false, '此章节不存在了,无法更新书签！'))
                                        ->withJson();


                        }

                    }else{


                        return with(new \Zank\Common\Message($response, false, '本书已经在书架中了！'))
                                        ->withJson();

                    }

                }else{//收藏没有要加入收藏的时候判断下是不是 满了
                    $total = $user->bookcase()->count();
                    if ($total >= $user->bookcount) {
                        return with(new \Zank\Common\Message($response, false, "您目前等级最多有{$user->bookcount}本收藏,请清理书架！"))
                                ->withJson();
                    }

                    if ($cid > 0) {
                        $articleChapter = 
                                        $articleArticle->ArticleChapter()
                                                        ->BaseChapter()
                                                        ->where('chapterid' ,$cid )
                                                        ->first();
                        
                        if ($articleChapter) {
                            $bookcase = new \Zank\Model\Novel\Wap\ArticleBookcase();
                            $bookcase->articleid = $articleArticle->articleid;
                            $bookcase->articlename = $articleArticle->articlename;
                            $bookcase->userid = $user->uid;
                            $bookcase->username = $user->uname;
                            $bookcase->chapterid = $articleChapter->chapterid;
                            $bookcase->chaptername = $articleChapter->chaptername;
                            $bookcase->chapterorder = $articleChapter->chapterorder;
                            $bookcase->joindate = time();
                            $bookcase->lastvisit = time();
                            if ($bookcase->save()) {
                                return with(new \Zank\Common\Message($response, true, '添加书签成功！'))
                                        ->withJson();
                            }else{

                                return with(new \Zank\Common\Message($response, false, '添加书签失败,请联系管理员！'))
                                        ->withJson();

                            }


                        }else{

                            
                            return with(new \Zank\Common\Message($response, false, '此章节不存在了,无法添加书签！'))
                                        ->withJson();

                        }
                    }else{

                        $bookcase = new \Zank\Model\Novel\Wap\ArticleBookcase();
                        $bookcase->articleid = $articleArticle->articleid;
                        $bookcase->articlename = $articleArticle->articlename;
                        $bookcase->userid = $user->uid;
                        $bookcase->username = $user->uname;
                        $bookcase->joindate = time();
                        $bookcase->lastvisit = time();
                        if ($bookcase->save()) {
                            return with(new \Zank\Common\Message($response, true, '添加本书成功！'))
                                    ->withJson();
                        }else{

                            return with(new \Zank\Common\Message($response, false, '添加本书失败,请联系管理员！'))
                                    ->withJson();

                        }
                    }

                }

            }

        }
        $this->errlog($request ,"本书不存在或登陆失效！传入的ID是{$bid}");
        return with(new \Zank\Common\Message($response, false, '本书不存在或登陆失效！'))
                                        ->withJson();

    }

    public function delbookcase(Request $request, Response $response,$args)
    {

        $id = $request->getParsedBodyParam('id');
        $id = intval($id);

        if ($id >0 && $this->ci->has('user')){
            $user = $this->ci->get('user');
            \Zank\Model\Novel\Wap\ArticleBookcase::where('caseid',$id)->where('userid',$user->uid)->forceDelete();
            //\Zank\Model\Novel\Wap\ArticleBookcase::destroy($id);
            return with(new \Zank\Common\Message($response, true, '已经删除！'))
                    ->withJson();
            
        }
        $this->errlog($request ,"删除收藏失败 传入的ID是 {$id}");
        return with(new \Zank\Common\Message($response, false, '删除失败！'))
                    ->withJson();



    }

    public function readbookcase(Request $request, Response $response,$args)
    {

        $bid  = isset($args['bid']) ? intval($args['bid']) : 0;
        $cid  = isset($args['cid']) ? intval($args['cid']) : 0;
        $jumpurl = "/";
        $router = $this->ci->get('router');
        
        if ($bid>0 && $cid>0) {

            if ($this->ci->has('user')){
                $user = $this->ci->get('user');
                $bookCase = $user->bookcase()->where('articleid' , $bid)->first();
                if ($bookCase) { 
                    $key = 'mulu_'. $bid; //删除目录缓存给予最新
                    $this->ci->fcache->delete($key);                 
                    $bookCase->lastvisit = time();
                    if (!$bookCase->save()) {
                        $this->comlog($request , "用户点击书架链接更新最新时间失败");
                    }                

                }

                $jumpurl = $router->pathFor('novelcontent', ['bid'=>$bid ,'cid' => $cid]);

            }else{


                $jumpurl = $router->pathFor('loginurl');
            }

        }
        

        return $response->withRedirect((string) $jumpurl, 302);

    }


    public function mailbox(Request $request, Response $response,$args)
    {
        
       $request  = new MobileRequest($request);
        if ($request->isMobile()) //如果是手机
        {
            return $this->mMailbox($request, $response,$args);
        }else{//不是手机
            return $this->mMailbox($request, $response,$args);
            
        } 
    }

    public function mMailbox(Request $request, Response $response,$args)
    {
        $type  = isset($args['type']) ? intval($args['type']) : 0;

        if ($this->ci->has('user')){
            $user = $this->ci->get('user');
            $jieqiSorts = NovelFunction::getNovelSort();//获取所有小说分类
            $mesDatas = '';
            if ($type > 0) {//发件
                $mesDatas = $user->messages('fromid')
                            ->where('fromdel',0)
                            ->where('toid' , 0)
                            ->orderBy('postdate','desc') 
                            ->get();
            }else{//收件
                $mesDatas = $user->messages('toid')
                                ->where('todel',0)
                                 ->where('fromid' , 0)
                                 ->orderBy('postdate','desc') 
                                 ->get();

            }

            if ($mesDatas && !$mesDatas->isEmpty()) {
                $mesDatas= $mesDatas->toArray(); 
            }

            //daying($mesDatas);
            return $this->ci->view
                    ->render($response, NOVELMB.'mailbox.html.twig', [
                        'jieqiSorts' => $jieqiSorts,
                        'mesDatas' => $mesDatas,
                        'title' => $type > 0 ? '发件箱' : '收件箱',
                        'type' =>$type,
                        'webconfig' => WEBCONFIG,
                     ]);
        }

        $this->errlog($request ,'用户验证成功,进入邮箱却没发现用户数据');
        return $response->withRedirect((string) "/", 302);
    }

//收邮件
    public function receiveMail(Request $request, Response $response,$args)
    {
        
       $request  = new MobileRequest($request);
        if ($request->isMobile()) //如果是手机
        {
            return $this->mReceiveMail($request, $response,$args);
        }else{//不是手机
            return $this->mReceiveMail($request, $response,$args);
            
        } 
    }


    public function mReceiveMail(Request $request, Response $response,$args)
    {
        
        $content = $request->getParsedBodyParam('content');
        $title = $request->getParsedBodyParam('title');
        $content = trim($content);
        //$invite_code = $request->getParsedBodyParam('invite_code');
        $lengs = strlen($content);
        if ($lengs <= 0) {
            return with(new \Zank\Common\Message($response, false, '内容不能为空'))
            ->withJson();
        }

        if ($this->ci->has('user')){
            $user = $this->ci->get('user');

            $systemMessage = new \Zank\Model\Novel\Wap\SystemMessage();

            $ipAddress = $request->getAttribute('ip_address')?:"未获取到IP";//Ip地址
            $content = $content . " ip：{$ipAddress}";
            $title = $title . " (wap)";
            $systemMessage->title = $title;
            $systemMessage->content = $content;
            $systemMessage->attachsig = 0;
            $systemMessage->fromid = $user->uid;
            $systemMessage->fromname = $user->uname;
            $systemMessage->toid = 0;
            $systemMessage->postdate = time();

            if ($systemMessage->save()) {
                return with(new \Zank\Common\Message($response, true, '您的消息我们已经接收到了！'))
                    ->withJson();
            }
            $this->errlog($request ,'通过验证,但是保存邮件去数据库失败');
            return with(new \Zank\Common\Message($response, false, '服务器压力太大 稍后再试！'))
                    ->withJson();
        }
        $this->errlog($request ,'用户验证成功,从中间件没取到用户数据');
        return with(new \Zank\Common\Message($response, false, '邮件服务器在维护中,稍后再试！'))
                    ->withJson();
    }

    public function delMail(Request $request, Response $response,$args)
    {
        $id = $request->getParsedBodyParam('id');
        $id = intval($id);
        $type = $request->getParsedBodyParam('type');
        $type  = intval($type);

        if ($id >0 && $this->ci->has('user')){
            $user = $this->ci->get('user');
            if ($type > 0) {//发件
                
                $systemMessage = \Zank\Model\Novel\Wap\SystemMessage::where('messageid',$id)->where('fromid' ,$user->uid)->where('todel' ,1)->first();

                if ($systemMessage) {
                    $systemMessage->forceDelete();
                    return with(new \Zank\Common\Message($response, true, '已经删除！'))
                                ->withJson();
                }else{

                    $systemMessage = \Zank\Model\Novel\Wap\SystemMessage::where('messageid',$id)->where('fromid' ,$user->uid)->first();
                    if ($systemMessage) {
                        $systemMessage->fromdel = 1;
                        if ($systemMessage->save()) {
                           return with(new \Zank\Common\Message($response, true, '已经删除！'))
                                    ->withJson();
                        }
                    }
                }       

            }else{//收件
              
                
                $systemMessage = \Zank\Model\Novel\Wap\SystemMessage::where('messageid',$id)->where('toid' ,$user->uid)->where('fromdel' ,1)->first();

                if ($systemMessage) {
                    $systemMessage->forceDelete();
                    return with(new \Zank\Common\Message($response, true, '已经删除！'))
                                ->withJson();
                }else{

                    $systemMessage = \Zank\Model\Novel\Wap\SystemMessage::where('messageid',$id)->where('toid' ,$user->uid)->first();
                    if ($systemMessage) {
                        $systemMessage->todel = 1;
                        if ($systemMessage->save()) {
                           return with(new \Zank\Common\Message($response, true, '已经删除！'))
                                    ->withJson();
                        }
                    }

                }
            
            }

        }
        $this->errlog($request ,"删除邮件失败 传入的id是 {$id}");
        return with(new \Zank\Common\Message($response, false, '删除失败！'))
                    ->withJson();

    }


    /**
     * 用户打卡.
     *
     * @param Request  $request  请求对象
     * @param Response $response 响应对象
     *
     * @return Response 请求对象
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function clock(Request $request, Response $response): Response
    {
       
        if ($this->ci->has('user')) {
            $user = $this->ci->get('user');
            
            if (($user->created_at !=  $user->updated_at) &&  $user->updated_at->isToday()) {
                return with(new \Zank\Common\Message($response, false, '今天您已经打过卡了！'))
                    ->withJson();
            }
            $user = \Zank\Model\Novel\Wap\SystemUsers::find($user->uid);
            $user->score = $user->score + DAKA;
            $user->experience = $user->score;
            if ($user->save()) {
                return with(new \Zank\Common\Message($response, true, '打卡成功获取'.DAKA.'点积分,刷新页面更新！'))
                    ->withJson();
            }

        }
        $this->errlog($request ,'用户验证成功,从中间件没取到用户数据或保存出错了');
        return with(new \Zank\Common\Message($response, false, "打卡失败,请联系管理员"))
                    ->withJson();
    }

    public function exitweb(Request $request, Response $response)
    {

        if ($this->ci->has('user')) {
            $user = $this->ci->get('user');
            \Zank\Model\SignToken::where('user_id', $user->uid)
                    ->delete();

            return with(new \Zank\Common\Message($response, true, '退出成功！'))
                    ->withJson();               
        }
        $this->errlog($request ,'退出失败');
        return with(new \Zank\Common\Message($response, false, '退出失败！'))
                    ->withJson();  
    }
    
} // END class Sign
