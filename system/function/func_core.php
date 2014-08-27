<?php !defined('IN_RUN') && exit('Access Denied');
/*********************************************
* @Name:   GAY - Framework v1.0
* @Type:   Function core
* @Author: Adolf
* @Email:  qingbin.zhang@baifendian.com
*********************************************/
/*--------------------------系统函数目录--------------------------*/

//-------------字符串,数组,验证,编码,数据处理---------------//
//检测，验证字符串---- [valid]
//字符串->带引号字符  'a,b,cd' => 'a','b','cd'---- [stringToChar]
//截取字符串指定位置后面所有内容 start($str,1,'&')---- [start]
//对象转数组---- [objectToArray]
//数组转对象---- [arrayToObject]
//xml转数组---- [xmlToArray]
//去重2个数组中相同的值---- [array_merge_unique]
//二维数组的根据不同键值来排序。---- [array_sort]
//生成随机数(1数字,0字母数字组合)---- [random]
//检测输入中是否含有错误字符---- [is_badword]
//检查id是否存在于数组中---- [check_in_array]
//对数据进行编码转换---- [array_iconv]
//字符截取 支持UTF8/GBK---- [str_cut]
//设置全局变量---- [setglobal]
//获取全局变量---- [getglobal]
//过滤html指定标签里面内容---- [get_html_content]
//纯文本输入---- [text]
//过滤JS脚本代码---- [cleanJs]
//统计字符长度---- [count_string_len]
//判断字符串是否存在---- [strexists]
//获取全局变量---- [G]
//GPC_反转义过滤(' " \ null)---- [dstripslashes]
//GPC_转义过滤(' " \ null)---- [daddslashes]
//截取中文字符,并去空格，换行，回车---- [csubstr]
//自定义JS_alert---- [jsalert]
//过滤___html---- [shtmlspecialchars]
//对用户的密码进行加密---- [md5_password]
//字符串_解密_加密---- [authcode]
//AES 加密算法---- [class][aes] 

//-------------HTTP处理---------------------//
//CURL处理---- [curl]
//下载---- [download]
//文件下载---- [file_down]++
//上传函数---- [upload]
//获取url中指定参数值---- [getUrlPar]
//跳转---- [redirect]
//返回指定数据格式与http状态---- [set_headers]
//定位---- [dheader]
//获取客户IP---- [_get_client_ip]
//机器人检测---- [checkrobot]
//设置cookie处理---- [dsetcookie]
//获取cookie---- [getcookie]
//获取GET POST---- [getgpc]

//-------------文本,文件---------------------//
//读取有格式的文件(txt等)---- [openFile]
//上传后 合并多个txt[结尾追加 可删除]---- [UniteText]
//删选数据工具---- [array2txt]
//文件是否存在，包括本地和远程文件 ---- [my_file_exists]
//返回字节数---- [return_bytes]
//删除smarty_class/view_c/目录下得所有文件svn目录不删---- [deleteDir]
//记录用户日志---- [userlog]
//计算文件大小---- [format_bytes]
//除数不为0转换---- [zto1]
//保留2位小数---- [toformat]

//-------------行为事件----------------------//
//翻页---- [page]
//翻页Ajax---- [page_ajax]
//Email发送---- [sendmail]
//返回时间---- [timeop]
//封装一个采集函数---- [getHtmlByCurl]

//-------------其件--------------------------//

/*--------------------------系统函数目录--------------------------*/

/*字符串->带引号字符  'a,b,cd' => 'a','b','cd'*/
function stringToChar($a){
	$val="'";	
	if(is_array($a)) $a = implode(',',$a);
	for($i = 0 ; $i < strlen($a); $i++) {
		$a{$i} != "," ? $val .= $a{$i} : $val .= "'".$a{$i}."'";
		if($i+1 == strlen($a)) $val .= "'";	
	}
	return $val;
}
//截取字符串指定位置后面所有内容 start($str,1,'&')
function start($str, $n, $find){
	$str_arr = explode($find,$str);
	for($i=$n; $i<count($str_arr); $i++){
		$str_arr2[] = $str_arr[$i];
	}
	return implode($find,$str_arr2);
}
//对象转数组
function objectToArray($d) {
	if (is_object($d)) {
		$d = get_object_vars($d); 
	}
	if (is_array($d)) {
		return array_map(__FUNCTION__, $d);
	}
	else {
		return $d;
	}
}
//数组转对象
function arrayToObject($d) {
	if (is_array($d)) {
		return (object) array_map(__FUNCTION__, $d);
	}
	else {
		return $d;
	}
}
//xml转数组
function xmlToArray($simple){
	$p = xml_parser_create();
	xml_parse_into_struct($p, $simple, $vals, $index);
	xml_parser_free($p);
	/*echo '<pre>';
	echo "Index array\n";
	print_r($index);
	echo '<pre>';
	echo "\nVals array\n";
	print_r($vals);	*/
	return $vals;
}

//去重2个数组中相同的值 $a1 = array(30,29,1,2,3), $a2 = array(30,3,2) => 29,1
function array_merge_unique($arr1=array(),$arr2=array()){
$arr3 = array_merge($arr1,$arr2);
$arr4 = array_unique($arr3);
//找出重复的值
$arr5 = array_diff_assoc($arr3,$arr4);
//从arr3中删除值等于
foreach($arr5 as $item){
	$k1 = array_search($item,$arr1);
	$k2 = array_search($item,$arr2);
	unset($arr1[$k1]);
	unset($arr2[$k2]);
}
$arr6 = array_merge($arr1,$arr2);
return $arr6;
}

/**
 * name:上传后 合并多个txt[结尾追加 可删除]
 * FPath: 多个文件上传路径
 * val: 多文件处理生成后路径
 * file_array: 多文件数组变量
 * isdel: 是否删除多个上传文件 
 */
function UniteText($FPath='',$val='',$file_array='',$isdel=0){
	@mkdir($FPath,0777);
	@mkdir($FPath.'val',0777);
	if(count($file_array)>0){
		for($i=1; $i<=count($file_array); $i++){
			if($file_array['upfile'.$i]['name'] != ''){
				move_uploaded_file($file_array['upfile'.$i]['tmp_name'],$FPath.$i.'.txt');
				@chmod($FPath.$i.'.txt',0777);
			}
		}
	
		$f2 = fopen($val,"wt");	
		for($i=1; $i<=count($file_array); $i++){
			if(file_exists($FPath.$i.'.txt')){
				$f1 = fopen($FPath.$i.'.txt',"r");
				$myFileContent = fread($f1,filesize($FPath.$i.'.txt'));
				fputs($f2,$myFileContent."\n"); 
			}		
		}
		@chmod($val,0777);
		if($isdel==1){ 
			for($i=1; $i<=count($file_array); $i++){
				@unlink($FPath.$i.'.txt');
			}
		}
	}	
} 
/**
 * name:二维数组的根据不同键值来排序。 第一个参数是二位数组名，第二个是依据键，第三个是升序还是降序，默认是升序
 */
