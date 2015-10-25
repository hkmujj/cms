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
     <li> <span>信息发送</span><span class="divider">/</span><span>充值历史</span></li>
  </ul>
        <table width="100%" class="table table-bordered">
            <tr>
              <th>序号</th>
              <th>条数</th>
              <th>价格</th>
              <th>申请时间</th>
              <th>充值时间</th>
              <th>备注</th>
              <th>状态</th>
            </tr>
            {if empty($list)}<tr><td colspan="10">您还没有订单~</td></tr>{/if}
            {loop $list $val}
              <tr>
                  <td align="center"><span class="index">{$val['order_id']}</span></td>

                 
  
                  <td align="center"><font color="blue">{$val['sms_count']}</font></td>
                  <td align="center"><font color="brown">{$val['sms_price']}</font></td>
                   
                  <td align="center">{$val['sms_time']}</td>
                  <td align="center">{if !empty($val['sms_ptime'])}{$val['sms_ptime']}{else}----{/if}</td>
                  <td align="center">{truncate($val['sms_remark'],10)}</td>
                 <td align="center">{if $val['sms_state']==1}<font color="blue">成功</font>{else}<font color="red">未处理</font>{/if}</td> 
              </tr>
            {/loop}
            {if !empty($page)}<tr><td colspan="10">{$page}</td></tr>{/if}
            
            
        </table>
</div>
