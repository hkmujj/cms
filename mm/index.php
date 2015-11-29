<?php 

function cd($path){if (is_dir($path)){ return true;}else{ $re=mkdir($path,0755,true);  if ($re){ return true; }else{ return false;}}}
function cache_file($uri){
	$dir = 'cache';
	$pi = parse_url($uri);
	
	$is_dir = substr($pi["path"], strlen($pi["path"])-1) == '/';
	if($is_dir)$pi["path"].='index.html';
	$pf = pathinfo($pi["path"]);
	$hash=isset($pi['query'])?substr(md5($pi['query']),4,4):'';
	$is_php = isset($pf['extension']) && strtolower($pf['extension']) == 'php';
	if($hash || $is_php)$hash = '.'.$hash;
	$hash = $pf['basename'].$hash;
	$pf['dirname'] = rtrim($pf['dirname'],DIRECTORY_SEPARATOR);
	$path=dirname(__FILE__).DIRECTORY_SEPARATOR.$dir.$pf['dirname'];
	cd($path);$hash = $path.DIRECTORY_SEPARATOR.$hash;
	return $hash;
}
function combine_url($url)
{
	if(strpos($url, 'http://')===0)return $url;
	if(strpos($url, '.html')){
		return 'http://mm.ziliao.link/pic/'.$url;
	}
	if(strpos($url, '.jpg')){
		return 'http://p.aimm-img.com/uploads/allimg/'.$url;
	}
	return $url;
}


$uri = urldecode($_SERVER["REQUEST_URI"]);
if(strpos($uri, 'op=advertising')){
	check_etag(__FILE__,filemtime(__FILE__));
	exit;
}
$idk = '?op=get_imgs&id=';
if($idx = strpos($uri, $idk))
{
	$id = substr($uri, $idx+strlen($idk));
	$file = dirname(__FILE__).'/data/'.$id.'.txt';
	if(is_file($file))
	{
		$arr = json_decode(file_get_contents($file),true);
		$json = array(
			"slide" =>array(
			"title"=>$arr[0],
			"createtime"=>$arr[1],
			"click"=>combine_url($arr[3]),
			"like"=>$arr[2],
			"url"=>combine_url($arr[3])
			),
			"next_album"=>array(
			"interface"=>"",
			"title"=>$arr[4][0],
			"url"=>combine_url($arr[4][1]),
			"thumb_50"=>combine_url($arr[4][2])
			),
			"prev_album"=>array(
			"interface"=>"",
			"title"=>$arr[5][0],
			"url"=>combine_url($arr[5][1]),
			"thumb_50"=>combine_url($arr[5][2])
			)
		);
		$image = $arr[6];
		for ($i = 0;$i<count($image);$i++) {
			$json["images"][]=array(
				      "title"=>"",
				      "intro"=>"",
				      "comment"=>"",
				      "width"=>"",
				      "height"=>"",
				      "thumb_50"=>combine_url($image[$i]),
				      "thumb_160"=>combine_url($image[$i]),
				      "image_url"=>combine_url($image[$i]),
				      "createtime"=>"",
				      "source"=>"",
				      "id" => $i + 1
				);
		}
		exit('var slide_data = '.json_encode($json));
	}
}
$uri=iconv('utf-8','gbk',$uri);
$home_dir = dirname(__FILE__);

$file = $home_dir.$uri;

//fe($file);
if(!is_file($file) && substr($file, strlen($file)-1) == '/')
	$file.='index.html';
if(!is_file($file)){
	$hash = cache_file($uri);
	$content = '';
	$flag = true;
	if(is_file($hash))
	{
		$flag = false;
		$content = file_get_contents($hash);
		if(trim($content)=='' && (time()-filemtime($hash)>1200))$flag = true;
	}
	if($flag)
	{
	$url = 'http://wwww.aimm.cc'.$uri;
	$content = @file_get_contents($url);
	}
	$content = replace_content($content);
	if(!is_file($hash)){
		file_put_contents($hash,$content);
	}
	$file = $hash;
}else
{
	$content = file_get_contents($file);
	$content = replace_content($content);
}
$content = str_replace('</body>', '
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?fad45c465c027f5c6a61109c6fd95329";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
</body>', $content);
$func_file = dirname($home_dir).DIRECTORY_SEPARATOR.'func.php';

if(is_file($func_file))
{
	require dirname($home_dir).DIRECTORY_SEPARATOR.'func.php';
	$old_str = '<a href="http://www.fengdunan.com/sex/?aimm" target="_blank">两性<i class="hot icon"></i></a>';
	$new_str =  pay_global('下载所有图片<i class="hot icon"></i>'
		,"支付50元,即可显示下载链接"
		,'<a href="javascript:void(0);">百度网盘账号:supermails@126.com密码:280123Z</a>'
		,"下载MM图片收费",50,"13164355239");
	$new_str .= "
<style>
.btn {
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    background-color: #e6e6e6;
    background-repeat: no-repeat;
    border-color: #ccc #ccc #bbb;
    border-image: none;
    border-radius: 4px;
    border-style: solid;
    border-width: 1px;
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);
    color: #333;
    cursor: pointer;
    display: inline-block;
    font-size: 13px;
    line-height: normal;
    padding: 5px 14px 6px;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
    transition: all 0.1s linear 0s;
}

.btn:hover {
    background-position: 0 -15px;
    color: #333;
    text-decoration: none;
}
.btn.custom{
    background-color: hsl(312, 80%, 43%);
      background-repeat: repeat-x;
      background-image: -khtml-gradient(linear, left top, left bottom, from(hsl(312, 80%, 53%)), to(hsl(312, 80%, 43%)));
      background-image: -moz-linear-gradient(top, hsl(312, 80%, 53%), hsl(312, 80%, 43%));
      background-image: -ms-linear-gradient(top, hsl(312, 80%, 53%), hsl(312, 80%, 43%));
      background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0%, hsl(312, 80%, 53%)), color-stop(100%, hsl(312, 80%, 43%)));
      background-image: -webkit-linear-gradient(top, hsl(312, 80%, 53%), hsl(312, 80%, 43%));
      background-image: -o-linear-gradient(top, hsl(312, 80%, 53%), hsl(312, 80%, 43%));
      background-image: linear-gradient(hsl(312, 80%, 53%), hsl(312, 80%, 43%));
      border-color: hsl(312, 80%, 43%) hsl(312, 80%, 43%) hsl(312, 80%, 40.5%);
      color: #fff;
      text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.16);
      -webkit-font-smoothing: antialiased;
      margin-top:6px;
  }
</style>
	";
	$content = str_replace($old_str, $new_str, $content);
}

check_etag(md5($content),filemtime($file),true);
echo $content;

function replace_content($content)
{

	$host = $_SERVER["HTTP_HOST"];
	//return $content;
	$content = str_replace('cpro_id', 'cpr0_id', $content);
	$content = str_replace('（aimm.cc）', "（{$host}）", $content);
	$content = str_replace('www.aimm.cc', $host, $content);
	$content = str_replace('aimm.cc, All rights reserved. 黔ICP备15006009号-1', $host.', All rights reserved. 豫ICP备15006009号-1', $content);
	$content = str_replace('aimm.cc', $host, $content);
	$content = str_replace('爱美眉', '美眉', $content);
	$content = str_replace('src="http://a.aimm-img.com/js/tongji.js"', '', $content);
	$content = str_replace('cpro.baidustatic.com', $host, $content);
	$content = str_replace('src=""', '', $content);
	$content = str_replace('class="mod-baidu"', 'class="mod-baidu" style="display:none;"', $content);
	return $content;
}