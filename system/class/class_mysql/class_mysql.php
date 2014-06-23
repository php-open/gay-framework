<?php !defined('IN_RUN') && exit('Access Denied');
/********************************
*  Type:    Mysql_class
*  Author:  Adolf
*********************************/

class db_mysql {
	var $version = '';
	var $querynum = 0;
	var $slaveid = 0;
	var $curlink;
	var $link = array();
	var $config = array();
	var $sqldebug = array();
	var $map = array();

	function db_mysql($config = array()) {
		if(!empty($config)) {
			$this->set_config($config);
		}
	}

	function set_config($config) {
		$this->config = &$config;
		if(!empty($this->config['map'])) {
			$this->map = $this->config['map'];
		}
	}

	function connect($serverid = 'default') {

		if(empty($this->config) || empty($this->config[$serverid])) {
		    $this->halt('config_db_not_found');
		}
			$this->link[$serverid] = $this->_dbconnect(
				$this->config[$serverid]['dbhost'],
				$this->config[$serverid]['dbuser'],
				$this->config[$serverid]['dbpw'],
				$this->config[$serverid]['dbcharset'],
				$this->config[$serverid]['dbname'],
				$this->config[$serverid]['pconnect']
				);	
		$this->curlink = $this->link[$serverid];
	}	

	function _dbconnect($dbhost, $dbuser, $dbpw, $dbcharset, $dbname, $pconnect) {
		$link = null;
		$func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
		if(!$link = @$func($dbhost, $dbuser, $dbpw, 1)) {
			$this->halt('notconnect', $this->errno());
		} else {
			$this->curlink = $link;
			if($this->version() > '4.1') {
				$dbcharset = $dbcharset ? $dbcharset : $this->config['default']['dbcharset'];
				$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
				$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && mysql_query("SET $serverset", $link);
			}
			$dbname && @mysql_select_db($dbname, $link);
		}
		return $link;
	}

	function table_name($tablename) {
		if(!empty($this->map) && !empty($this->map[$tablename])) {
			$id = $this->map[$tablename];
			if(!$this->link[$id]) {
				$this->connect($id);
			}
			$this->curlink = $this->link[$id];
		} else {
			$this->curlink = $this->link['default'];
		}
		return $tablename;
	}

	function select_db($dbname) {
		return mysql_select_db($dbname, $this->curlink);
	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}

	function fetch_first($sql) {
		return $this->fetch_array($this->query($sql));
	}
	
	function fetch_all($sql, $keyfield = '') {
		$data = array();
		$query = $this->query($sql);
		while ($row = $this->fetch_array($query)) {
			if ($keyfield && isset($row[$keyfield])) {
				$data[$row[$keyfield]] = $row;
			} else {
				$data[] = $row;
			}
		}
		$this->free_result($query);
		return $data;
	}
	
