<?php

namespace Zank;

use Zank\Traits\Container;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Controller 基础类.
 *
 * @author Seven Du <lovevipdsw@outlook.com>
 **/
abstract class Controller
{
    use Container;


    //添加了日记系统
    public function comlog(Request $request ,$message){
      
        $getRequestTarget = $request->getRequestTarget();//来路
        $ipAddress = $request->getAttribute('ip_address')?:"未获取到IP";//Ip地址
        $host = $request->getUri()->getHost();
        $this->ci->commonlogger->debug($message,['HOST'=>$host,'请求'=>$getRequestTarget,'IP'=>$ipAddress]);
    }

    public function errlog(Request $request ,$message){
      
        $getRequestTarget = $request->getRequestTarget();//来路
        $ipAddress = $request->getAttribute('ip_address')?:"未获取到IP";//Ip地址
        $host = $request->getUri()->getHost();
        $this->ci->importantlogger->debug($message,['HOST'=>$host,'请求'=>$getRequestTarget,'IP'=>$ipAddress]);
    }


} // END class Controller


