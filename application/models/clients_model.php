<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Clients_model extends CI_Model
{
	function __construct(){
		parent::__construct();
	
	}
	function get($columns,$limit, $offset, $sort_by, $sort_order)
	{
		$fields=array();
		foreach($columns as $key=>$value){
			array_push($fields, $value);
		}
		$sort_by=in_array($sort_by,$fields)?$sort_by:'name';
		$q=$this->db->select(implode(",", $fields).',client_id')->from("clients")->limit($limit,$offset)->order_by($sort_by,$sort_order);
		$ret['results']= $q->get()->result();
		$q=$this->db->select("count(*) as count")->from('clients');
		$row=$q->get()->result();
		$ret['total_num']=$row[0]->count;
		return $ret;
	}
	
	
	function getData($query){
		return $this->db->query($query);
	}
	
	function getby_id($id)
	{
		return $this->db->select('*')->from('clients')->where('client_id',$id)->get()->result_array();
	}
	function update($id,$data)
	{
		return $this->db->update('clients', $data, array('client_id'=>$id));
	}
	function insert($item)
	{
		$this->db->insert('clients',$item);
		return $this->db->insert_id();
	}
	function exsit($item)
	{
		return false;
	}

	function filter(&$data)
	{
		foreach($data as $key=>$value)
		{
			if($key == 'client_name')
				$ret[$key]=preg_replace("/[^\x{4E00}-\x{9FFF}]+/u","", $value);
			else if($key == 'client_cell_phone' || $key == 'client_phone')
				$ret[$key]=preg_replace("/[^0-9]/","", $value); //非数字
			else if($key=='client_person_card')
				$ret[$key]=preg_replace("/[^0-9]/","", $value); //非数字
			else
				$ret[$key]=$value;			
					
		}
		return $ret;
	}
	function clearClientTmp()
	{
		 $this->db->empty_table('clients_tmp');
	}
	
	function insertToClientTmp($item)
	{
		return $this->db->insert('clients_tmp',$item);
	}
	
	function selectClientByPhone($phone){	
		$phone=preg_replace("/[^0-9]/","", $phone);
		if($phone != "" && substr($phone,0,1) == '0')
			$phone=substr($phone,1);
		$sql="SELECT * from clients where client_cell_phone='$phone' 
or client_phone='$phone' or client_cell_phone='0$phone' or client_phone='0$phone'";		
		return $this->db->query($sql)->result_array();
	}
	
}