<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Client extends CI_Controller{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('DataTabes_helper');
		$this->load->library('excel_helper');
		$this->load->library('firephp');
	}
	public function all($agentId='')
	{
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
		$data["dySearch"]=$this->$dyModelName->getAllClientSearchData();

		$this->load->view('client_all_view', $data);
	}
	
	public function arrange($agentId='')
	{
		$data['agentId']=$agentId;
		$this->load->view('client_arrange_view', $data);
	}
	
	//查询待沟通的用户的视图
	public function wait($agentId){
		$data['agentId']=$agentId;
		$this->load->view('client_wait_communicate_view', $data);
	}	
	public function order($agentId){
		$data['agentId']=$agentId;
		$this->load->view('client_yuyue_view', $data);
	}
	function ajaxDeleteOneClient(){
		header('Content-type: Application/json',true);
		$req=$this->input->post();
		$this->firephp->info($req['ids']);
		$sWhere=$this->datatabes_helper->getSearchSql($req['ids']);
		if($sWhere != ""){
			$sTable="clients";
			$sQuery = "
			DELETE
			FROM   $sTable
			$sWhere";
			$this->firephp->info($sQuery);
			$ret=$this->Clients_model->getData($sQuery);	
			$res['ok']=true;
		}else{
			$res['ok']=false;
		}
		
		echo json_encode($res);
	}
	
	function ajaxDeleteAllClient(){
		header('Content-type: Application/json',true);
		$req=$this->input->post();
		$searchObject=json_decode($req['filterString']);
		if($searchObject->searchType == 0){
				$searchItems= $this->createDefaultSearchObject($searchObject->searchText);
		}
		else if($searchObject->searchType == 1){
			$searchItems=$searchObject->searchText;
		}
		
		
		
		$this->firephp->info($searchObject);
		$this->firephp->info($searchItems);
		$sWhere=$this->datatabes_helper->getSearchSql($searchItems);	
		$sTable="clients";
		$sQuery = "
		DELETE
		FROM   $sTable
		$sWhere";
		
		$this->firephp->info($sQuery);
		$ret=$this->Clients_model->getData($sQuery);
		
		$res['ok']=true;
		echo json_encode($res);	
		
	}
	
	
	public function ajaxYuyueClientLook(){
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
		$this->load->library('Agent_helper',array('agent_id'=>$searchObject->agentId));
		$aColumns = array('client_id', 'client_name', 'client_sex','client_cell_phone','client_phone','client_address','client_yuyue_content','client_yuyue_time','client_agent','client_id','name');
		
		$sLimit=$this->datatabes_helper->getPageSql($req);
		//获得where语句
		$sWhere="";
		$searchItem=$this->createSearchSql($searchObject);
		if($searchItem)
			array_push($searchItem,array('and','int','client_yuyue',1));
		else
			$searchItem=array(array('and','int','client_yuyue',1));
		
		$sWhere=$this->datatabes_helper->getSearchSql($searchItem);
		$sOrder=$this->datatabes_helper->getOrderSql($req,$aColumns,'client_yuyue_time','desc');
	
		$sTable="clients left join agents on client_agent=code";
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
		
		$this->firephp->info($sCount);
		$output["iTotalRecords"]=$output["iTotalDisplayRecords"]=$ret[0]["sCount"];
		
		echo json_encode($output); 
	}
	
	public function ajaxAllWaitCommunicateClient(){
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
		
		$this->load->library('Agent_helper',array('agent_id'=>$searchObject->agentId));
	
		$aColumns = array('client_id', 'client_name', 'client_sex', 'client_cell_phone', 'client_phone','client_address','client_ctime','client_agent','client_id','name');
		
		$sLimit=$this->datatabes_helper->getPageSql($req);
		//获得where语句
		$sWhere="";
		$searchItem=$this->createSearchSql($searchObject);
		if($searchItem)
			array_push($searchItem,array('and','int','client_iswaitcom',1));
		else
			$searchItem=array(array('and','int','client_iswaitcom',1));
		
		$sWhere=$this->datatabes_helper->getSearchSql($searchItem);
		$sOrder=$this->datatabes_helper->getOrderSql($req,$aColumns,'client_ctime','desc');
	
		$sTable="clients left join agents on client_agent=code";
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
		
		$this->firephp->info($sCount);
		$output["iTotalRecords"]=$output["iTotalDisplayRecords"]=$ret[0]["sCount"];
		
		echo json_encode($output);
	}
	
		//查询所有客户
	public function ajaxArrangeClientLook(){
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
		$this->firephp->info($searchObject);
		$this->load->library('Agent_helper',array('agent_id'=>$searchObject->agentId));
		
		$aColumns = array('client_id', 'client_name', 'client_sex',  'client_cell_phone','client_phone','client_address','client_ctime','client_modify_time','client_agent','client_id','name');
		$sLimit=$this->datatabes_helper->getPageSql($req);
		//获得where语句
		$sWhere="";
		
		$sWhere=$this->datatabes_helper->getSearchSql($this->createSearchSql($searchObject));
		$sOrder=$this->datatabes_helper->getOrderSql($req,$aColumns,'client_ctime','desc');
	
		$sTable="clients left join agents on client_agent=code";
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
		
		echo json_encode($output);
	}
	//查询所有客户
	public function ajaxAllClientLook(){
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
		$this->firephp->info($searchObject);
		$this->load->library('Agent_helper',array('agent_id'=>$searchObject->agentId));
		
		$aColumns = array('client_id', 'client_name', 'client_sex',  'client_cell_phone','client_phone','client_address','client_ctime','client_modify_time','client_agent','client_id','name');
		$sLimit=$this->datatabes_helper->getPageSql($req);
		//获得where语句
		$sWhere="";
		if($searchObject->searchType == 0){
				$seachItems= $this->createDefaultSearchObject($searchObject->searchText);
		}
		else if($searchObject->searchType == 1){
			$seachItems=$searchObject->searchText;
		}
		$this->load->library('Agent_helper',array('agent_id'=>$searchObject->agentId));
		
		$agents=$this->agent_helper->getClientAgentsCanShow();
		//$agents[2]='client_creater';
		$agents[2]='client_agent';
		array_push($seachItems,$agents);
		$this->firephp->info($seachItems);
		$sWhere=$this->datatabes_helper->getSearchSql($seachItems);
		$this->firephp->info($sWhere);
		$sOrder=$this->datatabes_helper->getOrderSql($req,$aColumns,'client_ctime','desc');
	
		$sTable="clients left join agents on client_agent=code";
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
		
		echo json_encode($output);
	}
	
	function createSearchSql($searchObject){
		$sWhere="";
		if($searchObject->searchType == 0){
				return $this->appendAgentToSearchObject($this->createDefaultSearchObject($searchObject->searchText));
		}
		else if($searchObject->searchType == 1){
			return $this->appendAgentToSearchObject($searchObject->searchText);
		}
		return null;
	}
	
	function createDefaultSearchObject($text){
		if($text == '')
			return array();
		$searchObject=array();
		array_push($searchObject,array('or','varchar','client_name',$text));
		array_push($searchObject,array('or','varchar','client_phone',$text));	
		array_push($searchObject,array('or','varchar','client_address',$text));
		array_push($searchObject,array('or','varchar','client_sex',$text));
		return $searchObject;
	}
	
	function appendAgentToSearchObject($searchObject){
		
		$agents=$this->agent_helper->getAssocatieAgentsCanShow();
		$setData=array();
		foreach($agents as $agent){
			array_push($setData,$agent['name_value']);
		}
		array_push($searchObject,array('and','set','client_agent',$setData));
		$this->firephp->info($searchObject);
		return $searchObject;
	}
	 

	//加载添加新客户视图
	public function add($agentId){
		$res['agentId']=$agentId;
		$this->load->view('client_add_view',$res);	
	}
	public function ajaxAdd(){
		header('Content-type: Application/json',true);
		$req=$this->input->post();
		
		$items=array_combine($req['field'],$req['fieldValue']);
		$res['ok']=true;
		
		//判断号码是否存在
		if($items['client_phone'] !='' && $this->Clients_model->selectClientByPhone($items['client_phone'])){
			$res['ok']=false;
			$res['fail']=$items['client_phone'].'已存在';
		}	
		
		if($items['client_cell_phone'] !='' && $this->Clients_model->selectClientByPhone($items['client_cell_phone'])){
			$res['ok']=false;
			$res['fail']=$items['client_cell_phone'].'号码已存在';
			
		}
			$this->firephp->info($res);
		if($items['client_person_card'] !='' && $this->db->query("select client_id from clients where client_person_card='".$items['client_person_card']."'")->result_array()){
			$res['ok']=false;
			$res['fail']=$items['client_cell_phone'].'号码已存在';
			
		}
	
		//判断身份证是否存在
		if($res['ok']){
			$items['client_ctime']=date("Y-m-d H:i:s");
			$items['client_agent']=$req['agentId'];
			$items['client_creater']=$req['agentId'];
			$client_id=$this->Clients_model->insert($items);
			$res['nextUrl']=site_url('communicate/connected/manulClick').'/'.$req['agentId'].'/'.$client_id;
		}
		$this->firephp->info($res);
		echo json_encode($res);
	}

	
	
	public function import($agentId)
	{
		$data['agentId']=$agentId;
		$this->load->view('client_import_view',$data);
	}
	public function record()
	{
		$this->load->view('client_import_record_view');
	}
	

	public function tooltips($id)
	{
		$data['title']=$id;
		$this->load->view('user_tooltips_view',$data);
	}
	
	public function uploadMap()
	{
		header('Content-type: Application/json',true);
		
		$req=$this->input->post();
		
		$this->load->library('excel_helper');
		$this->load->library('firephp');
		
		$data[0]=array('value'=>-1,'text'=>'');
		
		$this->excel_helper->load('./uploaddir/'.$req['file']);
		$this->firephp->info($this->excel_helper->get_columns());		
		foreach($this->excel_helper->get_columns() as $key=>$value){
			array_push($data,array('value'=>$value,'text'=>$value));
		}
		
		$this->load->library('Dynamicui',array("agentId"=>""));
		$modelName=$this->dynamicui->getDynamicuiModel();
		$this->load->model($modelName);
		
		$res['importMap']=$this->$modelName->getImportTableMap();
		
		$this->firephp->info($data);	
		$this->excel_helper->clear();	
		
		$res["excelColumns"]=$data;
		echo json_encode($res);
	}
	
	
	public function doUpload()
	{
		header('Content-type: Application/json',true);
		
    	$req=$this->input->post();
		//插入数据导临时表	
        $this->insertDataToTmpTable($req['agentId'],$req['file'],$req['dataMap']);
		$this->firephp->info($req['rules']);
		
		//临时表和总表数据排重
		$res=$this->removeDup('clients_tmp','clients',$req['rules']);		
		if($res['ok'] == 1){
		  
		  $fields=implode(",",array_keys($req['dataMap']));
		  $fields.=",client_ctime,client_status,client_creater";
		  $sql="INSERT INTO clients(".$fields.") SELECT ".$fields." FROM clients_tmp";
		  $this->firephp->info($sql);
		  $this->db->query($sql);	
		}
        echo json_encode($res); 	
	}
	
	function insertDataToTmpTable($agentId,$file,&$columnMap)
	{
		$this->excel_helper->load('./uploaddir/'.$file);	
		$count=0;
		$this->Clients_model->clearClientTmp();
		//数据插入临时表
		while ($item=$this->excel_helper->next())
		{
			foreach($columnMap as $key=>$value){
				if($value != '-1')
					$data[$key]=$item[$value];
			}			
			$this->Clients_model->filter($data);
			$data['client_ctime']=date("Y-m-d H:i:s",time()); 
			$data['client_status']=0;	
			$data['client_creater']=$agentId;	
			if ($this->Clients_model->insertToClientTmp($data))
			$count++;
			
		}				
		$this->excel_helper->clear();	
	}
	
	function removeDup($srcTable, $dstTable,$rules)
	{
		$dup['ok']=1;
		$dup['datas']=array();
		$excuteDup=0; 
				
		$ret=$this->db->select('*')->from($srcTable)->get()->result_array();
		$countClients=0;
		foreach($ret as $item)
		{  	  
			//排重	
			$phones=array();
			if($item['client_cell_phone'] != ''){
				$str=$item['client_cell_phone'];
				array_push($phones,$str);
				if(substr($str,0,1) == '0'){
					array_push($phones,substr($str,1));
				}
				else{
					array_push($phones,'0'.$str);
				}
			}
			
			if($item['client_phone'] != ''){
				$str=$item['client_phone'];
				array_push($phones,$str);
				if(substr($str,0,1) == '0'){
					array_push($phones,substr($str,1));
				}
				else{
					array_push($phones,'0'.$str);
				}
			}
			
			if(count($phones) > 0 ){
				$excuteDup=1;
				$this->db->where_in('client_cell_phone', $phones);
				$this->db->or_where_in('client_phone', $phones);
			}
			
			if($item['client_person_card'] != ''){
				$excuteDup=1;
				$this->db->or_where('client_person_card',$item['client_person_card']);
			}
			
		    if($excuteDup == 1){
				$q=$this->db->select("count(*) as count")->from($dstTable);
				$row=$q->get()->result();
				if($row[0]->count>0){
					$this->db->where('client_id', $item['client_id']);
					$this->db->delete($srcTable);
					array_push($dup['datas'],array($item['client_excel_id'],"身份证:".$item['client_person_card']."电话:".implode(',',$phones)));				
					$dup['ok']=0;									
				}		
			}else{
				//$dup['ok']=0;	
				array_push($dup['datas'],array($item['client_excel_id'],"所有电话号码为空的数据"));
			}
			$countClients++;
			$excuteDup=0;		
		}		
		$dup['counts']=$countClients;
		return $dup;
	}
	
}