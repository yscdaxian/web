<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php echo config_item('charset');?>" />
<base href="<?php echo $this->config->item('base_url') ?>/"/>

<link rel="stylesheet" href="www/css/main.css" type="text/css" media="screen" />
<link rel='stylesheet' href='www/lib/jquery/ui/themes/base/jquery.ui.all.css'   type='text/css' media="screen"/>

<script type="text/javascript" src="www/lib/jquery/jquery-1.5.2.min.js"></script>
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
function onCallClick(name,url){	
	if(name !="")
		window.parent.iAddTab(name,url);
	else
		window.parent.iAddTab("未命名",url);
	//location.href=url;	
}

$(document).ready(function() {	
	createSelect('#sZsjd',"<?php echo $dySearch["fir"]["source"]?>","<?php echo site_url('dictionary/ajaxGetKeyValue')?>");
	createSelect('#sAgent',$('#agentId').attr('value'),"<?php echo site_url('role/ajaxGetCAgentsCanShow')?>");
	createSelect('#client_gtzt',"<?php echo $dySearch["sec"]["source"]?>","<?php echo site_url('dictionary/ajaxGetKeyValue')?>");
	
	//招生状态赋值
	function getDateString(ymd, hour, minut){
	 	return ymd+" "+hour+":"+minut+":00";
	}
	setDatePickerLanguageCn();
	$("#start_ymd").datepicker(); 
	$("#end_ymd").datepicker();   

	function getSearchString(){		
		if($("#searchPanel").css("display") == "none")
			filterString='{"searchType":0,"agentId":\"'+$('#agentId').attr('value')+'\","searchText":\"'+$("#searchText").attr("value")+'\"}';
		else{			
			filterString='{"searchType":1,"agentId":\"'+$('#agentId').attr('value')+'\","searchText":[["and","varchar","client_name",\"'+$("#sName").attr("value")+'\"],["and","varchar","client_phone",\"'+$("#sPhone").attr("value")+'\"],["or","varchar","client_cell_phone",\"'+$("#sPhone").attr("value")+'\"],["and","varchar","client_person_card",\"'+$("#sCard").attr("value")+'\"],["and","varchar","client_agent",\"'+$("#sAgent").val()+'\"],';
			if($('#stimeType').val() == 0){
				filterString+='["and","datetime","client_ctime",\"'+getDateString($('#start_ymd').attr('value'), $('#s_hour').val(),$('#s_min').val())+'\",\"'+getDateString($('#end_ymd').attr('value'), $('#e_hour').val(),$('#e_min').val())+'\"]]}';
			}else{
				filterString+='["and","datetime","client_modify_time",\"'+getDateString($('#start_ymd').attr('value'), $('#s_hour').val(),$('#s_min').val())+'\",\"'+getDateString($('#end_ymd').attr('value'), $('#e_hour').val(),$('#e_min').val())+'\"]]}';
			}
		}
		return filterString;
	}
	
	createTables=function (filterString){
		$('#dataList').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"bStateSave" : false,
			"aoColumnDefs": [
				{
					"fnRender": function ( oObj ) {
						return '<input type=\"checkbox\"  value="'+ oObj.aData[0] +'"> ';
					},
					"aTargets": [0]
				}
			],
			"fnCreatedRow": function( nRow, aData, iDataIndex ) {
			  // Bold the grade for all 'A' grade browsers 
			 $('td:eq(8)', nRow).html(aData[8]+'['+aData[10]+']');
			 var htmlStr='<center><a href="javascript:onCallClick(\''+aData[1]+'\',\'<?php echo site_url('communicate/connected')?>/manulClick/'+$('#agentId').attr('value')+'/'+aData[9]+'\')"><img src="www/images/dxzx.png"></img></a></center>';
			 if(aData[3]){
			 $('td:eq(3)',nRow).html('<a href="javascript:onClientUiCall(\''+$('#agentId').attr("value")+'\',\''+aData[3]+'\')">'+aData[3]+'<img src="www/images/dxzx.png"></a>');
			 }
			 if(aData[4]){
			 	$('td:eq(4)',nRow).html('<a href="javascript:onClientUiCall(\''+$('#agentId').attr("value")+'\',\''+aData[4]+'\')">'+aData[4]+'<img src="www/images/dxzx.png"></a>');
			 }
			 $('td:eq(9)', nRow).html(htmlStr);
    		},
			"aoColumns": [
				{"bSortable":false,"mDataProp":"0"},{"mDataProp":"1"},{"mDataProp":"2"},
				{"mDataProp":"3"},{"mDataProp":"4"},{"mDataProp":"5"},{"mDataProp":"6"},
				{"mDataProp":"7"},{"mDataProp":"8"},{"mDataProp":"9"}
			],
			"iDisplayLength": 16,
			"fnServerParams": function (aoData) {
				var externData={ "name": "filterString", "value": "my_value" };
				externData.value=filterString;
				aoData.push(externData);
			},
			"sAjaxSource": "<?php echo site_url('client/ajaxAllClientLook')?>",
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
	function refreashTable(){
		var oTable = $('#dataList').dataTable();
		oTable.fnDestroy();	
		createTables(getSearchString());
	}
	
	//给时间控件付初值
	var ctime=new Date();
	$("#s_hour").get(0).selectedIndex="00";//index为索引值
	$("#s_min").get(0).selectedIndex="00"	
	$("#start_ymd").attr('value', ctime.format('yyyy-MM-dd'));	
	$("#e_hour").get(0).selectedIndex="23";//index为索引值
	$("#e_min").get(0).selectedIndex="59"
	$("#end_ymd").attr('value', ctime.format('yyyy-MM-dd'));	
	
	//高级搜索
	$("#btnAdvance").click(function(){		
		if($("#searchPanel").css("display") == "none")
			$("#searchPanel").css("display","block");
		else
			$("#searchPanel").css("display","none");
	});
	
	//导出文件
	$("#btnExport").click(function(){
		 var req={"filterString":""};
		 req.filterString=getSearchString();
		 $.post("<?php echo site_url('export/ajaxClientExport')?>",req,function(res){	
			$("#csvUrl").attr("href", res.path);
			$("#csvUrl").html("下载");					  							
		});  	
	});
	
	$("#btnDelAll").click(function(){
		 if(confirm("确定要删除满足查询条件的所有客户吗？")){
			$req={'filterString':''};
			$req.filterString= getSearchString();	
			$.post("<?php echo site_url('client/ajaxDeleteAllClient')?>",$req,function(res){	
				if(res.ok)
					refreashTable();									
			});  
		 }
	});
	
	$("#btnDel").click(function(){
		 if(confirm("确定要删除选中的客户吗？")){		 
			$ids=[];
			var datas=getSelectedItem();
			for(var i in datas){			
				var $item=[];
				$item.push('or');
				$item.push('varchar');
				$item.push('client_id');
				$item.push(datas[i]);	
				$ids.push($item);
			}			
			$req={'ids':[]};
			$req.ids=$ids;
			$.post("<?php echo site_url('client/ajaxDeleteOneClient')?>",$req,function(res){
				if(res.ok)	
					refreashTable();									
			}); 		 
		 }
	});
});
</script>    
</head>
<body>
<input id='agentId' type="hidden" value="<?php  echo $agentId;?>">
<div class="page_main page_tops">
	<div class="page_nav">
         <div class="nav_ico"><img src="www/images/page_nav_ico.jpg" /></div>
         <div class="nav_">当前位置：&gt; 所有客户</div>
         <div class="nav_other"></div>
	</div>
    <div class="func-panel">
			 <div class="left"><input type="text" id="searchText">
			 	<input type="button" id="btnSearch" value="搜索" class="btnSearch"/>
                <input type="button" id="btnAdvance" value="高级" class="btnSearch"/>
                <input type="button" id="btnExport" value="导出" class="btnSearch"/>
                <a id="csvUrl" href='export_datas/clients_09Apr12.csv'></a>
			 </div>
			 <div align='right' class="right">
				 <input  id='btnDel' type="button" name="btnSearch" value="删除 " class="btnDel" />
                 <input  id='btnDelAll' type="button" name="btnSearch" value="删除所有 " class="btnDel" />&nbsp;
			 </div>		
			 <div style="clear:both;"></div>  
	</div>	
   <div id="searchPanel" style="display:none;margin-top:5px;margin-bottom:5px">
    <table>
    	<tr><td width="80">电话号码：</td><td width="100"><input type="text" id="sPhone" style="width:100px"></td><td width="80">身份证:</td><td><input type="text" id="sCard" style="width:100px"/></td><td>时间类型</td><td><select id="stimeType" name="select">
          <option value="0">导入时间</option>
          <option value="1">最后沟通时间</option>
        </select></td><td width="16">从</td><td><input type="text" name="start_ymd"   id="start_ymd" value="" style="width:80px"/>
        <?php echo form_dropdown('s_hour',$beginTime['hourOptions'],$beginTime['hourDef'],'id="s_hour"')?><?php echo form_dropdown('s_min',$beginTime['minOptions'],$beginTime['minDef'],'id="s_min"')?></td>
    	  
      </tr>        		
      <tr><td>姓名：</td><td><input type="text" id="sName" style="width:100px" /></td>
      <td width="72">所属坐席：</td><td width="38"><select  name="sAgent" id="sAgent" style="width:100px"></select></td>
<td>&nbsp;</td><td></td><td>到</td><td><input type="text" name="end_ymd"   id="end_ymd" value="" style="width:80px"/>
  <?php echo form_dropdown('e_hour',$endTime['hourOptions'],$endTime['hourDef'],'id="e_hour"')?><?php echo form_dropdown('e_min',$endTime['minOptions'],$endTime['minDef'],'id="e_min"')?></td></tr>
    </table>	
    </div>
   <div id="example" style='display:block'>
          <table width="100%" cellpadding="0" cellspacing="0" border="0"  id="dataList" >
          		<thead>
                	<tr>
                	<th align="left" id="cbAll" width="20px"><input   type="checkbox" value="全选" /></td>
                    <th align="left" width="50px">姓名</th>
                    <th align="left" width="40px">性别</th>
                    <th align="left" width="100px">手机</th>
                    <th align="left" width="100px">固话</th>
                    <th align="left">地址</th>    
                    <th align="left" width="120px">创建时间</th>
                    <th align="left" width="120px">沟通时间</th>
                    <th align="left" width="80px">所属坐席</th>
                 	<th align="center" width="30px">操作</th>
                    </tr>               
            </thead>
          </table>
      </div>
</div>
</body>
</html>