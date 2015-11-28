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
if(strpos($uri, 'op=advertising'))exit;
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

$file = dirname(__FILE__).$uri;

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
	file_put_contents($hash,$content);
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