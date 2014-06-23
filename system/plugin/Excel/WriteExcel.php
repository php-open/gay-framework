<?php
require_once 'class/PHPExcel.php' ;
require_once 'class/PHPExcel/Writer/Excel5.php' ;
require_once 'class/PHPExcel/IOFactory.php';

class WriteExcel
{
	private $objExcel;
	private $objWriter;
	private $excel_name;
	
	public function WriteExcel($excel_name)
	{
		$this->excel_name = $excel_name;

		$this->objExcel = new PHPExcel();
		$this->objWriter = new PHPExcel_Writer_Excel5($this->objExcel);
		$this->objExcel->removeSheetByIndex(0);
	}
	
	/**
	 * 把推荐产生订单明细数据写入Excel文件
	 */
	public function writeExport2Data( $data )
	{	
		$this->objExcel->createSheet();
		$sheet_count = $this->objExcel->getSheetCount();
		$this->objExcel->setActiveSheetIndex($sheet_count-1);

		$active_sheet = $this->objExcel->getActiveSheet();
		
		$active_sheet->setTitle("推荐产生的订单明细");	// 设置sheet标题
		
		$columns =  array('A'=>'订单时间', 'B'=>'订单号', 'C'=>'商品名', 'D'=>'数量', 'E'=>'单价', 'F'=>'商品链接', 'G'=>'商品ID');
		$columns_fields =  array('A'=>'creation_time', 'B'=>'order_id', 'C'=>'item_name', 'D'=>'quantity', 'E'=>'price', 'F'=>'item_link', 'G'=>'item_id');
		
		// 设置列标题
		foreach( $columns as $column=>$name )
		{
			//$active_sheet->getColumnDimension($column);		
			$active_sheet->setCellValue($column . '1', $name);
		}
		
		$n = count($data);
		for( $i=1; $i<=$n; $i++ )
		{
			foreach( $columns_fields as $column=>$name )
			{
				$active_sheet->setCellValue($column.($i+1), $data[$i-1][$name]);
			}
		}
		
	}
	
	
	/**
	 * 把动销商品分析数据写入Excel文件
	 */
	public function writeExport4Data( $data )
	{	
		$this->objExcel->createSheet();
		$sheet_count = $this->objExcel->getSheetCount();
		$this->objExcel->setActiveSheetIndex($sheet_count-1);

		$active_sheet = $this->objExcel->getActiveSheet();
		
		$active_sheet->setTitle("动销商品分析");	// 设置sheet标题
		
		$columns =  array('A'=>'产生销售的动销商品总数', 'B'=>'纯推荐产生销售的动销商品数');
		
		// 设置列标题
		$active_sheet->setCellValue('A1','产生销售的动销商品总数');
		$active_sheet->setCellValue('B1','纯推荐产生销售的动销商品数');

		$active_sheet->setCellValue('A2', $data['total']);
		$active_sheet->setCellValue('B2', $data['rec_total']);
		
	}
	
	
	public function save()
	{
		if( $this->objExcel->getSheetCount() == 0 )
		{
			$this->objExcel->createSheet();
		}
		$this->objWriter->save($this->excel_name . '.xls');
	}
	
	/**
	 * 把excel输出到浏览器
	 * @return 
	 */
	public function writeToBrowser()
	{	
			ob_clean();

		header("Content-Type: application/force-download"); 
		header("Content-Type: application/download");  
		header("Cache-Control:public");
		header("Pragma:public");
		//header("Content-type:application/csv");
		header("Content-Type:application/vnd.ms-excel;");
		header("Accept-Ranges: bytes"); 
		header("Content-Transfer-Encoding: binary"); 
		header("Content-Disposition:attachment; filename=\"" . $this->excel_name . ".xls\"" );
		$this->objWriter->save('php://output');
	}
	
}
?>