<?php
class smsController extends commonController
{
  public function __construct()
  {
     parent::__construct();
     if(!$this->auth['account']){
        if(empty($_SERVER['HTTP_REFERER'])) $_SERVER['HTTP_REFERER']=url('default/index/login');
        $this->error('您还没有登陆~',$_SERVER['HTTP_REFERER']);
     }
  }
      public function index()
      {
        $account = $this->auth['account'];

        $listRows=10;//每页显示的信息条数
        $url=url('order/index',array('page'=>'{page}'));
        $limit=$this->pageLimit($url,$listRows);
        $where="account='{$account}'";
        $count=model('orders')->count($where);

        
        $list=model('orders')->select($where,'','id DESC',$limit);
        $this->list=$list;
        $this->page=$this->pageShow($count);
        $this->display();
      }
      public function send()
      {
      $_POST['phone']=trim(in($_POST['phone']));
      if(!preg_match("/^13[0-9]{9}$|15[012356789][0-9]{8}$|18[0-9]{9}$|14[57][0-9]{8}$/",$_POST['phone'])){   
     $this->error('手机号填写错误~');
}else
{
        $_POST['content']=trim(in($_POST['content']));
        if(!$_POST['content'])$this->error('请输入短信内容！~');
        $auth=$this->auth;
        $id=$auth['id'];
        $info=model('members')->find("id='{$id}'","sms_lcount");
        $lcount=intval($info['sms_lcount']);
        $str = preg_replace('/[\x80-\xff]{1,3}/', ' ', $_POST['content'], -1);
        $num = strlen($str);
        $sms_count = ceil($num/60);
        if($sms_count>3)$this->error('短信发送失败，你的短信内容过长！~');
        if($sms_count>$lcount)$this->error('短信发送失败，你的剩余条数不足！~');
        $lcount = $lcount - $sms_count;

        $log['sms_phone']=$_POST['phone'];
        $log['sms_userid']=$id;
        $log['sms_username']=$auth['account'];
        $log['sms_content']=$_POST['content'];
        $log['sms_time']=date('Y-m-d H:i:s');
        $log['sms_ip']=get_client_ip();
        $log['sms_leftc']=$lcount;
        $log['sms_strlen']=$num;
        $log['sms_count']=$sms_count;
        $log['sms_state']=false;
        $log['sms_code']='';
        $log['sms_reason']='';
        $log['sms_tleftc']='';
        $sms_id=model('sms_log')->insert($log);
        if($sms_id)
        {
            $ret = model('members')->query("update {pre}members set sms_lcount=sms_lcount-$sms_count,sms_scount=sms_scount+$sms_count where id=$id");
           if($ret)
            { 
            
                $left_tc =getSmsCount();
                if($left_tc < $sms_count)
                {
                    $update=array();
                    $update['sms_tleftc']=$left_tc;
                    $update['sms_reason']='提供商短信库存不足，请与其联系!';
                    model('sms_log')->update("sms_id='{$sms_id}'",$update);
                    $this->error('短信发送失败，提供商短信库存不足，请与其联系！~');
                }
            
            
                $sret = sendSms($_POST['phone'],$_POST['content'],false);
                
                if($sret)
                {
                    $sret['sms_reason']='发送成功';
                    model('sms_log')->update("sms_id='{$sms_id}'",$sret);
                    $this->success("恭喜，短信发送成功，您的剩余条数为：{$lcount}！~",NULL,300);
                }else{
                $update=array();
                $update['sms_tleftc'] = getSmsCount();
                $update['sms_reason']='系统发送异常，请联系管理员维护!';
                model('sms_log')->update("sms_id='{$sms_id}'",$update);
                $this->error('系统发送异常，请联系管理员维护！~');
                }
            }
            else
            {   
                $update=array();
                $update['sms_tleftc']=getSmsCount();
                $update['sms_reason']='更新用户短信条数失败!';
                model('sms_log')->update("sms_id='{$sms_id}'",$update);
                $this->error('短信发送失败，更新用户短信条数失败！~');
            }
        }else $this->error('短信发送失败，插入日志失败！~');

        
}
      }
      
      public function logs()
      {
        $id=$this->auth['id'];

        $listRows=20;//每页显示的信息条数
        $url=url('sms/logs',array('page'=>'{page}'));
        $limit=$this->pageLimit($url,$listRows);
        $where="sms_userid='{$id}'";
        $count=model('sms_log')->count($where);

        
        $list=model('sms_log')->select($where,'','sms_id DESC',$limit);
        $this->list=$list;
        $this->page=$this->pageShow($count);
        
        $this->display();
      
      }
      public function getPrice()
      {
        $count = intval(trim(in($_REQUEST['sms_count'])));
        $price = 0;
        if(is_numeric($count))
        {
            if($count>0 && $count<=100)
            $price = $count * 0.1;
            elseif($count>100 && $count<=500)
            $price = $count * 0.09;
            elseif($count>500 && $count<=1000)
            $price = $count * 0.08;
            elseif($count>1000 && $count<=5000)
            $price = $count * 0.07;
            elseif($count>5000 && $count<=20000)
            $price = $count * 0.06;
            elseif($count>20000)
            $price = $count * 0.05;
            if($price<1)$price=1;
            $price = round($price);
        }
        if($this->isPost())return $price;
        echo $price;
        
      }
      public function add()
      {
        if($this->isPost())
        {
            
            $order['user_id'] = $this->auth['id'];
            $order['user_name'] =$this->auth['account'];
            $order['sms_count'] = intval(trim(in($_POST['sms_count'])));
            $order['sms_price'] = $this->getPrice();
            if($order['sms_price']==0)
            $this->error('短信条数请输入大于0的整数！~');
            $order['sms_time'] = date('Y-m-d H:i:s');
            $order['sms_state'] = 0;
            $order['sms_remark'] = trim(in($_POST['sms_remark']));
            $sms_id=model('sms_order')->insert($order);
            if($sms_id)
            {
                
                $this->success("恭喜，订单提交成功，请联系QQ:458677503充值！~",NULL,300);
            }else $this->error('订单提交失败！~');
        }
        $this->display();
      }
      public function addlog()
      {
        $id=$this->auth['id'];

        $listRows=20;//每页显示的信息条数
        $url=url('sms/addlog',array('page'=>'{page}'));
        $limit=$this->pageLimit($url,$listRows);
        $where="user_id='{$id}'";
        $count=model('sms_order')->count($where);

        
        $list=model('sms_order')->select($where,'','order_id DESC',$limit);
        $this->list=$list;
        $this->page=$this->pageShow($count);
        
        $this->display();
      
      }

     
}