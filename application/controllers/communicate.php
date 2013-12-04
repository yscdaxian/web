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
	}
	public function ajaxNextClient(){
		header('Content-type: Application/json',true);
		$req=$this->input->post();
		$nextId=intval($req['clientBh'])+1;
		$sql="select * from clients where client_iswaitcom=1 and client_agent='".$req['agentId']."' and client_id>".$nextId." order by client_ctime desc limit 0,1";
		$this->firephp->info($sql);
		$q=$this->db->query($sql)->result_array();
		if($q)
			$res['nextUrl']=site_url('communicate/connected')."/manulClick/".$req['agentId']."/".$q[0]['client_id'];
		else
			$res['nextUrl']='';
		echo json_encode($res);
	}
	
	function connectedd($from="manulClick",$agentId="", $clientBh="",$phoneNumber="",$uniqueid=""){
		$data['from']=$from;
		$data['agentId']=$agentId;
		$data['uniqueid']=$uniqueid;
			
		if($from == 'manulClick'){				
			$data['phoneNumber']='';
			$data['uniqueid']='';
			$data['clientItem']=$this->Clients_model->getby_id($clientBh);	
		}else if($from == 'callEvent'){			
			$data['phoneNumber']=$phoneNumber;
			$data['uniqueid']=$uniqueid;
			
			$data['clientItem']=$this->Clients_model->selectClientByPhone($phoneNumber);							
		}
		
		$data['phoneNumber']=isset($data['clientItem'][0]['client_cell_phone'])?$data['clientItem'][0]['client_cell_phone']:$phoneNumber;
		
		$data['clientBh']=isset($data['clientItem'][0]['client_id'])?$data['clientItem'][0]['client_id']:'';
		
		
		$data['nationOptions']=$this->Dictionary_model->getSelectOption('民族');
		
		$data['majorTypeOptions']=$this->Dictionary_model->getSelectOption('专业类型');
		$data['studentTypeOptions']=$this->Dictionary_model->getSelectOption('报考类型');
		$data['competeSchoolOptions']=$this->Dictionary_model->getSelectOption('竞争院校');
		$data['educationBackgroudOptions']=$this->Dictionary_model->getSelectOption('学历');
			
		$nationDef=isset($data['clientItem'][0]['client_nation'])?$data['clientItem'][0]['client_nation']:'未填写';
		$studentTypeDef=isset($data['clientItem'][0]['client_student_type'])?$data['clientItem'][0]['client_student_type']:'未填写';
		$majorTypeDef=isset($data['clientItem'][0]['client_major_type'])?$data['clientItem'][0]['client_major_type']:'未填写';
		$competeSchoolDef=isset($data['clientItem'][0]['client_compete_school'])?$data['clientItem'][0]['client_compete_school']:'未填写';
		$educationBackgroudDef=isset($data['clientItem'][0]['client_education_background'])?$data['clientItem'][0]['client_education_background']:'未填写';
		
		$data['nationOptions'][$nationDef]=$nationDef;	
		$data['nationDef']=$nationDef;
		
		$data['studentTypeOptions'][$studentTypeDef]=$studentTypeDef;
		$data['studentTypeDef']=$studentTypeDef;
		
		$data['majorTypeOptions'][$majorTypeDef]=$majorTypeDef;
		$data['majorTypeDef']=$majorTypeDef;
		
		$data['competeSchoolOptions'][$competeSchoolDef]=$competeSchoolDef;
		$data['competeSchoolDef']=$competeSchoolDef;
		
		$data['educationBackgroudOptions'][$educationBackgroudDef]=$educationBackgroudDef;
		$data['educationBackgroudDef']=$educationBackgroudDef;
		
		$this->load->library('Utility_func');
		$data['yuyue']=$this->utility_func->creatHourMinOptions();
		$data['yuyue']['ymh']=date('Y-m-d');
		$data['yuyue']['hourDef']='00';
		$data['yuyue']['minDef']='00';
	
		$this->load->view('call_connect_edu_view',$data);
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
		
			$data['clientItem']=$this->Clients_model->selectClientByPhone($phoneNumber);							
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
				
		//组织数据
		$item=$this->Communicate_model->getItemsFromReq($req);
		$this->firephp->info($item);
		$ret=$this->Clients_model->getby_id($req['clientBh']);			
		if($ret){
			if(isset($item['client']['client_note']) && $item['client']['client_note'] != $ret[0]['client_note']){
				$item['client']['client_note'].=date("Ymd");
				
			}
			//存在更新客户信息
			$this->Clients_model->update($ret[0]['client_id'],$item['client']);
			$status['ok']=true;
			$clientId=$ret[0]['client_id'];		
		}else{
			if(isset($item['client']['client_note'])){
				$item['client']['client_note'].=date("Ymd");
				
			}
			//不存在新建客户信息
			$item['client']['client_agent']=$req['agentId'];
			$item['client']['client_creater']=$req['agentId'];
			$item['client']['client_ctime']=date("Y-m-d H:i:s");
			$clientId=$this->Clients_model->insert($item['client']);
			$status['ok']=true;
		}	
		
		//如果有沟通，插入沟通信息到bill表
		if($req['uniqueid']){
			$bill['bill_uniqueid']=$req['uniqueid'];
			$bill['bill_client_id']=$clientId;
			$bill['bill_stime']=date("Y-m-d H:i:s");
			if(isset($item['client']['client_note']))
				$bill['bill_note']=$item['client']['client_note'];
			
			$this->firephp->info($bill);
			$this->Communicate_model->insertBill($bill);
		}
		$status['clientBh']=$clientId;
		echo json_encode($status);
	}
	
	function ajaxSetYuyueTime(){
		header('Content-type: Application/json',true);
		$req=$this->input->post();		
		$this->firephp->info($req);		
		$item['client_yuyue_time']=$req['time'];
		$item['client_yuyue']=1;
		$item['client_yuyue_content']=$req['content'];
		$this->Clients_model->update($req['client_id'],$item);
		
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
		
		$aColumns = array('phone_number','call_type','call_stime','agent','location');
		
		$sLimit=$this->datatabes_helper->getPageSql($req);
		$sOrder=$this->datatabes_helper->getOrderSql($req,$aColumns,'call_stime','desc');
		$sTable="cc_call_history left join bill on call_id=bill_uniqueid";
		$sWhere="where status = 'CONNECTED' ";
		if($phone != "" && $cellPhone != ""){
			$sWhere.=" and (phone_number='$phone' or phone_number='$cellPhone')";
		}else if($phone != ""){
			$sWhere.=" and phone_number='$phone'";
		}else if($cellPhone != ""){
			$sWhere.=" and phone_number='$cellPhone'";
		}
		
		$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit ";
		
		$this->firephp->info($sQuery);
		
		$ret=$this->db->query($sQuery);	
	
		$output["aaData"]=$this->datatabes_helper->reverseResult($ret->result_array(),$aColumns,'department_id');
		$this->firephp->info($output["aaData"]);
		$sCount="select count(*) as sCount from $sTable $sWhere $sOrder";
		$ret=$this->db->query($sCount)->result_array();
		
		$output["iTotalRecords"]=$output["iTotalDisplayRecords"]=$ret[0]["sCount"];
		
		echo json_encode($output);
	}
}