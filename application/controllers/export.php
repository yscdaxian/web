<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Export extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->library('DataTabes_helper');
		$this->load->model('Clients_model');
		$this->load->library('Utility_func');
	}
	function ajaxClientExport(){
		header('Content-type: Application/json',true);
		
		$this->load->library('firephp');	
		$req=$this->input->post();
		
		$this->firephp->info($req);
		
		$searchObject=json_decode($req['filterString']);	
		$aColumns = array('client_id', 'client_name', 'client_sex',  'client_phone','client_address','client_ctime');
		
		if($searchObject->searchType == 0)
			$sWhere=$this->datatabes_helper->getFilteringSql($searchObject->searchText,$aColumns);
		else if($searchObject->searchType == 1)
			$sWhere=$this->datatabes_helper->getSearchSql($searchObject->searchText);
		
		$sOrder=$this->datatabes_helper->getOrderSql($req,$aColumns);
	
		$sTable="clients";
		$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		";
		
		$this->firephp->info($sQuery);
		$ret=$this->Clients_model->getData($sQuery);
		
		$path='export_datas/clients_'.date('dMy').'.csv';
		
		$this->firephp->info($ret->result_array());
		$this->utility_func->creatHourMinOptions();
	    $this->utility_func->array_to_csv($ret->result_array(),"./$path");
	
		$data['path']=$this->config->item('base_url').'/'.$path;
		
		echo json_encode($data);
	}
	
	function ajaxCallCountExport(){
		header('Content-type: Application/json',true);
		$this->load->library('firephp');
	
		$req=$this->input->post();
		

		$searchObject=json_decode($req['filterString']);
		$this->firephp->info($req['filterString']);

		$this->load->library('Dynamicui',array("agentId"=>""));
	    $dyModelName=$this->dynamicui->getDynamicuiModel();
		$this->load->model($dyModelName);
		
		$aColumns =$this->$dyModelName->getCustomClientColumns();
		
		$sLimit=$this->datatabes_helper->getPageSql($req);
		
		//获得where语句
		$sWhere="";
		$sWhere=$this->datatabes_helper->getSearchSql($searchObject);
	

		$eField="坐席,case when 日期 is null and 坐席 <>'总计' then '合计' else 日期 end as '时间', 呼出接通,呼出未接通,呼出,呼入接通,呼入未接通,呼入";
		$sField="case when agent is null then '总计' else agent end as '坐席'  ,
DATE_FORMAT(link_stime,'%Y-%m-%d')  as '日期', 
sum(case status when 'CONNECTED' then case call_type when 1 then 1 else 0 end else 0 end) as '呼出接通', 
sum(case call_type when 1 then case `status` when 'CONNECTED' then 0 else 1 end else 0 end) as '呼出未接通', 
sum(case call_type when 1 then 1 else 0 end) as '呼出', 
sum(case status when 'CONNECTED' then case call_type when 0 then 1 else 0 end else 0 end) as '呼入接通', 
sum(case call_type when 0 then case `status` when 'CONNECTED' then 0 else 1 end else 0 end) as '呼入未接通',
sum(case call_type when 0 then 1 else 0 end) as '呼入'";
		$sTable="cc_call_history";
		$sGroup="group by agent ,DATE_FORMAT(link_stime,'%Y-%m-%d') with ROLLUP";
		
		$sQuery = "SELECT
		$eField 
		from  (select $sField  from $sTable $sWhere $sGroup) baseTable";
	
		$this->firephp->info($sQuery);
		$this->firephp->info("begin create data");
		$path='export_datas/callcount_'.date('dMy').'.csv';
		
		$data = $this->db->query($sQuery);
		
		$this->load->dbutil();
		$data=$this->dbutil->csv_from_result($data); 
		
	
		
		$this->load->helper('file');
		$this->firephp->info("./$path");
		if(write_file("./$path",$data,'w+')){
			$this->firephp->info("write ok");
		}else{
			$this->firephp->info("write false");
		}
		$ret['path']=$this->config->item('base_url').'/'.$path;
		echo json_encode($ret);	
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
	}
}