	function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}

	function query($sql, $type = '') {

		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ?
		'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql, $this->curlink))) {
			if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
				$this->connect();
				return $this->query($sql, 'RETRY'.$type);
			}
			if($type != 'SILENT' && substr($type, 5) != 'SILENT') {
				$this->halt($this->error(), $this->errno(), $sql);
			}
		}

		$this->querynum++;
		return $query;
	}

	function affected_rows() {
		return mysql_affected_rows($this->curlink);
	}

	function error() {
		return (($this->curlink) ? mysql_error($this->curlink) : mysql_error());
	}

	function errno() {
		return intval(($this->curlink) ? mysql_errno($this->curlink) : mysql_errno());
	}

	function result($query, $row = 0) {
		$query = @mysql_result($query, $row);
		return $query;
	}

	function num_rows($query) {
		$query = mysql_num_rows($query);
		return $query;
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}

	function free_result($query) {
		return mysql_free_result($query);
	}

	function insert_id() {
		return ($id = mysql_insert_id($this->curlink)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	function fetch_row($query) {
		$query = mysql_fetch_row($query);
		return $query;
	}

	function fetch_fields($query) {
		return mysql_fetch_field($query);
	}

	function version() {
		if(empty($this->version)) {
			$this->version = mysql_get_server_info($this->curlink);
		}
		return $this->version;
	}

	function close() {
		return mysql_close($this->curlink);
	}

    function lock($tblname,$op="WRITE") {
        if(mysql_query("lock tables ".$tblname." ".$op)) return true;
        else return false;
    }

    function unlock()
        {if(mysql_query("unlock tables")) return true; else return false;}


	function delete($table, $condition, $limit = 0, $unbuffered = true) {
		if(empty($condition)) {
			$where = '1';
		} elseif(is_array($condition)) {
			$where = DB::implode_field_value($condition, ' AND ');
		} else {
			$where = $condition;
		}
		$sql = "DELETE FROM ".DB::table($table)." WHERE $where ".($limit ? "LIMIT $limit" : '');
		return $this->query($sql, ($unbuffered ? 'UNBUFFERED' : ''));
	}

	function insert($table, $data, $return_insert_id = false, $replace = false, $silent = false) {

		$sql = DB::implode_field_value($data);

		$cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';

		$table = DB::table($table);
		$silent = $silent ? 'SILENT' : '';

		$return = $this->query("$cmd $table SET $sql", $silent);

		return $return_insert_id ? DB::insert_id() : $return;

	}

	function update($table, $data, $condition, $unbuffered = false, $low_priority = false) {
		$sql = DB::implode_field_value($data);
		$cmd = "UPDATE ".($low_priority ? 'LOW_PRIORITY' : '');
		$table = DB::table($table);
		$where = '';
		if(empty($condition)) {
			$where = '1';
		} elseif(is_array($condition)) {
			$where = DB::implode_field_value($condition, ' AND ');
		} else {
			$where = $condition;
		}
		$res = $this->query("$cmd $table SET $sql WHERE $where", $unbuffered ? 'UNBUFFERED' : '');
		return $res;
	}

	
	function halt($message = '', $sql = '') {
		define('CACHE_FORBIDDEN', TRUE);

		$timestamp = time();
		$errmsg = '';
		
		$dberror = mysql_error();
		$dberrno = mysql_errno();
		
		global $_G;
		if($_G['config']['log']['open'] && $_G['config']['system']['environment'] == 'production'){
			$lm = Core::load('Log');
			$lm->set('MySQL # Info:'.$message.' --- SQL:'.htmlspecialchars($sql).' --- Error:'.$dberror);
		}
				
		if($_G['config']['system']['environment'] != 'production'){
			if($dberrno == 1114) {
				exit();
			} else {
				if($message) {
					$errmsg = "<b>Project Info</b>: $message\n<br><br>";
				}
				$errmsg .= "<b>Time</b>: ".date("Y-m-d  /  H:i:s", time())."\n<br><br>";
				$errmsg .= "<b>Script</b>: ".$_SERVER['PHP_SELF']."\n<br><br>";
				if($sql) {
					$errmsg .= "<b>SQL</b>: ".htmlspecialchars($sql)."\n<br><br>";
				}
				$errmsg .= "<b>Error</b>:  $dberror\n<br><br>";
				$errmsg .= "<b>Errno.</b>:  $dberrno";
				echo "<p style=\"font-family: Verdana, Tahoma; font-size: 11px; background: #FFFFFF;\">";
				echo $errmsg;
				echo '</p>';
				exit();
			}
		}
	}	

}


/*--------------------------------------------------------------------------------------*/
/* Class_DB
/*--------------------------------------------------------------------------------------*/

class DB
{

	public static function table($table) {
		return DB::_execute('table_name', $table);
	}

	public static function implode_field_value($array, $glue = ',') {
		$sql = $comma = '';
		foreach ($array as $k => $v) {
			$sql .= $comma."`$k`='$v'";
			$comma = $glue;
		}
		return $sql;
	}

	public static function delete($table, $condition, $limit = 0, $unbuffered = true) {
		return DB::_execute('delete',$table, $condition, $limit, $unbuffered);
	}
	
	public static function insert($table, $data, $return_insert_id = false, $replace = false, $silent = false) {
		return DB::_execute('insert',$table, $data, $return_insert_id, $replace, $silent);
	}
	
	public static function update($table, $data, $condition, $unbuffered = false, $low_priority = false) {
		return DB::_execute('update',$table, $data, $condition, $unbuffered, $low_priority);
	}
	
	public static function insert_id() {
		return DB::_execute('insert_id');
	}

	public static function fetch_array($resourceid, $type = MYSQL_ASSOC) {
		return DB::_execute('fetch_array', $resourceid, $type);
	}

	public static function fetch_first($sql) {
		DB::checkquery($sql);
		return DB::_execute('fetch_first', $sql);
	}

	public static function fetch_all($sql, $keyfield = '') {
		DB::checkquery($sql);
		return DB::_execute('fetch_all', $sql, $keyfield);
	}
	
	public static function result($resourceid, $row = 0) {
		return DB::_execute('result', $resourceid, $row);
	}

	public static function result_first($sql) {
		DB::checkquery($sql);
		return DB::_execute('result_first', $sql);
	}

	public static function query($sql, $type = '') {
		DB::checkquery($sql);
		return DB::_execute('query', $sql, $type);
	}

	public static function num_rows($resourceid) {
		return DB::_execute('num_rows', $resourceid);
	}

	public static function affected_rows() {
		return DB::_execute('affected_rows');
	}

	public static function free_result($query) {
		return DB::_execute('free_result', $query);
	}

	public static function error() {
		return DB::_execute('error');
	}

	public static function errno() {
		return DB::_execute('errno');
	}


//& object
	public static function _execute($cmd , $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = '', $arg5 = '') {
		static $db;
		if(empty($db)) $db = & DB::object();
		$res = $db->$cmd($arg1, $arg2, $arg3, $arg4, $arg5);
		return $res;
	}

	public static function &object($dbclass = 'db_mysql') {
		static $db;
		if(empty($db)) $db = new $dbclass();
		return $db;
	}

	//SQL安全检测，防止SQL注入攻击
	public static function checkquery($sql) {
		$checkcmd = array('SELECT', 'UPDATE', 'INSERT', 'REPLACE', 'DELETE');
			$cmd = trim(strtoupper(substr($sql, 0, strpos($sql, ' '))));
			if(in_array($cmd, $checkcmd)) {
				$test = DB::_do_query_safe($sql);
				if($test < 1) DB::_execute('halt', 'security_error', $sql);
			}
		
		return true;
	}

	public static function _do_query_safe($sql) {

		$sql = str_replace(array('\\\\', '\\\'', '\\"', '\'\''), '', $sql);
		$mark = $clean = '';
		if(strpos($sql, '/') === false && strpos($sql, '#') === false && strpos($sql, '-- ') === false) {
			$clean = preg_replace("/'(.+?)'/s", '', $sql);
		} else {
			$len = strlen($sql);
			$mark = $clean = '';
			for ($i = 0; $i <$len; $i++) {
				$str = $sql[$i];
				switch ($str) {
					case '\'':
						if(!$mark) {
							$mark = '\'';
							$clean .= $str;
						} elseif ($mark == '\'') {
							$mark = '';
						}
						break;
					case '/':
						if(empty($mark) && $sql[$i+1] == '*') {
							$mark = '/*';
							$clean .= $mark;
							$i++;
						} elseif($mark == '/*' && $sql[$i -1] == '*') {
							$mark = '';
							$clean .= '*';
						}
						break;
					case '#':
						if(empty($mark)) {
							$mark = $str;
							$clean .= $str;
						}
						break;
					case "\n":
						if($mark == '#' || $mark == '--') {
							$mark = '';
						}
						break;
					case '-':
						if(empty($mark)&& substr($sql, $i, 3) == '-- ') {
							$mark = '-- ';
							$clean .= $mark;
						}
						break;

					default:

						break;
				}
				$clean .= $mark ? '' : $str;
			}
		}

		$clean = preg_replace("/[^a-z0-9_\-\(\)#\*\/\"]+/is", "", strtolower($clean));


        $dfunction = array('load_file','hex','substring','if','ord','char');
        $daction = array('intooutfile','intodumpfile');
        $dnote = array('/*','*/','#','--','"');
        $dlikehex = 1;
        $afullnote= 1;

		if($afullnote) {
			$clean = str_replace('/**/','',$clean);
		}

		if(is_array($dfunction)) {
			foreach($dfunction as $fun) {
				if(strpos($clean, $fun.'(') !== false) return '-1';
			}
		}

		if(is_array($daction)) {
			foreach($daction as $action) {
				if(strpos($clean,$action) !== false) return '-3';
			}
		}

		if($dlikehex && strpos($clean, 'like0x')) {
			return '-2';
		}

		if(is_array($dnote)) {
			foreach($dnote as $note) {
				if(strpos($clean,$note) !== false) return '-4';
			}
		}

		return 1;

	}
}