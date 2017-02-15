<?php

use Zank\Util\Yaml;

/**
 * t函数用于过滤标签，输出没有html的干净的文本
 * @param string text 文本内容
 * @return string 处理后内容
 */
if (!function_exists('t')) {
    function t($text)
    {
        $text = nl2br($text);
        $text = real_strip_tags($text);
        $text = addslashes($text);
        $text = trim($text);

        return $text;
    }
}

if (!function_exists('real_strip_tags')) {
    function real_strip_tags($str, $allowable_tags = '')
    {
        $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');

        return strip_tags($str, $allowable_tags);
    }
}
function mylog($data,$fname='syslog.txt'){
    $ts = fopen(ROOT."/logs/{$fname}","a+");
    fputs($ts,date("Y-m-d H:i:s", time())." : ".$data . PHP_EOL);
    fclose($ts);
}
if (!function_exists('random')) 
{
    function random($length = 6 , $numeric = 0) {
        PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
        if($numeric) {
            $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
        } else {
            $hash = '';
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
            $max = strlen($chars) - 1;
            for($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
        }
        return $hash;
    }
}
function Post($curlPost,$url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_TIMEOUT,10);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}
function xml_to_array($xml){
  $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
  $arr = "";
  if(preg_match_all($reg, $xml, $matches)){
    $count = count($matches[0]);
    for($i = 0; $i < $count; $i++){
    $subxml= $matches[2][$i];
    $key = $matches[1][$i];
      if(preg_match( $reg, $subxml )){
        $arr[$key] = xml_to_array( $subxml );
      }else{
        $arr[$key] = $subxml;
      }
    }
  }
  return $arr;
}

//验证手机号
if (!function_exists('isPhoneNumber')) 
{
    function isPhoneNumber($phone)
    {
        if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){ 

          return true;

        }else{

          return false;
        }
    }
}

function open_session()
{
    //session 设置
   // ini_set('session.cookie_httponly', 1);//那么通过程序(JS脚本、Applet等)将无法读取到Cookie信息
    //设置session路径到本地
    if (strtolower(ini_get('session.save_handler')) == 'files') {
        $session_dir = ROOT.'storage/session';
        if (!is_dir($session_dir)) {
            mkdir($session_dir, 0777, true);
        }
        session_save_path($session_dir);
    }
    session_start();   
}

function my_file_exists($file ,$length = 8)  
{  
    if(preg_match('/^http:\/\//',$file) || preg_match('/^https:\/\//',$file)){  
        //远程文件  
        if(ini_get('allow_url_fopen')){  
            if(@fopen($file,'r')) return true;  
        }  
        else{  
            $parseurl=parse_url($file);  
            $host=$parseurl['host'];  
            $path=$parseurl['path'];  
            $fp=fsockopen($host,80, $errno, $errstr, 10);  
            if(!$fp)return false;  
            fputs($fp,"GET {$path} HTTP/1.1 \r\nhost:{$host}\r\n\r\n");  
            if(preg_match('/HTTP\/1.1 200/',fgets($fp,$length))) return true;  
        }  
        return false;  
    }  
    return file_exists($file);  
}

function jieqi_socket_url($url){
    if(!function_exists('fsockopen')) return false;
    $method = "GET";
    $url_array = parse_url($url);
    $port = isset($url_array['port'])? $url_array['port'] : 80;
    $fp = fsockopen($url_array['host'], $port, $errno, $errstr, 30);
    if(!$fp) return false;
    $getPath = $url_array['path'];
    if(!empty($url_array['query'])) $getPath .= "?". $url_array['query'];
    $header = $method . " " . $getPath;
    $header .= " HTTP/1.1\r\n";
    $header .= "Host: ". $url_array['host'] . "\r\n"; //HTTP 1.1 Host域不能省略
    /*
    //以下头信息域可以省略
    $header .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13 \r\n";
    $header .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,q=0.5 \r\n";
    $header .= "Accept-Language: en-us,en;q=0.5 ";
    $header .= "Accept-Encoding: gzip,deflate\r\n";
    */
    $header .= "Connection:Close\r\n\r\n";
    fwrite($fp, $header);
    if(!feof($fp)) fgets($fp, 8);
    //while(!feof($fp)) echo fgets($fp, 128);
    fclose($fp);
    return true;
} 

