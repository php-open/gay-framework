<?php !defined('IN_RUN') && exit('Access Denied');

class M_demo extends Core{
	
	public function getV(){
		//基础操作
		//DB::query("UPDATE honghaizi SET item_id=555,item_name=23424 WHERE id=5");
		//DB::query("INSERT INTO honghaizi(item_id,item_name)VALUES('1','fdf')");
		//DB::query("DELETE FROM honghaizi WHERE id=183");
		//DB::affected_rows();
		
		//默认配置数据库操作(default)
		//DB::update("testtable",array('name'=>'ggggggggg'),"id=1");		
		//$result = DB::fetch_all("SELECT * FROM testtable LIMIT 1");

		//自定义配置数据库操作(custom)		
		//$this->DBC('custom')->insert("shop",array('f1'=>'3388'));			
		//$result = $this->DBC('custom')->fetch_first("SELECT * FROM user"); //返回一行关联数组
			
		//$this->DBC('custom2')->delete("test3389","id=1");		
		//$result = $this->DBC('custom2')->fetch_all("SELECT * FROM test3389 LIMIT 1");  //返回多行关联数组
		
		//return $result;
	}
	
}