<?php

namespace Zank\Controller\Api\Captcha;

use Carbon\Carbon;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zank\Controller;

/**
 * 手机验证码控制器.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
class ZankPhone extends Controller
{
    /**
     * 获取手机验证码接口.
     *
     * @param Request  $request  请求对象
     * @param Response $response 响应对象
     *
     * @return Response 请求对象
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function get(Request $request, Response $response): Response
    {
        $phone = $request->getParsedBodyParam('mobile');
        $type = $request->getParsedBodyParam('type');
        if (!$phone) {
            $response = new \Zank\Common\Message($response, false, '手机号码不正确');

            return $response->withJson();
        }

        $captcha = \Zank\Model\CaptchaPhone::byPhone($phone)->first();

        if ($captcha) {
            //$s = $this->ci->get('settings')->get('send:phone:captcha:space');
            $s = SENDTIME;
            $es = $captcha->created_at->diffInSeconds(Carbon::now());

            if ($es <= $s) {
                $outdata['codeAlreadySend'] = true;
                $outdata['seconds'] = ($s - $es);
                $response = new \Zank\Common\Message($response, false, sprintf('%s秒内只能发送一次短信（剩余：%ss）', $s, $outdata['seconds']),$outdata);

                return $response->withJson();
            }

            // 删除（软删除）其他验证码
            \Zank\Model\CaptchaPhone::byPhone($phone)->delete();
        }

        //第3方接口
        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
        $captcha_code = rand(1000, 9999);

        if ($type == 3) {//找回密码
            $mss = "您的找回密码验证码是：【{$captcha_code}】。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";
        }else{

            $mss = "您的注册验证码是：【{$captcha_code}】。请不要把验证码泄露给其他人。如非本人操作，可不用理会！";

        }

        $post_data = "account=cf_sugyli&password=fbe97a3c996d10179cd3f8a18c83f683&mobile=".$phone."&content=".rawurlencode("{$mss}");
        $gets =  xml_to_array(Post($post_data, $target));
        if (isset($gets['SubmitResult']['code']) && $gets['SubmitResult']['code']==2) {
            $captcha = new \Zank\Model\CaptchaPhone();
            $captcha->phone = $phone;
            $captcha->captcha_code = $captcha_code;
            $captcha->expires = EXTIME;

            if ($captcha->save()) {
                //$response = new \Zank\Common\Message($response, true, '获取验证码成功！', $test == 1 ? $captcha->captcha_code : null);
                $response = new \Zank\Common\Message($response, true, '获取验证码成功,已经发送到手机上！',SENDTIME);
                return $response->withJson();
            }
        }

        $response = new \Zank\Common\Message($response, false, '验证码获取失败！请稍后再试');

        return $response->withJson();
    }

    /**
     * 验证手机验证码
     *
     * @param Request  $request  请求对象
     * @param Response $response 响应对象
     *
     * @return Response 请求对象
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    public function has(Request $request, Response $response): Response
    {
        $response = new \Zank\Common\Message($response, true, '验证码正确');

        return $response->withJson();
    }
} // END class Phone extends Controller
