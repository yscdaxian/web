<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Createdatabase extends CI_Controller
{
	public function __construct(){
		parent::__construct();
		$this->load->dbforge();
		$this->load->library('DataTabes_helper');
		$this->load->library('Utility_func');
		$this->load->library('firephp');
	}
	
	private function clearDbField(){
		$defaultDbField=array('client_id','client_iswaitcom','client_agent','client_modify_time','client_ctime','client_excel_id','client_creater','client_batch_number','client_address','client_cell_phone','client_phone','client_sex');
		$fields = $this->db->list_fields('clients');
		echo "begin";
		foreach ($fields as $field){
		 
		   if(!in_array($field,$defaultDbField)){
		   		$this->dbforge->drop_column('clients', $field);
				$this->dbforge->drop_column('clients_tmp', $field);
				echo "drop".$field."<br>";
				
		   }
		} 
	}
	
	private function addDefaultDbField(){
		
	}
	private function addDbFieldByDynamicUi(){
		
	  	$this->load->library('Dynamicui',array("agentId"=>""));
	  	$dyModelName=$this->dynamicui->getDynamicuiModel();
		$this->load->model($dyModelName);
		$allDbField=$this->$dyModelName->getAllDbFileds();
		foreach($allDbField as $dbField){
			echo $dbField."<br>";
			if ($this->db->field_exists($dbField, 'clients')){
					
			}else{
				$addFieldStr="$dbField varchar(255) CHARACTER SET utf8  COLLATE utf8_general_ci ";
				
				$fields = array(
                        $dbField => array(
                                                 'type' => 'VARCHAR',
                                                 'constraint' => '255',
												 'null'=>true

                                          ),
                );
			
				$this->dbforge->add_column('clients', $fields);	
				$this->dbforge->add_column('clients_tmp', $fields);	
				echo "add".$dbField."<br>";
			} 
		}	
	}
	
	public function create(){	
		$this->addDefaultDbField();
		$this->addDbFieldByDynamicUi();
		
	}
	
	public function clear(){
			$this->clearDbField();
	}
	
	public function sampleAddField(){
		$this->dbforge->add_field("label varchar(100) NOT NULL DEFAULT 'default label'");
	}
	
}