function array_sort($arr,$keys,$type='asc'){ 
	$keysvalue = $new_array = array();
	foreach ($arr as $k=>$v){
		$keysvalue[$k] = $v[$keys];
	}
	if($type == 'asc'){
		asort($keysvalue);
	}else{
		arsort($keysvalue);
	}
	reset($keysvalue);
	foreach ($keysvalue as $k=>$v){
		$new_array[$k] = $arr[$k];
	}
	return $new_array; 
} 
/**
 * name:CURL处理
 * value:返回数据
 * @url:请求地址,@return_type:返回数据类型[json,xml],@ifpost:POST请求,@datafields:post请求参数
 * 测试服务器必须配HOST
 */
function curl($url, $return_type='', $ifpost = 0, $datafields = '', $cookiefile = '', $v = false) { 
	$header = array("Connection: Keep-Alive","Accept: text/html, application/xhtml+xml, */*", "Pragma: no-cache", "Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3","User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; WOW64; Trident/6.0)"); 
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_HEADER, $v); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
	$ifpost && curl_setopt($ch, CURLOPT_POST, $ifpost); 
	$ifpost && curl_setopt($ch, CURLOPT_POSTFIELDS, $datafields); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate'); 
	$cookiefile && curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiefile); 
	$cookiefile && curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiefile); 
	$r = curl_exec($ch); 
	if($r === false){ //请求失败
	   echo 'last error : ' . curl_error($ch);
	}	
	curl_close($ch); 
	return $return_type != '' ? ($return_type == 'json' ? objectToArray(json_decode($r)) : xmlToArray($r)): $r; 
}  
//-----------------------------------------------------------------------------------
//下载
function download($file=''){
	if (file_exists($file)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		ob_clean();
		flush();
		readfile($file);
		exit;
	}
}
/**
 * 文件下载
 * @param $filepath 文件路径
 * @param $filename 文件名称
 */

function file_down($filepath, $filename = '') {
	if(!$filename) $filename = basename($filepath);
	if(is_ie()) $filename = rawurlencode($filename);
	$filetype = fileext($filename);
	$filesize = sprintf("%u", filesize($filepath));
	if(ob_get_length() !== false) @ob_end_clean();
	header('Pragma: public');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: pre-check=0, post-check=0, max-age=0');
	header('Content-Transfer-Encoding: binary');
	header('Content-Encoding: none');
	header('Content-type: '.$filetype);
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header('Content-length: '.$filesize);
	readfile($filepath);
	exit;
}

/**
 * IE浏览器判断
 */
function is_ie() {
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) return false;
	if(strpos($useragent, 'msie ') !== false) return true;
	return false;
}

/**
 * 取得文件扩展
 *
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}
//-----------------------------------------------------------------------------------
/*
 * 上传函数 
 * $file 要上传的文件 如$_FILES['photo']
 * $projectid 上传针对的项目id  如$userid
 * $dir 上传到目录  如 user
 * $uptypes 上传类型，数组 array('jpg','png','gif')
 *
 * 返回数组：array('name'=>'','path'=>'','url'=>'','path'=>'','size'=>'')
 */
function upload($files,$projectid,$dir,$uptypes){
		
	if ($files['size']>0) {
		
		$menu2=intval($projectid/1000);
		
		$menu1=intval($menu2/1000);
		
		$path = $menu1.'/'.$menu2;
		
		$dest_dir='uploadfile/'.$dir.'/'.$path;
		
		createFolders($dest_dir);
		
		$arrType = explode('.',strtolower($files['name'])); //转小写一下
		
		$type = array_pop($arrType);
		
		if (in_array($type,$uptypes)) {
			
			$name = $projectid.'.'.$type;
			
			$dest=$dest_dir.'/'.$name;
			
			//先删除
			unlink($dest);
			//后上传
			move_uploaded_file($files['tmp_name'],mb_convert_encoding($dest,"gb2312","UTF-8"));
			
			chmod($dest, 0777);
			
			$filesize=filesize($dest);
			if(intval($filesize) > 0){
				return array(
					'name'=>$files['name'],
					'path'=>$path,
					'url'=>$path.'/'.$name,
					'type'=>$type,
					'size'=>$files['size'],
				);
			}else{
				return false;
			}
			
		}else{
			return false;
		}
	}
}
function createFolders($path)  {  
	//递归创建  
	if (!file_exists($path)){  
		createFolders(dirname($path));//取得最后一个文件夹的全路径返回开始的地方  
		mkdir($path, 0777);  
	}  
}

/**
 * name:读取有格式的文件(txt等)
 * value:返回数组
 */
function openFile($path){
	if (!file_exists($path)) {
		echo "unexists:$path";
		exit();
	}
	$result = array();
	//读取txt内容列表
	$file = fopen($path,"r");
	while(! feof($file)){
		$linestr = fgets($file);
		$linestr = preg_replace("/[\n\r]/i","",$linestr);
		//先对行做trim
		$linestr = trim($linestr);
		if($linestr==="")
		continue;	
		//支持"#"开头的行注释
		if($linestr[0] === "#"){
			continue;
		}
		//支持锁紧或者空格分隔
		$values = preg_split("/[\s\t]+/", $linestr); 
		$result[] = $values;
	}
	fclose($file);
	return $result;
}

/**
 * 删选数据工具
 * 正则：删选文字中含有其它内容的值
 * name:删选指定文件数据，移植删选数据生成到指定文件
 * param：1.删选文件2.生成文件
 */
function array2txt($old_txt='',$new_txt=''){
	$arr = openFile($old_txt);
	foreach($arr as $k=>$v){
		if(@eregi("[^\x80-\xff]",$v[2])){	
			$val[] = $arr[$k];
		}else{
			$val2[] = $arr[$k];	
		}
	}	
	//unix和linux下面是\n，windows下面是\r\n
	$str_new = '';
	foreach($val as $k2=>$v2){
		$str_new.=$v2[0]."\t".$v2[1]."\t".$v2[2]."\r\n";        
	}
	$kv=fopen($new_txt,"w+");
	fwrite($kv,$str_new);
	fclose($kv);	
	
	unlink($old_txt);
	$str_old = '';
	foreach($val2 as $k3=>$v3){
		@$str_old.=$v3[0]."\t".$v3[1]."\t".$v3[2]."\r\n";        
	}
	$kv2=fopen($old_txt,"w+");
	fwrite($kv2,$str_old);
	fclose($kv2);		
}

