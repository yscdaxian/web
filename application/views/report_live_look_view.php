<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php echo config_item('charset');?>" />
<base href="<?php echo $this->config->item('base_url') ?>/www/"/>
<link rel="stylesheet" href="css/main.css" type="text/css" media="screen" />

<style type="text/css" title="currentStyle">
			@import "lib/dataTable/css/demo_page.css";
			@import "lib/dataTable/css/demo_table.css";
.dataTables_filter{display:none}
.dataTables_length{display:none}
</style>
<script src="js/jquery-1.6.4.js" 				type="text/javascript"></script>
<script src="js/jquery-impromptu.3.1.min.js"  type="text/javascript"></script>
<script src="lib/dataTable/js/jquery.dataTables.js" type="text/javascript"  ></script>
<script>
var data=Array();
function updateMonitorView(msg){
	if(msg.eventId === 8){
		data=pushAgentStatusItem(data,msg);	
	}else{
		data=pushLiveCallItem(data,msg);
	}
	
	$('#liveAgent').dataTable({
		"bDestroy":true,
		"aaData": data,
		"oLanguage": {"sUrl": "<?php echo $this->config->item('base_url') ?>/www/lib/dataTable/de_DE.txt"}			
	});
	
}

function pushAgentStatusItem(data,msg){	
	data=Array();
	$.each(msg.eventEx,function(index,msgValue){
		data[index]=Array();
		data[index][0]=msgValue[0];
		data[index][1]=msgValue[2];
		if(msgValue[3] == "false")
			data[index][2]="示闲";
		else
			data[index][2]="示忙";
		data[index][3]=msgValue[4];
	});
	return data;
}

function pushLiveCallItem(data,msg){
	$.each(msg.eventEx,function(index,msgValue){
		$.each(data,function(index,dataValue){
			if(dataValue[0] === msgValue[0]){
				//data[index][2]=msgValue[1];
			}
		});
	});
	return data;
}
$(document).ready(function(){
	var json_one={"eventEx":[["1000","true","2012-12-13 14:18:32.0","false","2012-12-13 14:18:32.0",null,null],["8004","true","2012-12-13 13:37:58.0","false","2012-12-13 13:37:58.0",null,null],["8001","true","2012-12-13 11:32:08.0","true","2012-12-13 11:32:30.0",null,null]],"eventId":8,"eventName":"ProxyAgentStatusEvent"};
	var json_two={"eventEx":[],"eventId":11,"eventName":"ProxyLiveCallEvent"};
	
	data=pushAgentStatusItem(data,json_one);
	data=pushLiveCallItem(data,json_two);
	/*
	$('#liveAgent').dataTable({
		"bDestroy":true,
		"aaData": data,
		"oLanguage": {"sUrl": "<?php echo $this->config->item('base_url') ?>/www/lib/dataTable/de_DE.txt"}			
	});
	*/
});
</script>
</head>
<body>
	<div class='page_main page_tops'>
		<div class="page_nav">
         <div class="nav_ico"><img src="images/page_nav_ico.jpg" /></div>
         <div class="nav_"></div>
         <div class="nav_other"></div>
	</div>
		<div class='func-panel' style="height:23px">
			<div class='left'></div>
			<div align='right' class='right'></div>
		</div>
		<div class='work-list'>
			<table id="liveAgent" width="100%">
            	 <thead><th align="left">登陆工号</th><th align="left">登录时间</th><th align="left">示闲示忙</th><th align="left">示闲示忙开始时间</th><thead>       	
                 <tbody></tbody>
            </table>
		</div>
	</div>
</body>
</html>