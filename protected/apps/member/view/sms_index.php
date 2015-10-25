<?php if(!defined('APP_NAME')) exit;?>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/highslide.css" />
<link rel="stylesheet" type="text/css" href="__PUBLIC__/uploadify/uploadify.css" />
<script type="text/javascript" src="__PUBLIC__/js/highslide.js"></script>
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/kindeditor/kindeditor.js"></script>
<script type="text/javascript" src="__PUBLIC__/uploadify/jquery.uploadify-3.1.min.js"></script>
<script  type="text/javascript" language="javascript" src="__PUBLIC__/js/jquery.skygqCheckAjaxform.js"></script>
<script language="javascript">
KindEditor.ready(function(K) {
	K.create('#content', {
		allowPreviewEmoticons : false,
		allowImageUpload : false,
		items : [
				'source', '|','fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
				'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright','lineheight', 'insertorderedlist',
				'insertunorderedlist', '|', 'emoticons', 'image','pagebreak','link','clearhtml']

	});
});
//封面图效果
hs.graphicsDir = "__PUBLIC__/images/graphics/";
hs.showCredits = false;
hs.outlineType = 'rounded-white';
hs.restoreTitle = '关闭';

function addcover(){//提取封面图事件绑定
	 $(".photo").click(function(){
		var tag=$(this).attr('id');
		$("#picture").val(tag);
		$("#cover").attr('href','{$picpath}thumb_'+tag);
	 });
}
function picdel(){//单图删除
	$('.picdel').click(function(){
		var picname=$(this).prev().val();
		var tag=$(this).parent().parent();
		$.post("{url('photo/delpic')}", { picname: picname },
				function(data){
				 alert(data);
				tag.remove();
			});
	});
}
  $(function ($) { 
  $('#DelColor').click(function(){
	  $('#picker').hide();
	  $('#color').val('');
	  $('#color').css('background-color','#ffffff');
  });
	$('.all_cont tr').hover(//行颜色效果	
	function () {
        $(this).children().css('background-color', '#f2f2f2');
	},
	function () {
        $(this).children().css('background-color', '#fff');
	});
   //副栏目
  $('#exs').click(function(){
    var obj=$("#exsort");
    if(obj.css('display')=='none') {
      obj.show();
      $(this).html('－副栏目');
    }else{
        obj.hide();
      $(this).html('＋副栏目');
    }
    });
 var hode='<img src="__PUBLICAPP__/images/minus.gif">';
  var show='<img src="__PUBLICAPP__/images/plus.gif">';
  $.each($(".exsort"), function(i,val){  
       if($(this).next().html()){
	      $(this).find('.fold').html(show);
	   }
   });
  $('.exsort a').click(function(){
	var obj=$(this).parent().next();
	if(obj.css('display')=='none') {
      if(obj.html()=='') {$(this).html('');}else {$(this).html(hode);obj.show();}
    }else{
       obj.hide();
	   $(this).html(show);
    }  
  });
   //表单验证
	var items_array = [
	    { name:"sort",min:6,simple:"类别",focusMsg:'选择类别'},
		{ name:"title",min:6,simple:"标题",focusMsg:'3-30个字符'},
		{ name:"tpcontent",simple:"模板",focusMsg:'选择模板'}
	];

	$("#info").skygqCheckAjaxForm({
		items			: items_array
	});

	//图片批量上传
    function getval(domid,ifcheck){
		if(ifcheck){
			if($("#"+domid).attr("checked")) return $("#"+domid).val();
		}else{
			return $("#"+domid).val();
		}
		return false;
    }

	 $('#file_upload').uploadify({
			'auto'     : false,
			'buttonImage' : '__PUBLIC__/uploadify/downbut.jpg',
            'swf'      : '__PUBLIC__/uploadify/uploadify.swf',
            'uploader' : "{url('photo/images_upload',array('phpsessid'=>session_id()))}",
          'onUploadStart': function (file) {
             $("#file_upload").uploadify("settings", "formData", {'ifthumb':getval('ifthumb',true),'ttype':getval('thumbtype',false),width:getval('thumbwidth',false),height:getval('thumbheight',false)});  
           },
			    'onUploadSuccess' : function(file, data, response) {
                  // alert('The file ' + file.name + ' was successfully uploaded with a response of ' + response + ':' + data);
				  if(data){
			      var pstr=$("#imginfo").html();
				  var ifthumb=getval('ifthumb',true)?"thumb_":"";
		          var itml = pstr + '<div class="photolist"><div class="pcon"><img width="{$twidth}" height="{$theight}" class="photo" id="'+data+'" src="{$picpath}'+ifthumb+data+'" title="点击设置为封面"></div><div class="pinfo"><input style="width:{$twidth}px" type="text" name="conlist[]"><input type="hidden" name="photolist[]" value="'+data+'"><a href="javascript:void(0);" class="picdel"></a></div></div>';
		          $("#imginfo").html(itml);
		          addcover();
		          picdel();
				  $("#picture").val(data);//自动获得封面图
				  $("#cover").attr('href','{$picpath}'+ifthumb+data);
				  }
         }
        });
  });
  
