<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Report extends CI_Controller
{
	public function __construct(){
		parent::__construct();
		$this->load->library('DataTabes_helper');
		$this->load->library('Utility_func');
		$this->load->library('firephp');
	}
	
	function callCount($agentId){		
		$data['agentId']=$agentId;
		$this->load->library('Utility_func');
		$timeOptions=$this->utility_func->creatHourMinOptions();
		$data['beginTime']=$timeOptions;
		$data['beginTime']['ymh']=date('Y-m-d');
		$data['beginTime']['hourDef']='00';
		$data['beginTime']['minDef']='00';
		
		$data['endTime']=$timeOptions;
		$data['endTime']['ymh']=date('Y-m-d');
		$data['endTime']['hourDef']='23';
		$data['endTime']['minDef']='59';
		
		$this->load->view('report_call_count_view',$data);
	}
	function clientStatusCount($agentId){		
		$data['agentId']=$agentId;
		$this->load->library('Utility_func');
		$timeOptions=$this->utility_func->creatHourMinOptions();
		$data['beginTime']=$timeOptions;
		$data['beginTime']['ymh']=date('Y-m-d');
		$data['beginTime']['hourDef']='00';
		$data['beginTime']['minDef']='00';
		
		$data['endTime']=$timeOptions;
		$data['endTime']['ymh']=date('Y-m-d');
		$data['endTime']['hourDef']='23';
		$data['endTime']['minDef']='59';
		
		$this->load->view('report_client_status_count_view',$data);
	}
	function customClient($agentId){
		$data['agentId']=$agentId;
		$this->load->library('Utility_func');
		$timeOptions=$this->utility_func->creatHourMinOptions();
		$data['beginTime']=$timeOptions;
		$data['beginTime']['ymh']=date('Y-m-d');
		$data['beginTime']['hourDef']='00';
		$data['beginTime']['minDef']='00';
		
		$data['endTime']=$timeOptions;
		$data['endTime']['ymh']=date('Y-m-d');
		$data['endTime']['hourDef']='23';
		$data['endTime']['minDef']='59';
		$this->load->library('Dynamicui',array("agentId"=>$agentId));
		$dyModelName=$this->dynamicui->getDynamicuiModel();
		$this->load->model($dyModelName);
		$data["searchPanelTableData"]=$this->$dyModelName->getCustomSearchPanel();
		$data["tableHeader"]=$this->$dyModelName->getCustomClientTableHeader();
		
		$this->load->view('report_custom_client_view',$data);
		
	}
	function communicate($agent){
		$timeOptions=$this->utility_func->creatHourMinOptions();
		$data['beginTime']=$timeOptions;
		$data['beginTime']['ymh']=date('Y-m-d');
		$data['beginTime']['hourDef']='00';
		$data['beginTime']['minDef']='00';
		
		$data['endTime']=$timeOptions;
		$data['endTime']['ymh']=date('Y-m-d');
		$data['endTime']['hourDef']='23';
		$data['endTime']['minDef']='59';
		$data['agentId']=$agent;
		$this->load->view('report_communicate_view',$data);
	}
	
	function liveLook(){
		$this->load->view('report_live_look_view');
	}
	function misscall($agent){
		$data['agentId']=$agent;
		$this->load->view('report_misscall_view',$data);
	}
	
	function ajaxReportCallCount(){
		header('Content-type: Application/json',true);
		$this->load->library('firephp');
		$sEcho=$this->input->get('sEcho');
		$req=$this->input->get();
		$output = array(
		"sEcho" => intval($sEcho),
		"iTotalRecords" => 1,
		"iTotalDisplayRecords" => 1,
		"aaData" => array()
		);
		
		$searchObject=json_decode($req['filterString']);
		//$this->firephp->info($req['filterString']);

		$this->load->library('Dynamicui',array("agentId"=>""));
	    $dyModelName=$this->dynamicui->getDynamicuiModel();
		$this->load->model($dyModelName);
		
		$aColumns =$this->$dyModelName->getCustomClientColumns();
		
		$sLimit=$this->datatabes_helper->getPageSql($req);
		
		//获得where语句
		$sWhere="";
		$sWhere=$this->datatabes_helper->getSearchSql($searchObject);
		$this->firephp->info($sWhere);
		$sLimit=$this->datatabes_helper->getPageSql($req);
		$sTable="cc_call_history";
		$sGroup="group by agent ,DATE_FORMAT(link_stime,'%Y-%m-%d') with ROLLUP";
		$sField="agent ,DATE_FORMAT(link_stime,'%Y-%m-%d') as 'callDate',
sum(case status when  'CONNECTED' then case call_type when 1 then 1 else 0 end  else 0 end) as sumCalloutConnect,
sum(case call_type when 1 then case `status` when 'CONNECTED' then 0 else 1 end  else 0 end) as sumCalloutUnConnnect,
sum(case call_type when 1 then 1 else 0 end) as sumCallout,
sum(case status when  'CONNECTED' then case call_type when 0 then 1 else 0 end  else 0 end) as  sumCallinConnect,
sum(case call_type when 0 then case `status` when 'CONNECTED' then 0 else 1 end  else 0 end) as sumCallinUnConnnect,
sum(case call_type when 0 then 1 else 0 end) as sumCallin";
		$sQuery = "SELECT
		$sField 
		from  $sTable 
		$sWhere
		$sGroup
		$sLimit";
	
		//$this->firephp->info($sQuery);
		
		$ret=$this->db->query($sQuery);	
				$aColumns=array('agent','callDate','sumCalloutConnect','sumCalloutUnConnnect','sumCallout','sumCallinConnect','sumCallinUnConnnect','sumCallin');
		$output["aaData"]=$this->datatabes_helper->reverseResult($ret->result_array(),$aColumns);
		$sCount="select count(*) as sCount from (select agent,DATE_FORMAT(link_stime,'%Y-%m-%d') 
		from cc_call_history $sWhere $sGroup) dataTable";
		
		$ret=$this->db->query($sCount)->result_array();
	
		$output["iTotalRecords"]=$output["iTotalDisplayRecords"]=$ret[0]["sCount"];
		//$this->firephp->info($output);
		
		echo json_encode($output);	
	}
	function ajaxReportClientStatusCount(){
		header('Content-type: Application/json',true);
		$this->load->library('firephp');
		$sEcho=$this->input->get('sEcho');
		$req=$this->input->get();
		$output = array(
		"sEcho" => intval($sEcho),
		"iTotalRecords" => 1,
		"iTotalDisplayRecords" => 1,
		"aaData" => array()
		);
		
		$searchObject=json_decode($req['filterString']);
		//$this->firephp->info($req['filterString']);

		$this->load->library('Dynamicui',array("agentId"=>""));
	    $dyModelName=$this->dynamicui->getDynamicuiModel();
		$this->load->model($dyModelName);
		
		$aColumns =$this->$dyModelName->getCustomClientColumns();
		
		$sLimit=$this->datatabes_helper->getPageSql($req);
		
		//获得where语句
		$sWhere="";
		$sWhere=$this->datatabes_helper->getSearchSql($searchObject);
		$this->firephp->info($sWhere);
		$sLimit=$this->datatabes_helper->getPageSql($req);
		$sTable="cc_call_history";
		$sGroup="group by agent ,DATE_FORMAT(link_stime,'%Y-%m-%d') with ROLLUP";
		$sField="agent ,DATE_FORMAT(link_stime,'%Y-%m-%d') as 'callDate',
sum(case status when  'CONNECTED' then case call_type when 1 then 1 else 0 end  else 0 end) as sumCalloutConnect,
sum(case call_type when 1 then case `status` when 'CONNECTED' then 0 else 1 end  else 0 end) as sumCalloutUnConnnect,
sum(case call_type when 1 then 1 else 0 end) as sumCallout,
sum(case status when  'CONNECTED' then case call_type when 0 then 1 else 0 end  else 0 end) as  sumCallinConnect,
sum(case call_type when 0 then case `status` when 'CONNECTED' then 0 else 1 end  else 0 end) as sumCallinUnConnnect,
sum(case call_type when 0 then 1 else 0 end) as sumCallin";
		$sQuery = "SELECT
		$sField 
		from  $sTable 
		$sWhere
		$sGroup
		$sLimit";
	
		//$this->firephp->info($sQuery);
		
		$ret=$this->db->query($sQuery);	
				$aColumns=array('agent','callDate','sumCalloutConnect','sumCalloutUnConnnect','sumCallout','sumCallinConnect','sumCallinUnConnnect','sumCallin');
		$output["aaData"]=$this->datatabes_helper->reverseResult($ret->result_array(),$aColumns);
		$sCount="select count(*) as sCount from (select agent,DATE_FORMAT(link_stime,'%Y-%m-%d') 
		from cc_call_history $sWhere $sGroup) dataTable";
		
		$ret=$this->db->query($sCount)->result_array();
	
		$output["iTotalRecords"]=$output["iTotalDisplayRecords"]=$ret[0]["sCount"];
		//$this->firephp->info($output);
		
		echo json_encode($output);	
	}
	function ajaxReportCustomClientLook(){
		header('Content-type: Application/json',true);
		$this->load->library('firephp');
		$sEcho=$this->input->get('sEcho');
		$req=$this->input->get();
		$output = array(
		"sEcho" => intval($sEcho),
		"iTotalRecords" => 0,
		"iTotalDisplayRecords" => 0,
		"aaData" => array()
		);
		
		$searchObject=json_decode($req['filterString']);
		$this->firephp->info($req['filterString']);

		$this->load->library('Dynamicui',array("agentId"=>"1000"));
	    $dyModelName=$this->dynamicui->getDynamicuiModel();
		$this->load->model($dyModelName);
		
		$aColumns =$this->$dyModelName->getCustomClientColumns();
		array_push($aColumns,'client_id');
		$sLimit=$this->datatabes_helper->getPageSql($req);
		//获得where语句
		$sWhere="";
		$sWhere=$this->datatabes_helper->getSearchSql($searchObject);
		$this->firephp->info($sWhere);
		
		$sOrder=$this->datatabes_helper->getOrderSql($req,$aColumns,'client_ctime','desc');
	
		$sTable="clients";
		$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit ";
	
		$this->firephp->info($sQuery);
		
		$ret=$this->Clients_model->getData($sQuery);	
		
		$output["aaData"]=$this->datatabes_helper->reverseResult($ret->result_array(),$aColumns);
		
		
		$sCount="select count(*) as sCount from $sTable $sWhere $sOrder";
		
		$ret=$this->Clients_model->getData($sCount)->result_array();
	
		$output["iTotalRecords"]=$output["iTotalDisplayRecords"]=$ret[0]["sCount"];
		$this->firephp->info($output);
		
		echo json_encode($output);		
	}
	
	function ajaxReportCommunicate(){	
		header('Content-type: Application/json',true);	
		$sEcho=$this->input->get('sEcho');
		$req=$this->input->get();
		$output = array(
		"sEcho" => intval($sEcho),
		"iTotalRecords" => 0,
		"iTotalDisplayRecords" => 0,
		"aaData" => array()
		);
		
		$searchObject=json_decode($req['filterString']);
		
		$aColumns = array('agent','name','phone_number','call_type' ,'status' ,'link_stime','call_times', 'inqueue_wait_times','location');
		
		$sLimit=$this->datatabes_helper->getPageSql($req);
		//获得where语句
		$sWhere="";
		
		$this->firephp->info($req['filterString']);
		
		$this->firephp->info($searchObject);
		$this->load->library('Agent_helper',array('agent_id'=>$searchObject->agentId));
	
		$setData=$this->agent_helper->getReportAgentsCanShow();
		array_push($setData[3],'0000');
		array_push($searchObject->searchText,$setData);
		
		$sWhere=$this->datatabes_helper->getSearchSql($searchObject->searchText);
		
		$this->firephp->info($sWhere);

		$sOrder=$this->datatabes_helper->getOrderSql($req,$aColumns,'link_stime','desc');
	
		$sTable="cc_call_history left join agents on cc_call_history.agent=agents.code";
		$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit ";
	
		$this->firephp->info($sQuery);
		
		$ret=$this->db->query($sQuery);	
		
		$output["aaData"]=$this->datatabes_helper->reverseResult($ret->result_array(),$aColumns,'autoid');
		
		$this->firephp->info($output["aaData"]);
		
		$sCount="select count(*) as sCount from $sTable $sWhere $sOrder";
		
		$this->firephp->info($sCount);
		
		$ret=$this->db->query($sCount)->result_array();
	
		
		$output["iTotalRecords"]=$output["iTotalDisplayRecords"]=$ret[0]["sCount"];
		
		echo json_encode($output);
	}
	
	
	function ajaxGetOneRecord(){
		header('Content-type: Application/json',true);
		$this->load->model('Communicate_model');
		$req=$this->input->post();
		$data=$this->Communicate_model->getOneRecordById($req['autoid']);
		echo json_encode($data);
	}	
	
	function ajaxThreeDaysCount(){
		header('Content-type: Application/json',true);
		$sEcho=$this->input->get('sEcho');
		$req=$this->input->get();
		$output = array(
		"sEcho" => intval($sEcho),
		"iTotalRecords" => 1,
		"iTotalDisplayRecords" => 1,
		"aaData" => array()
		);
		
		$sfiled="agent,call_type,count(*) as count ";
		
		$sLimit=$this->datatabes_helper->getPageSql($req);
		
		$sWhere="";
		$this->load->library('Agent_helper',array('agent_id'=>$req['agentId']));
			$this->load->library('firephp');
		$allAgents=$this->agent_helper->getBrotherAgents();
		$seachItems[]=array('and','set','agent',$allAgents);
		$sWhere=$this->datatabes_helper->getSearchSql($seachItems);
	
		if($sWhere == ""){
		//获得where语句
			$sWhere="where agent <> ''";
		}else{
			$sWhere.=" and  agent <> ''";
		}
		$sOrder="order by count desc";
		$sGroup="group by agent,call_type";
		$sTable="cc_call_history";
		$sQuery = "
		SELECT $sfiled
		FROM   $sTable
		$sWhere
		$sGroup
		$sOrder
		$sLimit ";
			
		$this->firephp->info($sQuery);
		$ret=$this->db->query($sQuery)->result_array();	
		
		$allData=array();
		$retAgents=$this->Users_model->getNameValueByIds($allAgents);
		foreach($retAgents as $row){
			$allData[$row['name_value']]=array($row['name_value'],$row['name_text'],0,0,0);
		}		
	
		foreach ($ret as  $aRow){
			if(array_key_exists($aRow['agent'],$allData)){
				$index=$aRow['call_type']+2;
				$allData[$aRow['agent']][$index]=$aRow['count'];
				$allData[$aRow['agent']][4]=$allData[$aRow['agent']][3]+$allData[$aRow['agent']][2];
			}
		}
		$this->firephp->info($allData);
		$output['aaData']=array_values($allData);
		$output["iTotalRecords"]=$output["iTotalDisplayRecords"]=count($output['aaData']);
		
		echo json_encode($output);
	}
	
	function ajaxReportMisscall(){	
		header('Content-type: Application/json',true);	
		$sEcho=$this->input->get('sEcho');
		$req=$this->input->get();
		$output = array(
		"sEcho" => intval($sEcho),
		"iTotalRecords" => 1,
		"iTotalDisplayRecords" => 1,
		"aaData" => array()
		);
		
		$searchObject=json_decode($req['filterString']);
		
		$aColumns = array('agent','name','phone_number','call_type' ,'status' ,'link_stime','autoid');
		
		$sLimit=$this->datatabes_helper->getPageSql($req);
		//获得where语句
		$sWhere="";
		
		$this->firephp->info($req['filterString']);
		
		$this->firephp->info($searchObject);
		$this->load->library('Agent_helper',array('agent_id'=>$searchObject->agentId));
	
		$setData=$this->agent_helper->getReportAgentsCanShow();
		array_push($setData[3],'0000');
		array_push($searchObject->searchText,$setData);
		
		$sWhere=$this->datatabes_helper->getSearchSql($searchObject->searchText);
		
		$this->firephp->info($sWhere);

		
		$sOrder=$this->datatabes_helper->getOrderSql($req,$aColumns,'link_stime','desc');
		$sTable="cc_call_history left join agents on cc_call_history.agent=agents.code";
		$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit ";
	
		$this->firephp->info($sQuery);
		
		$ret=$this->db->query($sQuery);	
		
		$output["aaData"]=$this->datatabes_helper->reverseResult($ret->result_array(),$aColumns,'autoid');
		
		$this->firephp->info($output["aaData"]);
		
		$sCount="select count(*) as sCount from $sTable $sWhere $sOrder";
		
		$this->firephp->info($sCount);
		
		$ret=$this->db->query($sCount)->result_array();
	
		
		$output["iTotalRecords"]=$output["iTotalDisplayRecords"]=$ret[0]["sCount"];
		
		echo json_encode($output);
	}
	
}