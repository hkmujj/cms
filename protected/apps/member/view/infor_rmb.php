<?php if(!defined('APP_NAME')) exit;?>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/base.css" />
<div id="contain">
<ul class="breadcrumb">
     <li> <span>账户管理</span><span class="divider">/</span><span>我的账户</span></li>
</ul>
        <table class="table table-bordered">
            <tr>
              <th width="200" align="right">短信总额 ：</th>
              <td>{$info['sms_tcount']}条</td>
            </tr>
            <tr>
              <th width="200" align="right">使用总额：</th>
              <td>{$info['sms_scount']}条</td>
            </tr>
            <tr>
              <th width="200" align="right">短信余额：</th>
              <td>{$info['sms_lcount']}条</td>
            </tr>
        </table>
</div>