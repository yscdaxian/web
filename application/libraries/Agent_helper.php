<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Agent_helper
{	
	private $agent_id=null;
	function __construct($param)
    {
		$this->agent_id=$param['agent_id'];
	}
	
	public function get_roleid()
	{
		$CI =& get_instance();
		$CI->load->model('Users_model');
		$q=$CI->Users_model->get_byid($this->agent_id);
		if($q)
			return $q[0]->role_id;
		else
			-1;
	}
	
	/*获取和agent关联的role的信息
	*
	*/
	function get_role()
	{
		$ret=array();
		$CI =& get_instance();
		$CI->load->model('Role_model');
		$q=$CI->Role_model->get_role_byid($this->get_roleid());
		foreach($q as $row)
		{
			$item['id']=$row->id;
			$item['name']=$row->role_name;
			array_push($ret, $item);
		}
		return $ret;
	}
	
	function getAllAgents(){
		$ret=array();
		$CI =& get_instance();
		$CI->load->model('Users_model');
		$q=$CI->Users_model->getDatas();
		foreach($q as $row)
		{
			$item['name_value']=$row->code;
			$item['name_text']=$row->name;
			array_push($ret, $item);
		}
		return $ret;
	}
	
	function getAssocatieAgentsCanShow(){
		$CI =&get_instance();
		$role_id=$this->get_roleid();
		$CI->load->library('Role_helper',array('role_id'=>$role_id));
		$CI->firephp->info('roleId'.$role_id);
		
		$CI->load->model('Users_model');
		$agents=array();
		$CI->Users_model->getAllChildrenAgents($agents,$this->agent_id);
		
		array_push($agents,$this->agent_id);

		$ret=$CI->Users_model->getNameValueByIds($agents);
		$CI->firephp->info($ret);
		return $ret;
	  }
	  
	  function getClientAgentsCanShow(){
	  	$CI =&get_instance();
		$agents=array();
	    $CI->Users_model->getAllChildrenAgents($agents,$this->agent_id);
		//查找是包含 everyone 1111
		array_push($agents,$this->agent_id);
		$CI->firephp->info($agents);
		return array('and','set','client_agent',$agents);
	  }
	  
	  function getReportAgentsCanShow(){
	  	$CI =&get_instance();
		$agents=array();
		$CI->Users_model->getAllChildrenAgents($agents,$this->agent_id);
		array_push($agents,$this->agent_id);
		return array('and','set','agent',$agents);
	  }
	  
	  function getNoticeAgentsCanShow(){
	  	$CI =&get_instance();
		$agents=array();
		$CI->Users_model->getAllParentAgents($agents,$this->agent_id);
		array_push($agents,$this->agent_id);
		return array('and','set','notice_creator',$agents);
	  }
	  function getBrotherAgents(){
		  $CI =&get_instance();
		$agents=array();
		$CI->Users_model->getAllBrotherAgents($agents,$this->agent_id);
		array_push($agents,$this->agent_id);
		return $agents;
	  }
}