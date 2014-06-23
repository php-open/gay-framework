<?php
!defined('IN_RUN') && exit('Access Denied');

	class MemSession {
		private static $handler=null;
		private static $lifetime=7200;
		private static $del_time=0;
		private static $time = null;
		const NS='sess_cds_';
		
		private static function init($handler){
			self::$handler=$handler;
			//self::$lifetime=ini_get('session.gc_maxlifetime');
			self::$time=time();
		}

		public static function start(Memcache $memcache){
			self::init($memcache);

			session_set_save_handler(
					array(__CLASS__, 'open'),
					array(__CLASS__, 'close'),
					array(__CLASS__, 'read'),
					array(__CLASS__, 'write'),
					array(__CLASS__, 'destroy'),
					array(__CLASS__, 'gc')
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
			$out=self::$handler->get(self::session_key($PHPSESSID));

			if($out===false || $out == null)
				return '';

			return $out;
		}

		public static function write($PHPSESSID, $data){
			
			$method=$data ? 'set' : 'replace';

			return self::$handler->$method(self::session_key($PHPSESSID), $data, MEMCACHE_COMPRESSED, self::$lifetime);
		}

		public static function destroy($PHPSESSID){
			return self::$handler->delete(self::session_key($PHPSESSID),self::$del_time);
		}

		public static function gc($lifetime){
			return true;
		}

		private static function session_key($PHPSESSID){
			$session_key=self::NS.$PHPSESSID;

			return $session_key;
		}	
	}
    include BASE_ROOT.'config.inc.php';

/*    $memcache = new Memcached();
      $memcache->addServer($_config['memcache']['server'], $_config['memcache']['port']);*/

	$memcache=new Memcache;
	$memcache->connect($_config['memcache']['server'], $_config['memcache']['port']);

	MemSession::start($memcache);
