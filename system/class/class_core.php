<?php 
/*********************************************
* @Name:   GAY - Framework v1.0
* @Type:   Core
* @Author: Adolf
* @Email:  qingbin.zhang@baifendian.com
*********************************************/	
class Adolf_core {
	var $var = array();
	var $superglobal = array(
		'GLOBALS' => 1,
		'_GET' => 1,
		'_POST' => 1,
		'_REQUEST' => 1,
		'_COOKIE' => 1,
		'_SERVER' => 1,
		'_ENV' => 1,
		'_FILES' => 1
	);
	
	public static function GAY() {
		static $object;
		if(empty($object)) {
			$object = new self();
		}
		return $object;
	}
	
	public function __construct($is_construct='') {
		if(empty($is_construct)){       
			$this->_init_env();
			$this->_init_config();
			$this->_init_input();
			$this->_init_output();
		}
	}
		
	public function _init_env(){
		if(function_exists('date_default_timezone_set')) date_default_timezone_set("PRC");
		define('IN_RUN', true);		
		define('BASE_ROOT', substr(dirname(__FILE__), 0, -12));
	    define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
        define('TIME', time()); 
		define('GZIP', 0);
		define('CHARSET', 'utf-8'); 
		define('GET_POST_MERGE',1);	

		require_once BASE_ROOT.'config.inc.php';		
        require_once BASE_ROOT.'system/function/func_core.php';
		if($_config['custom']['check_function']['open']){require_once BASE_ROOT.'system/function/func_custom.php'; }
		
		$memcache=''; $redis='';
		if($_config['memcache']['open']){ $memcache = new Memcache();
			$memcache->connect($_config['memcache']['server'], $_config['memcache']['port']); }
		if($_config['redis']['open']){ $redis = new Redis();
			$redis->connect($_config['redis']['server'], $_config['redis']['port']); }
				
		switch ($_config['system']['environment']){
			case 'development':
				error_reporting(E_ALL); break;
			case 'testing':
				error_reporting(7); break;
			case 'production':
				error_reporting(0); break;
			default:
				exit('ERROR ENVIRONMENT.');
		}
			
		if(phpversion() < '5.3.0') {
			set_magic_quotes_runtime(0);
		}
			
		define('IS_ROBOT', checkrobot());
		
		if(function_exists('ini_get')) {
			$memorylimit = @ini_get('memory_limit');
			if($memorylimit && return_bytes($memorylimit) < 33554432 && function_exists('ini_set')) {
				ini_set('memory_limit', '128m');
			}
		}
		
		foreach ($GLOBALS as $key => $value) {
			if (!isset($this->superglobal[$key])) {
				$GLOBALS[$key] = null; unset($GLOBALS[$key]);
			}
		}	
		
		define('XSS_CHECK', $_config['custom']['check_xss']['open']);
        define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
        define('SCHEME', $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://'); 
		$directory = explode('index.php',$_SERVER['SCRIPT_NAME']);
	    define('HTTP_HOST', SCHEME.$_SERVER['HTTP_HOST'].'/' . substr($directory[0],1));
		
		global $_G;
		$_G = array('time' => TIME,
					'starttime' => dmicrotime(),
					'client_ip' => _get_client_ip(),
					'session' => array('store'=>$_config['session']['store'],'lifetime'=>$_config['session']['lifetime']),
					'cookie' => array('cookie_pre' => $_config['cookie']['cookiepre'],'cookie_path' => $_config['cookie']['cookiepath'],
					'cookie_domain' => $_config['cookie']['cookiedomain'],'cookie_lifetime' => $_config['cookie']['lifetime']),				
					'PHP_SELF' => '',
					'siteurl' => HTTP_HOST,
					'referer' => HTTP_REFERER,
					'siteroot' => '',
					'base_root' => BASE_ROOT,
					'memcache'=> is_object($memcache) ? $memcache : '',
					'redis'=> is_object($redis) ? $redis : '',
					'default_controller' => $_config['system']['default_controller']
					);
					
		$_G['PHP_SELF'] = htmlspecialchars($_SERVER['SCRIPT_NAME'] ? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF']);
		$_G['basefilename'] = basename($_G['PHP_SELF']);
		$_G['siteroot'] = substr($_G['PHP_SELF'], 0, -strlen($_G['basefilename']));

		$this->var = & $_G;
		$this->var['config'] = & $_config;
		$this->var['url'] = substr($this->var['siteurl'],0,-1);
	}
	
	public function _init_config() {
		global $_G;
		switch($_G['session']['store']){
			case 'local': session_start(); break;
			case 'mysql': include BASE_ROOT.'system/class/class_mysql/class_mysql_session.php'; break;
			case 'memcache': include BASE_ROOT.'system/class/class_mysql/class_memcache_session'; break;	
		}
      	
        include BASE_ROOT.'system/class/smarty_class/Smarty.class.php';     
		global $smarty;
        $smarty = new Smarty();                            
        $smarty->debugging = $_G['config']['smarty']['debugging'];         
        $smarty->template_dir = BASE_ROOT."view";       
        $smarty->compile_dir = BASE_ROOT."system/class/smarty_class/view_c";      
        $smarty->left_delimiter = $_G['config']['smarty']['left_delimiter'];
        $smarty->right_delimiter = $_G['config']['smarty']['right_delimiter'];
        $smarty->caching = $_G['config']['smarty']['caching'];                            
        $smarty->cache_dir = BASE_ROOT."system/class/smarty_class/cache";             
        $smarty->cache_lifetime = $_G['config']['smarty']['cache_lifetime'];                      
 
        $smarty->assign('css',$this->var['siteurl'].'view/css');
        $smarty->assign('image',$this->var['siteurl'].'view/images');
        $smarty->assign('js',$this->var['siteurl'].'view/js');
        $smarty->assign('url',substr($this->var['siteurl'],0,-1));
		$smarty->assign('G',$_G);
	}
			   
	public function _init_input(){		
		if (isset($_GET['GLOBALS']) ||isset($_POST['GLOBALS']) ||  isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS'])) {
			exit('Core_input_error');
		}
		
		if(MAGIC_QUOTES_GPC) {
			$_GET = dstripslashes($_GET);
			$_POST = dstripslashes($_POST);
			$_COOKIE = dstripslashes($_COOKIE);
		}
		
		$prelength = strlen($this->var['cookie']['cookie_pre']);
		foreach($_COOKIE as $key => $val) {
			if(substr($key, 0, $prelength) == $this->var['cookie']['cookie_pre']) {
				$this->var['cookie'][substr($key, $prelength)] = $val;
			}
		}

		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
			$_GET = array_merge($_GET, $_POST);
		}

		if(GET_POST_MERGE) {
			foreach($_GET as $k => $v) {
				$this->var['gp_'.$k] = daddslashes($v);
			}
		}		
	}	
	
	public function _init_output(){
		if($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_SERVER['REQUEST_URI'])){
			if(XSS_CHECK){$this->_xss_check();}
	 	}
		
		if(IS_ROBOT) {
			exit(header("HTTP/1.1 403 Forbidden"));
		}
		
		$gzip = GZIP && function_exists('ob_gzhandler'); 
        @header('Content-Type: text/html; charset=' . CHARSET);
		RouteUrl();
	}			

