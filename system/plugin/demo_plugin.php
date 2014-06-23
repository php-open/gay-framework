<?php
!defined('IN_RUN') && exit('Access Denied');

//提示：插件文件名要与插件类名一致

class demo_plugin{
	
	var $pp='pd';
	
	public function pf($par=''){
		echo $par.'demo plugin information.';
	}
}