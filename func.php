<?php
$is_Select = true;
error_reporting(0);
ini_set('display_errors', true);
ini_set('log_errors', 1);
ini_set('error_log', '!phperror-t.log');
require("Db.php");
require("mail/class.phpmailer.php");
require("Alipay/Alipay.class.php");
function getPayResult()
{
    session_start();
    $orderid = $_POST['payid'];
    $hash = $_SESSION["_$orderid"];
    if (isset($_SESSION[$hash]['pay_over'])) {
        echo 1;
        exit;
    }
    $where = "ali_orderid='$orderid' and paystate=1";
    $db = new db_class();
    $item = $db->db_select('xp_alipayorder', '*', $where);
    if ($item) {
        echo 1;
        unset($_SESSION[$hash]['pay_html']);
        $_SESSION[$hash]['pay_over'] = 1;
    } else echo 0;
    exit;
}

//将数据插入到数据库
function insert_data($table, $data)
{
    $keys = array();
    $vals = array();
    foreach ($data as $key => $val) {
        $keys[] = $key;
        $vals[] = "'" . $val . "'";
    }
    $key_s = implode(',', $keys);
    $val_s = implode(',', $vals);
    $db = new db_class();
    return $db->db_insert($table, $key_s, $val_s);
}
function pay_global($beforetitle="我要下载",$showfee="",$afterlink="",$title = "下载收费",$money = 5,$is_direct=false,$phone="13164355239")
{
    session_start();
    $ret = "";
    $promt = '';
    $hash = md5($title);
    if (isset($_SESSION[$hash]['pay_over'])) {
        $ret .=  $afterlink;
    } else if(!(isset($_GET['pay'])&&$_GET['pay']) && !isset($_SESSION[$hash]['pay_html'])) {
        $ret .=  '<a href="?pay='.$hash.'">'.$beforetitle.'</a>';
    }
    if (isset($_GET['pay'])&&$_GET['pay']) {
        $hash = $_GET['pay'];
        if (!isset($_SESSION[$hash]['pay_html']) && !isset($_SESSION[$hash]['pay_over'])) {
            $_SESSION[$hash]=array(
                'phone'=>$phone,
            );
            $alipay = new Alipay();
            //封装订单ID
            $ali_orderId = $alipay->getAlipayOrderId();
            $data = array('money' => $money, 'ali_orderid' => $ali_orderId, 'createtime' => time());
            insert_data('xp_alipayorder', $data);
            //设置请求数据$money
            $alipay->setRequestForm($ali_orderId,$title, $money);
            if(!$showfee)$showfee = "支付{$money}元即可显示连接";
            $html = $alipay->sendRequest($showfee);//"支付5元,即可显示APK下载链接"
            $html .= "<script>window.payid='{$ali_orderId}'</script>";
            $_SESSION[$hash]['order_id'] = $ali_orderId;
            $_SESSION[$hash]['pay_html'] = $html;
            $_SESSION["_$ali_orderId"] = $hash;
            if($is_direct)$promt = '$("#paybtn_'.$ali_orderId.'").click();';
        }
    }
    //显示到界面
    if (isset($_SESSION[$hash]['pay_html'])) {
        $payid = $_SESSION[$hash]['order_id'];
        $ret .=  $_SESSION[$hash]['pay_html'];
        $ret .=
        "<script>
            $(function () {
                $('#paybtn_{$payid}').click(function (event) {
                    if (!window['paybtn_c_{$payid}']) {
                        window['paybtn_c_{$payid}'] = true;
                        var timer = setInterval(function () {
                            $.post('/payr.php?payresult', {payid: '{$payid}'}, function (data) {
                                if (data == '1') {
                                    clearInterval(timer);
                                    window.location.reload();
                                }
                            })
                        }, 2000);
                    }
                });
            {$promt}
            });
          </script>";
    }
    return $ret;
}
function notify()
{
    $alipay = new Alipay();
    $res = $alipay->verifyNotify();
    if (!$res) {
        echo 'fail';
        exit();
    }

//商户订单号
    $out_trade_no = $_POST['out_trade_no'];
//支付宝交易号
    $trade_no = $_POST['trade_no'];
//交易状态
    $db = new db_class();
    $trade_status = $_POST['trade_status'];
    if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
        $order = $db->db_select('xp_alipayorder', '*', "ali_orderid='$out_trade_no'");
        if (!$order) {
            echo 'fail';
            exit();
        }
        $db = new db_class();
        $time = date('Y-m-d H:i:s');
        $r = $db->db_update('xp_alipayorder', "paystate=1,paytime='" . $time . "'", "ali_orderid='$out_trade_no'");
        if (!$r) {
            echo 'fail';
            exit();
        }
        $time = explode(' ', $time);
        $time = $time[1];
        SendMsg('13164355239', '下载通知', '支付', '订单号:' . $out_trade_no . ',时间:' . $time . ',金额:' . $order[0]['money'] . '元', false);
    } else {
        echo 'fail';
        exit();
    }
    echo 'success';
    exit();
}

