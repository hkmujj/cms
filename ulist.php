<?php
require("Db.php");
if($_POST){
  if($_POST['phone'])
  {
    $db=new db_class();
    $condition="phone='".$_POST['phone']."'";
    if(isset($_POST['chk_send']))
    {
        $v=$_POST['chk_send']==0?0:1;
        $modify="chk_send=".$v;
    }else if(isset($_POST['valid']))
    {
        $v=$_POST['valid']==0?0:1;
        $modify="valid=".$v;
    }else if(isset($_POST['city']))
    {
         $modify="city='".str_replace("'",'',stripslashes($_POST['city']))."'";
    }
    else if(isset($_POST['mise']))
    {
         $modify="mise='".str_replace("'",'',stripslashes($_POST['mise']))."'";
    }
    if(isset($modify))$db->db_update( 'xp_se_weather_user', $modify, $condition );
  }
exit('OK');
}else if($_GET){

}else{

} 
include 'page.class.php';
?>

<!DOCTYPE html>
<html>
<head>
<script src="jquery-min-1.7.js"></script>
<meta charset=utf-8 />
<title>用户管理</title>
<style>
#p2{margin:20px 0;}
#p2 .disabled{display:none;}
#p2 a,#p2 span{display:block;float:left;}
#p2 a{margin-left:5px;}
#p2 .f a{color:#2D5D8E;width:57px;height:22px;border:1px solid #cacaca;line-height:22px;text-align:center;}
#p2 .number a,#p2 span.current{color:#60719F;width:24px;height:22px;border:1px solid #cacaca;line-height:22px;text-align:center;}
#p2 span.omit{color:#666;border:0;width:24px;text-align:center;padding-top:9px;}
#p2 span.current{border-color:#B1723D;color:#B1723D;margin-left:5px;}
#p2 .total{margin-left:5px;height:22px;line-height:22px;padding:0 5px;color:#888;}
#p2 .up a{padding-left:4px;background:url('images/tbup.jpg') no-repeat 2px center;}
#p2 .down a{padding-right:4px;background:url('images/tbdown.jpg') no-repeat 52px center;}
#p2 input{text-align:center;padding:0px;float:left;border:1px solid #ccc;width:30px;height:22px;line-height:22px;margin-right:5px;}
#p2 button{float:left;}
</style>
<script>
$(function(){
  $('.chk').click(
    function()
      {
        $.post(location.href,'phone='+$.trim($(this).parents('tr').children(":eq(1)").children('a').html())+"&"+$(this).attr('data')+"="+(this.checked?'1':'0'),
        function()
        {
            alert('修改成功！');
        });
      }
  );
  $('.ipt').blur(
    function()
      {
         if($(this).attr('ov')!=this.value)
          {
                 $(this).attr('ov',this.value) ;    
                  $.post(location.href,'phone='+$.trim($(this).parents('tr').children(":eq(1)").children('a').html())+"&"+$(this).attr('data')+"="+this.value,
                  function()
                  {
                      alert('修改成功！');
                  });
                }
          }
  );
})
</script>
</head>
<body>
<a href='/ulist.php?get=user'>用户</a>
<a href='/ulist.php?get=send'>天气</a>
<a href='/ulist.php'>归属</a>
<a href='/post.php'>操作</a>
<table>
<?php 
$db=new db_class();
if(isset($_GET['get']))
{
if($_GET['get']=='user' || $_GET['get']=='send')
{
$t='xp_se_weather_'.$_GET['get'];
}
}
else $t='xp_se_phone_gs';
$r=$db->db_select($t,'count(*) c');
$page = new Page($r[0]['c'],10);
$where='1=1 order by id desc limit '.$page->limit;
$r=$db->db_select($t,'*',$where);
if(count($r)!==0)
{
echo '<tr>';
foreach ($r[0] as $key => $value)
{
echo '<td>';
echo $key;
echo '</td>';
}
echo '<tr>';
}
for($i=0;$i<count($r);$i++)
{
echo '<tr>';
foreach ($r[$i]  as $key => $value)
{
echo '<td>';
$sv=$value;
if($t=='xp_se_weather_user')
{
  if($key=='phone')
  $sv="<a href='/post.php?phone=$sv'>$sv</a>";
  else if($key=='chk_send')
  {
  $sv="<input class='chk' data='chk_send' type='checkbox' ".($value==1?'checked':'')."/>";
  }
  else if($key=='valid')
  {
  $sv="<input class='chk' data='valid' type='checkbox' ".($value==1?'checked':'')."/>";
  }
  else if($key=='city')
  {
  $sv="<input class='ipt' data='city' style='width:40px' ov='$value' value='$value' />";
  }
  else if($key=='mise')
  {
  $sv="<input  class='ipt' data='mise' style='width:40px' ov='$value' value='$value' />";
  }
  else if($key=='state' && $value==6)
  {
  $sv="<font color=blue>$value</font>";
  }

}
echo $sv;
echo '</td>';
}
echo '</tr>';
}

?>
</table>
<?php 
	$page->pageType = '<span class="f up">%up%</span><span class="number">%numberF%</span>%omitE%<span class="f down">%down%</span><span class="total">共%total%条%pageToatl%页</span>%input%%GoTo%';
	$page->pageShow = array('GoTo'=>'确定');
	$p2 = $page->pageShow();
?>
	<div id="p2">
		<?php echo $p2;?>
	</div>
</body>
</html>