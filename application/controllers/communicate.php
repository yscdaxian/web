<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Communicate extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('firephp');
		$this->load->library('DataTabes_helper');
		$this->load->model('Communicate_model');	
		$this->load->model('Dictionary_model');
		date_default_timezone_set('Asia/Shanghai');
	}
	public function ajaxNextClient(){
		header('Content-type: Application/json',true);
		$req=$this->input->post();
		$nextId=intval($req['clientBh']);
		$sql="select clients_wait.client_id from clients_wait left join clients on clients_wait.client_id=clients.client_id where clients_wait.client_id >".$nextId." and client_agent='".$req['agentId']."'  order by client_id  limit 0,1";
		$this->firephp->info($sql);
		$q=$this->db->query($sql)->result_array();
		if($q){
			$this->firephp->info($q);
			$res['nextUrl']=site_url('communicate/connected')."/manulClick/".$req['agentId']."/".$q[0]['client_id'];
		}
		else{
			$sql="select min(client_id) as client_id from clients_wait";
			$this->firephp->info($sql);
			$q=$this->db->query($sql)->result_array();
			if($q)
				$res['nextUrl']=site_url('communicate/connected')."/manulClick/".$req['agentId']."/".$q[0]['client_id'];
			else
				$res['nextUrl']='';
		}
		echo json_encode($res);
	}
	
	function connected($from="manulClick",$agentId="", $clientBh="",$phoneNumber="",$uniqueid=""){
		$data['from']=$from;
		$data['agentId']=$agentId;
		$data['uniqueid']=$uniqueid;
		
		$this->load->library('Dynamicui',array("agentId"=>$agentId));
		$modelName=$this->dynamicui->getDynamicuiModel();
		$this->load->model($modelName);
		
		if($from == 'manulClick'){				
			$data['phoneNumber']='';
			$data['uniqueid']='';
			$data['clientItem']=$this->Clients_model->getby_id($clientBh);	
		}else if($from == 'callEvent'){			
			$data['phoneNumber']=$phoneNumber;
			$data['uniqueid']=$uniqueid;
			$allDbFields=$this->$modelName->getAllDbFileds();
			$data['clientItem']=$this->Clients_model->selectClientByPhone($phoneNumber,$allDbFields);	
			
		}
		
		$data['phoneNumber']=isset($data['clientItem'][0]['client_cell_phone'])?$data['clientItem'][0]['client_cell_phone']:$phoneNumber;
		
		$data['clientBh']=isset($data['clientItem'][0]['client_id'])?$data['clientItem'][0]['client_id']:'';
		$this->load->library('Utility_func');
		$data['yuyue']=$this->utility_func->creatHourMinOptions();
		$data['yuyue']['ymh']=date('Y-m-d');
		$data['yuyue']['hourDef']='00';
		$data['yuyue']['minDef']='00';
		
		
		if(isset($data['clientItem'][0])){
			$data['baseInfo']=$this->$modelName->getBaseInfoTableData($data['clientItem'][0]);
			$data['bussniessInfo']=$this->$modelName->getBussniessInfoTableData($data['clientItem'][0]);
		}
		else{
			$data['baseInfo']=$this->$modelName->getBaseInfoTableData(array('client_phone'=>$phoneNumber));
			$data['bussniessInfo']=$this->$modelName->getBussniessInfoTableData(array('client_phone'=>$phoneNumber));
		}
		
		$this->load->view('call_connect_view',$data);
	}
	
	function ajaxCommunicateSave(){
		header('Content-type: Application/json',true);
		$req=$this->input->post();		
		$status['ok']=false;	
		//组织数据
		$item=$this->Communicate_model->getItemsFromReq($req);
		$this->firephp->info($item);
		$ret=$this->Clients_model->getby_id($req['clientBh']);			
		if($ret){		
			//存在更新客户信息
			$this->Clients_model->update($ret[0]['client_id'],$item['client']);
			$status['ok']=true;
			$clientId=$ret[0]['client_id'];		
		}else{		
			//不存在新建客户信息
			$item['client']['client_agent']=$req['agentId'];
			$item['client']['client_creater']=$req['agentId'];
			$item['client']['client_ctime']=date("Y-m-d H:i:s");
			$clientId=$this->Clients_model->insert($item['client']);
			$status['ok']=true;
		}	
		
		//如果有沟通，插入沟通信息到bill表
		if($req['uniqueid'] != "0" && $req['uniqueid'] !=""){
			$bill['bill_uniqueid']=$req['uniqueid'];
			$bill['bill_client_id']=$clientId;
			$bill['bill_stime']=date("Y-m-d H:i:s");
			if(isset($item['client']['client_note']))
				$bill['bill_note']=$item['client']['client_note'];
			
			$this->firephp->info($bill);
			$this->Communicate_model->insertBill($bill);
		}
		
		$status['clientBh']=$clientId;
		if($status['ok']){
			$sql="delete from clients_wait where client_id='$clientId'";
			$this->db->query($sql);
		}
		echo json_encode($status);
	}
	
	function ajaxSetYuyueTime(){
		header('Content-type: Application/json',true);
		$req=$this->input->post();		
		$this->firephp->info($req);		
		$item['client_yuyue_time']=$req['time'];
		$item['client_yuyue']=1;
		$item['client_yuyue_content']=$req['content'];
		$sql="select client_id from clients_yuyue where client_id='".$req['client_id']."'";
		$ret=$this->db->query($sql);
		if($ret->num_rows() > 0){
			$sql="update clients_yuyue set yuyue_time='".$req['time']."',yuyue_note='".$req['content']."' where client_id='".$req['client_id']."'";
		}else{
			$sql="insert  into clients_yuyue (client_id,yuyue_time,yuyue_note) values('".$req['client_id']."','".$req['time']."','".$req['content']."')";
		}
		$this->db->query($sql);
		//$this->Clients_model->update($req['client_id'],$item);
		
	}
	
	function ajaxCommunicateRecord(){
		header('Content-type: Application/json',true);
		$sEcho=$this->input->get('sEcho');
		$req=$this->input->get();
		
		$output = array(	
		"sEcho" => intval($sEcho),
		"iTotalRecords" => 1,
		"iTotalDisplayRecords" => 1,
		"aaData" => array()
		);
		//$agent=$req['agentId'];
		$phone=$req['phone'];
		$cellPhone=$req['cellPhone'];
		
		$aColumns = array('agent','phone_number','call_type','call_stime','call_id','location');
		
		$sLimit=$this->datatabes_helper->getPageSql($req);
		$sOrder=$this->datatabes_helper->getOrderSql($req,$aColumns,'call_stime','desc');
		
		if($cellPhone != "" && $cellPhone[0] == '0'){
			$cellPhone=substr($cellPhone,1);
		}
		
		if($phone != "" && $phone[0] == '0'){
			$phone=substr($phone,1);
		}
		
		$sTable="cc_call_history";
		$sWhere="where status = 'CONNECTED' ";
		if($phone != "" && $cellPhone != ""){
			$sWhere.=" and (phone_number='$phone' or phone_number='0$phone' or phone_number='0$cellPhone' or phone_number='$cellPhone')";
		}else if($phone != ""){
			$sWhere.=" and (phone_number='$phone' or phone_number='0$phone')";
		}else if($cellPhone != ""){
			$sWhere.=" and (phone_number='$cellPhone' or phone_number='0$cellPhone')";
		}
		
		$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit ";
		
		$this->firephp->info($sQuery);
		
		$ret=$this->db->query($sQuery);	
		
		foreach($ret->result_array() as $row){
			$sql="select bill_note from bill where bill_uniqueid='".$row['call_id']."'";
			$billRs=$this->db->query($sql)->result_array();
			foreach($billRs as $billRow)
				array_push($output["aaData"],array($row['agent'],$row['phone_number'],$row['call_type'],$row['call_stime'],$billRow['bill_note'],$row['location']));
			
		}
		//$output["aaData"]=$this->datatabes_helper->reverseResult($ret->result_array(),$aColumns,'department_id');
		$this->firephp->info($output["aaData"]);
		$sCount="select count(*) as sCount from $sTable $sWhere $sOrder";
		$ret=$this->db->query($sCount)->result_array();
		
		$output["iTotalRecords"]=$output["iTotalDisplayRecords"]=$ret[0]["sCount"];
		
		echo json_encode($output);
	}
}