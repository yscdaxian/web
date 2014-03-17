<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo config_item('charset');?>" />
	<base href="<?php echo $this->config->item('base_url') ?>/"/>
	<link rel="stylesheet" href="www/css/main.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="www/css/zTree.css" type="text/css">
	<link rel="stylesheet" href="www/css/ztree/zTreeStyle/zTreeStyle.css" type="text/css">
    <link rel='stylesheet' href='www/lib/jquery/ui/themes/base/jquery.ui.all.css'   type='text/css' media="screen"/>
	<style>
    	.dui-control{
			width:160px;
		}
		.person-info{
			font-size:14px;
			color:#0088DD;
		}
		.panelOne{
			margin-left:10px;
			margin-right:10px;
		}
    </style> 
<style type="text/css" title="currentStyle">
			@import "www/lib/dataTable/css/demo_page.css";
			@import "www/lib/dataTable/css/demo_table.css";
.dataTables_filter{display:none}
.dataTables_length{display:none}
</style>
	<script type="text/javascript" src="www/lib/jquery-1.6.4.js"></script>
    <script type="text/javascript" src="www/lib/jquery.idTabs.min.js"></script>
    <script type="text/javascript" src="www/lib/jquery.ztree.core-3.0.min.js"></script>
    <script type="text/javascript" src="www/js/multi-select.js"></script>
    <script type="text/javascript" src="www/js/work.js"></script>
    <script type="text/javascript" src="www/js/call.js"></script>
    <script type="text/javascript" src="www/lib/jgrowl/jquery.jgrowl.js"></script>
    <script type='text/javascript' src='www/lib/jquery.timers.js'></script>
    <script type='text/javascript' src='www/lib/jquery/jquery-ui-1.8.16.custom.js'></script>
    <script type="text/javascript" src="www/lib/dataTable/js/jquery.dataTables.js"  ></script>
    <script type="text/javascript" src="www/lib/myDynamicUI/dynamicUI.js" ></script>  
    <script>	
		function webCallPhone(id){
				var number=$('#'+id).attr('value');						
			    number=number.replace(/[\D]/g,'');
				if(number != ''){
					$('#'+id).attr('value',number);
					window.parent.iUpdateTabTitle(number);
					call(number);
				}
		}	
		function webVoipCallPhone(id){
				var number=$('#'+id).attr('value');						
			    number=number.replace(/[\D]/g,'');
				if(number != ''){
					$('#'+id).attr('value',number);
					window.parent.iUpdateTabTitle(number);
					call('4'+number);
				}
		}		
		function updateUniqueid(uniqueid){	
			$('#uniqueid').attr('value',uniqueid);
		}
		$(document).ready(function(){
			//$('body').everyTime('10s',function(){
				//$.jGrowl("超过5分钟，请留意时间！",{'theme':'jGrowl bottom-right'});
			//},1);	
			
			setDatePickerLanguageCn();			
			$("#yuyue-ymd").datepicker(); 
			$('#btnYuyue').click(function(){
				$("#yuyue-dialog" ).dialog({
						autoOpen:true,
						height: 140,
						width: 300,
						modal: true,
						buttons:{
							"确认": function(){
									$req={'content':'','time':'','client_id':''}
									$req.client_id=$('#clientBh').attr('value');
									$req.content=$('#yuyue-content').attr('value');
									$req.time=getYmdhmDateString('yuyue-ymd','yuyue-hour','yuyue-min');
									//设置预约时间
									$.post('<?php echo site_url("communicate/ajaxSetYuyueTime")?>',$req,function(){
									
									});
									$(this).dialog('destroy');
							},
							"取消": function(){
								$(this).dialog( "close" );
							}
						},
						close: function(){
							$(this).dialog('destroy');
						}
				});	
			});
			
			$('#btnCreatWorkOrder').click(function(){
				$("#createWorkOrder-dialog" ).dialog({
						autoOpen:true,
						height: 140,
						width: 300,
						modal: true,
						buttons:{
							"确认": function(){	
									$req={'reciever':'','lastTime':'','ids':[],'values':[]}
									$req.reciever=$('#workOrder-reciever').attr('value');
									$req.lastTime=getYmdhmDateString('workOrder-ymd','workOrder-hour','workOrder-min');	
									var bessDatas=$('#bussniessInfoTable').dynamicui.getTextDatas('#bussniessInfoTable');
									$req.ids=bessDatas.ids;
									$req.values=bessDatas.values;										
									//设置预约时间
									$.post('<?php echo site_url("order/createOrder")?>',$req,function(res){
										if(res.isOk){
											alert('生成订单成功');
										}
									});
									$(this).dialog('destroy');
							},
							"取消": function(){
								$(this).dialog( "close" );
							}
						},
						close: function(){
							$(this).dialog('destroy');
						}
				});	
			});
			
				
			$('#btnBack').click(function(){
				window.history.back();
			});
				
			$('#nextClient').click(function(){
				var req={'agentId':'','clientBh':'0'};
				req.agentId=$('#agentId').attr('value');
				req.clientBh=$('#clientBh').attr('value');
				$.post("<?php echo site_url('communicate/ajaxNextClient')?>",req,function(res){	
					 if(res.nextUrl != '')	
					 	location.href=res.nextUrl;	
					 else
					 	alert("无待沟通客户");  							
				});  
			});
			
			$('#agentId').attr('value','<?php echo $agentId;?>');
			$('#clientBh').attr('value',"<?php echo $clientBh;?>");
			$('#phoneNumber').attr('value',"<?php echo $phoneNumber;?>");
			$('#callFrom').attr('value',"<?php echo $from?>");
			
	
		  	$('#baseInfoTable').dynamicui(<?php echo json_encode($baseInfo);?>);
			$('#bussniessInfoTable').dynamicui(<?php echo json_encode($bussniessInfo);?>);
			
			$('#btnSave').click(function(){			
			    var datas={'agentId':'','uniqueid':'','columText':{'colum':[],'datas':[]},'columInt':{'colum':[],'datas':[]},'from':'','clientBh':'','phone':''};
				datas.agentId=$('#agentId').attr('value');
				datas.from=$('#callFrom').attr('value');
				datas.clientBh=$('#clientBh').attr('value');
				datas.phone=$('#phoneNumber').attr('value');
				datas.uniqueid=$('#uniqueid').attr('value');
		
				var baseDatas=$('#baseInfoTable').dynamicui.getTextDatas('#baseInfoTable');	
				var bessDatas=$('#bussniessInfoTable').dynamicui.getTextDatas('#bussniessInfoTable');
				
				datas.columText.colum=baseDatas.ids.concat(bessDatas.ids);
				datas.columText.datas=baseDatas.values.concat(bessDatas.values);
				
				datas.columInt.colum=[];
				datas.columInt.datas=[];
						
				$.post("<?php echo site_url('communicate/ajaxCommunicateSave')?>",datas,function(res){	
					 if(res.ok){
					 	alert('成功保存');
					 }
					 if(res.clientBh){
					 	$('#clientBh').attr('value',res.clientBh);
					 }			  							
				});  
				
			});
			
		$('#connectInfoTable').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"bStateSave" : false,
			"fnCreatedRow": function( nRow, aData, iDataIndex ) {
			  // Bold the grade for all 'A' grade browsers
			  if(aData[2] == 0)
			 	 $('td:eq(2)', nRow).html("呼入");
			  else
			  	 $('td:eq(2)', nRow).html("呼出");
			 
			  $('td:eq(5)', nRow).html("<a href='javascript:listenRecord(\""+aData[5]+"\")'>收听</a>");
			  
    		},"aoColumns": [
				{"bSortable":false,"mDataProp":"0"},
				{"mDataProp":"1"},
				{"mDataProp":"2"},
				{"mDataProp":"3"},
				{"mDataProp":"4"},
				{"mDataProp":"5"}
			],"fnServerParams": function (aoData) {
				var externData={ "name": "agentId", "value": "my_value" };
				var externPhoneData={"name": "phone", "value": "<?php echo isset($clientItem[0]['client_phone'])?$clientItem[0]['client_phone']:'';?>" };
				var externCellPhoneData={ "name": "cellPhone", "value": "<?php echo isset($clientItem[0]['client_cell_phone'])?$clientItem[0]['client_cell_phone']:'';?>" };
				
				externData.value="<?php echo $agentId;?>";
				aoData.push(externData);
				aoData.push(externPhoneData);
				aoData.push(externCellPhoneData);
				
			},
			"sAjaxSource": "<?php echo site_url('communicate/ajaxCommunicateRecord')?>",
			"oLanguage": {"sUrl": "<?php echo $this->config->item('base_url')?>/www/lib/dataTable/de_DE.txt"}
    	});
	});
    </script>
 
