<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>亚铭科技呼叫中心</title>
<base href="<?php echo $this->config->item('base_url') ?>/www/" />

<link rel="stylesheet" href="css/layout.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/left.css" type="text/css" media="screen" />
<link rel='stylesheet' href='lib/jquery/ui/themes/base/jquery.ui.all.css'   type='text/css' media="screen"/>
<link rel='stylesheet' href='lib/jgrowl/jquery.jgrowl.css'   type='text/css' media="screen"/>

<script type='text/javascript' src='lib/jquery/jquery-1.5.2.min.js'></script>
<script type='text/javascript' src='lib/jquery/jquery-ui-1.8.16.custom.js'></script>
<script type='text/javascript' src='lib/jgrowl/jquery.jgrowl.js'></script>
<script type='text/javascript' src='lib/jquery.timers.js'></script>

<style>
#demo {height:100%}
#tabs {height:100%;border:0;}
#tabs li .ui-icon-close { float: left; margin: 0.4em 0.2em 0 0; cursor: pointer; }
#add_tab { cursor: pointer; }

</style>
<script language="javascript"> 

function nav(title,url){
	iAddTab(title,url);
}
  
function showDivMenu(id){var e=document.getElementById(id);if(e.style.display=="block"||e.style.display==""){e.style.display="none"}else{e.style.display="block"}};

function showFoldClass(id){if($("#"+id).attr("title")=="开启"){$("#"+id).removeClass("opnFd");$("#"+id).addClass("clsFd");$("#"+id).attr("title","折叠")}else{$("#"+id).removeClass("clsFd");$("#"+id).addClass("opnFd");$("#"+id).attr("title","开启")}};

function showDiv(id,isShow){var e=document.getElementById(id);if(!isShow){e.style.display="none"}else{e.style.display="block"}}; 
  
function addClassName(id,cl){$("#"+id).addClass("on")};
 