function p($v)
{
    echo var_dump($v);
}

function g($s, $n)
{
    return ExtractStr($s, '<Weather>', '<' . $n . '>', '</' . $n . '>');
}

function gs($s, $n)
{
    return ExtractStr($s, $n . '":', '"', '"');
}

function SendAllPhone()
{
    $db = new db_class();
    $r = $db->db_select('xp_se_weather_user', 'phone', 'chk_send=1 and send_date<>' . "'" . date('Y-m-d') . "'");

    for ($i = 0; $i < count($r); $i++) {
        SendWeather($r[$i]['phone']);
    }
    return ("{\"summary\":\"success\"}");
}

function get_ip()
{
    if (isset ($_SERVER)) {
        if (isset ($_SERVER ['HTTP_X_FORWARDED_FOR'])) {
            $aIps = explode(',', $_SERVER ['HTTP_X_FORWARDED_FOR']);
            foreach ($aIps as $sIp) {
                $sIp = trim($sIp);
                if ($sIp != 'unknown') {
                    $sRealIp = $sIp;
                    break;
                }
            }
        } elseif (isset ($_SERVER ['HTTP_CLIENT_IP'])) {
            $sRealIp = $_SERVER ['HTTP_CLIENT_IP'];
        } else {
            if (isset ($_SERVER ['REMOTE_ADDR'])) {
                $sRealIp = $_SERVER ['REMOTE_ADDR'];
            } else {
                $sRealIp = '0.0.0.0';
            }
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $sRealIp = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $sRealIp = getenv('HTTP_CLIENT_IP');
        } else {
            $sRealIp = getenv('REMOTE_ADDR');
        }
    }
    return $sRealIp;
}

function GetWeather($phone)
{
    $db = new db_class();
    $gs = GetGS($phone);
    $wea_date = date('Y-m-d', strtotime("+1 day"));
    $r = $db->db_select('xp_se_weather_send', 'wea_detail', 'wea_date=' . "'" . $wea_date . "' and city='" . $gs . "'");
    $rgs = iconv('utf-8', 'gbk', $gs);
    if (count($r) != 0)
        return $r[0]['wea_detail'];
    $xml = file_get_contents("http://php.weather.sina.com.cn/xml.php?city=$rgs&password=DJOYnieT8234jlsK&day=1");

    if (strpos($xml, '<Weather>') !== false) {
        $weather = g($xml, 'savedate_weather') . g($xml, 'city') . ",白天" . g($xml, 'status1') . g($xml, 'temperature1') . "℃" . g($xml, 'direction1') . g($xml, 'power1') . "极;夜间" . g($xml, 'status2') . g($xml, 'temperature2') . "℃" . g($xml, 'direction1') . g($xml, 'power1') . "极;衣着:" . g($xml, 'chy_l') . "," . g($xml, 'gm_s') . g($xml, 'yd_s') . '污染:' . g($xml, 'pollution_s') . "。";
        $db->db_insert('xp_se_weather_send', 'wea_detail,wea_date,city', "'" . $weather . "','" . $wea_date . "','" . $gs . "'");
    } else
        $weather = "天气预报发布失败，归属地:" . $gs . "未找到!";
    return $weather;
}

function GetGS($phone)
{
    if (preg_match("/^13[0-9]{9}$|15[012356789][0-9]{8}$|18[0-9]{9}$|14[57][0-9]{8}$/", $phone)) {
        $db = new db_class();
        $r = $db->db_select('xp_se_weather_user', 'city', 'phone=' . "'" . $phone . "'");
        if (count($r) != 0 && $r[0]['city'])
            return $r[0]['city'];
        $sphone = substr($phone, 0, 7);
        $r = $db->db_select('xp_se_phone_gs', 'city', 'phone=' . "'" . $sphone . "'");
        if (count($r) != 0 && $r[0]['city'])
            return $r[0]['city'];
        $phone = $sphone . rand(1000, 9999);
        $url = "http://v.showji.com/Locating/showji.com20150416273007.aspx?m=$phone&output=json&callback=querycallback&timestamp=1439173023585";
        //$url = "http://v.showji.com/Locating/showji.com20150108.aspx?m=$phone&output=json&callback=querycallback&timestamp=1421062515437";
        //$url="http://api.showji.com/locating/showji.com.aspx?m=$phone&output=json&callback=querycallback&timestamp=1412001691339";
        //$xml = file_get_contents( "http://api.showji.com/Locating/www.showji.co.m.aspx?m=$phone&output=json&callback=querycallback");
//"http://api.showji.com/Locating/www.show.ji.c.o.m.aspx?m=$phone&output=json&callback=querycallback&timestamp=1405581137824"
        $xml = file_get_contents($url);
        $xml = ExtractStr($xml, '(', '{', '}');
        $city = gs($xml, 'City');
        if ($city != '')
            $db->db_insert('xp_se_phone_gs', 'phone,province,city', "'" . $sphone . "','" . gs($xml, 'Province') . "','" . $city . "'");
        return $city;
    }
    return '';
}

