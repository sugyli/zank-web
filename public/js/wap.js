﻿document.writeln("<script src='/js/layer.js'><\/script>");
$(function(){
    $widthwindow = $(window).width();
    if($widthwindow < 350){ $("#zjgx .zjgxjj").fadeOut(500);}
    if($("#xinxi").width()-$("#xinxi .xx ul li").width() <110){$("#xinxi .xx ul li").width($("#xinxi").width()-110)}
    $("#zjlb .spage").click(function(){$("#zjlb .showpage").show(300);  $("#spagebg").css("opacity","0.7").fadeIn(500).height($("body").height());  });
    $("#spagebg").click(function(){$(this).fadeOut(700);$("#zjlb .showpage").hide(300);});
    $tjImgHight = $("#tj img").width();
    $("#tj img").height($tjImgHight*1.25);
});
$(function(){
    $("#showsearch").click(function(){$("#search").slideToggle();  });
    $("#type").click(function(){if($("#type").text() == "书名"){$(this).html("作者");$("#searchType").val("author");}else{  $(this).html("书名");$("#searchType").val("articlename");}    });
    $("#formsearch").submit(function(){if($("#s_key").val() == "输入搜索词" || !$("#s_key").val()){mg.tishi("请输入搜索词！");return false;}});
})
$(function(){
    var b = "22px";var m = "16px"; var s = "12px";
    if($.cookie("fontSise")){font($.cookie("type"),$.cookie("fontSise"));}
    if($.cookie("light")){light($.cookie("light"));}    
    $("#b").click(function(){font("#b",b);});
    $("#m").click(function(){font("#m",m);});
    $("#s").click(function(){font("#s",s);});
    $("#light").click(function(){if($("#light").html() == "关灯"){light("off")}else{light("on");}});
});
function font(type,fontSize){
    $(type).addClass("fc").siblings().removeClass("fc");
    $.cookie("type",type,{path:'/',expires:365});
    $.cookie("fontSise",fontSize,{path:'/',expires:365});
    $("#nr").css("font-size",fontSize);
}
function light(type){
    if(type == "off"){$("#zj #light").html("开灯");$("#zj").addClass("gd");$("#zj .dise").removeClass("dise").removeClass("header").addClass("gddise").addClass("tmpc");$.cookie("light","off",{path:'/',expires:365});}
    else{$("#zj #light").html("关灯");$("#zj").removeClass("gd");$("#zj .tmpc").removeClass("gddise").addClass("dise");$("header").addClass("header");$.cookie("light","on",{path:'/',expires:365});}
}
function zuoyoufy(url){
    window.location.href = url;
}

