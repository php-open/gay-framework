<?php
/**
 * 配置规则
 * HOST后第一个位置必须为<控制器>
 * $id,$gid为<变量>位置随意必须在<？号>前与<控制器>后
 * display为<方法>位置与<变量>要求相同
 * <?号>后面随意写get参数值
 */
//DEMO
//HOST/demo/$id/display/$gid/?pid=4&gid=5&cid=6
$custom_rest['demo'][] = 'c=demo/$id/f=display/$gid/';
//HOST/user/$id/get/?uid=6
//$custom_rest['user'][] = 'c=user/$id/f=get/?pid=&gid=&cid';