//文件是否存在，包括本地和远程文件 
function my_file_exists($file)
{
	if(preg_match('/^http:\/\//',$file)){
		//远程文件
		if(ini_get('allow_url_fopen')){
			if(@fopen($file,'r')) return true;
		}
		else{
			$parseurl=parse_url($file);
			$host=$parseurl['host'];
			$path=$parseurl['path'];
			$fp=fsockopen($host,80, $errno, $errstr, 10);
			if(!$fp)return false;
			fputs($fp,"GET {$path} HTTP/1.1 \r\nhost:{$host}\r\n\r\n");
			if(preg_match('/HTTP\/1.1 200/',fgets($fp,1024))) return true;
		}
		return false;
	}
	return file_exists($file);
}

//获取url中指定参数值 //$data['id']
function getUrlPar($str){
	$data = array();
	$parameter = explode('&',end(explode('?',$str)));
	foreach($parameter as $val){
		$tmp = explode('=',$val);
		$data[$tmp[0]] = $tmp[1];
	}
	return $data;
}
/**
 * 检测字符串
 *
 * @param string $key :检测的类型，如:"email" 或者是自定义的正则表达式,如:"/^a/"
 * @param string $string :要检测的字符串
 * 
 * @return boolean
 */
 /*
Email    => 是否为有效的Email地址
Numeric  => 是否为全是数字的字符串(可以是 "0" 开头的数字串)
QQ       => 腾讯QQ号
IdCard   => 身份证号码
China    => 是否为中文
Onchina  => 是否含有中文
Zip      => 邮政编码
Phone    => 固定电话(区号可有可无)
Mobile   => 手机号码
MobilePhone => 手机和固定电话
Url      => URL地址
Account  => 用户帐号(字母开头，由字母数字下划线组成，4-20字节)
ip       => IP地址
word     => 合法字符(字母，数字，下划线)
*/
function valid($key,$string){
	//定义的正则表达式
	$_regExp = array
	(
		'email'       => '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([_a-z0-9]+\.)+[a-z]{2,5}$/',
		'numeric'     => '/^[0-9]+$/',
		'zip'         => '/^[1-9]\d{5}$/',
		'phone'       => '/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/',
		'mobile'      => '/^((\(\d{2,3}\))|(\d{3}\-))?13\d{9}$/',
		'mobilephone' => '/(^[0-9]{3,4}\-[0-9]{3,8}$)|(^[0-9]{3,12}$)|(^\([0-9]{3,4}\)[0-9]{3,8}$)|(^0{0,1}13[0-9]{9}$)/',
		'qq'          => '/^[1-9]*[1-9][0-9]*$/',
		'china'       => '/^[\x7f-\xff]+$/',
		'onchina'     => '/[\x7f-\xff]/',
		'idcard'      => '/(^\d{15}$)|(^\d{17}([0-9]|X)$)/',
		'url'         => '/[a-zA-Z]+:\/\/[^\s]*/',
		'account'     => '/^[a-zA-Z][a-zA-Z0-9_]{3,19}$/',
		'ip'          => '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/',
		'word'        => '/[a-zA-Z0-9_]$/',
	);
	
	$key = strtolower($key);
	// 是定义的正则表达式
	if(array_key_exists($key,$_regExp)){
		return preg_match($_regExp[$key],$string);
	}else{
		// 直接正则来判断
		return preg_match($key,$string);
	}
}	
//------------------------------------------------------------------------------------------------------
/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function md5_password($password, $encrypt='') {
	$pwd = array();
	$pwd['encrypt'] =  $encrypt ? $encrypt : random(6);
	$pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
	return $encrypt ? $pwd['password'] : $pwd;
}

/**
* 生成随机数(1数字,0字母数字组合)
*
* @param int $length 随机数长度
* @param int $length 随机数长度
*/
function random($length, $numeric = 0) {
	PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
	$seed = base_convert(md5(print_r($_SERVER, 1).microtime()), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed[mt_rand(0, $max)];
	}
	return $hash;
}
//------------------------------------------------------------------------------------------------------

 /**
 * 检测输入中是否含有错误字符
 *
 * @param char $string 要检查的字符串名称
 * @return TRUE or FALSE
 */
function is_badword($string) {
	$badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
	foreach($badwords as $value){
		if(strpos($string, $value) !== FALSE) {
			return TRUE;
		}
	}
	return FALSE;
}

/**
 * 检查id是否存在于数组中
 *
 * @param $id
 * @param $ids
 * @param $s
 */
function check_in_array($id, $ids = '', $s = ',') {
	if(!$ids) return false;
	$ids = explode($s, $ids);
	return is_array($id) ? array_intersect($id, $ids) : in_array($id, $ids);
}

/**
 * 对数据进行编码转换
 * @param array/string $data       数组
 * @param string $input     需要转换的编码
 * @param string $output    转换后的编码
 */
function array_iconv($data, $input = 'gbk', $output = 'utf-8') {
	if (!is_array($data)) {
		return iconv($input, $output, $data);
	} else {
		foreach ($data as $key=>$val) {
			if(is_array($val)) {
				$data[$key] = array_iconv($val, $input, $output);
			} else {
				$data[$key] = iconv($input, $output, $val);
			}
		}
		return $data;
	}
}

/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...') {
	$strlen = strlen($string);
	if($strlen <= $length) return $string;
	$string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
	$strcut = '';
	if(strtolower(CHARSET) == 'utf-8') {
		$length = intval($length-strlen($dot)-$length/3);
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) {
				break;
			}
		}
		if($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
		$strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
	} else {
		$dotlen = strlen($dot);
		$maxi = $length - $dotlen - 1;
		$current_str = '';
		$search_arr = array('&',' ', '"', "'", '“', '”', '—', '<', '>', '·', '…','∵');
		$replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
		$search_flip = array_flip($search_arr);
		for ($i = 0; $i < $maxi; $i++) {
			$current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			if (in_array($current_str, $search_arr)) {
				$key = $search_flip[$current_str];
				$current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
			}
			$strcut .= $current_str;
		}
	}
	return $strcut.$dot;
}
//定位	
function dheader($string, $replace = true, $http_response_code = 0) {
	$string = str_replace(array("\r", "\n"), array('', ''), $string);
	if(empty($http_response_code) || phpversion() < '4.3' ) {
		@header($string, $replace);
	} else {
		@header($string, $replace, $http_response_code);
	}
	if(preg_match('/^\s*location:/is', $string)) {
		exit();
	}
}
//跳转
function redirect($uri = '', $method = 'location', $http_response_code = 302){
	switch($method)
	{
		case 'refresh'	: header("Refresh:0;url=".$uri);
			break;
		default			: header("Location: ".$uri, TRUE, $http_response_code);
			break;
	}
	exit;
}