</head>
<body>
<input id="agentId"  type="hidden" value="">
<input id='callFrom' type='hidden' value="<?php echo isset($from)?$from:''?>"/>
<input id='clientBh' type="hidden" vaule="<?php echo isset($clientBh)?$clientBh:''?>"/>
<input id='phoneNumber' type="hidden" value="<?php echo isset($phoneNumber)?$phoneNumber:''?>"/>
<input id='uniqueid' type="hidden" value="<?php echo isset($uniqueid)?$uniqueid:''?>"/>

<div id="yuyue-dialog"  style="display:none">
	预约内容:<input id="yuyue-content" type="text" style="width:180px" value="" />
    <br>
	预约时间:<input id='yuyue-ymd' type="text" style="width:90px" value="<?php echo $yuyue['ymh']?>">&nbsp;<?php echo form_dropdown('s_hour',$yuyue['hourOptions'],$yuyue['hourDef'],'id="yuyue-hour"')?>&nbsp;<?php echo form_dropdown('s_min',$yuyue['minOptions'],$yuyue['minDef'],'id="yuyue-min"')?>
</div>
<div id="createWorkOrder-dialog"  style="display:none">
	&nbsp;&nbsp;&nbsp;接收人:<input id="workOrder-reciever" type="text" style="width:180px" value="" />
    <br>
	截止时间:<input id='workOrder-ymd' type="text" style="width:90px" value="<?php echo $yuyue['ymh']?>">&nbsp;<?php echo form_dropdown('s_hour',$yuyue['hourOptions'],$yuyue['hourDef'],'id="workOrder-hour"')?>&nbsp;<?php echo form_dropdown('s_min',$yuyue['minOptions'],$yuyue['minDef'],'id="workOrder-min"')?>
