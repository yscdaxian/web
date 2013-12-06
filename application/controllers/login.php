<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class login extends CI_Controller{
	private $role_ids;
	public function __construct(){
		parent::__construct();
		$users=$this->load->model("Pbx_model");
	;
	}
	public function  index(){
		$this->load->view('login_new_view');
	}
	public function filter_item($row)
	{		
		if ($row->p_id == 0)
		{
			if(in_array($row->id,$this->role_ids['pids']))
				return true;
		}
		else
		{
			if(in_array($row->id,$this->role_ids['ids']))
				return true;
		}
		return false;
	}
	public  function  log(){
		$this->form_validation->set_rules('name', '�û���', 'required');
		$this->form_validation->set_rules('passwd', '����', 'required');
		if ($this->form_validation->run()){
			if ($this->Users_model->check()){
				$session['is_login']=true;
				$session['login_id']=$this->input->post('name');
				$data=$this->Pbx_model->get_pbx_reginfo($session['login_id']);
				$agent=$data['agent'];
				$this->load->library('func_helper');		
				$this->load->library('Agent_helper', array('agent_id'=>$agent));		
				$role_id=$this->agent_helper->get_roleid();
				if($role_id != -1){		
					$this->load->library('Role_helper',array('role_id'=>$role_id));
					$this->role_ids=$this->role_helper->get_assocatie_func_ids();
					$data["items"]=$this->func_helper->get_items($this);
				}
					
				$data['agentId']=$agent;
				$data['user']=$this->db->query("select code,name,role_name from agents left join role on role_id=role.id where code=$agent")->result_array();
				$data['agentId']=$agent;
				$this->load->view("new_main_view.php",$data);
				////$this->session->set_userdata($session);
				//$this->load->view("main_view", $data);
				
				return;			
			}		
		}
		redirect('login');			
	}
	
	public	function add(){	
		if ($this->Users_model->add()){
			redirect('login');
		}else{
			$data['title']='ע��';
			$this->load->view('sign_view',$data);
		}
	}
	public  function  signup(){
		$data['title']='ע��ҳ��';
		$this->load->view('sign_view',$data);
	}
	public  function  login_out(){
		$this->session->sess_destroy();
		redirect('login');
	}
}