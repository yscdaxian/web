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
	//招生状态赋值
	function getDateString(ymd, hour, minut){
	 	return ymd+" "+hour+":"+minut+":00";
	}	 
	$("#start_ymd").datepicker(); 
	$("#end_ymd").datepicker();   
	
	function getSearchString(){		
		$seachValue=$('#searchText').attr('value');	
		filterString='{"searchType":0,"agentId":\"'+$('#agentId').attr('value')+'\","searchText":[["or","varchar","agent","'+$seachValue+'"],["or","varchar","name","'+$seachValue+'"],["or","varchar","phone_number","'+$seachValue+'"]]}';	
		return filterString;
	}	
	createTables=function (filterString){
		$('#dataList').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"bStateSave" : false,
			"fnCreatedRow": function( nRow, aData, iDataIndex ) {
			  // Bold the grade for all 'A' grade browsers 
    		},"aoColumns": [
				{"bSortable":false,"mDataProp":"0"}
			],
			"iDisplayLength": 15,
			"fnServerParams": function (aoData) {
				var externData={ "name": "filterString", "value": "my_value" };
				externData.value=filterString;
				aoData.push(externData);
			},
			"sAjaxSource": "<?php echo site_url('report/ajaxReportMisscall')?>",
			"oLanguage": {"sUrl": "<?php echo $this->config->item('base_url')?>/www/lib/dataTable/de_DE.txt"}
    	}); 
	}	
	createTables(getSearchString());
	$("#btnSearch").click(function(){
		filterString=getSearchString();
		var oTable = $('#dataList').dataTable();
		oTable.fnDestroy();	
		createTables(filterString);	
	});	
});
</script>    
</head>
<body>
<div><input type="hidden" value="" id="agentId"></div>
<div class="page_main page_tops" >
	<div class="page_nav">
         <div class="nav_ico"><img src="www/images/page_nav_ico.jpg" /></div>
         <div class="nav_">当前位置：<a href="Index.php">首页</a> &gt; 所有客户</div>
         <div class="nav_other"></div>
	</div>
    <div class="func-panel">
			 <div class="left"><input type="text" id="searchText">
			 	<input type="button" id="btnSearch" value="搜索" class="btnSearch"/>
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
                    <th align="left" width="120px">时间</th>
                 	<!--th width="60px">操作</th-->
                    </tr>               
                </thead>
          </table>
      </div>
</div>
</body>
</html>