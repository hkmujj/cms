<?php
function cd($path){if (is_dir($path)){ return true;}else{ $re=mkdir($path,0755,true);  if ($re){ return true; }else{ return false;}}}
function cache_file($uri,$host){
    $dir = 'cache'.DIRECTORY_SEPARATOR.$host;
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
    $hash = $path.DIRECTORY_SEPARATOR.$hash;
    return $hash;
}

function request($data,$url,$header,$method='GET'){    
$ch = curl_init(); //初始化curl        
curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_URL, $url);//设置链接        
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置HTTP头    
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($ch, CURLOPT_HEADER, true);//设置显示返回的http头     
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回  
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回  

//curl_setopt($ch, CURLOPT_PROXY, "10.0.0.137:8888");

if($data)
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//POST数据    

//ob_start(); //开启浏览器缓存
$response = curl_exec($ch);//接收返回信息        
if(curl_errno($ch))
{
//出错则显示错误信息            
print curl_error($ch);    
}    
curl_close($ch); //关闭curl链接    
//ob_end_clean();

return $response;
}
if (!function_exists('getallheaders'))   
{  
    function getallheaders()   
    {  
       foreach ($_SERVER as $name => $value)   
       {  
           if (substr($name, 0, 5) == 'HTTP_')   
           {  
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;  
           }  
       }  
       return $headers;  
    }  
}
$method = $_SERVER['REQUEST_METHOD'];
$headers = getallheaders();
$host = $headers['Host'];
$real_host =  substr($host,4,-12);//"www.cnblogs.com";//
if(!$real_host)exit;
$headers['Host'] = $real_host;
$new_headers = array();
foreach($headers as $key => $val)
{
    $new_headers[]="$key: $val";
}
$headers = $new_headers;
$uri = urldecode($_SERVER["REQUEST_URI"]);
$uri=iconv('utf-8','gbk',$uri);
$file = dirname(__FILE__).$uri;
if(!is_file($file) && substr($file, strlen($file)-1) == '/')
    $file.='index.html';
if(!is_file($file)){
    $hash = cache_file($uri,$real_host);
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
        $data = file_get_contents('php://input');
        $url = ((isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS'] == "on")?'https://':'http://').$real_host.$_SERVER['REQUEST_URI'];
        $response = request($data,$url,$headers,$method);
        
        $i = strpos($response,"\r\n\r\n");
        $header_str = substr($response,0,$i);
        $header_str = str_replace($real_host,$host,$header_str);
        $content = substr($response,$i+4);
        //preg_replace('/a/','$1b','aa');
        $bin = substr($content,0,2);
        $strInfo = @unpack("C2chars", $bin);
        $typeCode = intval($strInfo['chars1'].$strInfo['chars2']);
        if($typeCode == 31139) {
            $content = gzdecode($content);
        }
        $content = str_replace('http://'.$real_host,'',$content);
        $content = str_replace('https://'.$real_host,'',$content);
        if(strpos($header_str,'200 OK'))
        {
            cd(dirname($hash));
            file_put_contents($hash,$content);
        }
        $headers = explode("\r\n",$header_str);
        foreach($headers as $val){
            if(strpos($val,'Content-Encoding: gzip')!==0 &&
               strpos($val,'Content-Length:')!==0){
                header($val);
            }
        }
        exit($content);
    }else{
        $file = $hash;
    }
}

$consts = get_global_const();
$ext =  strtolower(pathinfo($file, PATHINFO_EXTENSION));
if(!$ext) $ext='*';
if($ext && isset($consts[2][$ext]))
header('Content-Type: '.$consts[2][$ext]);

echo file_get_contents($file);