function removeClassName(id,cl){$("#"+id).removeClass("on")};
$(document).ready(function(){
$("a[rel=fold]").removeClass("opnFd").addClass("clsFd").attr("title", "折叠");
$("#set_a_line_area").toggle(function() {
    $(".gFdBdy").css("display", "none");
    $("a[rel=fold]").removeClass("clsFd").addClass("opnFd").attr("title", "开启")
},
function() {
    $(".gFdBdy").css("display", "block");
    $("a[rel=fold]").removeClass("opnFd").addClass("clsFd").attr("title", "折叠")
});

var $tabs = $("#tabs").tabs({
			tabTemplate: "<li><a href='#{href}'>#{label}</a><span class='ui-icon ui-icon-close'>Remove Tab</span></li>",
			add: function(event, ui) {
				if($tab_content === "<?php echo site_url('report/liveLook/'.$agentId);?>"){
					$(ui.panel).append("<iframe id='"+ui.panel.id+"-frame' scrolling='auto' noresize='noresize' name='liveLook' frameborder='0'  style='border-width:0;' height='100%' width='100%' src='"+$tab_content+"' ></iframe>" );		
				}else{
					$(ui.panel).append("<iframe  id='"+ui.panel.id+"-frame' scrolling='auto' noresize='noresize' frameborder='0'  style='border-width:0;' height='100%' width='100%' src='"+$tab_content+"' ></iframe>" );	
				}
				$tabs.tabs('select', '#' + ui.panel.id);
			}
		});	
	// actual addTab function: adds new tab using the title input from the form above
		iAddTab=function addTab(title,url){
			$tab_title ="<font size=18px >"+title+"</font>" || "Tab ";
			$tab_title=title;
			$tab_content=url+"/<?php echo $agentId;?>";
			var timestamp=new Date().getTime();
			$tabs.tabs("add", "#tabs-"+title+timestamp, $tab_title );
		}
		
		iUpdateTabTitle=function updateTabTitle(ntitle){
			var selected = $tabs.tabs('option', 'selected');
			$("li a", $tabs).each(function(){	
				var index=$("li", $tabs).index($(this).parent());	
				if(index === selected){
					$(this).html(ntitle);
				}
			});	
		}	
		
		updateCallUniqueid=function updateTab($title,$uniqueid){
			var res=iFindTabByTitle($title);
			if(res.isFind){
				$(res.iframeId)[0].contentWindow.updateUniqueid($uniqueid);	
			}
		}
		
		function iFindTabByTitle($title){
			var index=-1;
			var iframeId="";
			var res={isFind:false,iframeId:''};
			$("li a", $tabs).each(function(){	
				if($(this).html() === $title){			
					index=$("li", $tabs).index($(this).parent());	
				}
				res.iframeId=$(this).attr("href")+'-frame';
			});	
			if(index != -1)
				res.isFind=true;
			return res;
		}
		iUpdateTab=function updateTab($title,$url){
			var index=-1;
			var iframeId="";
			var res=iFindTabByTitle($title);
			if(!res.isFind){
				iAddTab($title,$url);			
			}else{	
				$(res.iframeId).attr('src',$url);
			}				
			//改变原有选项卡的内容  
		}
		
		// close icon: removing the tab on click
		// note: closable tabs gonna be an option in the future - see http://dev.jqueryui.com/ticket/3924
		$( "#tabs span.ui-icon-close" ).live( "click", function() {
			var index = $("li", $tabs).index($( this ).parent());
			$tabs.tabs( "remove", index );
			
		});
		
		
	var $agentid="<?php echo $agentId;?>"	
	window.external.ExtLogin($agentid,$agentid);
	makeBusy($agentid,false);
	onProxyEvent=function(type,msg){
	  var  json_msg=eval( '( '+msg+' )' ); 	
	  if(json_msg.eventId === 1){		  
		  var url="";
		  var title="";
		  //建立连接
		  if(json_msg.floatInfo != 'callout'){	
			 //来电
			  url="<?php echo site_url('communicate/connected')?>"+"/callEvent/"+json_msg.releatedNum+"/0/"+json_msg.exten+"/"+json_msg.uniqueid;
			  title=json_msg.exten;
			  if(json_msg.releatedNum == $agentid){
				 makeBusy($agentid,true);
			  	 updateCallUniqueid(title,json_msg.uniqueid);
			  }
			   
		  }else{
		  	 //去电
			  url="<?php echo site_url('communicate/connected')?>"+"/callEvent/"+json_msg.exten+"/0/"+json_msg.releatedNum+"/"+json_msg.uniqueid;		
			  title=json_msg.releatedNum;	  
			  if(json_msg.exten == $agentid){
				   iAddTab(title,url);
			  }			   
		  }	  
	   }
	   
	   if(json_msg.eventId === 9){		
	   		var url="<?php echo site_url('communicate/connected')?>"+"/callEvent/"+$agentid+"/0/"+json_msg.callerId+"/0";
			var title=json_msg.callerId;
			iAddTab(title,url);
	   }
	   
	   if(json_msg.eventId === 21){
	   		var url="<?php echo site_url('communicate/connected')?>"+"/callEvent/"+$agentid+"/0/"+json_msg.callerId+"/"+json_msg.uniqueid;
			var title=json_msg.callerId;
			iAddTab("转接坐席","<?php echo site_url("pbx/transfer/".$agentId); ?>");
	   }
	   
	   if(json_msg.eventId === 8 || json_msg.eventId === 11){
	   		updateMonitorView(json_msg);
	   }
	}
});
  
