<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dynamicui_model extends CI_Model{
	function __construct(){
		parent::__construct();
		$this->load->model('Dictionary_model');
		$this->load->model('Dictionary_tree_model');	
	}
	
	function getCustomSearchPanel(){
		$xml = simplexml_load_file($this->getLayoutFileName());
		$ret["elements"]=array();	
		foreach($xml->customSearchPanel->children() as $children){
			$row=array();
			foreach($children->children() as $item){
				$itemArray=array("dbtype"=>(string)$item->dbtype, "colspan"=>(int)$item->colspan,"name"=>(string)$item->name,"lspace"=>(int)$item->lspace,"type"=>(int)$item->type,"id"=>(string)$item->id,"value"=>array("defaultValue"=>'',"values"=>array()));	
				$itemArray["value"]["defaultValue"]=isset($defaultValues[(string)$item->dbfield])?$defaultValues[(string)$item->dbfield]:'';
				$itemArray["value"]["values"]=$this->getValues((int)$item->type,(string)$item->valuesource,(string)$item["value"]["defaultValue"]);
				array_push($row,$itemArray);
			}
			array_push($ret["elements"],$row);		
		}	
		return $ret;	
		
	}
		
	function getAllClientSearchData(){
		$xml = simplexml_load_file($this->getLayoutFileName());	
		foreach($xml->searchAllClientPanel->children() as $children){
				$ret[(string)$children->name]["id"]=(string)$children->id;
				$ret[(string)$children->name]["source"]=(string)$children->source;
				$ret[(string)$children->name]["text"]=(string)$children->text;
		}
		return $ret;
	}
	
	function getCustomClientTableHeader(){
		$xml = simplexml_load_file($this->getLayoutFileName());
		$ret=array();
		
		foreach($xml->customBodyPanel->children() as $children){
				$item["name"]=(string)$children->name;
				$item["align"]=(string)$children->align;
				$item["width"]=(string)$children->width;
				array_push($ret,$item);
		}
		return $ret;
	}
	
	function getCustomClientColumns(){
		$xml = simplexml_load_file($this->getLayoutFileName());
		$ret=array();	
		foreach($xml->customBodyPanel->children() as $children){		
				array_push($ret,(string)$children->dbfield);
		}
		return $ret;
	}
	
	function getCustomClientStatusCount(){
		$xml = simplexml_load_file($this->getLayoutFileName());
		$ret=array();	
		foreach($xml->customBodyPanel->children() as $children){		
				array_push($ret,(string)$children->dbfield);
		}
		return $ret;	
	}
	
	function getBussniessInfoTableData($defaultValues){
		$xml = simplexml_load_file($this->getLayoutFileName());	
		$items=$xml->baseInfoTable->row;
		$ret["elements"]=array();	
		foreach($xml->bussniessInfoTable->children() as $children){
			$row=array();
			foreach($children->children() as $item){
				$itemArray=array("colspan"=>(int)$item->colspan,"name"=>(string)$item->name,"lspace"=>(int)$item->lspace,"type"=>(int)$item->type,"width"=>(string)$item->width,"height"=>(string)$item->height,"id"=>(string)$item->id,"value"=>array("defaultValue"=>'',"values"=>array()));	
				$itemArray["value"]["defaultValue"]=isset($defaultValues[(string)$item->dbfield])?$defaultValues[(string)$item->dbfield]:'';
				$itemArray["value"]["values"]=$this->getValues((int)$item->type,(string)$item->valuesource,(string)$item["value"]["defaultValue"]);
				array_push($row,$itemArray);
			}
			array_push($ret["elements"],$row);		
		}	
		
		return $ret;	
	}
	function getBaseInfoTableData($defaultValues){		
		$xml = simplexml_load_file($this->getLayoutFileName());
		$items=$xml->baseInfoTable->row;
		$ret["elements"]=array();	
		foreach($xml->baseInfoTable->children() as $children){
			$row=array();
			foreach($children->children() as $item){
				$itemArray=array("colspan"=>(int)$item->colspan,"name"=>(string)$item->name,"lspace"=>(int)$item->lspace,"type"=>(int)$item->type,"width"=>(string)$item->width,"height"=>(string)$item->height,"id"=>(string)$item->id,"value"=>array("defaultValue"=>'',"values"=>array()));	
				$itemArray["value"]["defaultValue"]=isset($defaultValues[(string)$item->dbfield])?$defaultValues[(string)$item->dbfield]:'';
				$itemArray["value"]["values"]=$this->getValues((int)$item->type,(string)$item->valuesource,(string)$item["value"]["defaultValue"]);
				array_push($row,$itemArray);
			}
			array_push($ret["elements"],$row);		
		}		
		return $ret;	
	}
	
	function getImportTableMap(){
		$xml = simplexml_load_file($this->getLayoutFileName());
		$ret=array();
		foreach($xml->importDBMap->children() as $item){	
			array_push($ret,array("name"=>(string)$item->name,"dbfield"=>(string)$item->dbfield));
		}
		return $ret;
	}
	
	private function getLayoutFileName(){
		$xml = simplexml_load_file("./layoutxml/layout_config.xml");
		$defaultPath="./layoutxml/beijing-jiaoyu.xml";
		$defaultPath="./layoutxml/".$xml->config->layout_file;
		return $defaultPath;
	}
	
	private function getValues($type,$source,$default){
		if($type == 1){
			return array($default);
		}else if($type == 2 ){
			$ret=array();
			$data=$this->Dictionary_model->getSelectOption($source);
			array_push($ret,array("name_value"=>"未填写","name_text"=>"未填写"));		
			foreach($data as $key=>$value){
				array_push($ret,array("name_value"=>$value,"name_text"=>$value));
			}
			return $ret;
		}else if($type == 3){		
			$id=$this->Dictionary_tree_model->getItemByText($source);
			$data=array();		
			if($id && $id[0])
				$data=$this->Dictionary_tree_model->getTreeDataByPid($data,$id[0]['treenames_id']);		
			return $data;
			
		}else if($type == 4){
			return $this->Dictionary_model->getNormalDictionaryByType($source);
		}else if($type == 6){
			$data=array();
			$this->Dictionary_tree_model->getAreaData($data);
			return $data;
		}else {return array();}
		
	}
	
}