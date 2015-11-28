<?php if(!defined('APP_NAME')) exit;?>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/base.css" />
<link href="__PUBLIC__/artDialog/skins/blue.css" rel="stylesheet" type="text/css">
<script src="__PUBLIC__/artDialog/artDialog.js"></script>
<style>
.table td{
font-size:12px;
padding:4px;
font-family: "宋体","Verdana","Arial","Helvetica","sans-serif";
}
.index{color: #135b96;}
</style>
<div id="contain">
<ul class="breadcrumb">
     <li> <span>信息发送</span><span class="divider">/</span><span>短信充值</span></li>
  </ul>
        
  <form class="form-horizontal" method="post" id="form">
    <fieldset>
      <div id="legend" class="">
        <legend class="">短信充值</legend>
      </div>
    

    <div class="control-group">

          <!-- Text input-->
          <label class="control-label" for="input01">申请条数</label>
          <div class="controls">
            <input autocomplete="off"  onafterpaste="this.value=this.value.replace(/\D/g,'')"  placeholder="请输入你要购买的短信条数" class="input-xlarge" type="text" name="sms_count">
            <p class="help-block">输入数字之后会自动计算价格</p>
          </div>
        </div>

    

    <div class="control-group">

          <!-- Text input-->
          <label class="control-label" for="input01">留言备注</label>
          <div class="controls">
            <input placeholder="请输入备注" class="input-xlarge" type="text" name="sms_remark">
          </div>
        </div>

    

    <div class="control-group">
          <label class="control-label"></label>

          <!-- Button -->
          <div class="controls">
            <button class="btn btn-success">提交</button>
          </div>
        </div>

    </fieldset>
  </form>

</div>
<script>
    function postinfo(){
        var tips=art.dialog({
            title: '正在提交...'
            ,lock:true
        });
        $.post(location.href,$.param($('input[name=sms_count]')),function(data){
            tips.close();
            if(data.code){
                var dlg=art.dialog({
                    title: '提示，正在为您跳转...',
                    content: '支付完成前请不要关闭此窗口！<br><font size="2" color="red">请注意右上角，不要使弹出窗口被拦截！</font><div id="post_html"></div>',
                    icon: 'question',
                    lock:true
                });

                var timer=setInterval(function(){
                    $.post(location.href,'orderid='+data.orderid,function(data){
                        if(data==1){
                            clearInterval(timer);
                            if(typeof dlg != 'undefined')dlg.close();
                            art.dialog({
                                title: '信息提示',
                                content: '恭喜支付成功！',
                                icon: 'succeed',
                                ok: function(){
                                   // $('#form button').removeAttr('disabled');
                                    return true;
                                },
                                lock:true
                            });
                        }
                    })
                },2000);
                //clearInterval(timer);
                var sub=$('#alipaysubmit');
                data.html = data.html.replace(/__LESS__/g,'<').replace(/__MORE__/g,'>');
                if(sub.length){
                    sub.replaceWith(data.html);
                }else $('#post_html').append(data.html);
                $('#alipaysubmit').submit();

            }
            else{
                var dlg=art.dialog({
                    title: '错误提示',
                    content: data.msg,
                    icon: 'error',
                    ok: function(){
                        return true;
                    },
                    close:function(){
                    },
                    lock:true
                });
            }
        },'json')
    }
$(function(){
    $('#form').submit(function(){
        var val = $('input[name=sms_count]').val();
        if(isNaN(val) || val<=0)
        {
            art.dialog({
                title: '错误提示',
                content: "请输入正确的短信条数!",
                icon: 'error',
                ok: function(){
                    return true;
                },
                lock:true
            });
            return false;
        }
        $('#form button').attr('disabled',true);
        postinfo();
        return false;
    });
    $('input[name=sms_count]').bind('change keyup',function(){
         this.value=this.value.replace(/[\D-]/g,'');
         window.sending = new Date().getTime();
        (function(time){
         if(!isNaN($('input[name=sms_count]').val()))
         setTimeout(function () {
           if(time==sending) $.get("<?php echo url('sms/getPrice');?>",$('form').serialize(),function(data){
                 $('.help-block').html("<font size=6 color='blue'>"+data+"</font>"+'元');
             });
         },1500);
        })(sending);
    });

})


</script>