</script>
</head>
<body>
<div id="frame-header" class="frame-header">
<input name="notic_num" id="notic_num" type="hidden" value=""/>
<div id="auto_save_res" class="load_layer"></div>
<div class="head_menu" ondragstart='return false'  >
	<div class="logo" title="首创科技电话外呼管理系统" onClick="tab_frame('index');"></div>
    <div class="head_info"> 
    	 <div class="round_" title="其他信息">
        	<div class="round_main">
           	  <div class="head_notice_img"><img src="images/home.png" alt="返回系统主页" /></div>
              <div style="height:32px;line-height:32px;width:28px;float:left"><a href="javascript:void(0);" onClick="onClickMax();">布局</a></div>
                
           	  <div class="head_notice_img"><img src="images/login_out.png" alt="退出登录" /></div>
              <div style="height:32px;line-height:32px;width:28px;float:left"><a href="javascript:void(0);" onClick="logout();">退出</a></div>  
         	</div>
         </div>      
    	 <div class="round_" title="用户信息">
        	<div class="round_main">
            	<div class="head_notice_img"><img src="images/user_info.png" alt="用户信息" /></div>
                <div class="head_notice">
           	  <ul>
                    	<li>用户名：<a href="javascript:void(0);" title="teltion[admin]"><span id="names"><?php echo isset($user[0]['name'])?$user[0]['name']:'';?></span> [<?php echo isset($user[0]['code'])?$user[0]['code']:'';?>]</a></li>
                    	<li>角&nbsp;&nbsp;&nbsp;色：<a href="javascript:void(0);"><?php echo isset($user[0]['role_name'])?$user[0]['role_name']:'';?> </a></li>
                    </ul>
              </div>       
         	</div>
         </div>
 
    	 <div class="round_" title="操作面板" id="info_list">
            <div class="round_main">
           	  <div class="head_notice_img"><a href="javascript:void(0);"><img src="images/notice.png" alt="返回系统主页" /></a></div>
              <div style="height:32px;line-height:32px;width:28px;float:left"><a href="javascript:void(0);" onClick="onClickMax();">消息</a></div>           
         	</div>
          
         </div>
      
     </div>
</div>
</div>
<div class="frame-side" id="frame-side">
<div id="divLeftMenu" class="gMain">
  <div class="gLe" id="Menu_List">
    <div class="gMbtn" id="set_a_line_area"></div>
    <?php foreach ($items as $item){?> 
		<div class=gFd>
    	<h3 class="gfTit" onClick="javascript:showDivMenu('study_<?php echo $item["item_id"]?>');showFoldClass('fold');">
        	<a href="javascript:void(0);" id="fold" rel="fold" class="opnFd bgF1" title="开启" hidefocus="true"></a>
            <a href="javascript:void(0);" class="gfName" hidefocus="true"><?php echo $item["item_text"];?></a>
        </h3>       
  		      <ul class="gFdBdy" id="study_<?php echo $item["item_id"]?>"  style="display:none">
               <?php foreach($item["sub_items"] as $sub_item){?>
            <li onMouseOver="addClassName('li_<?php echo $sub_item["item_id"] ?>','on');" id="li_<?php echo $sub_item["item_id"] ?>" onMouseOut="removeClassName('li_<?php echo $sub_item["item_id"] ?>','on');" title="" rel="o_list">
            <b class="icon <?php echo $sub_item["item_logo"];?>"></b><a href="javascript:void(0)" hidefocus="true" onClick="nav('<?php echo $sub_item["item_text"]?>','<?php echo $sub_item["item_url"]?>')" class="gfNm"><?php echo $sub_item["item_text"];?></a>
            </li>
             <?php } ?>
        </ul>  
    </div>
    <?php } ?>
  </div>
</div> 
   <a class="side-switcher" hidefocus="true" title="点击收缩侧边栏" href="javascript:void(0);">侧边栏</a>    
</div>

<div id="page-main" class="page-main">
	<div id="demo" >
	<div id="tabs">
		 <ul>
			<li><a href="#tabs-0">首页</a> <span class="ui-icon ui-icon-close">Remove Tab</span></li>
		</ul>
		<div id="tabs-0" >
			 <iframe name='tabsBody' frameborder='0' style="border-width:0;margin-top:1px;" scrolling="no" height="100%" width="100%" src="<?php echo site_url('system/notice'."/".$agentId);?>" >
             </iframe>
		</div>
	</div>
</div>
</div>
<div class="footer">
	<div class="footer" oncontextmenu='return false' ondragstart='return false' onselectstart ='return false' onselect='document.selection.empty()' oncopy='document.selection.empty()' onbeforecopy='return false' onmouseup='document.selection.empty()'>
	<div class="welcome"><img src="images/welcome.jpg" width="101" height="27" /></div>
    <div class="copyright">CopyRight&copy;2010 - 2011 . All Rights Reserved</div>
    <div class="version"></div>
</div>
</div>
</body>
</html>