function ajax_fields()
 {
	var sid = $('#sort').val();
	var sid = sid.substring(sid.lastIndexOf(',')+1);
	$.ajax({
		type: 'POST',
		url: "{url('photo/ex_field')}",
		data: {
			sid: sid
		},
		dataType: "json",
		success: function(data) {
			$('#extend').html('');
			if(typeof(data[0].tableinfo)!='undefined'){
			for (var i in data) {
				var list_html = '<tr>';
				list_html += '<td width="100"  align="right" valign="middle">' + data[i].name + ':</td>';
				list_html += '<td>';
				if (data[i].type == 1) {
					list_html += '<input name="ext_' + data[i].tableinfo + '" type="text" value="' + data[i].defvalue + '" />';
				}
				if (data[i].type == 2) {
					list_html += '<textarea name="ext_' + data[i].tableinfo + '"  cols="0" style="width:300px !important; height:80px">' + data[i].defvalue + '</textarea>';
				}
				if (data[i].type == 3) {
					list_html += '<textarea class="excontent" name="ext_' + data[i].tableinfo + '"  cols="0" style="width:100%;height:300px;visibility:hidden;">' + data[i].defvalue + '</textarea>';
				}
				if (data[i].type == 4) {
					list_html += '<select name="ext_' + data[i].tableinfo + '"  >';
					default_ary = data[i].defvalue;
					ary = default_ary.split("\r\n");
					for (var x in ary) {
						strary = ary[x].split(",");
						list_html += '<option value="' + strary[0] + '">' + strary[1] + '</option>';
					}
					list_html += '</select>';
				}
				if (data[i].type == 5) {
					list_html += '<input name="ext_' + data[i].tableinfo + '" id="ext_' + data[i].tableinfo + '" type="text" value="' + data[i].defvalue + '" /><br>';
					list_html += '<iframe scrolling="no"; frameborder="0" src="{url("extendfield/file")}/&inputName=ext_' + data[i].tableinfo + '" style="width:300px; height:35px;"></iframe>';
				}
				if (data[i].type == 6) {
					default_ary = data[i].defvalue;
					ary = default_ary.split("\r\n");
					for (var x in ary) {
						strary = ary[x].split(",");
						list_html += '<option value="' + strary[0] + '">' + strary[1] + '</option>';
						list_html += strary[1] + '<input type="checkbox" name="ext_' + data[i].tableinfo + '[]" value="' + strary[0] + '" />';
					}
				}
				list_html += '<input type="hidden" name="tableid" value="' + data[i].pid + '">';
				list_html += '</td><td></td>';
				list_html += '</tr>';
				$('#extend').append(list_html);
			}
        KindEditor.create('.excontent', {
              allowFileManager : true,
              filterMode:false,
              uploadJson : "{url('photo/UploadJson')}",
              fileManagerJson : "{url('photo/FileManagerJson')}"
         });
			}
		}
	});
}
function tpchange()
{
   var tpc={$tpc};
   var paths = $('#sort').val();
   if(''!=tpc[paths]){
        $("#tpcontent").val(tpc[paths]);
   }
}
</script>
<div id="contain">
<ul class="breadcrumb">
   <li> <span>发送信息</span></li>
</ul>
  <form  action="{url('sms/send')}" method="post" id="info" name="info" onSubmit="return check_form(document.add);">
    <table class="table table-bordered">
    <tr>
      <td width="80" align="right">手机号：</td>
      <td  align="left">
        <input type="text" name="phone" id="title" maxlength="60" size="30" >
        </td>
      <td class="inputhelp"><font color="red">短信通温馨提醒：企业短信平台发送时间段：每天08：30-18：00，请在正确时间段提交短信!</font></td>
    </tr>
    <tr>
      <td align="right">短信内容：</td>
      <td align="left"><textarea name="content" id="content" style=" width: 307px; height: 92px;"></textarea></td>
      <td class="inputhelp">大批量发送短信前请一定测试一下短信内容。一般给移动手机做测试为准，测试收到再批量发送。未测试发送导致产生问题，公司概不负责</td>
    </tr>
    <tbody id="extend"></tbody>
    <tr>
      <td></td>
      <td colspan="2" align="left">
        <input type="submit" class="btn btn-primary btn-small" value="发送">
        &nbsp;
        <input class="btn btn-primary btn-small" type="reset" value="重置"></td>
    </tr>
  </table>
</form>
</div>