</div>
<div class="page_main page_tops">
	<div class="page_nav">
         <div class="nav_ico"><img src="www/images/page_nav_ico.jpg" /></div>
         <div class="nav_">当前位置：&gt; 正在沟通</div>
         <div class="nav_other"></div>
        
	</div>		
     <div class="func-panel">
                    <div class='left'>&nbsp;&nbsp;&nbsp;基本信息</div>
                    <div class="right" align="right">
                    <input id="btnCreatWorkOrder" type="button" value="生成工单">&nbsp
                    <input id="nextClient" type="button" value="下一个">&nbsp 
                    <input id="btnSave" type="button" value="保存">&nbsp;
                    <input id="btnYuyue" type="button" value="预约">&nbsp;
                    <input id="btnBack" type="button" value="返回"></div>            
                   <div style="clear:both"></div>						
    </div> 
    
        <div class='content'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;姓名: <span class="person-info"><?php echo isset($clientItem[0])?$clientItem[0]['client_name']:''; ?></span>&nbsp;所属坐席：<span class="person-info"><?php echo isset($clientItem[0]['client_agent'])?$clientItem[0]['client_agent']:''; ?></span> &nbsp;电话：<span class="person-info"><?php echo isset($clientItem[0])?$clientItem[0]['client_phone']:''; ?> </span> &nbsp; 地址： <span class="person-info"><?php echo isset($clientItem[0]['client_address'])?$clientItem[0]['client_address']:'';?></span></div>

		<div class='work-list'>			
			<div class='tabs' style="padding-left:40px">		
				<ul class="idTabs">   
                	<li><a href="#personInfo">个人资料</a></li> 	    
                	<li><a href="#bussniessInfo">业务信息</a></li>	             	          
					<li><a href="#connectInfo">沟通记录</a></li> 	
                    <li><a href="#helprDoc"> 知识库</a></li>  	                   
				</ul> 
			</div>
			<br>
			<br>
			<div id="personInfo" class="panelOne">
						<table  id="baseInfoTable" width="100%">
                        <tbody></tbody>	
						</table>	
						<br>
			</div> 
            <div id="connectInfo" class='panelOne'>
            	<table width="100%" id="connectInfoTable"><thead><tr align="left" class="dataHead">
                <td width="100px">坐席</td>
                <td width="120px" >对方电话</td>
                <td width="80px">通话类型</td>
                <td width="120px">沟通时间</td>
                <td width="120px">保存时间</td>
                <td>通话内容</td>
                <td width="60px">录音</td>
              </tr></thead></table>
			</div>
            <div id="bussniessInfo" class='panelOne'>       
				<table id="bussniessInfoTable" width="100%">
                 <tbody></tbody>
                </table>
               	
			</div>
            <div id="helprDoc" class='panelOne'>
            	
			</div>
		</div>
	</div>
    
  
</body>
</html>