//分割线
function html_encode(str)   
{   
  var s = "";   
  if (str.length == 0) return "";   
  s = str.replace(/&/g, "&gt;");   
  s = s.replace(/</g, "&lt;");   
  s = s.replace(/>/g, "&gt;");   
  s = s.replace(/ /g, "&nbsp;");   
  s = s.replace(/\'/g, "&#39;");   
  s = s.replace(/\"/g, "&quot;");   
  s = s.replace(/\n/g, "<br>");   
  return s;   
}   
 
function html_decode(str)   
{   
  var s = "";   
  if (str.length == 0) return "";   
  s = str.replace(/&gt;/g, "&");   
  s = s.replace(/&lt;/g, "<");   
  s = s.replace(/&gt;/g, ">");   
  s = s.replace(/&nbsp;/g, " ");   
  s = s.replace(/&#39;/g, "\'");   
  s = s.replace(/&quot;/g, "\"");   
  s = s.replace(/<br>/g, "\n");   
  return s;   
}

var createFragment = function(data) {
    var html = "";
    if (data && data.length > 0) {
        for (var i = 0; i < data.length; i++) {
            if (data[i]['author'].length > 12) { data[i]['author'] = data[i]['author'].substring(0,12)};
            data[i]['author'] = html_encode(data[i]['author']);
            if (data[i]['articlename'].length > 8) { data[i]['articlename'] = data[i]['articlename'].substring(0,8)};
            data[i]['articlename'] = html_encode(data[i]['articlename']);
            data[i]['intro'] = html_encode(data[i]['intro']);

            html += '<ul class="xbk" onClick="window.location.href=' +  "'/info-"  + data[i]['articleid'] + "/'"  + '">';
            html +=    '<li class="tjimg"><img onerror="this.src=' + "'/css/noimg.jpg'"  + '" src="' + data[i]['cover'] + '" /></li>';
            html +=        '<li class="tjxs">';
            html +=            '<span class="xsm"><a href="/info-'  + data[i]['articleid']  + '/">' + data[i]['articlename'] + '</a></span>';
            html +=             '<span class="">作者：' + data[i]['author'] +'</span>';
            html +=             '<span class="">' + data[i]['intro'] +'</span>';
            html +=             '<span class="tjrs"><i>'+ data[i]['time'] +'</i></span>';
            html +=        '</li>';
            html +=  '</ul>';
                      
        } 

        
    }

    return html;  
}

var mg = {

    tishi:function(msg){
        return layer.open({
                content: msg,
                btn: '确定'
              });

    },
    loading : function(){
       
        return layer.open({
                type: 2,
                content: '玩命的请求中',
                shadeClose: false
              });
    },
    close : function(){
        layer.closeAll();
    },
    htemail:function(){
          layer.open({
            title: [
              '收件人：管理员',
              'background-color: #FF4351; color:#fff;'
            ],
            btn: ['确认', '取消'],
            shadeClose: false,
            content: "<textarea id='reportCon' class='userselect' placeholder= '我有问题要咨询'></textarea>",
            yes: function(index){
                var text = jQuery.trim($('#reportCon').val());
                if (text.length<=0) {
                    mg.ts("内容不能为空,发送失败了");
                }else{
                    var mesurl = config.api.base + config.api.sendmail;
                    var body = {
                            content: text,
                            title : "来自用户中心的问题"
                          } 

                    $.post(mesurl, body, function(data) {
                            layer.close(index);
                            if (data && data.status) {                 
                                mg.ts(jQuery.trim(unescape(data.message)));
                                return;
                            }

                            if (data && data.status == false && data.data < 0) {
                                //location.reload();
                                zuoyoufy(config.api.base + config.api.user); 
                                return;
                            };
                            if (data && data.status == false) {
                                mg.ts(jQuery.trim(unescape(data.message)));
                                return;
                            };

                            mg.ts('服务器繁忙,请稍后再试'); 
                            return;
           
                  }).error(function(xhr,errorText,errorType){
                        layer.close(index);
                        mg.ts('网络太差了,稍后再试吧！');
                        
                    });

                }

              
            }
        });
    },
    qtemail:function(bn,cn ,url){

        var selecthtml = '<select class="userselect" id="userselect"><option value= "" >请选择类型</option><option value="本内容不是本书">章节不是本书的</option><option value="作者防盗章节">作者防盗章节</option><option value="内容丢字少字严重">缺少相邻章节</option><option value="内容丢字少字严重">章节不全</option><option value="其他问题">其他问题</option></select>';
        layer.open({
            title: [
              '收件人：管理员',
              'background-color: #FF4351; color:#fff;'
            ],
            btn: ['确认', '取消'],
            shadeClose: false,
            content: selecthtml + "<textarea id='reportCon' class='userselect' placeholder= '请详细说明问题,方便我们处理,谢谢合作'></textarea>",
            yes: function(index){
                var text = jQuery.trim($('#reportCon').val());
                var ts = $("#userselect option:selected").val();
                if (text.length<=0 || ts.length <=0) {
                    mg.ts("内容不能为空或类型没有选,发送失败了");
                }else{
                    var mesurl = config.api.base + config.api.sendmail;
                    var title = bn +' ' + cn + ' ' + ts;
                    var body = {
                            content: text + ' ---->来路：' + window.location.href,
                            title : title
                          } 

                    $.post(mesurl, body, function(data) {
                            layer.close(index);
                            if (data && data.status) {                 
                                mg.ts(jQuery.trim(unescape(data.message)));
                                return;
                            }

                            if (data && data.status == false && data.data < 0) {
                                mg.ts('请先登陆账户！'); 
                               // zuoyoufy(config.api.base + config.api.user); 
                                return;
                            };
                            if (data && data.status == false) {
                                mg.ts(jQuery.trim(unescape(data.message)));
                                return;
                            };

                            mg.ts('服务器繁忙,请稍后再试'); 
                            return;
           
                  }).error(function(xhr,errorText,errorType){
                        layer.close(index);
                        mg.ts('网络太差了,稍后再试吧！');
                        
                    });

                }

              
            }
        });
    },
    ts:function(msg){

          layer.open({
            content: msg,
            skin: 'msg',
            time: 3 //2秒后自动关闭
          });
    }

}

var config={
    api:{
        base:'http://www.sugyli.com/',
        verify:'novel/api/phone/get/verify',//获取验证
        forgetpass:'novel/user/forgetpass',//找回密码
        sign :'novel/user/sign',//注册
        login : 'novel/user/in',//登陆
        user: 'novel/user/usercore',//用户中心
        sendmail:'novel/api/post/receivemail',
        delmail:'novel/api/post/delmail',
        delbookcase:'novel/api/post/delbookcase',
        addbookcase:'novel/api/post/addbookcase',
        clock:'novel/api/post/clock',
        exitweb:'novel/api/post/exitweb',
    },
    questtime:300,//网络请求延迟

}
function mail_del(id ,type){
   
    var delmail = config.api.base + config.api.delmail;

    var body = {
            id: id,
            type : type
          };     
    $.post(delmail, body, function(data) {
        if (data && data.status) {                 
            $("#"+id).html("<tr><td class='del1'>已从邮箱删除！</td></tr>");

            mg.ts(jQuery.trim(unescape(data.message)));
            return;
        }

        if (data && data.status == false && data.data < 0) {
            zuoyoufy(config.api.base + config.api.user); 
            return;
        };
        if (data && data.status == false) {
            mg.ts(jQuery.trim(unescape(data.message)));
            return;
        };

        mg.ts('服务器繁忙,请稍后再试'); 
        return;   
    }).error(function(xhr,errorText,errorType){
        mg.ts('网络太差了,稍后再试吧！')
    });

}

function case_del(id){
    var delbookcase = config.api.base + config.api.delbookcase;

    var body = {
            id: id,
          };     
    $.post(delbookcase, body, function(data) {
        if (data && data.status) {                 
            $("#"+id).html("<tr><td class='del1'>已从书架删除！</td></tr>");

            mg.ts(jQuery.trim(unescape(data.message)));
            return;
        }

        if (data && data.status == false && data.data < 0) {
            zuoyoufy(config.api.base + config.api.user); 
            return;
        };
        if (data && data.status == false) {
            mg.ts(jQuery.trim(unescape(data.message)));
            return;
        };

        mg.ts('服务器繁忙,请稍后再试'); 
        return;   
    }).error(function(xhr,errorText,errorType){
        mg.ts('网络太差了,稍后再试吧！')
    });
}

$(function() {
    $.fn.fadeInWithDelay = function() {
        var delay = 0;
        return this.each(function() {
            $(this).delay(delay).animate({
                opacity: 1
            }, 300);
            delay += 100
        })
    }
    
});
function loadingbook(){
    if (islast) {
        $('#loading').fadeOut();
        $('#nomoreresults').fadeIn();
        return;
    };
    $('#loading').fadeOut();
    $('#content_list').children().attr('rel', 'loaded');
    $.ajax({
        type: 'post',
        //url: opts.contentPage + curpage,
        url: geturl,
        //data: opts.contentData,
        data: {
            'dwtime': dwtime,
            'sortajax': sortajax
         },
        success: function(data) {
            if(data.status){
                dwtime = data.data.ajaxPage ;
                var html = createFragment(data['data']['items']);                      
                $('#content_list').append(html);
                if (data.data.islast) { islast = true; };
                var objectsRendered = $('#content_list').children('[rel!=loaded]');
                $(objectsRendered).fadeInWithDelay();
                $('#loading').fadeIn();              
            }else{
                //isLoadingTail = true;
                $('#loading').fadeOut();
                $('#nomoreresults').fadeIn();

            }            
        },
        error: function(err){
            console.log('ajax失败 ' + err);
            $('#loading').fadeIn();   
            return;
        },
        dataType: 'json'
    })

}

function clock(){

    var clock = config.api.base + config.api.clock;
    var body = {};     
    $.post(clock, body, function(data) {
        if (data && data.status) {                 
            mg.tishi(jQuery.trim(unescape(data.message)));
            return;
        }

        if (data && data.status == false && data.data < 0) {
           
            zuoyoufy(config.api.base + config.api.user); 
            return;
        };
        if (data && data.status == false) {
            mg.tishi(jQuery.trim(unescape(data.message)));
            return;
        };

        mg.tishi('服务器繁忙,请稍后再试'); 
        return;   
    }).error(function(xhr,errorText,errorType){
        mg.tishi('网络太差了,稍后再试吧！')
    });

}
function addbookcase(bid,cid){
    var addbookcase = config.api.base + config.api.addbookcase;
    var body = {
            bid: bid,
            cid:cid
          };     
    $.post(addbookcase, body, function(data) {
        if (data && data.status) {                 
            mg.ts(jQuery.trim(unescape(data.message)));
            return;
        }

        if (data && data.status == false && data.data < 0) {
            mg.ts('请先登陆账户！'); 
            return;
        };
        if (data && data.status == false) {
            mg.ts(jQuery.trim(unescape(data.message)));
            return;
        };

        mg.ts('服务器繁忙,请稍后再试'); 
        return;   
    }).error(function(xhr,errorText,errorType){
        mg.ts('网络太差了,稍后再试吧！')
    });
}

function tiaozhuan(a) {

    if (!a) {
        var params= document.location.toString();
        var b = new Array();
        b = document.URL.split("?url=");
        a = b[1]
    }
    if (a) {
        a = a.replace(/\%2F/g, "/");
        a = a.replace(/\%3A/g, ":");
        a = a.replace(/\%23/g, "");
        a = a.replace(/\%3F/g, "?");
        a = a.replace(/\%3D/g, "=");
        a = a.replace(/\%26/g, "&");
        window.location.href = a
    } else {

        window.location.href = config.api.base + config.api.user
    }
}


function exitweb() {
    layer.open({
        content: '您确定要退出吗？'
        ,btn: ['退出', '不要']
        ,yes: function(index){
            layer.close(index);
            var exitweb = config.api.base + config.api.exitweb;
            var body = {};     
            $.post(exitweb, body, function(data) {
                if (data && data.status) {                 
                    zuoyoufy(config.api.base + config.api.user); 
                    return;
                }

                if (data && data.status == false && data.data < 0) {                  
                    zuoyoufy(config.api.base + config.api.user); 
                    return;
                };
                if (data && data.status == false) {
                    mg.ts(jQuery.trim(unescape(data.message)));
                    return;
                };

                mg.ts('服务器繁忙,请稍后再试'); 
                return;   
            }).error(function(xhr,errorText,errorType){
                mg.ts('网络太差了,稍后再试吧！')
            });             
        }
    });
    

}


//顶
$(function(){   
    $(window).scroll(function() {       
        if($(window).scrollTop() >= 50){            
            $('#back-to-top').fadeIn(300);

        }else{    
            $('#back-to-top').fadeOut(300);    
        }  
    });

    $('.stop').click(function(){
        $('html,body').animate({scrollTop: '0px'}, 300);
    }); 
});

//广告开始
function _17mb_top(){//顶部广告
    document.writeln("<div style=\'margin-top:10px;text-align:center\'>");
    document.writeln("<script async src=\"//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>");
    document.writeln("<!-- dashubao移动1 -->");
    document.writeln("<ins class=\"adsbygoogle\"");
    document.writeln("     style=\"display:block\"");
    document.writeln("     data-ad-client=\"ca-pub-1047803567334338\"");
    document.writeln("     data-ad-slot=\"2258046049\"");
    document.writeln("     data-ad-format=\"auto\"></ins>");
    document.writeln("<script>");
    document.writeln("(adsbygoogle = window.adsbygoogle || []).push({});");
    document.writeln("</script>");
    document.writeln("</div>");
}

function _17mb_middle(){//中间广告
    document.writeln("<div style=\'margin-top:10px;text-align:center\'>");
    document.writeln("<script async src=\"//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>");
    document.writeln("<!-- 大书包移动2 -->");
    document.writeln("<ins class=\"adsbygoogle\"");
    document.writeln("     style=\"display:block\"");
    document.writeln("     data-ad-client=\"ca-pub-1047803567334338\"");
    document.writeln("     data-ad-slot=\"5211512441\"");
    document.writeln("     data-ad-format=\"auto\"></ins>");
    document.writeln("<script>");
    document.writeln("(adsbygoogle = window.adsbygoogle || []).push({});");
    document.writeln("</script>");
    document.writeln("</div>");
}

function _17mb_bottom(){//底端广告
    document.writeln("<div style=\'margin-top:10px;text-align:center\'>");
    document.writeln("<script async src=\"//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>");
    document.writeln("<!-- 大书包移动3 -->");
    document.writeln("<ins class=\"adsbygoogle\"");
    document.writeln("     style=\"display:block\"");
    document.writeln("     data-ad-client=\"ca-pub-1047803567334338\"");
    document.writeln("     data-ad-slot=\"1839243643\"");
    document.writeln("     data-ad-format=\"auto\"></ins>");
    document.writeln("<script>");
    document.writeln("(adsbygoogle = window.adsbygoogle || []).push({});");
    document.writeln("</script>");
    document.writeln("</div>");
    
}
function _17mb_tj(){//统计代码
    var _hmt = _hmt || [];
    (function() {
      var hm = document.createElement("script");
      hm.src = "https://hm.baidu.com/hm.js?5fa0598362a3c28fb1c1a612d70a0f9c";
      var s = document.getElementsByTagName("script")[0]; 
      s.parentNode.insertBefore(hm, s);
    })();
}