/**
 * 返回指定数据格式与http状态
 */
function set_headers($code=200,$codeMessage="",$type="application/json"){
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0

	header('Content-Type: text/html; charset=utf-8');	
	header("HTTP/1.1 ".$code." ".$codeMessage);
	header("Content-Type:".$type);
}
	
//删除smarty_class/view_c/目录下得所有文件svn目录不删
function deleteDir($dirName){
        if(!is_dir($dirName)){
            @unlink($dirName);
            return false;
        }
        $handle = @opendir($dirName);
        while(($file = @readdir($handle)) !== false){
            if($file != '.' && $file != '..'){
                $dir = $dirName . '/' . $file;
				if($file !== '.svn'){        //svn文件略过删除
                is_dir($dir) ? $this->deleteDir($dir) : @unlink($dir);
				}
            }
        }
        closedir($handle);
       // return rmdir($dirName);
    }

function dmicrotime() {
	return array_sum(explode(' ', microtime()));
}

function setglobal($key , $value, $group = null) {
	global $_G;
	$k = explode('/', $group === null ? $key : $group.'/'.$key);
	switch (count($k)) {
		case 1: $_G[$k[0]] = $value; break;
		case 2: $_G[$k[0]][$k[1]] = $value; break;
		case 3: $_G[$k[0]][$k[1]][$k[2]] = $value; break;
		case 4: $_G[$k[0]][$k[1]][$k[2]][$k[3]] = $value; break;
		case 5: $_G[$k[0]][$k[1]][$k[2]][$k[3]][$k[4]] =$value; break;
	}
	return true;
}

function getglobal($key, $group = null) {
	global $_G;
	$k = explode('/', $group === null ? $key : $group.'/'.$key);
	switch (count($k)) {
		case 1: return isset($_G[$k[0]]) ? $_G[$k[0]] : null; break;
		case 2: return isset($_G[$k[0]][$k[1]]) ? $_G[$k[0]][$k[1]] : null; break;
		case 3: return isset($_G[$k[0]][$k[1]][$k[2]]) ? $_G[$k[0]][$k[1]][$k[2]] : null; break;
		case 4: return isset($_G[$k[0]][$k[1]][$k[2]][$k[3]]) ? $_G[$k[0]][$k[1]][$k[2]][$k[3]] : null; break;
		case 5: return isset($_G[$k[0]][$k[1]][$k[2]][$k[3]][$k[4]]) ? $_G[$k[0]][$k[1]][$k[2]][$k[3]][$k[4]] : null; break;
	}
	return null;
}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

//记录用户日志
function userlog(&$array,$userid){
	if (is_array($array)) {        
		foreach ($array as $key => $value) {             
			if (!is_array($value)) {    
				$data = "UserId:".$userid."\n";
				$data .= "IP:"._get_client_ip()."\n";
				$data .= "TIME:".date('Y-m-d H:i:s')."\n";
				$data .= "URL:".$_SERVER['REQUEST_URI']."\n";
				$data .= "DATA:".$data."\n";
				$data .= "--------------------------------------\n";
				logging(date('Ymd').'-'.$userid.'.txt',$data);
			} else {
				userlog($array[$key]);
			}        
		}
	} 
}
/*************************************************************************
page
==========================================================================
$reload :url 
$page :当前页
$tpages :总数页数
$adjacents :翻页宽度
*************************************************************************/
function page($reload, $page, $tpages, $adjacents) {
	
	$prevlabel = "&lsaquo; Prev";
	$nextlabel = "Next &rsaquo;";
	
	$out = "<div class=\"pagin\">\n";
	
	// previous
	if($page==1) {
		$out.= "<span>" . $prevlabel . "</span>\n";
	}
	elseif($page==2) {
		$out.= "<a href=\"" . $reload . "\" onfocus=\"this.blur()\" >" . $prevlabel . "</a>\n";
	}
	else {
		$out.= "<a href=\"" . $reload . "&amp;page=" . ($page-1) . "\" onfocus=\"this.blur()\" >" . $prevlabel . "</a>\n";
	}
	
	// first
	if($page>($adjacents+1)) {
		$out.= "<a href=\"" . $reload . "\" onfocus=\"this.blur()\" >1</a>\n";
	}
	
	// interval
	if($page>($adjacents+2)) {
		$out.= "...\n";
	}
	
	// pages
	$pmin = ($page>$adjacents) ? ($page-$adjacents) : 1;
	$pmax = ($page<($tpages-$adjacents)) ? ($page+$adjacents) : $tpages;
	for($i=$pmin; $i<=$pmax; $i++) {
		if($i==$page) {
			$out.= "<span class=\"current\">" . $i . "</span>\n";
		}
		elseif($i==1) {
			$out.= "<a href=\"" . $reload . "\" onfocus=\"this.blur()\" >" . $i . "</a>\n";
		}
		else {
			$out.= "<a href=\"" . $reload . "&amp;page=" . $i . "\" onfocus=\"this.blur()\" >" . $i . "</a>\n";
		}
	}
	
	// interval
	if($page<($tpages-$adjacents-1)) {
		$out.= "...\n";
	}
	
	// last
	if($page<($tpages-$adjacents)) {
		$out.= "<a href=\"" . $reload . "&amp;page=" . $tpages . "\" onfocus=\"this.blur()\" >" . $tpages . "</a>\n";
	}
	
	// next
	if($page<$tpages) {
		$out.= "<a href=\"" . $reload . "&amp;page=" . ($page+1) . "\" onfocus=\"this.blur()\" >" . $nextlabel . "</a>\n";
	}
	else {
		$out.= "<span>" . $nextlabel . "</span>\n";
	}
	
	$out.= "</div>";
	
	return $out;
}
/*page_ajax*/
function page_ajax($reload, $page, $tpages, $adjacents, $pid=0, $cid=0) {
	
	$prevlabel = "&lsaquo; Prev";
	$nextlabel = "Next &rsaquo;";
	
	$out = "<div class=\"pagin\">\n";
	
	// previous
	if($page==1) {
		$out.= "<span>" . $prevlabel . "</span>\n";
	}
	elseif($page==2) {
		$out.= "<a href=\"javascript:page_do(".$pid.','.$cid.','.'1'.");\" onfocus=\"this.blur()\" >" . $prevlabel . "</a>\n";
	}
	else {
		$out.= "<a href=\"javascript:page_do(".$pid.','.$cid.','.($page-1).");\" onfocus=\"this.blur()\" >" . $prevlabel . "</a>\n";
	}
	
	// first
	if($page>($adjacents+1)) {
		$out.= "<a href=\"javascript:page_do(".$pid.','.$cid.','.'1'.");\" onfocus=\"this.blur()\" >1</a>\n";
	}
	
	// interval
	if($page>($adjacents+2)) {
		$out.= "...\n";
	}
	
	// pages
	$pmin = ($page>$adjacents) ? ($page-$adjacents) : 1;
	$pmax = ($page<($tpages-$adjacents)) ? ($page+$adjacents) : $tpages;
	for($i=$pmin; $i<=$pmax; $i++) {
		if($i==$page) {
			$out.= "<span class=\"current\">" . $i . "</span>\n";
		}
		elseif($i==1) {
			$out.= "<a href=\"javascript:page_do(".$pid.','.$cid.','.$i.");\" onfocus=\"this.blur()\" >" . $i . "</a>\n";
		}
		else {
			
			$out.= "<a href=\"javascript:page_do(".$pid.','.$cid.','.$i.");\" onfocus=\"this.blur()\" >" . $i . "</a>\n";
			
		}
	}
	
	// interval
	if($page<($tpages-$adjacents-1)) {
		$out.= "...\n";
	}
	
	// last
	if($page<($tpages-$adjacents)) {
		$out.= "<a href=\"javascript:page_do(".$pid.','.$cid.','.$tpages.");\" onfocus=\"this.blur()\" >" . $tpages . "</a>\n";
	}
	
	// next
	if($page<$tpages) {
		$out.= "<a href=\"javascript:page_do(".$pid.','.$cid.','.($page+1).");\" onfocus=\"this.blur()\" >" . $nextlabel . "</a>\n";
	}
	else {
		$out.= "<span>" . $nextlabel . "</span>\n";
	}
	
	$out.= "</div>";
	
	return $out;
}
//page返回数据值
function page_list($page = 1,$prePageNum,$where){
	global $_G;
	$start_limit = !empty($page) ? ($page - 1) * $prePageNum : 0;
	$limit = $prePageNum ? "LIMIT $start_limit, $prePageNum" : '';
	$res = DB::query("SELECT * FROM ".$where." $limit");
	$mlogs = array();
	while($row = DB::fetch_array($res)){
		$mlogs[] = $row;
	}
	return $mlogs;
}
	
