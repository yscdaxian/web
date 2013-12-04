<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php echo config_item('charset');?>" />
<base href="<?php echo $this->config->item('base_url') ?>/"/>

<link rel="stylesheet" href="www/css/main.css" type="text/css" media="screen" />
<link rel='stylesheet' href='www/lib/jquery/ui/themes/base/jquery.ui.all.css'   type='text/css'/>

<script type="text/javascript" src="www/lib/jquery.js"></script>
<script type="text/javascript" src="www/lib/dataTable/js/jquery.dataTables.js"  ></script>
<script src="www/js/work.js"  type="text/javascript"></script>
<script type='text/javascript' src='www/lib/jquery/jquery-ui-1.8.16.custom.js'></script>
<script type='text/javascript' src='www/lib/extenal.js'></script>
<script type='text/javascript' src='www/js/call.js'></script>
<style type="text/css" title="currentStyle">
			@import "www/lib/dataTable/css/demo_page.css";
			@import "www/lib/dataTable/css/demo_table.css";
.dataTables_filter{display:none}
.dataTables_length{display:none}
</style>

<script>
$(document).ready(function() {
	$('#agentId').attr('value','<?php echo $agentId?>');
	
	setDatePickerLanguageCn();
	$("#start_ymd").datepicker(); 
	$("#end_ymd").datepicker();  
	//招生状态赋值
	function getDateString(ymd, hour, minut){
	 	return ymd+" "+hour+":"+minut+":00";
	}	 
	$("#start_ymd").datepicker(); 
	$("#end_ymd").datepicker(); 
	
	var ctime=new Date();
	$("#start_ymd").attr('value', ctime.format('yyyy-MM-dd'));	
	$("#end_ymd").attr('value', ctime.format('yyyy-MM-dd'));	  
	
	function getSearchString(){		
		$seachValue=$('#searchText').attr('value');			
		
		filterString='{"searchType":0,"agentId":\"'+$('#agentId').attr('value')+'\","searchText":[["or","varchar","agent","'+$seachValue+'"],["or","varchar","name","'+$seachValue+'"],["or","varchar","phone_number","'+$seachValue+'"],["and","datetime","link_stime",\"'+getDateString($('#start_ymd').attr('value'), $('#s_hour').val(),$('#s_min').val())+'\",\"'+getDateString($('#end_ymd').attr('value'), $('#e_hour').val(),$('#e_min').val())+'\"]]}';	
		
		return filterString;
	}
	
	createTables=function (filterString){
		$('#dataList').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"bStateSave" : false,
			"fnCreatedRow": function( nRow, aData, iDataIndex ) {
			  // Bold the grade for all 'A' grade browsers 
			  if(aData[3] == 0)
			  	$('td:eq(3)', nRow).html('呼入');
			  else
			  	$('td:eq(3)', nRow).html('呼出');
			  if(aData[4] == 'CONNECTED')
			  	$('td:eq(4)', nRow).html('接通');
			  else 
			  	$('td:eq(4)', nRow).html('未接通');	
			 
			  $('td:eq(8)', nRow).html("<a href='javascript:listenRecord(\""+aData[8]+"\")'>收听</a>");
    		},
			"aoColumns": [
				{"bSortable":false,"mDataProp":"0"},
				{"mDataProp":"1"},
				{"mDataProp":"2"},
				{"mDataProp":"3"},
				{"mDataProp":"4"},
				{"mDataProp":"5"},
				{"mDataProp":"6"},
				{"mDataProp":"7"},
				{"mDataProp":"8"}
			],
			"iDisplayLength": 25,
			"fnServerParams": function (aoData) {
				var externData={ "name": "filterString", "value": "my_value" };
				externData.value=filterString;
				aoData.push(externData);
			},
			"sAjaxSource": "<?php echo site_url('report/ajaxReportCommunicate')?>",
			"oLanguage": {
				"sUrl": "<?php echo $this->config->item('base_url')?>/www/lib/dataTable/de_DE.txt"
			}
			
    	}); 
	}	
	createTables(getSearchString());
	
	$("#btnSearch").click(function(){
		filterString=getSearchString();
		var oTable = $('#dataList').dataTable();
		oTable.fnDestroy();	
		createTables(filterString);	
	});
	
	//高级搜索
	$("#btnAdvance").click(function(){		
		if($("#searchPanel").css("display") == "none")
			$("#searchPanel").css("display","block");
		else
			$("#searchPanel").css("display","none");
	});	
    $('#example tbody tr').live('dblclick', function(){	
		$req={'autoid':-1};
		$req.autoid=this.id;
		$.post('<?php echo site_url("report/ajaxGetOneRecord")?>',$req,function(res){
			$location="";
			$defText="评价";
			if(res[0].location)
				$location=res[0].location;
			else
				$defText="无录音";
			
			$("<div style='width:300px;height:400px'><center><object id='mplayer' classid='clsid:6BF52A52-394A-11D3-B153-00C04F79FAA6' id='phx' style='border:0px solid #F00;width: 200px; height: 45px; margin-bottom:-8px'><param name='URL' value='"+$location+"'/><param name='AutoStart' value='false' /></object> <input type='text' style='margin-top:4px;width:200px;height:44px;' value='"+$defText+"'></center></div>").dialog({
						autoOpen:true,
						modal: true,
						buttons:{
							"确认": function(){
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
		});
		
});
</script>    
</head>
<body>
<div><input type="hidden" value="" id="agentId"></div>
<div class="page_main page_tops" >
	<div class="page_nav">
         <div class="nav_ico"><img src="www/images/page_nav_ico.jpg" /></div>
         <div class="nav_">当前位置： &gt; 所有客户</div>
         <div class="nav_other"></div>
	</div>
    <div class="func-panel">
			 <div class="left">
                <table><tr><td><input type="text" id="searchText"> <input type="button" id="btnSearch" value="搜索" class="btnSearch"/></td><td>从</td><td><input type="text" name="start_ymd"   id="start_ymd" value="" style="width:80px"/>
        <?php echo form_dropdown('s_hour',$beginTime['hourOptions'],$beginTime['hourDef'],'id="s_hour"')?><?php echo form_dropdown('s_min',$beginTime['minOptions'],$beginTime['minDef'],'id="s_min"')?></td><td>到</td><td><input type="text" name="end_ymd"   id="end_ymd" value="" style="width:80px"/>
        <?php echo form_dropdown('e_hour',$endTime['hourOptions'],$endTime['hourDef'],'id="e_hour"')?><?php echo form_dropdown('e_min',$endTime['minOptions'],$endTime['minDef'],'id="e_min"')?></td></tr></table>   
			 </div>
			 <div align='right' class="right" ></div>	
			 <div style="clear:both;"></div>  
	</div>	
    <div id="example" style='display:block'>
          <table width="100%" cellpadding="0" cellspacing="0" border="0"  id="dataList" >
          		<thead>
                	<tr>
                	<th  align="left" width="80px">坐席工号</th>
                    <th  align="left"  width="80px">坐席名字</th>
                    <th  align="left" width="80px">对方电话</th>
                    <th	align="left" width="40px">类型</th>    
                    <th	align="left">状态</th> 
                    <th align="left" width="120px">开始时间</th>
                    <th align="left" width="120px">通话时长</th>
                    <th align="left" width="120px">排队时长</th>
                 	<th width="60px">录音</th>
                    </tr>               
                </thead>
          </table>
      </div>
</div>
</body>
</html>