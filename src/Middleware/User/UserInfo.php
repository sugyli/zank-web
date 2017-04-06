<?php

namespace Zank\Middleware\User;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zank\Traits\Container;

class UserInfo
{
    use Container;

    public function __invoke(Request $request, Response $response, callable $next)
    {
       
       if ($this->ci->has('user')){
            $user = $this->ci->get('user');
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

            $this->ci->offsetSet('user', $user);
            return $next($request, $response);
       }

        return with(new \Zank\Common\Message($response, false, '中间件未获取到用户信息'))
                ->withJson();

        
    }
} // END class AuthenticationUserToken