//page获取日志条数
function total_num($where){
	global $_G;	
	$res = DB::query("SELECT * FROM ".$where);
	$LogNum = DB::num_rows($res);
	return $LogNum;
}
//*********************************************************************************page_end/


function _get_client_ip() {
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$ip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}

function checkrobot($useragent = '') {
	static $kw_spiders = 'Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';
	static $kw_browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';

	$useragent = empty($useragent) ? $_SERVER['HTTP_USER_AGENT'] : $useragent;

	if(!strexists($useragent, 'http://') && preg_match("/($kw_browsers)/i", $useragent)) {
		return false;
	} elseif(preg_match("/($kw_spiders)/i", $useragent)) {
		return true;
	} else {
		return false;
	}
}

/**
 * name:过滤html指定标签里面内容
 * value:返回数据
 */
function get_html_content($url,$preg){
	$intro_info = curl($url);	
	$intro_info = preg_replace("/\s+/", " ", $intro_info); //过滤多余回车
	$intro_info = preg_replace("/<[ ]+/si","<",$intro_info); //过滤<__("<"号后面带空格)
	$intro_info = preg_replace("/<\!–.*?–>/si","",$intro_info); //注释
	//$intro_info = preg_replace("/<(\/?html.*?)>/si","",$intro_info); //过滤html标签
	$intro_info = preg_replace("/<(\/?br.*?)>/si","",$intro_info); //过滤br标签
	//$intro_info = preg_replace("/<(\/?strong.*?)>/si","",$intro_info); //过滤strong标签
	$intro_info = str_replace("　　","",$intro_info); //过滤空格标签
	//$intro_info = str_replace("&lt;P&gt;","",$intro_info); //过滤p标签
	//$intro_info = str_replace("&lt;/P&gt;","",$intro_info); //过滤p标签
	$intro_info = str_replace("<SPAN lang=EN-US>","",$intro_info); //过滤br标签
	$intro_info = str_replace("<o:p>","",$intro_info); //过滤br标签
	//$intro_info = str_replace("<P>","",$intro_info); //过滤<P>标签
	//$intro_info = str_replace("</P>","",$intro_info); //过滤<P>标签
		
	//(?s:.*)
	//$preg = "%<div class=\"Newn_Con\"> <h3>(.*?)</h3> <span>%si";
	//$preg='/<div class=\"Newn_Con\">\s+<h3>(\s+.*\s+)<\/h3>/';
	preg_match_all($preg, $intro_info, $arr);
	return $arr[1];
}

