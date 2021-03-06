<?php
class indexController extends appadminController{
	protected $layout = "layout";
	
	//显示app列表
	public function index(){
		foreach(getApps() as $app){
		  if($app!='default' && $app!='admin' && $app!='appmanage') $apps[$app]= appConfig( $app );
		}
		$this->apps = $apps;
		$this->reload = $_GET['reload'];
		$this->display();
	}
	
	//安装
	public function install(){	
		$app = $this->getApp();
		
		//安装数据库
		if( is_file( BASE_PATH . 'apps/' . $app . '/install.sql' ) ) {
			model('appmanage')->installSql( BASE_PATH . 'apps/' . $app . '/install.sql' );
		}
		//移动资源目录
		if( is_dir(  BASE_PATH . 'apps/' . $app . '/' . $app ) ){
			copy_dir(BASE_PATH . 'apps/' . $app . '/' . $app, 'public/' . $app);
			del_dir(BASE_PATH . 'apps/' . $app . '/' . $app);
		}
		
	    //YX新增添加后台管理菜单
	    $info=model('method')->find("rootid='0' AND pid='0' AND ifmenu='1' AND operate='{$app}'",'id');
	    if(empty($info)){
	    	$appconfig=appConfig($app);
		    $data['ifmenu']=1;
		    $data['rootid']=0;
		    $data['pid']=0;
		    $data['operate']=$app;
		    $data['name']=$appconfig['APP_NAME'];
	        model('method')->insert($data);
	    }
	 	//写入配置
		if( save_config($app, array('APP_STATE'=>1)) ){
			$this->success('安装成功！', url('index/index',array('reload'=>1)));
		}	
		$this->error('安装失败！');
	}
	
	//卸载
	public function uninstall(){
		$app = $this->getApp();
 		//YX新增删除后台管理菜单	
		model('method')->delete("operate='$app' AND rootid=0");
		//删除数据库
		config( require(BASE_PATH . 'apps/' . $app . '/config.php') );
		$tables = config('APP_TABLES');
		if( !empty($tables) ){
			$tables = explode(',', $tables);
			model('appmanage')->uninstallSql( $tables );
		}
		//删除资源文件
		if( is_dir( 'public/' . $app ) ){
			del_dir( 'public/' . $app );
		}	
		//删除app文件
		del_dir(BASE_PATH . 'apps/' . $app);

		$this->success('卸载成功！', url('index/index',array('reload'=>1)));
	}
	
		
	//修改状态，如启用，停用
	public function state(){
		$app = $this->getApp();
		$state = intval( $_GET['state'] ) == 1 ? 1 : 2;
		save_config($app, array('APP_STATE' => $state) );
		$this->redirect(url('index/index',array('reload'=>1)));
	}
	
	//导入
	public function import($zip_file=''){
		if(empty($zip_file)){
			if( !$this->isPost() ){
				$this->display();
				return true;
			}
			
			if($_FILES['file']['size'] < 0){
				$this->error('请选择文件');
			}
			$zip_file = $_FILES['file']['tmp_name'];
		}
		
		$zip_dir = BASE_PATH . 'cache/tmp/' .md5($zip_file) .'/';
		if(substr($zip_dir,0,1)=="/")  $zip_dir='.'.$zip_dir; //无法获得绝对路径情况
		
		$zip = new Zip();
		$zip->decompress($zip_file, $zip_dir);
		@unlink($zip_file);
		
		//获取app名称
		$app = '';
		$arr = glob($zip_dir .'*/config.php');
		if( !empty($arr) ){
			if( preg_match('#/([a-z0-9]+)/config.php#', $arr[0], $matches)){
				$app = $matches[1];
			}
		}
		if( empty($app) ){		
			del_dir($zip_dir);
			$this->error('安装包格式错误！');
		}
		
		//判断应用是否已经存在
		$app_path = BASE_PATH . 'apps/' . $app . '/';
		if( is_dir($app_path) ){
			del_dir($zip_dir);
			$this->error($app .'应用已存在，请先卸载！');
		}

		//将数据拷贝到apps目录
		copy_dir($zip_dir . $app , $app_path);
		del_dir($zip_dir);
		
		//执行安装
		$_GET['app'] = $app;
		$this->install();
	}
	
