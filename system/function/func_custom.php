<?php !defined('IN_RUN') && exit('Access Denied');
/*********************************************
* @Name:   GAY - Framework v1.0
* @Type:   Function custom
* @Author: Adolf
* @Email:  qingbin.zhang@baifendian.com
*********************************************/
/**
* 说明：
* 使用自定义函数须开启 $_config['custom']['check_function']['open'] = 1;  
* func_extend.php , func_dir.php 等都是任意添加的只是提供一些常用的方法库，
* 如果要使用其中的方法可直接把任意方法移植到当前func_custom.php文件下进行使用。
*/ 
//-----------------------------------------------------------------
/**
* 检测登录状态 DEMO
* 自定义操作方法
*/ 
function Check_login(){
	global $_G;
	if(!in_array($_G['gp_fun'],array('login','logout',''))){
		if(empty($_SESSION['uid'])){
			echo '<script type="text/javascript">window.location.href="/";</script>';
			exit;
		}
	}
}

/**
 * 检查登陆用户权限
 * 自定义操作方法
 */
function Check_Roles(){
	//if(!isset($_SESSION['user_name'])) return;
	//if($_SESSION['user_name']=='admin') return;
	if(!in_array(C.'/'.FUN,array('demo','demo/display'))){
		echo 'No access permissions page.<a href="javascript:window.history.back();">Back</a>';exit;
	}
}