//纯文本输入
function text($text){
	$text=preg_replace('/\[.*?\]/is','',$text); 
	$text = cleanJs ( $text );
	//彻底过滤空格BY QINIAO
	$text = preg_replace('/\s(?=\s)/', '', $text);
	$text = preg_replace('/[\n\r\t]/', ' ', $text);
	$text = str_replace ( '  ', ' ', $text );
	$text = str_replace ( ' ', '', $text );
	$text = str_replace ( '&nbsp;', '', $text );
	$text = str_replace ( '&', '', $text );
	$text = str_replace ( '=', '', $text );
	$text = str_replace ( '-', '', $text );
	$text = str_replace ( '#', '', $text );
	$text = str_replace ( '%', '', $text );
	$text = str_replace ( '!', '', $text );
	$text = str_replace ( '@', '', $text );
	$text = str_replace ( '^', '', $text );
	$text = str_replace ( '*', '', $text );
	$text = str_replace ( 'amp;', '', $text );
	$text = str_replace ( 'position', '', $text );
	
	$text = strip_tags ( $text );
	$text = htmlspecialchars ( $text );
	$text = str_replace ( "'", "", $text );
	return $text;
}
//过滤脚本代码
function cleanJs($text){
	$text = trim ( $text );
	$text = stripslashes ( $text );
	//完全过滤注释
	$text = preg_replace ( '/<!--?.*-->/', '', $text ); 
	//完全过滤动态代码
	$text = preg_replace ( '/<\?|\?>/', '', $text );
	//完全过滤js
	$text = preg_replace ( '/<script?.*\/script>/', '', $text );
	//过滤多余html
	$text = preg_replace ( '/<\/?(html|head|meta|link|base|body|title|style|script|form|iframe|frame|frameset)[^><]*>/i', '', $text );
	//过滤on事件lang js
	while ( preg_match ( '/(<[^><]+)(lang|onfinish|onmouse|onexit|onerror|onclick|onkey|onload|onchange|onfocus|onblur)[^><]+/i', $text, $mat ) ){
		$text = str_replace ( $mat [0], $mat [1], $text );
	}
	while ( preg_match ( '/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat ) ){
		$text = str_replace ( $mat [0], $mat [1] . $mat [3], $text );
	}
	return $text;
}
//统计字符长度
function count_string_len($str) {
	//return (strlen($str)+mb_strlen($str,'utf-8'))/2; //开启了php_mbstring.dll扩展
	$name_len = strlen ( $str );
	$temp_len = 0;
	for($i = 0; $i < $name_len;) {
		if (strpos ( 'abcdefghijklmnopqrstvuwxyz0123456789', $str [$i] ) === false) {
			$i = $i + 3;
			$temp_len += 2;
		} else {
			$i = $i + 1;
			$temp_len += 1;
		}
	}
	return $temp_len;
}

//判断字符串是否存在
function strexists($string, $find) {
	return !(strpos($string, $find) === FALSE);
}

function getgpc($k, $is_trim=TRUE) {
	if(isset($_GET[$k])) {
		$var = &$_GET;
	} else {
		$var = &$_POST;
	}
	return isset($var[$k]) ? ($is_trim != TRUE ? daddslashes($var[$k]) : daddslashes(trim($var[$k]))) : NULL;
}

function G(){
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
	
//GPC_反转义过滤(' " \ null)
function dstripslashes($string) {
	if(empty($string)) return $string;
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dstripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}
//GPC_转义过滤(' " \ null)
function daddslashes($string, $force = 1) {
	if(is_array($string)) {
		$keys = array_keys($string);
		foreach($keys as $key) {
			$val = $string[$key];
			unset($string[$key]);
			$string[addslashes($key)] = daddslashes($val, $force);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}


/**
 * 截取中文字符,并去空格，换行，回车
 * demo： $str_des = csubstr(loseSpace(strip_tags($info['info'])),0,80);
 */
function csubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){  
   if(function_exists("mb_substr")){
       if(mb_strlen($str, $charset) <= $length) return $str;  
       $slice = mb_substr($str, $start, $length, $charset);  
   }else{  
       $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";  
 
       $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";  
 
       $re['gbk']          = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";  
 
       $re['big5']          = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";  
 
       preg_match_all($re[$charset], $str, $match);  
 
       if(count($match[0]) <= $length) return $str;  
 
       $slice = join("",array_slice($match[0], $start, $length));  
   }  
   if($suffix) return $slice."…";  
   return $slice;  
} 
function loseSpace($pcon){
 $pcon = preg_replace("/ /","",$pcon);
 $pcon = preg_replace("/&nbsp;/","",$pcon);
 $pcon = preg_replace("/　/","",$pcon);
 $pcon = preg_replace("/\r\n/","",$pcon);
 $pcon = str_replace(chr(13),"",$pcon);
 $pcon = str_replace(chr(10),"",$pcon);
 $pcon = str_replace(chr(9),"",$pcon);
 return $pcon;
}

/**
* 自定义JS_alert
*
* @param int $msg 提示信息
* @param string $url 跳转URL
*/
function jsalert($msg,$url) {
    return "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'><script language='javascript' charset='utf-8' type='text/javascript'>alert(\"{$msg}\");location.href='{$url}';</script>";
}


//cookie处理
function dsetcookie($var, $value = '', $life = '', $prefix = 1, $httponly = false) {
	global $_G;
	$life = $life == '' ? $_G['cookie']['cookie_lifetime'] : $life;
	$_G['cookie'][$var] = $value;
	$var = ($prefix ? $_G['cookie']['cookie_pre'] : '').$var;
	$_COOKIE[$var] = $var;

	if($value == '' || $life < 0) {
		$value = '';
		$life = -1;
	}

	$life = $life > 0 ? $_G['time'] + $life : ($life < 0 ? $_G['time'] - 31536000 : 0);
	$path = $httponly && phpversion() < '5.2.0' ? $_G['cookie']['cookie_path'].'; HttpOnly' : $_G['cookie']['cookie_path'];

	$secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
	if(phpversion() < '5.2.0') {
		setcookie($var, $value, $life, $path, $_G['cookie']['cookie_domain'], $secure);
	} else {
		setcookie($var, $value, $life, $path, $_G['cookie']['cookie_domain'], $secure, $httponly);
	}
}

function getcookie($key) {
	global $_G;
	return isset($_G['cookie'][$key]) ? $_G['cookie'][$key] : '';
}


/**
* Email发送
*
* @param string $to 收件人
* @param string $title 标题
* @param string $mail_body 内容
*/
function sendmail($to,$title,$mail_body,$handle='HTML') {

include('system/class/class_email/config.email.php');
include('system/class/class_email/mail.class.php');

$smtp = new smtp($mail_server,$mail_port,true,$mail_user,$mail_pass);
$smtp->debug = true;
//附件
//$smtp->setAttachments('BDTaskReport2012-04-01.xlsx');
$smtp->sendmail($to, $mail_name,$title, $mail_body, $handle);
}
	
/**
* 字符串_解密_加密
*
* @param string $to  明文 或 密文
* @param string $operation  //1表示它表示加密
* @param string $key  密匙
* @param string $expiry  密文有效期
*/
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key != '' ? $key : 'authkey');
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}



//返回时间
function timeop($time,$type="talk") {
    $ntime=time()-$time;
    if ($ntime<60) {
        return("刚才");
    } elseif ($ntime<3600) {
        return(intval($ntime/60)."分钟前");
    } elseif ($ntime<3600*24) {
        return(intval($ntime/3600)."小时前");
    } else {
        if ($type=="talk") {
            return(gmdate('m月d日 H:i',$time+8*3600));
        } else {
            return(gmdate('Y-m-d H:i',$time+8*3600));
        }

    }
}	 


//检查邮箱是否有效
function is_email($email) {
return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

//检查日期是否有效
function is_date($ymd, $sep='-')
{
	if(empty($ymd)) return FALSE;
	list($year, $month, $day) = explode($sep, $ymd);
	return checkdate($month, $day, $year);
}


//过滤___html
function shtmlspecialchars($string) {
if(is_array($string)) {
foreach($string as $key => $val) {
$string[$key] = shtmlspecialchars($val);
}
} else {
$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
}
return $string;
}


//数组转化字符串
function c_implode($array, $s = ',')
{
	if(empty($array)) return '';
	return is_array($array) ? implode($s, $array) : $array;
}

/*
 *封装一个采集函数
 *@ $url 网址
 *@ $proxy 代理
 *@ $timeout 跳出时间
 */
function getHtmlByCurl($url,$proxy,$timeout){
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_PROXY, $proxy);
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$file_contents = curl_exec($ch);
	return $file_contents;
}

//计算文件大小
function format_bytes($size) {    
	$units = array(' B', ' KB', ' MB', ' GB', ' TB');    
	for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;    
	return round($size, 2).$units[$i];
}

/**
* 除数不为0转换
*/
function zto1($v1){
	$v2 = $v1 == 0 ? 1 : 0;
	return $v2;
}
/**
* 保留2位小数
*/
function toformat($v3){
	return number_format($v3,2,'.','');
}
/** 
 * url扩展5种的url重写路由方式
 * (1)index.php?c=group&f=topic&topicid=1 //标准默认模式
 * (2)index.php/group/topic/topicid-1   //path_info模式
 * (3)group-topic-topicid-1.html   //rewrite模式1  引用控制器内模块方法（&） <a href="{url('user','dd&v4','id=1+act=er',3)}">
 * (4)group/topic/topicid-1   //rewrite模式2       引用控制器内模块方法（-） <a href="{url('user','dd-v4','id=1+act=er',4)}">
 * (5)group/topic/topicid-1.html   //rewrite模式2  引用控制器内模块【默认类】方法（-） <a href="{url('user','v3','id=1+act=er',5)}">
 */
function url($app,$ac='',$params_str='',$urlset=4){
	global $_G;
	$str=''; $url='';
	$params_v = array();
	if($params_str != ''){
		$params_v = explode('+',$params_str);
		foreach($params_v as $k=>$v){
			list($k2,$v2) = explode('=',$v);
			$params[$k2] = $v2;
		}
	}

	switch($urlset){
		case 1:
			if(count($params_v)>0){
				foreach($params as $k=>$v){
					$str .= '&'.$k.'='.$v;
				}
			}
			if($ac==''){
				$ac = '';
			}else{
				$ac='&fun='.$ac;
			}
			$url = 'index.php?c='.$app.$ac.$str;		
		break;	
		case 2:
			if($ac == ''){
				$url = $app;
			}elseif($params_str == ''){
				$url = $app.'/'.$ac;
			}else{
				if(count($params_v)>0){
					foreach($params as $k=>$v){
						$str .= '/'.$k.'-'.$v;
					}
				}
				if($ac==''){
					$ac='';
				}else{
					$ac='/'.$ac;
				}
				$url = 'index.php/'.$app.$ac.$str;		
			}
		break;	
		case 3:
			if($ac != '' && strpos($ac,'&')!==false){
				list($gc,$gfun)=explode('&',$ac);
				$ac = $gc.'&'.$gfun;
			}
			if(count($params_v)>0){					
				foreach($params as $k=>$v){
					$str .= '-'.$k.'-'.$v;
				}
			}
			if($ac==''){
				$ac='';
			}else{
				$ac='-'.$ac;
			}
			
			$page = strpos($str,'page');
			
			if($page){
				$url = $app.$ac.$str;
			}else{
				$url = $app.$ac.$str.'.html';
			}	
		break;
		case 4:
			if($ac != '' && strpos($ac,'-')!==false){
				list($gc,$gfun)=explode('-',$ac);
				$ac = $gc.'-'.$gfun;
			}
			if($ac == ''){
				$url = $app;
			}elseif($params_str == ''){
				$url = $app.'/'.$ac;
			}else{
				if(count($params_v)>0){			
					foreach($params as $k=>$v){
						$str .= '/'.$k.'-'.$v;
					}
				}
				if($ac==''){
					$ac='';
				}else{
					$ac='/'.$ac;
				}
				$url = $app.$ac.$str;		
			}
		break;
		case 5:
			if($ac != '' && strpos($ac,'-')!==false){
				list($gc,$gfun)=explode('-',$ac);
				$ac = $gc.'-'.$gfun;
			}
			if($ac == ''){
				$url = $app;
			}elseif($params_str == ''){
				$url = $app.'/'.$ac;
			}else{
				if(count($params_v)>0){			
					foreach($params as $k=>$v){
						$str .= '/'.$k.'-'.$v;
					}
				}
				if($ac==''){
					$ac='';
				}else{
					$ac='/'.$ac;
				}
				$url = $app.$ac.$str.'.html';		
			}
		break;			
	}			
	return $_G['url'].'/'.$url;
}

function is_loaded($class = '')
{
	static $_is_loaded = array();
	if ($class != '')
	{
		$_is_loaded[strtolower($class)] = $class;
	}
	return $_is_loaded;
}

function load_class($class, $directory = 'plugin')
{
	static $_classes = array();
	if (isset($_classes[$class]))
	{
		return $_classes[$class];
	}
	$name = FALSE;
	
	if(is_dir('system/'.$directory.'/'.$class)){
		if (file_exists('system/'.$directory.'/'.$class.'/'.$class.'.php')){
			$name = $class;
			if (class_exists($name) === FALSE){
				require('system/'.$directory.'/'.$class.'/'.$class.'.php');
			}
		}
	}else{
		if (file_exists('system/'.$directory.'/'.$class.'.php')){
			$name = $class;
			if (class_exists($name) === FALSE){
				require('system/'.$directory.'/'.$class.'.php');
			}
		}	
	}
	
	if ($name === FALSE)
	{
		exit('Not found class: '.$class);
	}
	is_loaded($class);
	$_classes[$class] = new $name();
	return $_classes[$class];
}
	
/*************************************************************************
Core处理
*************************************************************************/

/**
* 加载C层内部文件
*/
function LoadC($file_OR_folder='', $fun='') {
	global $_G;
	$gc = ''; $gfun='';
	$path = BASE_ROOT.'controller/';
	if(is_dir($path.$file_OR_folder)){
		if($fun != '' && strpos($fun,'-')!==false){
			list($gc,$gfun)=explode('-',$fun);
			include_once  $path.$file_OR_folder.'/C_'.$gc.'.php';
		}else{
			include_once  $path.$file_OR_folder.'/C_'.$file_OR_folder.'.php';
		}
	}else{
		include_once  $path.'C_'.$file_OR_folder.'.php';
	}
	$class = empty($gc) ? 'C_'.$file_OR_folder : 'C_'.$gc;
	$fun_v = empty($gfun) ? $fun : $gfun;
	$obj = new $class('controller');
	empty($fun) ? (method_exists($obj,'index') ? $obj->index() : '') : $obj->$fun_v();
	return $obj;
}
/**
* URL路由解析
*/
function RouteUrl(){
	global $_G;	
	$scriptName = explode('index.php',$_SERVER['SCRIPT_NAME']);
	$rurl = @substr($_SERVER['REQUEST_URI'], strlen($scriptName[0]));
	
	//自定义路由方式
	if($_G['config']['custom']['check_url']['open']){
		require_once BASE_ROOT.'system/custom_URL.php';
		if(strpos($rurl,'?') != false){list($r1,$r2) = explode('?',$rurl);}else{$r1=$rurl;}	
		$rev = explode('/',$r1); 
		$rcv = $custom_rest["$rev[0]"];	 
		array_shift($rev);
		
		foreach($rcv as $kr1=>$vr1){
			if(strpos($vr1,'?') != false){list($r11,$r22) = explode('?',$vr1);}else{$r11=$vr1;}	
			$rcc = explode('/',$r11);
			foreach($rcc as $k2=>$v2){
				if(strpos($v2,'c=') !== false){@list($v22,$cc) = explode('=',$v2);}
				if(strpos($v2,'f=') !== false){@list($v22,$ff) = explode('=',$v2);}
				if(strpos($v2,'$') !== false){$pkey = substr($v2,1); $par[] = $pkey;}
			}
			if(strpos($r1,$ff) !== false){
				$i=0;
				foreach($rev as $k=>$v){
					if($ff == $v){
						$_G['gp_c']=$cc;
						$_G['gp_f']=$v;
					}else{
						@$_GET[$par[$i]] = $v;
						$i++;	
					}
				}			
			}
		}		
	}
	
	elseif(strpos($rurl,'?')===false){
		//?c=demo&f=display&id=3&gid=5 
		if(preg_match('/index.php/i',$rurl)){
			$rurl = str_replace('index.php','',$rurl);
			$rurl = substr($rurl, 1);
			$params = $rurl;
		}else{
			$params = $rurl;
		}
		if($rurl){
			//HOST/demo/display/id-3/gid-5
			if(strpos($rurl,'.html')===false){
				$params = explode('/', $params);	
				foreach( $params as $p => $v ){
					if(strpos($v,'-')!==false){

						$par = explode('-',$v);
						$par_list = '';
						if(count($par) > 2){
							for($i=1; $i<count($par); $i++){
								$par_list .= $par[$i].'-';
							}
							$par_list = substr($par_list,0,-1);
							$v = $par[0].'$@@$'.$par_list;		
						}else{
							list($gc,$gfun)=explode('-',$v);
							$v = $gc.'$@@$'.$gfun;						
						}
						
					}		
					switch($p){
						case 0:$_G['gp_c']=$v;break;
						case 1:$_G['gp_f']=$v;break;
						default:
							$kv = explode('$@@$', $v);
							if(count($kv)>1){
								$_GET[$kv[0]] = $kv[1];  
							}else{
								$_GET['par'.$p] = $kv[0]; 
							}
							break;
					}
				}						
			}elseif(substr_count($rurl,'/') >= substr_count($rurl,'-')){
			//HOST/demo/display/id-3/gid-5.html 
				list($url_v,$html) = explode('.', $params);	
				$params = explode('/', $url_v);	
				foreach( $params as $p => $v ){
					if(strpos($v,'-')!==false){

						$par = explode('-',$v);
						$par_list = '';
						if(count($par) > 2){
							for($i=1; $i<count($par); $i++){
								$par_list .= $par[$i].'-';
							}
							$par_list = substr($par_list,0,-1);
							$v = $par[0].'$@@$'.$par_list;		
						}else{
							list($gc,$gfun)=explode('-',$v);
							$v = $gc.'$@@$'.$gfun;						
						}
						
					}				
					switch($p){
						case 0:$_G['gp_c']=$v;break;
						case 1:$_G['gp_f']=$v;break;
						default:
							$kv = explode('$@@$', $v);					
							if(count($kv)>1){
								$_GET[$kv[0]] = $kv[1];  
							}else{
								$_GET['par'.$p] = $kv[0]; 
							}
							break;
					}
				}									
			}else{
			//HOST/demo-display-id-3.html 
				$params = explode('.', $params);
				
				$params = explode('-', $params[0]);
			
				foreach( $params as $p => $v ){
					if(strpos($v,'&')!==false){
						list($gc,$gfun)=explode('&',$v);
						$v = $gc.'-'.$gfun;
					}					
					switch($p){
						case 0:$_G['gp_c']=$v;break;
						case 1:$_G['gp_f']=$v;break;
						default:
							
							if($v) $kv[] = $v;
							
							break;
					}
				}	
				$ck = count($kv)/2;
				
				if($ck>=2){
					$arrKv = array_chunk($kv,$ck);
					foreach($arrKv as $key=>$item){
						$_GET[$item[0]] = $item[1];
					}
				}elseif($ck==1){
					$_GET[$kv[0]] = $kv[1];
				}else{
					
				}					
				
			}
		}
	//HOST/demo/display/?id=3&gid=5 
	}elseif(strpos($rurl,'?',0) != 0 && strpos($rurl,'index.php') === false){
		$params = explode('/?', $rurl);
		$params1 = explode('/', $params[0]);
		$params2 = explode('&', $params[1]);
		$_G['gp_c']=$params1[0];
		@$_G['gp_f']=$params1[1];
		foreach( $params2 as $p => $v ){
			$kv = explode('=',$v);	
			$_GET[$kv[0]] = $kv[1];		
		}	
	}
	define('C',empty($_G['gp_c']) ? $_G['default_controller'] : $_G['gp_c']);
	define('FUN',empty($_G['gp_f']) ? '' : $_G['gp_f']);
	$_G['gp_c'] = C; $_G['gp_f'] = FUN;	
	
	if($_G['config']['custom']['check_login']['open']){Check_login();}	
	if($_G['config']['custom']['check_roles']['open']){Check_Roles();}		
	FUN == '' ? LoadC(C) : LoadC(C,FUN);	
}	



