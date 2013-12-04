<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Pbx extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->library('DataTabes_helper');
	}
	function ajaxAgentMakeBusy(){
		header('Content-type: Application/json',true);
		$req=$this->input->post();
		$paused=$req['busy'];
		$this->db->query("update tx_queue_member set paused=$paused where membername='".$req['agentId']."'");	
		
	}
	function transfer($agent){
		$data['agentId']=$agent;
		$this->load->view("pbx_transfer_view",$data);		
	}
	function ajaxTransferTable(){
		header('Content-type: Application/json',true);
		$sEcho=$this->input->get('sEcho');
		$req=$this->input->get();
		$output = array(
		"sEcho" => intval($sEcho),
		"iTotalRecords" => 1,
		"iTotalDisplayRecords" => 1,
		"aaData" => array()
		);
		
		$sfiled="code,name,code";
		
		$sLimit=$this->datatabes_helper->getPageSql($req);
		
		$sWhere="";
		$this->load->library('Agent_helper',array('agent_id'=>$req['agentId']));
		$this->load->library('firephp');
		$allAgents=$this->agent_helper->getBrotherAgents();
		$this->firephp->info($allAgents);
		
		$seachItems[]=array('and','set','code',$allAgents);
		$sWhere=$this->datatabes_helper->getSearchSql($seachItems);
	
		
		$sOrder="order by code";
	
		$sTable="agents";
		$sQuery = "
		SELECT $sfiled
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit ";
			
		$this->firephp->info($sQuery);
		$ret=$this->db->query($sQuery)->result_array();	

		$output['aaData']=$this->datatabes_helper->reverseResult($ret,array('code','name','code'));
		$output["iTotalRecords"]=$output["iTotalDisplayRecords"]=count($allAgents);
		
		echo json_encode($output);
	}
}