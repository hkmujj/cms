<?php

class Alipay{
	//合作身份者id，以2088开头的16位纯数字
	private $partner='2088811002162296';
	//安全检验码，以数字和字母组成的32位字符
	private $key ='1lcosaqvwxbgs0ospwu0n8i7jhqcgp0x';
	//签名方式 不需修改
	private $sign_type ='MD5';
	//字符编码格式 目前支持 gbk 或 utf-8
	private $input_charset='utf-8';
	//ca证书路径地址，用于curl中ssl校验
	//请保证cacert.pem文件在当前文件夹目录中
	private $cacert='cacert.pem';
	//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
	private $transport =  'http';
	       //支付类型
              private $payment_type = "1";
             //服务器异步通知页面路径
              private  $notify_url = "";//"http://商户网关地址/create_direct_pay_by_user-PHP-UTF-8/notify_url.php";
              //需http://格式的完整路径，不能加?id=123这类自定义参数        //页面跳转同步通知页面路径
              private $return_url = "";//"http://商户网关地址/create_direct_pay_by_user-PHP-UTF-8/return_url.php";
              //email
              private  $seller_email = "458677503@qq.com";//$_POST['WIDseller_email'];
              //商户订单号
              private $out_trade_no = "";//$_POST['WIDout_trade_no'];
              //商户网站订单系统中唯一订单号，必填        //订单名称
              private $subject =""; //$_POST['WIDsubject'];
              //付款金额
              private $total_fee =""; //$_POST['WIDtotal_fee'];
               //订单描述        
              private $body = "";//$_POST['WIDbody'];
              //商品展示地址
              private $show_url = "";//$_POST['WIDshow_url'];
              //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html        //防钓鱼时间戳
             private $anti_phishing_key = "";
             //若要使用请调用类文件submit中的query_timestamp函数        //客户端的IP地址   //非局域网的外网IP地址，如：221.0.0.1
             private $exter_invoke_ip = "";

             //参数数组
             private $parameter=array();
             private $alipay_config = array();

            //支付宝网关地址（新）
             private $alipay_gateway_new = 'https://mapi.alipay.com/gateway.do?';

