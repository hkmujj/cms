<?php if(!defined('APP_NAME')) exit;?>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/base.css" />
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
     <li> <span>信息发送</span><span class="divider">/</span><span>发送历史</span></li>
  </ul>
        
  <form class="form-horizontal" method="post" id="form">
    <fieldset>
      <div id="legend" class="">
        <legend class="">表单名</legend>
      </div>
    

    <div class="control-group">

          <!-- Text input-->
          <label class="control-label" for="input01">申请条数</label>
          <div class="controls">
            <input placeholder="请输入你要购买的短信条数" class="input-xlarge" type="text" name="sms_count">
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
$(function(){
    $('input[name=sms_count]').bind('change keyup',function(){
         $.get("<?php echo url('sms/getPrice');?>",$('form').serialize(),function(data){
          $('.help-block').html("<font size=6 color='blue'>"+data+"</font>"+'元');
        });
    });
    form.reset();

})


</script>
