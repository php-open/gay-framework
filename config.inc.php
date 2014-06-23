<?php
/*********************************************
* @Name:   GAY - Framework v1.0
* @Type:   Config info
* @Author: Adolf
* @Email:  qingbin.zhang@baifendian.com
*********************************************/
$_config = array();

//------------------------------ CONFIG environment ---------------------------------#
$_config['system']['environment'] = 'development';	//【环境模式】 development testing production 
$_config['system']['default_controller'] = 'demo';	//【默认控制器】

//------------------------------ CONFIG custom open ---------------------------------#
$_config['custom']['check_function']['open'] = 0;	//【自定义函数库】
$_config['custom']['check_login']['open'] = 0;		//【自定义函数-全局检测登录状态】
$_config['custom']['check_roles']['open'] = 0;		//【自定义函数-全局检测用户权限】
$_config['custom']['check_url']['open'] = 0;		//【URL自定义路由】
$_config['custom']['check_xss']['open'] = 0;		//【XSS安全】    

//------------------------------ CONFIG db ------------------------------------------#
$_config['db']['default']['dbhost'] = 'localhost';	//【默认数据库配置】
$_config['db']['default']['dbuser'] = 'root';
$_config['db']['default']['dbpw'] = '';
$_config['db']['default']['dbname'] = 'test';
$_config['db']['default']['dbcharset'] = 'utf8';
$_config['db']['default']['pconnect'] = '0';
$_config['db']['default']['engine'] = 'mysql';

$_config['db']['custom']['dbhost'] = 'localhost:3388';	//【自定义数据库配置】
$_config['db']['custom']['dbuser'] = 'root';
$_config['db']['custom']['dbpw'] = '';
$_config['db']['custom']['dbname'] = 'test';
$_config['db']['custom']['dbcharset'] = 'utf8';
$_config['db']['custom']['pconnect'] = '0';
$_config['db']['custom']['engine'] = 'mysql';

//---------------------------- CONFIG smarty v3.1.12 --------------------------------#
$_config['smarty']['left_delimiter'] = "<?";
$_config['smarty']['right_delimiter'] = "?>";
$_config['smarty']['debugging'] = false;
$_config['smarty']['caching'] = false;
$_config['smarty']['cache_lifetime'] = 60;

//---------------------------- CONFIG cookie ----------------------------------------#
$_config['cookie']['cookiepre'] = 'c_';
$_config['cookie']['cookiedomain'] = '';
$_config['cookie']['cookiepath'] = '/';
$_config['cookie']['lifetime'] = 3600;

//---------------------------- CONFIG session ---------------------------------------#
$_config['session']['store'] = 'local';  //local mysql memcache
$_config['session']['lifetime'] = 3600;

//---------------------------- CONFIG memcache --------------------------------------#
$_config['memcache']['open'] = 0;
$_config['memcache']['server'] = 'localhost';
$_config['memcache']['port'] = 11211;
$_config['memcache']['pconnect'] = 1;
$_config['memcache']['timeout'] = 1;

//---------------------------- CONFIG redis -----------------------------------------#
$_config['redis']['open'] = 0;
$_config['redis']['server'] = 'localhost';
$_config['redis']['port'] = 6379;

// --------------------------- CONFIG plugin list------------------------------------#
$_config['log']['open'] = 1;
$_config['log']['log_threshold'] = 'error';
$_config['log']['log_path'] = 'system/log/';

//---------------------------- THE END ----------------------------------------------#