function SendMsg($phone, $fromName, $subject, $message, $inst = true)
{
    $idx = mt_rand(1, 20);
    if ('天气' != $subject) {
        if (isset($_POST['self'])) $message .= "[{$_POST['self']}]";
    }
    $mail = new PHPMailer(); //建立邮件发送类
    $address = "$phone@139.com";
    $mail->IsSMTP(); // 使用SMTP方式发送
    $mail->Host = "smtp.126.com"; // 您的企业邮局域名
    $mail->SMTPAuth = true; // 启用SMTP验证功能
    $mail->Username = "basefile$idx@126.com"; // 邮局用户名(请填写完整的email地址)
    $mail->Password = "wanfanee"; // 邮局密码
    $mail->CharSet = "utf-8";

    $mail->Encoding = "base64";
    $mail->From = "basefile$idx@126.com"; //邮件发送者email地址
    $mail->FromName = $fromName;
    $mail->AddAddress($address, "收件人");//收件人地址，可以替
    $mail->Subject = $subject; //邮件标题
    $mail->Body = $message;
//$mail->IsHTML(true);
    $ds = '';
    if ($inst) {
        $ra = insert_user($phone, '', 0);
        if ($ra['os'] < 6) {
            $ds .= '但该手机号还未';
            if ($ra['os'] < 5) {
                $ds .= '通过[';
                $ds .= ($ra['os'] < 1 ? '图像码' : '短信码') . ']验证';
            } else $ds .= '在该网站注册';
            $ds .= '，可能发送失败！';
        }
    }
    $r = $mail->Send();
    if ('天气' != $subject) {
        $db = new db_class();
        $db->db_insert('xp_se_sms', 'sms,self,recv,stime,state,ip', "'" . str_replace("'", "''", $message) . "','" . $_POST['self'] . "','" . $phone . "','" . date('Y-m-d H:i:s') . "'," . ($ra['os'] ? $ra['os'] : 0) . ",'" . get_ip() . "'");
    }
    $state = isset($ra['os']) ?',"state":' . $ra['os'] ."'":'';
    if ($r)
        return '{"summary":"success","desc":"' . $ds . '"' . $state . ' }';
    else
        return '{"summary":"' . $mail->ErrorInfo . '"}';
}

function SendWeather($phone)
{
    $db = new db_class();
    $msg = GetWeather($phone);
    $db->db_update('xp_se_weather_user', "send_date='" . date('Y-m-d') . "'", "phone='" . $phone . "'");
    $r = SendMsg($phone, '天气预报', '天气', $msg);
    return $r;
}

function ExtractStr($resource, $name, $stas, $ends, $ids = 1, $com = ",")
{
    $str = "";
    $index = 0;
    //首先定位到名称
    while ($ids != 0) {
        $ids--;
        if ($name == "") $bgn = $index; else
            $bgn = strpos($resource, $name, $index);

        //如果未找到直接返回
        if ($bgn !== false) {
            //再次定位到开始字符
            $sta = strpos($resource, $stas, $bgn + strlen($name));
            if ($sta !== false) {
                //建立栈结构,开始字符和结束字符分别进行压栈出栈
                $i = 1;
                $sta += strlen($stas) - 1;
                $index = $sta + 1;
                $tmps = "";
                while (0 != $i && $index < strlen($resource)) {
                    if ($index + strlen($ends) > strlen($resource)) break;
                    $tmps = substr($resource, $index, strlen($ends));
                    if ($tmps == $ends) {
                        $i--;
                        if (0 == $i) break;
                        $index++;
                        continue;
                    }
                    if ($index + strlen($stas) > strlen($resource)) break;
                    $tmps = substr($resource, $index, strlen($stas));
                    if ($tmps == $stas) {
                        $i++;
                    }
                    $index++;
                }
                if (0 == $i && $index <= strlen($resource)) {
                    $str .= substr($resource, $sta + 1, $index - $sta - 1);
                    if ($ids != 0) $str .= $com;
                }
            }
        }
    }
    return $str;
}

