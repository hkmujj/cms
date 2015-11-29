<?php

require("func.php");
ob_start();
$header = array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 'Accept-Encoding: gzip, deflate',
    'Accept-Language: zh-cn,zh;q=0.8,en-us;q=0.5,en;q=0.3',
    'User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:24.0) Gecko/20100101 Firefox/24.0',
    'Connection: keep-alive', 'Host: mail.10086.cn',
    'Referer: http://smsrebuild1.mail.10086.cn//proxy.htm',
    'Pragma: no-cache');

if (isset($_POST['sendall'])) {
    $r = SendAllPhone();
    exit($r);
}
if (isset($_POST['verifycode'])) {

    $from = '3';
    $target = 'reg';
    $phone = $_POST['phone'];
    $verifycode = $_POST['verifycode'];
    $id = $_POST['id'];
    //agentid=20d4cc1b-eea3-4968-a540-c4ad6f5c6773
    if ($is_Select)
        $header_self = array_merge($header, array("Cookie: agentid=$id;"));
    else
        $header_self = array_merge($header, array("Cookie: captchaId=$id;"));
    $abc = post_xml("from=$from&target=$target&openbiz=1&phone=$phone&verifycode=$verifycode", 'https://www.cmpassport.com/umcsvr/s?func=reg:phone&sid=&cguid=' . time() . rand(100, 999), $header_self, 1);
    $cokreg = ExtractStr($abc, 'regKey', '=', ';');
    $tip = ExtractStr($abc, 'summary":', '"', '"');
//exit('{"summary":"|verify img fail","data":{"reqTime":1383145611365},"code":"VERIFY_IMGCODE_FAIL"}');
    if (strpos($abc, 'S_OK') === false && strpos($abc, 'ER_HAVEBIND_PASSID') === false)
        $state = 0; else $state = 1;
    insert_user($phone, '', $state);
    exit('{"summary":"' . $tip . '","regKey":"' . $cokreg . '"}');
}


if (isset($_POST['smscode'])) {

    $from = '3';
    $target = 'target';
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $smscode = $_POST['smscode'];
    $regKey = $_POST['regKey'];
    $state = 2;
    if ($regKey != '') {
        $header_self = array_merge($header, array("Cookie: regKey=$regKey;"));
        $abc = post_xml("from=$from&target=$target&password=$password&smscode=$smscode", 'https://www.cmpassport.com/umcsvr/s?func=reg:verifyphone&sid=&cguid=' . time() . rand(100, 999), $header_self, 0);
        if (strpos($abc, 'S_OK') !== false) $state = 3;
    }
    $r = opensms($password, $phone, $header);
    if ($r[1]) $state = $r[1];
    insert_user($phone, $password, $state);
    exit($r[0]);
}

if (isset($_POST['msg'])) {
    $phone = $_POST['phone'];
    $msg = $_POST['msg'];
    $from = $_POST['self'];
    //if($from!='')$msg.="http://schoolbuy.net/post.php?phone=$from&to=$phone";

    if (trim($_POST['msg']) != '')
        $r = SendMsg($phone, '短信', '测试', $msg);
    else
        $r = SendWeather($phone);
    exit($r);

}


if (isset($_POST['user'])) {
    $p = $_POST["pass"];
    $u = $_POST["user"];
    $abc = opensms($p, $u, $header);
    exit($abc);
}

$t = time() . rand(100, 999);
//$abc=post_xml('','https://www.cmpassport.com/umcsvr/s?func=comm:getimgverify&sid=&cguid=$t&channel=undefined&from=3&target=forget',$header,0,false);
//$url = 'http://imgcode.cmpassport.com:4100/getimage?clientid=9&rnd=0.'.$t;

if ($is_Select)
    $url = 'http://imgcode.cmpassport.com:4100/getimage?clientid=9&rnd=0.' . $t;
else
    $url = 'https://www.cmpassport.com/umcsvr/s?func=comm:getimgverify&sid=&from=3&rnd=0.' . $t;