            // HTTPS形式消息验证地址
             private $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
            //HTTP形式消息验证地址
             private $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
             //---------------请求demo-------------
            // $alipay=new \Org\Alipay\Alipay();
            //封装订单ID
            //$alipayOrder_model=M('alipayorder');
            //$ali_orderId = $alipay->getAlipayOrderId();
            //$data=array('real_id'=>$rs['id'],'ali_orderid'=>$ali_orderId,'createtime'=>time());
            //$alipayOrder_model->add($data);
            //设置请求数据
            //$alipay->setRequestForm($ali_orderId,"财满店保证金支付","200.00");
            // $alipay->sendRequest();
             //-----------------end---------------------
             //构造函数，初始化必需的几个参数
             public function __construct(){
                 $this->setConfig();
             }
             //设置请求的参数
             public function setRequestForm($out_trade_no="",$subject="",$total_fee="",$show_url=""){
                    $this->out_trade_no = $out_trade_no;
                    $this->subject = $subject;
                    $this->total_fee = $total_fee;
                    $this->show_url = $show_url;
                    $this->setParameter();
                   
             }
             //构造要请求的参数数组，无需改动
             private function setParameter(){
             		$this->parameter = array(
        			"service" => "create_direct_pay_by_user",
        			"partner" => trim($this->partner),
        			"payment_type"=> $this->payment_type,
        			"notify_url"=> $this->notify_url,
        			"return_url"=> $this->return_url,
        			"seller_email"=> $this->seller_email,
        			"out_trade_no"=> $this->out_trade_no,
        			"subject"=> $this->subject,
        			"total_fee"=> $this->total_fee,
        			"body"=> $this->body,
        			"show_url"=> $this->show_url,
        			"anti_phishing_key"=> $this->anti_phishing_key,
        			"exter_invoke_ip"=>$this->exter_invoke_ip,
        			"_input_charset"=> trim(strtolower($this->input_charset))
		);
             		
             }
             //构造配置参数
             private function setConfig(){
                  $this->alipay_config=array(
                      'partner'=>$this->partner,
                      'key'    =>$this->key,
                      'sign_type'=>strtoupper($this->sign_type),
                      'input_charset'=>strtolower($this->input_charset),
                     'cacert' =>str_replace('\\', '/', dirname(__FILE__)).'/'.$this->cacert,
                     'transport'=>$this->transport,
                    );
                  $this->notify_url = "http://{$_SERVER['HTTP_HOST']}/notify";
                  $this->return_url = "http://{$_SERVER['HTTP_HOST']}/index.php?r=default/index/secuss";

             }
             //建立请求,发送表单数据
             public function sendRequest(){
                      $html_text = $this->buildRequestForm($this->parameter,"post", "确认");
                      return $html_text;
             }
             //建立请求表数据，将数据添加到表中
              //   CREATE TABLE `rent_requestform` (
              //   `id` int(11) unsigned NOT NULL auto_increment COMMENT '支付id',
              //   `_input_charset` varchar(10) NOT NULL COMMENT '支付字符集',
              //   `notify_url` text NOT NULL COMMENT '异步通知地址',
              //   `out_trade_no` varchar(50) NOT NULL COMMENT '交易号',
              //   `partner` varchar(50) NOT NULL COMMENT '合作者ID',
              //   `payment_type` tinyint(2) NOT NULL COMMENT '支付类型',
              //   `return_url` text NOT NULL COMMENT '同步返回地址',
              //   `seller_mail` varchar(255) NOT NULL COMMENT '合作者emal账号',
              //   `service` varchar(255) NOT NULL COMMENT '服务类型',
              //   `subject` varchar(255) NOT NULL COMMENT '支付主题',
              //   `total_fee` decimal(11,2) unsigned NOT NULL default '0.00' COMMENT '支付金额',
              //   `sign` varchar(32) NOT NULL COMMENT '支付签证',
              //   `sign_type` varchar(50) NOT NULL COMMENT '签证类型',
              //   PRIMARY KEY  (`id`)
              // ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
             private function add_requestForm($para){
                $requestform_model= model('requestform');
                $data = array(
                  '_input_charset'=>$para['_input_charset'],
                  'notify_url' =>$para['notify_url'],
                  'out_trade_no'=>$para['out_trade_no'],
                  'partner'=>$para['partner'],
                  'payment_type'=>$para['partner'],
                  'return_url'=>$para['return_url'],
                  'seller_mail'=>$para['seller_email'],
                  'service'=>$para['service'],
                  'subject'=>$para['subject'],
                  'total_fee'=>$para['total_fee'],
                  'sign'=>$para['sign'],
                  'sign_type'=>$para['sign_type'],
                  'createtime'=>time(),
                  );
                $res =  $requestform_model->insert($data);
                if($res){
                  return true;
                }else{
                  return false;
                }
             }
             //转换订单
              //   CREATE TABLE `rent_alipayorder` (
              //   `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '支付宝封装订单',
              //   `real_id` varchar(11) NOT NULL COMMENT '真实的ID',
              //   `ali_orderid` varchar(18) NOT NULL COMMENT '封装的ID',
              //   `createtime` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
              //   PRIMARY KEY (`id`)
              // ) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

               /**
                 * 建立请求，以表单HTML形式构造（默认）
                 * @param $para_temp 请求参数数组
                 * @param $method 提交方式。两个值可选：post、get
                 * @param $button_name 确认按钮显示文字
                 * @return 提交表单HTML文本
                 */
            private  function buildRequestForm($para_temp, $method, $button_name) {
                //待请求参数数组
                $para = $this->buildRequestPara($para_temp);
                
                $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->alipay_gateway_new."_input_charset=".trim(strtolower($this->alipay_config['input_charset']))."' method='".$method."' target='_blank'>";
                while (list ($key, $val) = each ($para)) {
                        $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
                    }

                //submit按钮控件请不要含有name属性
                    //$sHtml = $sHtml."<input type='submit' value='".$button_name."'></form>";
                $sHtml = $sHtml.'<input class="btn btn-danger" type="submit" value="直接弹出" style="float: right;"></form>';
               // $sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
                
                //将表单数据添加到数据库中，方便以后的查账
                if(!$this->add_requestForm($para)){
                  return false;
                }
                return $sHtml;
              }