	//导出
	public function export(){
		$app = $this->getApp();
		$app_path = BASE_PATH . 'apps/' . $app . '/';
		
		//导出数据表
		config( appConfig( $app ) );
		$tables = config('APP_TABLES');
		if( !empty($tables) ){
			$tables = explode(',', $tables);
			$sql = model('appmanage')->exportSql( $tables );
			if( !empty($sql) ){
				file_put_contents(BASE_PATH . 'apps/' . $app . '/install.sql', $sql);
			}
		}
		//拷贝资源文件
		if( is_dir( 'public/' . $app ) ){
			copy_dir( 'public/' . $app, $app_path . $app);
		}
		
		$zip = new Zip();
		$filename = BASE_PATH . 'cache/tmp/' . $app . '.zip';
		$zip->compress($filename, $app_path,  BASE_PATH . 'apps/'); //打包压缩
		
		del_dir( $app_path . $app); //删除资源文件
		Http::download($filename, $app.'.zip');//下载
		@unlink($filename);//删除文件
	}
	
	//创建app的基本目录和文件
	public function create(){
		if( !$this->isPost() ){
			$this->display();
			return true;
		}
			
		$app = trim($_POST['app_id']);
		$app_name = trim($_POST['app_name']);
		$app_path = BASE_PATH . 'apps/' . $app . '/' ;
		$msg = Check::rule( array(
			array( Check::must($app), '请输入应用ID'),
			array( preg_match('/^[a-z]+$/', $app), '应用ID必须为全小写字母'),
			array( Check::must($app_name), '请输入应用名称'),
			array( !is_dir($app_path), '应用已存在，请先卸载！'),
		));
		if( true !== $msg){
			$this->error($msg);
		}
		
		//拷贝默认代码
		copy_dir(BASE_PATH . 'apps/' . APP_NAME . '/code/', $app_path);
		
		//修改控制器,模型,api
		foreach(array('controller/indexController', 'model/demoModel', 'demoApi') as $value){			
			$value = $value . '.php';
			$file = $app_path . str_replace('demo', $app, $value);
			rename($app_path . $value, $file);
			file_put_contents($file, str_replace('demo', $app, file_get_contents($file)) );
		}
		
		//写入配置
		if( save_config($app, array('APP_NAME' => $app_name, 'APP_VER'=> date('1.0.Y.md'))) ){
			//YX新增添加后台管理菜单
		    $data['ifmenu']=1;
		    $data['rootid']=0;
		    $data['pid']=0;
		    $data['operate']=$app;
		    $data['name']=$app_name;
	        model('method')->insert($data);
			$this->success('创建成功！', url('index/index'));
		}	
		$this->error('创建失败！');
	}
	
	//在线安装
	public function onlineinstall(){
		$url = $_GET['url'];//在线安装地址
		if( empty($url) ){
			$this->error('参数传递错误');
		}
		$content = file_get_contents($url);
		if( empty($content) ){
			$this->error('数据下载错误');
		}
		$zip_file = BASE_PATH . 'cache/tmp/' .md5($url) .'.zip';
		if( file_put_contents($zip_file, $content) ){
			$this->import($zip_file);
		}else{
			$this->error('数据写入错误');
		}
	}
	
	//设置默认app
	public function setdefault(){
		$app = $this->getApp();
		$replace = "define('DEFAULT_APP', '$app');";
		
		$file = 'index.php';
		$content = file_get_contents($file);
		
		if( preg_match("/define\((\'|\")DEFAULT_APP(\'|\"), (\'|\")(.*?)(\'|\")\);/is", $content, $out)){
			$content = preg_replace("/define\((\'|\")DEFAULT_APP(\'|\"), (\'|\")(.*?)(\'|\")\);/is", $replace, $content);
		}else{
			$content = preg_replace("/<\?php/is", "<?php\r\n".$replace, $content);
		}
		
		if( file_put_contents($file, $content) ){
			$this->alert('设置成功');
		}else{
			$this->alert('设置失败');
		}
	}
	
	protected function getApp(){
		$app = trim( $_GET['app'] );
		if( empty($app) ){
			$this->error('请不要恶意操作');
		}
		return $app;
	}
}