<?php

namespace Zank\Middleware\Captcha;

use Carbon\Carbon;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * 验证手机验证码中间件.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class ValidateByPhoneCaptchaByZank
{
    protected $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $phone = $request->getParsedBodyParam('mobile');
        $phoneCaptcha = $request->getParsedBodyParam('phone_captcha');
        $phoneCaptcha = intval($phoneCaptcha);

        // 验证手机号码是否为空
        if (!$phone) {
            $response = new \Zank\Common\Message($response, false, '手机号码不能为空');
            return $response->withJson();
        }

        if (!isPhoneNumber($phone)) {
            return with(new \Zank\Common\Message($response, false, '手机号码非法。'))
                ->withJson();   
        }

        if (!$phoneCaptcha) {
            $response = new \Zank\Common\Message($response, false, '验证码不能为空');

            return $response->withJson();
        }

        $captcha = \Zank\Model\CaptchaPhone::byPhone($phone)->first();

        //  如果没有查询到数据
        if (!$captcha) {
            $response = new \Zank\Common\Message($response, false, '请先获取验证码');
            return $response->withJson();
        // 判断验证码是否正确
        }


        if ($captcha->captcha_code != $phoneCaptcha) {
            $response = new \Zank\Common\Message($response, false, '验证码不正确');

            return $response->withJson();
        }

        // 判断验证码是否过期
        if ($captcha->created_at->diffInSeconds(Carbon::now()) >= $captcha->expires) {
            $response = new \Zank\Common\Message($response, false, '验证码已经失效，请重新获取！');

            return $response->withJson();
        }

        // 注入到后层，便于完成后删除验证码
        $this->ci->offsetSet('phone_captcha', $captcha);

        return $next($request, $response);
    }
} // END class ValidateByPhoneCaptcha