            /**
               * 生成签名结果
               * @param $para_sort 已排序要签名的数组
               * return 签名结果字符串
               */
              private function buildRequestMysign($para_sort) {
                //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
                $prestr = $this->createLinkstring($para_sort);
                
                $mysign = "";
                switch (strtoupper(trim($this->alipay_config['sign_type']))) {
                  case "MD5" :
                    $mysign = $this->md5Sign($prestr, $this->alipay_config['key']);
                    break;
                  default :
                    $mysign = "";
                }
                
                return $mysign;
              }

              /**
                 * 生成要请求给支付宝的参数数组
                 * @param $para_temp 请求前的参数数组
                 * @return 要请求的参数数组
                 */
              private function buildRequestPara($para_temp) {
                //除去待签名参数数组中的空值和签名参数
                $para_filter = $this->paraFilter($para_temp);

                //对待签名参数数组排序
                $para_sort = $this->argSort($para_filter);

                //生成签名结果
                $mysign = $this->buildRequestMysign($para_sort);
                
                //签名结果与签名方式加入请求提交参数组中
                $para_sort['sign'] = $mysign;
                $para_sort['sign_type'] = strtoupper(trim($this->alipay_config['sign_type']));
                
                return $para_sort;
              }

              /**
                 * 生成要请求给支付宝的参数数组
                 * @param $para_temp 请求前的参数数组
                 * @return 要请求的参数数组字符串
                 */
             private  function buildRequestParaToString($para_temp) {
                //待请求参数数组
                $para = $this->buildRequestPara($para_temp);
                
                //把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
                $request_data = $this->createLinkstringUrlencode($para);
                
                return $request_data;
              }
              

              
              /**
                 * 建立请求，以模拟远程HTTP的POST请求方式构造并获取支付宝的处理结果
                 * @param $para_temp 请求参数数组
                 * @return 支付宝处理结果
                 */
             private function buildRequestHttp($para_temp) {
                $sResult = '';
                
                //待请求参数数组字符串
                $request_data = $this->buildRequestPara($para_temp);

                //远程获取数据
                $sResult = $this->getHttpResponsePOST($this->alipay_gateway_new, $this->alipay_config['cacert'],$request_data,trim(strtolower($this->alipay_config['input_charset'])));

                return $sResult;
              }
              
              /**
                 * 建立请求，以模拟远程HTTP的POST请求方式构造并获取支付宝的处理结果，带文件上传功能
                 * @param $para_temp 请求参数数组
                 * @param $file_para_name 文件类型的参数名
                 * @param $file_name 文件完整绝对路径
                 * @return 支付宝返回处理结果
                 */
             private function buildRequestHttpInFile($para_temp, $file_para_name, $file_name) {
                
                //待请求参数数组
                $para = $this->buildRequestPara($para_temp);
                $para[$file_para_name] = "@".$file_name;
                
                //远程获取数据
                $sResult = $this->getHttpResponsePOST($this->alipay_gateway_new, $this->alipay_config['cacert'],$para,trim(strtolower($this->alipay_config['input_charset'])));

                return $sResult;
              }
              
