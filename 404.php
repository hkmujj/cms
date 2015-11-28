<?php
function get_global_const(){
$host = $_SERVER["HTTP_HOST"];
$base_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.substr($host,0,strpos($host,'.'));
$mimetypes = array(
    '*' => 'application/octet-stream',
		'ez' => 'application/andrew-inset',
		'hqx' => 'application/mac-binhex40',
		'cpt' => 'application/mac-compactpro',
		'doc' => 'application/msword',
		'bin' => 'application/octet-stream',
		'dms' => 'application/octet-stream',
		'lha' => 'application/octet-stream',
		'lzh' => 'application/octet-stream',
		'exe' => 'application/octet-stream',
		'class' => 'application/octet-stream',
		'so' => 'application/octet-stream',
		'dll' => 'application/octet-stream',
		'oda' => 'application/oda',
		'pdf' => 'application/pdf',
		'ai' => 'application/postscript',
		'eps' => 'application/postscript',
		'ps' => 'application/postscript',
		'smi' => 'application/smil',
		'smil' => 'application/smil',
		'mif' => 'application/vnd.mif',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',
		'wbxml' => 'application/vnd.wap.wbxml',
		'wmlc' => 'application/vnd.wap.wmlc',
		'wmlsc' => 'application/vnd.wap.wmlscriptc',
		'bcpio' => 'application/x-bcpio',
		'vcd' => 'application/x-cdlink',
		'pgn' => 'application/x-chess-pgn',
		'cpio' => 'application/x-cpio',
		'csh' => 'application/x-csh',
		'dcr' => 'application/x-director',
		'dir' => 'application/x-director',
		'dxr' => 'application/x-director',
		'dvi' => 'application/x-dvi',
		'spl' => 'application/x-futuresplash',
		'gtar' => 'application/x-gtar',
		'hdf' => 'application/x-hdf',
		'js' => 'application/javascript',
		'skp' => 'application/x-koan',
		'skd' => 'application/x-koan',
		'skt' => 'application/x-koan',
		'skm' => 'application/x-koan',
		'latex' => 'application/x-latex',
		'nc' => 'application/x-netcdf',
		'cdf' => 'application/x-netcdf',
		'sh' => 'application/x-sh',
		'shar' => 'application/x-shar',
		'swf' => 'application/x-shockwave-flash',
		'sit' => 'application/x-stuffit',
		'sv4cpio' => 'application/x-sv4cpio',
		'sv4crc' => 'application/x-sv4crc',
		'tar' => 'application/x-tar',
		'tcl' => 'application/x-tcl',
		'tex' => 'application/x-tex',
		'texinfo' => 'application/x-texinfo',
		'texi' => 'application/x-texinfo',
		't' => 'application/x-troff',
		'tr' => 'application/x-troff',
		'roff' => 'application/x-troff',
		'man' => 'application/x-troff-man',
		'me' => 'application/x-troff-me',
		'ms' => 'application/x-troff-ms',
		'ustar' => 'application/x-ustar',
		'src' => 'application/x-wais-source',
		'xhtml' => 'application/xhtml+xml',
		'xht' => 'application/xhtml+xml',
		'zip' => 'application/zip',
		'au' => 'audio/basic',
		'snd' => 'audio/basic',
		'mid' => 'audio/midi',
		'midi' => 'audio/midi',
		'kar' => 'audio/midi',
		'mpga' => 'audio/mpeg',
		'mp2' => 'audio/mpeg',
		'mp3' => 'audio/mpeg',
		'aif' => 'audio/x-aiff',
		'aiff' => 'audio/x-aiff',
		'aifc' => 'audio/x-aiff',
		'm3u' => 'audio/x-mpegurl',
		'ram' => 'audio/x-pn-realaudio',
		'rm' => 'audio/x-pn-realaudio',
		'rpm' => 'audio/x-pn-realaudio-plugin',
		'ra' => 'audio/x-realaudio',
		'wav' => 'audio/x-wav',
		'pdb' => 'chemical/x-pdb',
		'xyz' => 'chemical/x-xyz',
		'bmp' => 'image/bmp',
		'gif' => 'image/gif',
		'ief' => 'image/ief',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'jpe' => 'image/jpeg',
		'png' => 'image/png',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'djvu' => 'image/vnd.djvu',
		'djv' => 'image/vnd.djvu',
		'wbmp' => 'image/vnd.wap.wbmp',
		'ras' => 'image/x-cmu-raster',
		'pnm' => 'image/x-portable-anymap',
		'pbm' => 'image/x-portable-bitmap',
		'pgm' => 'image/x-portable-graymap',
		'ppm' => 'image/x-portable-pixmap',
		'rgb' => 'image/x-rgb',
		'xbm' => 'image/x-xbitmap',
		'xpm' => 'image/x-xpixmap',
		'xwd' => 'image/x-xwindowdump',
		'igs' => 'model/iges',
		'iges' => 'model/iges',
		'msh' => 'model/mesh',
		'mesh' => 'model/mesh',
		'silo' => 'model/mesh',
		'wrl' => 'model/vrml',
		'vrml' => 'model/vrml',
		'css' => 'text/css',
		'html' => 'text/html',
		'htm' => 'text/html',
		'asc' => 'text/plain',
		'txt' => 'text/plain',
		'rtx' => 'text/richtext',
		'rtf' => 'text/rtf',
		'sgml' => 'text/sgml',
		'sgm' => 'text/sgml',
		'tsv' => 'text/tab-separated-values',
		'wml' => 'text/vnd.wap.wml',
		'wmls' => 'text/vnd.wap.wmlscript',
		'etx' => 'text/x-setext',
		'xsl' => 'text/xml',
		'xml' => 'text/xml',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpe' => 'video/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',
		'mxu' => 'video/vnd.mpegurl',
		'avi' => 'video/x-msvideo',
		'movie' => 'video/x-sgi-movie',
		'ice' => 'x-conference/x-cooltalk',
		'json' => 'application/json',
		);
  return array($host,$base_dir,$mimetypes);
}
function check_resource(){
	list($host,$base_dir,$mimetypes) = get_global_const();
	$host = $_SERVER["HTTP_HOST"];
	$base_dir = dirname(__FILE__).DIRECTORY_SEPARATOR.substr($host,0,strpos($host,'.'));
	if(is_dir($base_dir.'/'.'cache'))
	{
		$file =  $base_dir.DIRECTORY_SEPARATOR.'index.php';
		if(is_file($file)){
			if(!defined('PHP_404_BASE'))define('PHP_404_BASE',dirname($file));
			$_SERVER['SCRIPT_FILENAME'] = $file;
			$_SERVER['SCRIPT_NAME'] = substr($file,strlen($base_dir));
			include $file;
			exit;
		}

	}
	$uri = urldecode($_SERVER["REQUEST_URI"]);
	$pi = parse_url($uri);
	$is_dir = substr($pi["path"], strlen($pi["path"])-1) == '/';
	if(!$is_dir && is_dir($base_dir.$uri)){header("Location: $uri/");exit;}
	$pf =$is_dir?array('basename'=>'','dirname'=>$pi["path"]):pathinfo($pi["path"]);
	$ext = isset($pf['extension'])?strtolower($pf['extension']):'';
	if($pf['dirname']==DIRECTORY_SEPARATOR)$pf['dirname']='';
	$file = $base_dir.$pf['dirname'].DIRECTORY_SEPARATOR.$pf['basename'];
	if(!is_file($file)&&$is_dir)
	{		
		$file = $base_dir.$pf['dirname'].DIRECTORY_SEPARATOR.'index.php';
		if(is_file($file)){
			//if(!$pf['dirname'])return;
			$ext = 'php';
		}
		if(!is_file($file)){
			$file = $base_dir.$pf['dirname'].DIRECTORY_SEPARATOR.'index.html';
			if(is_file($file)){
				$ext = 'html';
			}
		}
		if(!is_file($file)){
			$file = $base_dir.$pf['dirname'].DIRECTORY_SEPARATOR.'index.htm';
			if(is_file($file))$ext = 'htm';
		}
	}
	$is_php = strtolower($ext) == 'php';
	
	if(!$is_php){
			if(is_file($file)){
        if(!$ext) $ext='*';
				if($ext && isset($mimetypes[$ext]))
				header('Content-Type: '.$mimetypes[$ext]);
				exit(file_get_contents($file));
			}else
			{
				header('HTTP/1.1 404 Not Found');
			    header("status: 404 Not Found");
			    exit;
			}
	}elseif(is_file($file)){
			if(!defined('PHP_404_BASE'))define('PHP_404_BASE',dirname($file));
			$_SERVER['SCRIPT_FILENAME'] = $file;
			$_SERVER['SCRIPT_NAME'] = substr($file,strlen($base_dir));
			include $file;
			exit;
	}
}
list($host,$base_dir,$mimetypes) = get_global_const();
if(is_dir($base_dir))
{
	if(!defined('MAIN_INDEX'))
	{
		parse_str(file_get_contents('php://input'),$_POST);
		parse_str($_SERVER['QUERY_STRING'],$_GET);
		$_REQUEST = array_merge($_GET,$_POST);
	}

	if(($index = strpos($_SERVER["REQUEST_URI"], '/index.php/'))!==false){
		$index_file = $base_dir.substr($_SERVER["REQUEST_URI"],0 , $index+10);
		if(is_file($index_file))
		{
			if(!defined('PHP_404_BASE'))define('PHP_404_BASE',dirname($index_file));
			$_SERVER['SCRIPT_NAME'] = substr($index_file,strlen($base_dir));
			include $index_file;
		}
		exit;
	}
	check_resource();
	exit;
}
else if(!defined('MAIN_INDEX'))
{
	header('HTTP/1.1 404 Not Found');
    header("status: 404 Not Found");
    exit;
}
