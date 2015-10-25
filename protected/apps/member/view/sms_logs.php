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
        <table width="100%" class="table table-bordered">
            <tr>
              <th>序号</th>
              <th>手机</th>
              <th>内容</th>
              <th>长度</th>
              <th>条数</th>
              <th>剩余条数</th>
              <th>时间</th>
              <th>备注</th>
              <th>跟踪码</th>
              <th>状态</th>
            </tr>
            {if empty($list)}<tr><td colspan="10">您还没有订单~</td></tr>{/if}
            {loop $list $val}
              <tr>
                  <td align="center"><span class="index">{$val['sms_id']}</span></td>
                  <td align="center">{$val['sms_phone']}</td>
                  <td align="center">{truncate($val['sms_content'],10)}</td>
                  <td align="center"><font color="violet">{$val['sms_strlen']}</font></td>    
                  <td align="center"><font color="blue">{$val['sms_count']}</font></td>
                  <td align="center"><font color="brown">{$val['sms_leftc']}</font></td>
                  <td align="center">{$val['sms_time']}</td>
                  <td align="center">{$val['sms_reason']}</td>
                  <td align="center">{$val['sms_code']}</td>
                 <td align="center">{if $val['sms_state']==1}<font color="blue">成功</font>{else}<font color="red">失败</font>{/if}</td> 
              </tr>
            {/loop}
            {if !empty($page)}<tr><td colspan="10">{$page}</td></tr>{/if}
            
            
        </table>
</div>
