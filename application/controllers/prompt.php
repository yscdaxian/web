<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Prompt extends CI_Controller
{
	public function __construct(){
		parent::__construct();
		$this->load->library('DataTabes_helper');
		$this->load->library('firephp');
		$this->load->model('Clients_model');
	}
	function  ajaxYuyue(){
		header('Content-type: Application/json',true);
		$req=$this->input->post();
		$this->firephp->info(array(array('and','set','agent',array($req['agentId']))));
		$sWhere=$this->datatabes_helper->getSearchSql(array(array('and','set','client_agent',array($req['agentId']))));
		$sWhere.= " and client_yuyue=1";
		$sql="select client_id,client_name,client_yuyue_time,client_yuyue_content from clients 
			  $sWhere";
		$this->firephp->info($sql);
		$q=$this->db->query($sql)->result_array();
		$ret=array();
		foreach($q as $row){
			$diffTime=strtotime($row['client_yuyue_time'])-time();
			if($diffTime<0){
				//预约时间过期
				$row['expire']=false;
				array_push($ret,$row);
			}else if($diffTime<240){
				array_push($ret,$row);
			}
		}
		$this->firephp->info($ret);
		echo json_encode($ret);
	}
	
	function ajaxMissCall(){
		header('Content-type: Application/json',true);
		$req=$this->input->post();
		
		$this->firephp->info(array(array('and','set','agent',array('0000',$req['agentId']))));
		$sWhere=$this->datatabes_helper->getSearchSql(array(array('and','set','agent',array('0000',$req['agentId']))));
		$sWhere.=" and call_type=0 and status<>'CONNECTED' and  miss_call_process=0";
		
		$sql="select call_id,phone_number,link_stime from cc_call_history 
		$sWhere";
		
		$this->firephp->info($sWhere);
		$this->firephp->info($sql);
		
		$q=$this->db->query($sql)->result_array();
		
		$ret=array();
		foreach($q as $row){
			$ret[]=$row;
		}
		
		$this->firephp->info($ret);
		
		echo json_encode($ret);
	}
	
	function processMissCall(){
		header('Content-type: Application/json',true);
		$req=$this->input->post();
		$this->firephp->info($req);
		$sql="update cc_call_history set miss_call_process=1 where call_id='".$req["callId"]."'";
		$this->db->query($sql);
		$data['ok']=true;
		echo json_encode($data);
	}
}