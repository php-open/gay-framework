<?php
//实现多服务器共享 SESSION 数据
!defined('IN_RUN') && exit('Access Denied');

class Session {
	private static $handler=null;
	private static $ip=null;
	private static $time=null;
	private static $domain=null;
	private static $lifetime=null;
	
	private static function init($handler){
		global $_G;
		self::$handler=$handler;
		self::$ip = !empty($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : 'unknown';
		self::$time=time();
		self::$domain='';
		self::$lifetime=$_G['session']['lifetime'];
	}

	static function start(PDO $pdo){
		self::init($pdo);
		//不使用 GET/POST 变量方式
		ini_set('session.use_trans_sid',    0);
		//设置垃圾回收最大生存时间
		ini_set('session.gc_maxlifetime',   self::$lifetime);
		//使用 COOKIE 保存 SESSION ID 的方式
		ini_set('session.use_cookies',      1);
		ini_set('session.cookie_path',      '/');
		//多主机共享保存 SESSION ID 的 COOKIE
		ini_set('session.cookie_domain',    self::$domain);
		//将 session.save_handler 设置为 user，而不是默认的 files
		session_module_name('user');
		//定义 SESSION 各项操作所对应的方法			
		session_set_save_handler(
				array(__CLASS__,"open"),
				array(__CLASS__,"close"),
				array(__CLASS__,"read"),
				array(__CLASS__,"write"),
				array(__CLASS__,"destroy"),
				array(__CLASS__,"gc")
			);

		session_start();
	}

	public static function open($path, $name){
		return true;
	}

	public static function close(){
		return true;
	}
	
	public static function read($PHPSESSID){
		$sql="select PHPSESSID, update_time, client_ip, data from session where PHPSESSID= ?";

		$stmt=self::$handler->prepare($sql);

		$stmt->execute(array($PHPSESSID));
		//var_dump(self::$handler->errorInfo())
		if(!$result=$stmt->fetch(PDO::FETCH_ASSOC)){
			return '';
		}

		if( self::$ip  != $result["client_ip"]){
			self::destroy($PHPSESSID);
			return '';
		}

		if(($result["update_time"] + self::$lifetime) < self::$time ){
			self::destroy($PHPSESSID);
			return '';
		}

		return $result['data'];

	}

	public static function write($PHPSESSID, $data){
		$sql="select PHPSESSID, update_time, client_ip, data from session where PHPSESSID= ?";

		$stmt=self::$handler->prepare($sql);

		$stmt->execute(array($PHPSESSID));

		if($result=$stmt->fetchAll()){  //fetch(PDO::FETCH_ASSOC)
			$result = $result[0];
			if($result['data'] != $data || self::$time > ($result['update_time']+30)){
				$sql="update session set update_time = ?, data =? where PHPSESSID = ?";
				
				$stm=self::$handler->prepare($sql);
				$stm->execute(array(self::$time, $data, $PHPSESSID));
			
			}
		}else{
			if(!empty($data)){
				$sql="insert into session(PHPSESSID, update_time, client_ip, data) values(?,?,?,?)";

				$sth=self::$handler->prepare($sql);

				$sth->execute(array($PHPSESSID, self::$time, self::$ip, $data));
			}
		}

		return true;
	}

	public static function destroy($PHPSESSID){
		$sql="delete from session where PHPSESSID = ?";

		$stmt=self::$handler->prepare($sql);

		$stmt->execute(array($PHPSESSID));

		return true;
	}

	private static function gc($lifetime){
		$sql = "delete from session where update_time < ?";

		$stmt=self::$handler->prepare($sql);

		$stmt->execute(array(self::$time-$lifetime));

		return true;
	}	
}

	//require substr(dirname(__FILE__), 0, -12).'/config.inc.php';
	include BASE_ROOT.'config.inc.php';
	try{
		$pdo=new PDO("mysql:host=".$_config['db']['default']['dbhost'].";dbname=".$_config['db']['default']['dbname']."", $_config['db']['default']['dbuser'], $_config['db']['default']['dbpw']);
	}catch(PDOException $e){
		echo $e->getMessage();
	}
	
	Session::start($pdo);
?>