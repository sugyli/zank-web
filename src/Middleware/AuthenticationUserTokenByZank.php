<?php

namespace Zank\Middleware;

use Carbon\Carbon;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * 用户token认证（用于API请求）.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class AuthenticationUserTokenByZank
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $token = $request->getCookieParam('zank-token');
        $router = $this->ci->get('router');
        $loginurl = $router->pathFor('loginurl');
        $uri = $request->getUri();
        $query = $uri->getQuery();
        if ($query) {
            $loginurl = $loginurl . "?" . $query;
        }
        //$token = $request->getHeaderLine('zank-token');
        $token = \Zank\Model\SignToken::byToken($token)->first();

        if (!$token) {
            if ($request->isPost()) {
                return with(new \Zank\Common\Message($response, false, '认证失败或者认证信息不存在。', -1))
                    ->withJson();
            }
            return $response->withRedirect((string) $loginurl, 302);

        // 是否过期
        } elseif ($token->updated_at->diffInSeconds(Carbon::now()) >= UEXTIME) {

            if ($request->isPost()) {
                return with(new \Zank\Common\Message($response, false, '登陆过期', -2))
                    ->withJson();

            }
            return $response->withRedirect((string) $loginurl, 302);

        
        }
        // 查询注入的用户是否存在
        $user = $token->user;
        if (!$user) {//关联查询
            if ($request->isPost()) {
                return with(new \Zank\Common\Message($response, false, '认证用户不存在！', -3))
                        ->withJson();
            }
            return $response->withRedirect((string) $loginurl, 302);

        }
        //查用户等级
        $userTitle = \Zank\Util\NovelFunction::getNoveTitle($user->score);
        $user['touxian'] = '基础会员'; 
        $user['bookcount'] = MAXBOOKCASE;
        if (is_array($userTitle)) {   
            //查这等级对应收藏量       
            $bookCount = \Zank\Util\NovelFunction::getUserBookCaseCount($userTitle['honorid']);
            $user['touxian'] = $userTitle['caption'];
            $user['bookcount'] = $bookCount;
        }
        if (CHANGECOOK) {
            $token->token = \Zank\Model\SignToken::createToken();
            $token->refresh_token = \Zank\Model\SignToken::createRefreshToken();
        }
            
        $token->expires = UEXTIME;

        if ($token->save() && CHANGECOOK) {
            //一定要注意 如果程序出现BUG 没有输出 服务器修改了 而客户端没有修改导致用户重新登陆
            $this->ci->cookies->set('zank-token',$token->token);
            $tokenCook = $this->ci->cookies->toHeaders();
            $response = $response->withAddedHeader('Set-Cookie', $tokenCook);
        }
        $this->ci->offsetSet('user', $user);

        return $next($request, $response);
    }
} // END class AuthenticationUserToken
