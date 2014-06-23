<?php !defined('IN_RUN') && exit('Access Denied');

class C_test extends Core{

	public function index(){
		$this->V()->display('test.html');
	}
	
	public function display(){
		echo 'test_view<br>';
		echo $this->request('id').'<br>';
		echo $this->request('act').'<br>';
		//echo getgpc('act',FALSE);
	}
	
	public function custom(){
	
	}
}