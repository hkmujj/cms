<?php if(!defined('APP_NAME')) exit;?>
      {if !empty($sortlist)} 
      <!--当前栏目子栏目调用-->
       <div class="panel margin-bottom">
            <div class="panel-head bg-blue"><h4 class="text-white">{$sorts[$id]['name']}</h4></div>
            <div class="panel-body">
                {loop sorttree($sortlist) $k1 $v1}
                    {if $v1['c']}
                       <ul class="rmenu">
                          {loop $v1['c'] $v2}
                              <li><a href="{$v2['url']}">{$v2['name']}</a>
                              {if $v2['c']}
                                  <ul>
                                     {loop $v2['c'] $v3}
                                        <li><a href="{$v3['url']}">{$v3['name']}</a></li>
                                     {/loop}
                                  </ul>
                              {/if}
                          {/loop}
                       </ul>
                    {/if}
                {/loop}
            </div>
         </div>
       {/if}
       {if empty($sortlist)} 
       <!--固定子栏目调用-->
       <div class="panel margin-bottom">
            <div class="panel-head bg-blue"><h4 class="text-white">{$sorts['100003']['name']}</h4></div>
            <div class="panel-body">
            <ul class="rmenu">
            {loop $sorts $key $vo}  
              {if (strpos($vo['path'],'100003,')!==false)}
                {if ($vo['deep']- $sorts[100003]['deep'])==1}
                  <li><a title="{$vo['name']}"  href="{$vo['url']}">{$vo['name']}</a>{if $vo['nextdeep']-$vo['deep']==1}<ul>{else}</li>{/if}
                {elseif ($vo['deep']- $sorts[100003]['deep'])==2}
                  <li><a title="{$vo['name']}"  href="{$vo['url']}">{$vo['name']}</a></li>{if $vo['deep']-$vo['nextdeep']==1}</ul>{/if}
                {/if}
              {/if}
            {/loop}
            </ul>
            </div>
         </div>
         {/if}

         <div class="panel margin-bottom">
            <div class="panel-head bg-blue"><h4 class="text-white">通知公告</h4></div>
            <div class="panel-body">
                <p class="text-indent">{piece:notice}</p>
            </div>
         </div>
         <div class="panel margin-bottom">
            <div class="panel-head bg-blue"><h4 class="text-white">热门资讯</h4></div>
            <div class="panel-body">
               <ul class="list-unstyle height-big">
                {news:{table=(news) field=(id,title,color,addtime,method)  limit=(5)}}
                     <li><a style="color:[news:color]" title="[news:title]" target="_blank" href="[news:url]">[news:title $len=25]</a><span class="float-right text-gray hidden-m text-little">{date($news['addtime'],Y-m-d)}</span></li>
                {/news}
               </ul>
            </div>
         </div>
         <div class="panel margin-bottom">
            <div class="panel-head bg-blue"><h4 class="text-white">随机资讯</h4></div>
            <div class="panel-body">
               <ul class="list-unstyle height-big">
                   {news:{table=(news) field=(id,title,color,addtime,method) order=(rand) limit=(5)}}
                     <li><a style="color:[news:color]" title="[news:title]" target="_blank" href="[news:url]">[news:title $len=25]</a><span class="float-right text-gray hidden-m text-little">{date($news['addtime'],Y-m-d)}</span></li>
                {/news}
               </ul>
            </div>
         </div>