<?php

    /*******************************常用*************************************/    
    define("NOVELURL", "http://www.dashubao.co");//设置一些基础的
    define("NOVELWAPURL", "http://m.dashubao.co");//设置一些基础的
    define("WEBNAME", "大书包");
    define("DS", DIRECTORY_SEPARATOR);
    define('ROOT',str_replace('\\',DS,realpath(dirname(__FILE__).'/../')).DS);
    define('FENMIAN', NOVELURL . "/xsfengmian/");    
    define('NOFENMIAN', "/css/noimg.jpg"); 
    define('TXTDIR', "http://txt.dashubao.co/"); 
    define('PAGENUM', 40);//目录默认多少分页
    define('SPRIT', 'sugyli.com');//兼容大书包处理/的问题
    define('NOVELMB', 'Novel/Wap/');//小说模板
    define('PAGESIZE', 30);//首页加载多少条
    define('NOVELMAX', 15000);//最大章节
    define('DFSORT', "未知分类");
    define('SENDTIME', 180);//发短信周期
    define('EXTIME', 3600);//短信验证过期时间
    define('UEXTIME', 2592000);//用户登陆过期时间 默认一个月
    define('MAXBOOKCASE', 200);//用户最大收藏
    define('DAKA', 5);//打卡积分
    define('TJCASE', 180);//推荐封面缓存
    define('MLCASE', 3600);
    define('NRCASE', 7200);
    define('SCASE', 300);//搜索缓存
    define('YS', true);//开启gzcompress
    define('WEBCONFIG', ['weburl'=>NOVELURL,'webtitle'=>WEBNAME."小说网手机版",'link'=>"<a href='" .NOVELWAPURL ."'>" . WEBNAME ."</a>"]);
    define('CHANGECOOK', false);//用户每次操作是否改变COOK