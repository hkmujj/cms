<?php
class inforController extends commonController
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
        if(!$this->isPost()){
           $auth=$this->auth;
           $id=$auth['id'];
           $info=model('members')->find("id='{$id}'");
           $this->info=$info;
           $this->display();
        }else{
           $id=intval($_POST['id']);

           $data['nickname']=in(trim($_POST['nickname']));
           $acc=model('members')->find("id!='{$id}' AND nickname='".$data['nickname']."'");
           if(!empty($acc['nickname'])) $this->error('该昵称已经有人使用~');

           $data['email']=$_POST['email'];
           $data['tel']=in($_POST['tel']);
           $data['qq']=in($_POST['qq']);
           model('members')->update("id='{$id}'",$data);
           $this->success('信息编辑成功~');
        }
	    }
      
      public function password()
      {
         if(!$this->isPost()){
           $this->display();
        }else{
           if($_POST['password']!=$_POST['surepassword']) $this->error('确认密码与新密码不符~');
           $auth=$this->auth;
           $id=$auth['id'];
           $info=model('members')->find("id='{$id}'",'password');
           $oldpassword=$this->codepwd($_POST['oldpassword']);
           if($oldpassword!=$info['password']) $this->error('旧密码不正确~');
           
           $data['password']=$this->codepwd($_POST['password']);
           model('members')->update("id='{$id}'",$data);
           $this->success('密码修改成功~');
        }
      }
      public function rmb()
      {
        $auth=$this->auth;
        $id=$auth['id'];
        $info=model('members')->find("id='{$id}'","sms_scount,sms_lcount");
        $info['sms_scount']=intval($info['sms_scount']);
        $info['sms_lcount']=intval($info['sms_lcount']);
        $info['sms_tcount']=$info['sms_scount']+$info['sms_lcount'];
        $this->info=$info;

        $this->display();
      }
}