/**
 * 读取一个文件内容
 * 
 * @param      string     $file_name 文件名
 * @access     public
 * @return     string      返回文件内容
 */
function jieqi_readfile($file_name){
    if (function_exists("file_get_contents")) {
        return file_get_contents($file_name);
    }else{
        $filenum = @fopen($file_name, "rb");
        @flock($filenum, LOCK_SH);
        $file_data = @fread($filenum, @filesize($file_name));
        @flock($filenum, LOCK_UN);
        @fclose($filenum);
        return $file_data;
    }
}

function deepslashes($data){

    return is_array($data) ? array_map('deepslashes', $data) : htmlentities($data , ENT_QUOTES , 'UTF-8'); ;

}

//时间换算
function tranTime($time){
    $rtime = date("Y-n-d h:i",$time);
    //$htime = date("H:i",$time);
    $time = time() - $time;
    if ($time < 60){
        $str = '刚刚';
    }elseif ($time < 60 * 60){
        $min = floor($time/60);
        $str = $min.'分钟前';
    }elseif ($time < 60 * 60 * 24){
        $h = floor($time/(60*60));
        $str = $h.'小时前';
    }elseif ($time < 60 * 60 * 24 * 3) {
        $d = floor($time/(60*60*24));
        if($d==1){
            $str = '昨天';
        }else{ 
            $str = '前天';
        }
    }else{
        $str = $rtime;
    }
    return $str;
}


/**
 * 求取字符串位数（非字节），以UTF-8编码长度计算
 *
 * @param string $string 需要被计算位数的字符串
 * @return int
 * @author Seven Du <lovevipdsw@vip.qq.com>
 **/

function getstrlength($string)
{
    $length = strlen($string);
    $index  = $num = 0;
    while ($index < $length) {
        $str = $string[$index];
        if ($str < "\xC0") {
            $index += 1;
        } elseif ($str < "\xE0") {
            $index += 2;
        } elseif ($str < "\xF0") {
            $index += 3;
        } elseif ($str < "\xF8") {
            $index += 4;
        } elseif ($str < "\xFC") {
            $index += 5;
        } else {
            $index += 6;
        }
        $num += 1;
    }
    return $num;
}

if (!function_exists('cfg')) {
    /**
     * cfg YAML配置获取.
     *
     * @param string $key     键名
     * @param mixed  $default 默认值
     *
     * @return mixed
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    function cfg(string $key, $default = null)
    {
        $clientKey = '.zank.yaml';
        $filename = dirname(__DIR__).'/.zank.yaml';

        Yaml::addClient($clientKey, $filename);

        return Yaml::getClient($clientKey)->get($key, $default);
    }
}

if (!function_exists('get_oss_bucket_name')) {
    /**
     * 获取oss的bucket名称.
     *
     * @return string
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    function get_oss_bucket_name()
    {
        return \Zank\Application::getContainer()->get('settings')->get('oss')['bucket'];
    }
}

if (!function_exists('attach_url')) {
    /**
     * 更具附件文件地址，获取URL.
     *
     * @param string $path 文件地址（相对）
     *
     * @return string URL
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    function attach_url(string $path)
    {
        $settings = \Zank\Application::getContainer()->get('settings')->get('oss');

        if ($settings['sign'] === true) {
            return \Zank\Application::getContainer()
                ->get('oss')
                ->signUrl($settings['bucket'], $path, $settings['timeout']);
        }

        return sprintf('%s/%s', $settings['source_url'], $path);
    }
}

if (!function_exists('database_source_dir')) {
    /**
     * 获取数据相关资源目录.
     *
     * @return string
     *
     * @author Seven Du <lovevipdsw@outlook.com>
     * @homepage http://medz.cn
     */
    function database_source_dir()
    {
        return dirname(__DIR__).'/database';
    }
}
function daying($arr = []){

    echo "<pre>";
    print_r($arr);
    echo "</pre>";
    exit;
}