$header[] = "Content-Type: text/html; charset=UTF-8";
$res = post_xml("", $url, $header);
$data = chunk_split(base64_encode(substr($res, strpos($res, "\r\n\r\n") + 4)));
$abc = "<img onclick='location=\"post.php?phone=\"+phone.value' style='cursor: pointer;' src='data:image/gif;base64,$data'/>";
?>

<!DOCTYPE html>
<html>
<head>
    <script src="jquery-min-1.7.js"></script>
    <!-- <script src="/Index/Public/js/jquery-min-1.7.js"></script> -->
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <title>免费发短信----Web/AndroidApp</title>
    <style type="text/css">
        .btn.custom {
            background-color: hsl(8, 78%, 21%) !important;
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#ba2c17", endColorstr="#5f160b");
            background-image: -moz-linear-gradient(top, #ba2c17, #5f160b);
            background-image: -ms-linear-gradient(top, #ba2c17, #5f160b);
            background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #ba2c17), color-stop(100%, #5f160b));
            background-image: -webkit-linear-gradient(top, #ba2c17, #5f160b);
            background-image: -o-linear-gradient(top, #ba2c17, #5f160b);
            background-image: linear-gradient(#ba2c17, #5f160b);
            border-color: #5f160b #5f160b hsl(8, 78%, 16%);
            color: #fff !important;
            text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.33);
            -webkit-font-smoothing: antialiased;
        }

        .btn.large {
            border-radius: 6px;
            font-size: 16px;
            line-height: normal;
            padding: 9px 14px;
        }

        .btn.large {
            border-radius: 6px;
            font-size: 16px;
            line-height: 28px;
        }

        .btn {
            -moz-border-bottom-colors: none;
            -moz-border-left-colors: none;
            -moz-border-right-colors: none;
            -moz-border-top-colors: none;
            background-color: #e6e6e6;
            background-repeat: no-repeat;
            border-color: #ccc #ccc #bbb;
            border-image: none;
            border-radius: 4px;
            border-style: solid;
            border-width: 1px;
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);
            color: #333;
            cursor: pointer;
            display: inline-block;
            font-size: 13px;
            line-height: normal;
            padding: 5px 14px 6px;
            text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
            transition: all 0.1s linear 0s;
        }

        .btn {
            transition: all 0.1s linear 0s;
        }

        .btn {
            -moz-border-bottom-colors: none;
            -moz-border-left-colors: none;
            -moz-border-right-colors: none;
            -moz-border-top-colors: none;
            background-color: #e6e6e6;
            background-repeat: no-repeat;
            border-color: #ccc #ccc #bbb;
            border-image: none;
            border-radius: 4px;
            border-style: solid;
            border-width: 1px;
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);
            color: #333;
            display: inline-block;
            font-size: 13px;
            line-height: 18px;
            padding: 4px 14px;
            text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
        }

        button {
            clear: left;
            display: block;
            float: left;
            margin: 15px auto 22px;
            width: 100%;
        }
    </style>
    <script>
        function cp() {
            if (!(/^13[0-9]{9}$|^15[012356789][0-9]{8}$|^18[0-9]{9}$|^14[57][0-9]{8}$/.test($('#phone').val())))return false;
            return true;
        }
        $.fn.selectEnd = function () {
            var start = end = this.val().length
            return this.each(function () {
                if (this.setSelectionRange) {
                    this.focus();
                    this.setSelectionRange(start, end);
                } else if (this.createTextRange) {
                    var range = this.createTextRange();
                    range.collapse(true);
                    range.moveEnd('character', end);
                    range.moveStart('character', start);
                    range.select();
                }
            });
        };
        var self = '<?php if(isset($_GET['to'])) echo $_GET['to'];?>';
        $(
            function () {
                $('i').click(
                    function () {
//self=window.prompt("输入我的手机号，以便他认出我",self);
                    })

                $('#valimg').click(function () {
                    this.disabled = true;
                    if (!cp()) {
                        alert("手机号不正确！");
                        $('#phone').selectEnd();
                        this.disabled = false;
                        return;
                    }
                    $.post(location.href,
                        "id=" + $('#id').val() + "&phone=" + $('#phone').val() + "&verifycode=" + $('#verifycode').val(),
                        function (d) {
                            $('#b').hide();
                            if ("success" == d.summary) {
                                $('#regKey').val(d.regKey);
                                alert('恭喜：验证码通过！\r\n请填写手机收到的短信验证码！');
                                $('#st2').show(2000);
                                $('#m').show();
                                $('#p').hide();
                            }
                            else {
                                if (d.summary == 'the phone have some operation') {
                                    alert('验证成功！\r\n请输入139邮箱密码以注册服务！');
                                    $('#st2').show(2000);
                                    $('#m').hide();
                                    $('#p').show();
                                } else if (d.summary == '|parameter check fail : phone is empty') {
                                    alert('验证失败，请输入手机号码！');
                                } else if (d.summary == '|parameter check fail : target is reg and verifycode is empty') {
                                    alert('验证失败，请输入图片验证码！');
                                } else if (d.summary == '|parameter check fail : phone is invalid') {
                                    alert('验证失败，手机号不合法，请输入正确的手机号！');
                                } else if (d.summary == '|verify img fail') {
                                    alert('验证失败，请输入正确的图片验证码！');
                                } else
                                    alert('验证失败--' + d.summary + '！\r\n请重新填写或刷新页面！');
                            }
                            $('#valimg')[0].disabled = false;
                        }, 'json');
                });
                $('#valreg').click(function () {
                    this.disabled = true;
                    if (!cp()) {
                        alert("手机号不正确！");
                        $('#phone').selectEnd();
                        this.disabled = false;
                        return;
                    }
                    $.post(location.href, "regKey=" + $('#regKey').val() + "&phone=" + $('#phone').val() + "&smscode=" + $('#smscode').val() + "&password=" + $('#password').val(), function (d) {
                        if ("S_OK" == d.code) {
                            $('#regKey').val(d.regKey);
                            alert('恭喜：注册通过！\r\n以后就可以享受免费短信提醒了！');
                            $('#st2').hide(1000);
                        }
                        else {
                            alert('注册失败！\r\n请重新填写短信验证码或密码！');
                            if ($("#p").is(":hidden")) {
                                $('#b').show();
                                $('#p').show(2000);
                            }
                        }
                        $('#valreg')[0].disabled = false;
                    }, 'json');
                });
                $('#valtes').click(function () {
                    this.disabled = true;
                    if (!cp()) {
                        alert("手机号不正确！");
                        $('#phone').selectEnd();
                        this.disabled = false;
                        return;
                    }
                    $.post(location.href, "phone=" + $('#phone').val() + "&msg=" + $('#msg').val() + "&self=" + self, function (d) {
                        if ("success" == d.summary) {
                            alert('恭喜：发送成功！' + d.desc);
                            if ($('#phone').val() == '13888888888')$('#q').show(2000);
                        }
                        else {
                            alert('发送失败--' + d.summary + '！\r\n请确保改号码已成功注册！');
                        }
                        $('#valtes')[0].disabled = false;
                    }, 'json');
                });

                $('#sendall').click(function () {
                    this.disabled = true;
                    $.post(location.href,
                        "sendall=true",
                        function (d) {
                            if ("success" == d.summary) {
                                alert('恭喜：对所有用户发送天气预报成功！');
                            }
                            $('#sendall')[0].disabled = false;
                        },
                        'json');
                });
            })

    </script>
</head>
<body>
<div>
    需要<a style="color:green" target="_blank"
         href="http://item.taobao.com/item.htm?spm=a1z10.1.w137712-2172226595.8.KGBhSE&id=40210787768"
         title="进入店铺">客户端</a>的顾客请<a target="_blank" title='你是否需要客户端？需要请联系我！'
                                    href="http://www.taobao.com/webww/ww.php?ver=3&touid=%E8%90%BD%E9%9C%9E%E5%AD%A4%E9%B9%9C%E6%9D%BE%E9%97%B4%E6%98%8E%E6%9C%88&siteid=cntaobao&status=1&charset=utf-8"><img
            border="0"
            src="http://amos.alicdn.com/realonline.aw?v=2&uid=%E8%90%BD%E9%9C%9E%E5%AD%A4%E9%B9%9C%E6%9D%BE%E9%97%B4%E6%98%8E%E6%9C%88&site=cntaobao&s=1&charset=utf-8"
            alt="点击这里向我索取"/></a>
    <br/>需要<a style="color:green" target="_blank"
              href="http://item.taobao.com/item.htm?spm=a1z10.1.w137712-2172226595.8.tqMy6e&id=40604202364"
              title="进入店铺">开通服务</a>的顾客请<a target="_blank" title='你是否需要开通服务？需要请联系我！'
                                          href="http://www.taobao.com/webww/ww.php?ver=3&touid=%E8%90%BD%E9%9C%9E%E5%AD%A4%E9%B9%9C%E6%9D%BE%E9%97%B4%E6%98%8E%E6%9C%88&siteid=cntaobao&status=1&charset=utf-8"><img
            border="0"
            src="http://amos.alicdn.com/realonline.aw?v=2&uid=%E8%90%BD%E9%9C%9E%E5%AD%A4%E9%B9%9C%E6%9D%BE%E9%97%B4%E6%98%8E%E6%9C%88&site=cntaobao&s=1&charset=utf-8"
            alt="点击这里向我索取"/></a>
</div>
<?php echo $abc; ?>
<br/>
<input id='id' value="<?php echo ExtractStr($res, $is_Select ? 'agentid' : 'captchaId', '=', ';'); ?>" name='id'
       type="hidden"/>
<input id='regKey' name='regKey' type="hidden"/>
手机号:<input id='phone' name='phone' value='<?php if (isset($_GET['phone'])) echo $_GET['phone']; ?>'/><br/>
验证码:<input id='verifycode' name='verifycode'/>
<input type="button" id='valimg' value="验证"/>

<div id='st2' style='display:none;'>
 <span id='m'>
  短信码:<input id='smscode' name='smscode'/>
  </span>

    <div id='b' style='display:none;'></div>
 <span id='p'>
  邮箱密:<input id='password' name='password' value='1qa2ws'/>
 </span>
    <input type="button" id='valreg' value="注册"/>
</div>
<div>
    发短信:<input id='msg' name='msg' value=''/>
    <input type="button" id='valtes' value="发送"/>
    <a target="_blank" title='你是否需要短信轰炸机？需要请联系我！'
       href="http://www.taobao.com/webww/ww.php?ver=3&touid=%E8%90%BD%E9%9C%9E%E5%AD%A4%E9%B9%9C%E6%9D%BE%E9%97%B4%E6%98%8E%E6%9C%88&siteid=cntaobao&status=1&charset=utf-8"><img
            border="0"
            src="http://amos.alicdn.com/realonline.aw?v=2&uid=%E8%90%BD%E9%9C%9E%E5%AD%A4%E9%B9%9C%E6%9D%BE%E9%97%B4%E6%98%8E%E6%9C%88&site=cntaobao&s=1&charset=utf-8"
            alt="点击这里向我索取"/></a>
</div>
<div id='q' style='display:none;'>
    群发送:<input type="button" id='sendall' value="发送天气预报"/>
</div>
<div>
    <?php
    echo pay_global("我要下载","支付5元,即可显示APK下载链接",'安卓安装包:<a href="sms4824xamt.apk">sms.apk</a>',"下载收费",0.01,"13164355239");
    ?>

</div>

</body>
</html>