	private function _xss_check() {
		$temp = strtoupper(urldecode(urldecode($_SERVER['REQUEST_URI'])));
		if(strpos($temp, '<') !== false || strpos($temp, '"') !== false || strpos($temp, 'CONTENT-TRANSFER-ENCODING') !== false) {
			exit('Core_xss_check_error');
		}
		return true;
	}

	public function _init_db($dbc='') {
		global $_G;
		$engine = $_G['config']['db']['default']['engine'];
		include_once BASE_ROOT.'system/class/class_'.$engine.'/class_'.$engine.'.php';		
		$class = 'db_'.$engine;
		if($dbc){
			$_G['dbc'][$dbc] = new $class;
		}else{
			$this->db = & DB::object($class);
			$this->db->set_config($_G['config']['db']);			
			$this->db->connect(); 
		}
	}

	public function DBC($dbc='') {
		self::_init_db($dbc);
		global $_G;
		$_G['dbc'][$dbc]->set_config($_G['config']['db']);	
		$_G['dbc'][$dbc]->connect($dbc);		
		return $_G['dbc'][$dbc];
	}	

	public function G(){
		global $_G; 		
		$par = func_get_args();
		foreach($par as $key => $val){
			if($val != ''){$k[] =$val;}
		}
		switch(count($k)){
			case 0: $gav = $_G; break;
			case 1: $gav = $_G[$k[0]]; break;
			case 2: $gav = $_G[$k[0]][$k[1]]; break;
			case 3: $gav = $_G[$k[0]][$k[1]][$k[2]]; break;
			case 4: $gav = $_G[$k[0]][$k[1]][$k[2]][$k[3]]; break;
		}
		return $gav;
	}

	public function request($Param='',$is_trim=TRUE){		
		return getgpc($Param,$is_trim);
	}
		
	public function load($plugin_v){	
		foreach (is_loaded() as $varl => $class){
			$this->$varl = load_class($class);
		}
		$this->$plugin_v = load_class($plugin_v);
		return $this->$plugin_v;
	}
	
	public function M($name=''){
		$m_name = 'M_'.$name;
		if(file_exists(BASE_ROOT.'model/'.$m_name.'.php')){
			self::_init_db();
			include_once BASE_ROOT.'model/'.$m_name.'.php';
			$this->$m_name = new $m_name('model');
			return $this->$m_name;
		}
	}	 

	public function V(){
		global $smarty;
		return $smarty;	
	}	

	public function C($file_OR_folder=''){
		$path = BASE_ROOT.'controller/';
		if(strpos($file_OR_folder,'/')!==false){
			list($dir,$file) = explode('/',$file_OR_folder);
			include_once  $path.$dir.'/C_'.$file.'.php';
			$c_name = '/C_'.$file;
		}else{
			include_once  $path.'C_'.$file_OR_folder.'.php';
			$c_name = 'C_'.$file_OR_folder;
		}
		$this->$c_name = new $c_name('controller');
		return $this->$c_name;
	}	
}

class Core extends Adolf_core {}