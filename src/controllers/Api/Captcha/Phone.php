<?php

namespace Zank\Controller\Api\Captcha;

use Carbon\Carbon;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zank\Controller;

/**
 * 手机验证码控制器
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class Phone extends Controller
{
    /**
     * 获取手机验证码接口
     *
     * @param Request $request 请求对象
     * @param Response $response 响应对象
     * @return Response 请求对象
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function get(Request $request, Response $response): Response
    {
        $phone = $request->getParsedBodyParam('phone');
        $test  = $request->getParsedBodyParam('test');

        if (!$phone) {
            $response = new \Zank\Common\Message($response, false, '手机号码不正确');

            return $response->withJson();
        } 

        $captcha = \Zank\Model\CaptchaPhone::byPhone($phone)->first();

        if ($captcha) {

            $s = $this->ci->get('settings')->get('send:phone:captcha:space');
            $es = $captcha->created_at->diffInSeconds(Carbon::now());

            if ($es <= $s) {
                $response = new \Zank\Common\Message($response, false, sprintf('%s秒内只能发送一次短信（剩余：%ss）', $s, ($s - $es)));

                return $response->withJson();
            }

            // 删除（软删除）其他验证码
            \Zank\Model\CaptchaPhone::byPhone($phone)->delete();
        }

        $captcha = new \Zank\Model\CaptchaPhone;
        $captcha->phone = $phone;
        $captcha->captcha_code = rand(1000, 9999);
        $captcha->expires = 3600;

        if ($captcha->save()) {
            $response = new \Zank\Common\Message($response, true, '获取验证码成功！', $test == 1 ? $captcha->captcha_code : null);

        // 如果保存失败！
        } else {
            $response = new \Zank\Common\Message($response, false, '验证码获取失败！');
        }

        return $response->withJson();
    }

    public function has(Request $request, Response $response): Response
    {
        $response = new \Zank\Common\Message($response, true, '验证码正确');

        return $response->withJson();
    }
} // END class Phone extends Controller
