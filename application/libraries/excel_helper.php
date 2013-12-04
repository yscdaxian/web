<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Excel_helper{
	private $objReader=null;
	private $objWorksheet=null;
	private $columns=null;
	private $iter=null;
	private $highestRow=null;
	private $highestColumn=null;
	
	function __construct()
	{
		set_include_path(get_include_path().PATH_SEPARATOR .BASEPATH.'libraries/excel/PHPExcel');
  		require_once 'excel/PHPExcel.php';
		require_once 'excel/PHPExcel/IOFactory.php';
		require_once 'excel/PHPExcel/Reader/Excel5.php';	
	}
	
	public function load($inputFileName)
	{
		$this->iter=1;
		/*
		/**  Identify the type of $inputFileName  **/
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		/**  Create a new Reader of the type that has been identified  **/
		$this->objReader = PHPExcel_IOFactory::createReader($inputFileType);
		

		/**  Load $inputFileName to a PHPExcel Object  **/
		
		$this->objPHPExcel = $this->objReader->load($inputFileName);	
		$this->objWorksheet = $this->objPHPExcel->getActiveSheet();
		
		$this->highestRow = $this->objWorksheet->getHighestRow();
		$this->highestColumn = $this->objWorksheet->getHighestColumn(); 

		$this->columns=array();
	  	for($i=0;$i<$this->columns_count();++$i) 
	  	{
	      array_push($this->columns, $this->objWorksheet->getCellByColumnAndRow($i, 1)->getValue());
	      
	  	}
		$this->iter++;
		//print_r($this->columns);
	}
	public function get_columns()
	{
		return $this->columns;
	}
	public function columns_count()
	{
		return PHPExcel_Cell::columnIndexFromString($this->highestColumn);
	}
	
	public function rows_count()
	{
		return $this->highestRow;
	}
	public function next()
	{
		if($this->iter <= $this->rows_count())
		{   
			$data=array();
		  	for($i=0;$i<$this->columns_count();$i++)  
		  	{
		  		$data[$this->columns[$i]]=$this->objWorksheet->getCellByColumnAndRow($i, $this->iter)->getValue();
			}
			$this->iter++;	
			return $data;
		}
		else
		{
			return null;
		}
			
	}
	
	public function clear()
	{
		$this->objPHPExcel->disconnectWorksheets();
		unset($this->objPHPExcel);
		
	}
	
}