function post_xml($data, $url, $header = false, $show = true, $post = true, $get_c = false)
{
    $dir = 'cache';
    $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR;
    if (!file_exists($path)) mkdir($path);
//$cookie_file = dirname(__FILE__).'/cookie.txt';
    $ch = curl_init(); //初始化curl
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, $url);//设置链接
    if ($header)
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置HTTP头
    curl_setopt($ch, CURLOPT_HEADER, $show);//设置显示返回的http头
    if ($get_c)
        curl_setopt($ch, CURLOPT_COOKIEFILE, $path . $get_c);
    else
        curl_setopt($ch, CURLOPT_COOKIEJAR, $path . md5($url));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//POST数据
    }
    ob_start(); //开启浏览器缓存
    $response = curl_exec($ch);//接收返回信息
    if (curl_errno($ch)) {
//出错则显示错误信息			 
        print curl_error($ch);
    }
    curl_close($ch); //关闭curl链接
    ob_end_clean();

    return $response;
}

function insert_user($phone, $pass, $state)
{
    $db = new db_class();
    $r = $db->db_select('xp_se_weather_user', '*', 'phone=' . "'" . $phone . "'");
    $ra = array('e' => true, 'os' => 0);
    if (count($r) == 0) {
        $ra['e'] = false;
        $db->db_insert('xp_se_weather_user', 'phone,pass,reg_time,city,state', "'" . $phone . "','" . $pass . "','" . date('Y-m-d H:i:s') . "','" . GetGS($phone) . "',$state");
    } else {
        if (!$r[0]['city']) {
            $c = GetGS($phone);
            $c = "city='$c'";
            $db->db_update('xp_se_weather_user', $c, 'phone=' . "'" . $phone . "'");
        }

        $ra['os'] = $r[0]['state'];

        if ($state != 0) {
            $p = '';//不能少
            if ($state > 4) $p = "pass='" . $pass . "',";
            if ($state < $ra['os']) $state = $ra['os'];
            $db->db_update('xp_se_weather_user', $p . "state=$state", 'phone=' . "'" . $phone . "'");
            if ($state == 6 && $ra['os'] != 6) SendMsg('13164355239', '校购网', '注册', '手机:' . $phone . '注册成功!归属地:' . GetGS($phone) . ',连接:http://' . $_SERVER["HTTP_HOST"] . '/post.php?phone=' . $phone, false);
        }
    }
    return $ra;

}

function opensms($p, $u, $header)
{
    $url = 'https://mail.10086.cn/Login/Login.ashx';
    $res = post_xml("Password=$p&UserName=$u", $url, $header);
    $header[] = "Content-Type: application/xml; charset=UTF-8";
    $header[] = "Host: smsrebuild1.mail.10086.cn";
    file_put_contents('cook.txt', $res);
    $cookie = ExtractStr($res, 'Set-Cookie:', ' ', ';', 100, ';');
    if (strpos($cookie, 'Os_SSo_Sid') === false)
        $state = 4; else $state = 5;
    $header[] = "Cookie: $cookie;";
    $sid = ExtractStr($cookie, 'Os_SSo_Sid', '=', ';');
    $url = "http://smsrebuild1.mail.10086.cn/setting/s?func=user:updateMailNotify&sid=$sid&cguid=" . time() . rand(100, 999);


    $xml = '<object> <array name="mailnotify"> <object> <int name="notifyid">2</int> <boolean name="enable">true</boolean> <int name="notifytype">1</int> <int name="fromtype">0</int> <boolean name="supply">true</boolean> <array name="timerange"> <object> <string name="tid">tid_1_1_0</string> <int name="begin">7</int> <int name="end">24</int> <string name="weekday">1,2,3,4,5,6,7</string> <string name="discription">每天，7:00 ~ 24:00</string> </object> <object> <string name="tid">tid_0_0_1</string> <int name="begin">0</int> <int name="end">2</int> <string name="weekday">1,2,3,4,5,6,7</string> <string name="discription">每天，0:00 ~ 2:00</string> </object> </array> <array name="emaillist"> </array> </object> <object> <int name="notifyid">1</int> <boolean name="enable">true</boolean> <int name="notifytype">1</int> <int name="fromtype">1</int> <boolean name="supply">true</boolean> <array name="timerange"> <object> <string name="tid">tid_1_1_0</string> <int name="begin">7</int> <int name="end">24</int> <string name="weekday">1,2,3,4,5,6,7</string> <string name="discription">每天，7:00 ~ 24:00</string> </object> <object> <string name="tid">tid_1_1_1</string> <int name="begin">0</int> <int name="end">2</int> <string name="weekday">1,2,3,4,5,6,7</string> <string name="discription">每天，0:00 ~ 2:00</string> </object> </array> <array name="emaillist"> </array> </object> </array> </object>';

    $abc = post_xml($xml, $url, $header, 0);
    if (strpos($abc, 'S_OK') !== false) $state = 6;

    return array($abc, $state);
}
