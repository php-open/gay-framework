<?php /* Smarty version Smarty-3.1.12, created on 2013-11-12 21:26:43
         compiled from "D:\wamp\www\GAY\view\demo.html" */ ?>
<?php /*%%SmartyHeaderCode:29352821d16c401a1-66689988%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '705e3a64769aebfa533524d1e7e9326910e87c96' => 
    array (
      0 => 'D:\\wamp\\www\\GAY\\view\\demo.html',
      1 => 1384262801,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '29352821d16c401a1-66689988',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_52821d16cb0974_06727291',
  'variables' => 
  array (
    'css' => 0,
    'js' => 0,
    'v' => 0,
    'image' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_52821d16cb0974_06727291')) {function content_52821d16cb0974_06727291($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>GAY</title>
<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['css']->value;?>
/demo.css">
<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['js']->value;?>
/demo.js"></script>
</head>
<body>
<br>
<center>Welcome come to GAY Framework!
<br>
<br><br>
DEMO:
<br><hr>
<?php echo $_smarty_tpl->tpl_vars['v']->value;?>
<img src="<?php echo $_smarty_tpl->tpl_vars['image']->value;?>
/"><br>
</center>
GAY[v1.0] <br>
----------------------------------------------------  <br>
配置文件: /config.inc.php<br>
核心文件: /system/class/class_core.php<br>
----------------------------------------------------  <br>
URL支持路由访问方式:<br>
(1)index.php?c=demo&f=display&id=3&gid=5  <br>
(2)[Rewrite] HOST/demo/display/?id=3&gid=5  <br> 
(3)[Rewrite] HOST/demo/display/id-3/gid-5<br>
(4)[Rewrite] HOST/demo/display/id-3/gid-5.html <br>     
(5)[Rewrite] HOST/demo-display-id-3.html <br>
URL自定义路由方式：<br>
配置文件：$_config['custom']['url']['open'] = 1 <br>
#[不推荐使用，REST模式才使用]<br>
配置位置：/system/custom_URL.php <br>
路由demo：HOST/demo/1/2/display/?pid=4&gid=5&cid=6<br>
----------------------------------------------------  <br>
MVC 操作使用方法->详见位置： <br>
控制器：/controller/C_demo.php <br>
模型：/model/M_demo.php <br>
视图：/view/demo.html [smarty原生模版引擎]<br>
---------------------------------------------------- <br>
函数库->详见位置： <br>
系统: /system/function/func_core.php <br>
自定义：/system/function/func_custom.php <br>
---------------------------------------------------- <br> <br>
GAY压力测试报告：
<table width="50%" cellspacing="1" cellpadding="1" border="1">
    <tbody >
        <tr>
            <td colspan="5">
            <p>&nbsp;&nbsp;&nbsp; 以下是在同一台机器上用：ab -n 1000 -c 200 "http://test.gay.com" 进行的测试结果，View的输出"Hello Word"。</p>
            <p>大家不要关注具体数字，因为这个与机器性能有关。列出这个表格只是说明各框架之间的大概性能差异。</p>
            </td>
        </tr>
        <tr>
            <td>&nbsp;框架名称</td>
            <td>&nbsp;版本</td>
            <td>&nbsp;每秒请求数(平均)</td>
            <td>&nbsp;每次并发请求时间(所有并发)(ms)</td>
        </tr>
        <tr>
            <td>&nbsp;CodeIgniter</td>
            <td>&nbsp;2.1</td>
            <td>&nbsp;171.25</td>
            <td>&nbsp;1167.101</td>
        </tr>
        <tr>
            <td>&nbsp;GAY</td>
            <td>&nbsp;1.0</td>
            <td>&nbsp;384.96</td>
            <td>&nbsp;520.632</td>
        </tr>
    </tbody>
</table>
</body>
</html>
<?php }} ?>