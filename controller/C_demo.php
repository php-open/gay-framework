<?php !defined('IN_RUN') && exit('Access Denied');

class C_demo extends Core{
		
	public function __construct(){	
		//构造内部demo模型
		//$this->M('demo');
	}
	
	public function index(){   //默认控制器
		//调用内部demo模型
		//$this->V()->assign('data1',$this->M_demo->getV());
		
		//调用外部test模型
		//$this->M('test');
		//$this->V()->assign('data2',$this->M_test->getV());
		
		//调用内部demo控制器
		//$this->display2();
		
		//调用外部test控制器
		//$this->C('test');
		//$this->C_test->custom();			
		
		//自定义log插件调用
		//$this->load('Log');
		//$this->Log->set('66');
		
		//自定义demo插件调用
		//$dp = $this->load('demo_plugin');
		//$this->demo_plugin->pf();
		//$dp->pf('demo---');
		
		//调用core内部全局变量
		//$this->G('cookie','cookie_lifetime');
		
		//调用memcache与redis(部署后,需在配置文件中开启)
		//$this->G('memcache')->set('c1','memcache info');
		//echo $this->G('memcache')->get('c1');	
		//$this->G('redis')->set('c2','redis info');	
		
		//url跳转[系统内置函数]
		//redirect('test/display');
		
		//基于smarty视图操作
		$this->V()->assign('v','Hello World!');		
		$this->V()->display('demo.html');		
	}
	
	public function display(){
		//GET 与 POST 值2种获取方式
		//1. echo $this->request('par');  //$this->request('par',false); 第二个参数默认为TRUE执行（trim）
		//2. echo getgpc('par');
		//COOKIE 获取方式
		//getcookie('par');		
		echo 'hello demo!'.'<br>';
		echo 'id: '.$this->request('id').'<br>';
		echo 'gid: '.$this->request('gid').'<br>';
	}

	public function display2(){
	}
}