              /**
                 * 用于防钓鱼，调用接口query_timestamp来获取时间戳的处理函数
               * 注意：该功能PHP5环境及以上支持，因此必须服务器、本地电脑中装有支持DOMDocument、SSL的PHP配置环境。建议本地调试时使用PHP开发软件
                 * return 时间戳字符串
               */
             private function query_timestamp() {
                $url = $this->alipay_gateway_new."service=query_timestamp&partner=".trim(strtolower($this->alipay_config['partner']))."&_input_charset=".trim(strtolower($this->alipay_config['input_charset']));
                $encrypt_key = "";    

                $doc = new DOMDocument();
                $doc->load($url);
                $itemEncrypt_key = $doc->getElementsByTagName( "encrypt_key" );
                $encrypt_key = $itemEncrypt_key->item(0)->nodeValue;
                
                return $encrypt_key;
              }
/**
 * *****************处理支付宝各接口通知返回**************
 */
    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
  public function verifyNotify(){
      if(empty($_POST)) {//判断POST来的数组是否为空
            return false;
      }else {
          //生成签名结果
      $isSign = $this->getSignVeryfy($_POST, $_POST["sign"]);
      //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
      $responseTxt = 'true';
      if (! empty($_POST["notify_id"])) {
        $responseTxt = $this->getResponse($_POST["notify_id"]);
      }
      

      //写日志记录
      //if ($isSign) {
      //  $isSignStr = 'true';
      //}
      //else {
      //  $isSignStr = 'false';
      //}
      //$log_text = "responseTxt=".$responseTxt."\n notify_url_log:isSign=".$isSignStr.",";
      //$log_text = $log_text.createLinkString($_POST);
      //logResult($log_text);
      if(!$this->add_notifyform($_POST)){
        return false;
      }
      
      //验证
      //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
      //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
      if (preg_match("/true$/i",$responseTxt) && $isSign) {
        return true;
      } else {
        return false;
      }
    }
  }
  /**
   * 向数据库中插入返回的数据，以便查询对账
   */
    //    CREATE TABLE `rent_notifyform` (
    //   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    //   `discount` decimal(11,2) unsigned NOT NULL COMMENT '费用',
    //   `payment_type` tinyint(2) unsigned NOT NULL COMMENT '支付类型',
    //   `subject` varchar(255) NOT NULL COMMENT '付款描述',
    //   `trade_no` varchar(18) NOT NULL COMMENT '支付宝交易号',
    //   `buyer_email` varchar(255) NOT NULL COMMENT '付款人账号',
    //   `gmt_create` datetime NOT NULL COMMENT '创建交易时间',
    //   `notify_type` varchar(50) NOT NULL COMMENT '通知类型',
    //   `quantity` tinyint(1) unsigned NOT NULL,
    //   `out_trade_no` varchar(18) NOT NULL COMMENT '交易号',
    //   `seller_id` varchar(16) NOT NULL COMMENT '卖家ID',
    //   `notify_time` datetime NOT NULL COMMENT '通知时间',
    //   `trade_status` varchar(50) NOT NULL COMMENT '交易状态',
    //   `is_total_fee_adjust` varchar(10) NOT NULL COMMENT '是否调价',
    //   `total_fee` decimal(11,2) unsigned NOT NULL COMMENT '交易总费用',
    //   `gmt_payment` datetime NOT NULL COMMENT '支付时间',
    //   `seller_email` varchar(255) NOT NULL COMMENT '卖家email地址',
    //   `price` decimal(11,2) unsigned NOT NULL COMMENT '价格',
    //   `buyer_id` varchar(16) NOT NULL COMMENT '购买者ID',
    //   `notify_id` varchar(255) NOT NULL COMMENT '通知ID',
    //   `use_coupon` varchar(10) NOT NULL,
    //   `sign_type` varchar(50) NOT NULL COMMENT '签证类型',
    //   `sign` varchar(255) NOT NULL COMMENT '验签字符串',
    //   PRIMARY KEY (`id`)
    // ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  private function add_notifyform($post){
      $notifyform_model=model("notifyform");
      $data=array(
        'discount'=>$post['discount'],
        'payment_type'=>$post['payment_type'],
        'subject'=>$post['subject'],
        'trade_no'=>$post['trade_no'],
        'buyer_email'=>$post['buyer_email'],
        'gmt_create'=>$post['gmt_create'],
        'notify_type'=>$post['notify_type'],
        'quantity'=>$post['quantity'],
        'out_trade_no'=>$post['out_trade_no'],
        'seller_id'=>$post['seller_id'],
        'notify_time'=>$post['notify_time'],
        'trade_status'=>$post['trade_status'],
        'is_total_fee_adjust'=>$post['is_total_fee_adjust'],
        'total_fee'=>$post['total_fee'],
        'gmt_payment'=>$post['gmt_payment'],
        'seller_email'=>$post['seller_email'],
        'price'=>$post['price'],
        'buyer_id'=>$post['buyer_id'],
        'notify_id'=>$post['notify_id'],
        'use_coupon'=>$post['use_coupon'],
        'sign_type'=>$post['sign_type'],
        'sign'=>$post['sign'],
        );
      if($notifyform_model->insert($data)){
        return true;
      }else{
        return false;
      }
  }
    /**
     * 针对return_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
  public function verifyReturn(){
    if(empty($_GET)) {//判断GET来的数组是否为空
      return false;
    }else {
      //生成签名结果
      $isSign = $this->getSignVeryfy($_GET, $_GET["sign"]);

      //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
      $responseTxt = 'true';
      if (! empty($_GET["notify_id"])) {
        $responseTxt = $this->getResponse($_GET["notify_id"]);
      }
      //写日志记录
      //if ($isSign) {
      //  $isSignStr = 'true';
      //}
      //else {
      //  $isSignStr = 'false';
      //}
      //$log_text = "responseTxt=".$responseTxt."\n return_url_log:isSign=".$isSignStr.",";
      //$log_text = $log_text.$this->createLinkString($_GET);

      
      //logResult($log_text);
      
      //验证
      //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
      //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
      if (preg_match("/true$/i",$responseTxt) && $isSign) {
        return true;
      } else {
        return false;
      }
    }
  }
  
    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
  private function getSignVeryfy($para_temp, $sign) {
    //除去待签名参数数组中的空值和签名参数
    $para_filter = $this->paraFilter($para_temp);
    
    //对待签名参数数组排序
    $para_sort = $this->argSort($para_filter);
    
    //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
    $prestr = $this->createLinkstring($para_sort);
    
    $isSgin = false;
    switch (strtoupper(trim($this->alipay_config['sign_type']))) {
      case "MD5" :
        $isSgin = $this->md5Verify($prestr, $sign, $this->alipay_config['key']);
        break;
      default :
        $isSgin = false;
    }
    
    return $isSgin;
  }

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空 
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
 private  function getResponse($notify_id) {
    $transport = strtolower(trim($this->alipay_config['transport']));
    $partner = trim($this->alipay_config['partner']);
    $veryfy_url = '';
    if($transport == 'https') {
      $veryfy_url = $this->https_verify_url;
    }
    else {
      $veryfy_url = $this->http_verify_url;
    }
    $veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
    $responseTxt = $this->getHttpResponseGET($veryfy_url, $this->alipay_config['cacert']);
    return $responseTxt;
  }


/* *
 * 支付宝接口公用函数
 * 详细：该类是请求、通知返回两个文件所调用的公用函数核心处理文件
 * 版本：3.3
 * 日期：2012-07-19
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 */

        /**
         * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
         * @param $para 需要拼接的数组
         * return 拼接完成以后的字符串
         */
        private function createLinkstring($para) {
          $arg  = "";
          while (list ($key, $val) = each ($para)) {
            $arg.=$key."=".$val."&";
          }
          //去掉最后一个&字符
          $arg = substr($arg,0,count($arg)-2);
          
          //如果存在转义字符，那么去掉转义
          if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
          
          return $arg;
        }
        /**
         * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
         * @param $para 需要拼接的数组
         * return 拼接完成以后的字符串
         */
        private function createLinkstringUrlencode($para) {
          $arg  = "";
          while (list ($key, $val) = each ($para)) {
            $arg.=$key."=".urlencode($val)."&";
          }
          //去掉最后一个&字符
          $arg = substr($arg,0,count($arg)-2);
          
          //如果存在转义字符，那么去掉转义
          if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
          
          return $arg;
        }
        /**
         * 除去数组中的空值和签名参数
         * @param $para 签名参数组
         * return 去掉空值与签名参数后的新签名参数组
         */
        private function paraFilter($para) {
          $para_filter = array();
          while (list ($key, $val) = each ($para)) {
            if($key == "sign" || $key == "sign_type" || $val == "")continue;
            else  $para_filter[$key] = $para[$key];
          }
          return $para_filter;
        }
        /**
         * 对数组排序
         * @param $para 排序前的数组
         * return 排序后的数组
         */
        private  function argSort($para) {
          ksort($para);
          reset($para);
          return $para;
        }
        /**
         * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
         * 注意：服务器需要开通fopen配置
         * @param $word 要写入日志里的文本内容 默认值：空值
         */
        private function logResult($word='') {
          $fp = fopen("log.txt","a");
          flock($fp, LOCK_EX) ;
          fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
          flock($fp, LOCK_UN);
          fclose($fp);
        }

        /**
         * 远程获取数据，POST模式
         * 注意：
         * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
         * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
         * @param $url 指定URL完整路径地址
         * @param $cacert_url 指定当前工作目录绝对路径
         * @param $para 请求的数据
         * @param $input_charset 编码格式。默认值：空值
         * return 远程输出的数据
         */
        private function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = '') {

          if (trim($input_charset) != '') {
            $url = $url."_input_charset=".$input_charset;
          }
          $curl = curl_init($url);
          curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
          curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
          curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
          curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
          curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
          curl_setopt($curl,CURLOPT_POST,true); // post传输数据
          curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
          $responseText = curl_exec($curl);
          //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
          curl_close($curl);
          
          return $responseText;
        }

        /**
         * 远程获取数据，GET模式
         * 注意：
         * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
         * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
         * @param $url 指定URL完整路径地址
         * @param $cacert_url 指定当前工作目录绝对路径
         * return 远程输出的数据
         */
        private function getHttpResponseGET($url,$cacert_url) {
          $curl = curl_init($url);
          curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
          curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
          curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
          curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
          curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
          $responseText = curl_exec($curl);
          //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
          curl_close($curl);
          
          return $responseText;
        }

        /**
         * 实现多种字符编码方式
         * @param $input 需要编码的字符串
         * @param $_output_charset 输出的编码格式
         * @param $_input_charset 输入的编码格式
         * return 编码后的字符串
         */
        private function charsetEncode($input,$_output_charset ,$_input_charset) {
          $output = "";
          if(!isset($_output_charset) )$_output_charset  = $_input_charset;
          if($_input_charset == $_output_charset || $input ==null ) {
            $output = $input;
          } elseif (function_exists("mb_convert_encoding")) {
            $output = mb_convert_encoding($input,$_output_charset,$_input_charset);
          } elseif(function_exists("iconv")) {
            $output = iconv($_input_charset,$_output_charset,$input);
          } else die("sorry, you have no libs support for charset change.");
          return $output;
        }
        /**
         * 实现多种字符解码方式
         * @param $input 需要解码的字符串
         * @param $_output_charset 输出的解码格式
         * @param $_input_charset 输入的解码格式
         * return 解码后的字符串
         */
        private function charsetDecode($input,$_input_charset ,$_output_charset) {
          $output = "";
          if(!isset($_input_charset) )$_input_charset  = $_input_charset ;
          if($_input_charset == $_output_charset || $input ==null ) {
            $output = $input;
          } elseif (function_exists("mb_convert_encoding")) {
            $output = mb_convert_encoding($input,$_output_charset,$_input_charset);
          } elseif(function_exists("iconv")) {
            $output = iconv($_input_charset,$_output_charset,$input);
          } else die("sorry, you have no libs support for charset changes.");
          return $output;
        }

        /* *
         * MD5
         * 详细：MD5加密
         * 版本：3.3
         * 日期：2012-07-19
         * 说明：
         * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
         * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
         */

        /**
         * 签名字符串
         * @param $prestr 需要签名的字符串
         * @param $key 私钥
         * return 签名结果
         */
        private function md5Sign($prestr, $key) {
          $prestr = $prestr . $key;
          return md5($prestr);
        }

        /**
         * 验证签名
         * @param $prestr 需要签名的字符串
         * @param $sign 签名结果
         * @param $key 私钥
         * return 签名结果
         */
        private function md5Verify($prestr, $sign, $key) {
          $prestr = $prestr . $key;
          $mysgin = md5($prestr);

          if($mysgin == $sign) {
            return true;
          }
          else {
            return false;
          }
        }
        /**
         * 生成支付宝封装订单号
         */
        public function getAlipayOrderId(){
            return date('YmdHis',time()).mt_rand(1000,9999);
        }

}

?>