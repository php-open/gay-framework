[gay-framework] [v1.0]

    配置文件: /config.inc.php
    核心文件: /system/class/class_core.php
    URL支持路由访问方式:
    (1)HOST/?c=demo&f=display&id=3&gid=5
    (2)[Rewrite] HOST/demo/display/?id=3&gid=5
    (3)[Rewrite] HOST/demo/display/id-3/gid-5
    (4)[Rewrite] HOST/demo/display/id-3/gid-5.html
    (5)[Rewrite] HOST/demo-display-id-3.html
    URL自定义路由方式：
    配置文件：$_config['custom']['url']['open'] = 1
    [不推荐使用此模式，REST模式才使用]
    配置位置：/system/custom_URL.php
    路由demo：HOST/demo/1/display/5/?pid=4&gid=5&cid=6
    MVC 操作使用方法->详见位置：
    控制器：/controller/C_demo.php
    模型：/model/M_demo.php
    视图：/view/demo.html [smarty原生模版引擎]
    函数库->详见位置：
    系统: /system/function/func_core.php
    自定义：/system/function/func_custom.php
    GAY压力测试报告：
    以下是在同一台机器上用：ab -n 1000 -c 200 "http://test.gay.com" 进行的测试结果，View的输出"Hello Word"。
    大家不要关注具体数字，因为这个与机器性能有关。列出这个表格只是说明各框架之间的大概性能差异。
     框架名称 	          版本 	     每秒请求数(平均) 	    每次并发请求时间(所有并发)(ms)
     Cakephp 	         1.3.4 	         65.25 	                     2272.05
     Zend Framework      1.11 	         76.25 	                     2035.32
     CodeIgniter 	     2.1 	         171.25 	                 1167.16
     GAY 	             1.0 	         384.96 	                 520.63
