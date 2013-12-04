<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php echo config_item('charset');?>" />
<base href="<?php echo base_url() ?>/www/"/>

<link rel="stylesheet" href="css/main.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/examples.css" type="text/css" media="screen" />
<script src="<?php echo base_url()?>www/js/jquery-1.6.4.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>www/js/jquery-impromptu.3.1.min.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>www/js/agent.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>www/js/jquery.json-2.3.min.js" type="text/javascript"></script>
<style>
.view_data{width:100%}
</style>

</head>
<body>

<div class='"page_main page_tops"'>
		 <div class="page_nav">
         <div class="nav_ico"><img src="images/page_nav_ico.jpg" /></div>
         <div class="nav_">当前位置：&gt; 角色信息</div>
         <div class="nav_other"></div>
		</div>
	<?php echo form_open($dst)?>
		<div class="func-panel">
			 <div class="left"></div>
			 <div align='right' class="right">
				 <input  type="submit" value="保存"   class="btnSave"/>
                 <input  type='button' value='返回'  onclick='javascript:location.href="<?php echo site_url('role/look');?>"' class='btnDel'/>
			 </div>
			 <div style="clear:both"></div> 
		</div>	
		<div class='work-list'  style='margin-top:8px;'>
				<fieldset><legend onClick="show_div('data_table2');">昨日业务统计</legend>

                <center><p><font color="#FF0000"><?php echo validation_errors(); ?></font></p></center>
                <center>
               		<div style='width:100%;' align="left">
                	<p>角色名称：<input name='role_name' size='30px' type='text' value='<?php echo $role_name;?>' size="10px"></p>         
					<table width="100%">      	
                    	<tr><th style="width:60px">权限</th><th style="width:30px;">可用</th><th>可操作的项</th><th style="width:20px">编辑</th></tr>
                         <tr><td>功能菜单</td>
                            <td><input name='look_client_check' type='checkbox'></td>
                            <td><input class='post_data'  name='look_func_data'  value='<?php echo $look_func_data['values'] ?>' type='hidden'>
                            	<input class="view_data" type='text' value='<?php echo $look_func_data['names']; ?>'></td>
                            <td class='add_agent'>
                                <input type='hidden' class='target_url' value="<?php echo site_url('role/select_items/3/'.$role_id)?>">
                                <input name='add_agent'  type="button"  class='btnAdd' value="编辑">
                            </td>
                         </tr>
                        <tr><td>查询客户</td>
                            <td><input name='look_client_check' type='checkbox'></td>
                            <td><input class='post_data' name='look_client_agnet_data' value='<?php echo $look_client_agent_data ?>' type='hidden' > 
                            	<input class="view_data"  type='text' value='<?php echo $look_client_agent_data ?>'></td>
                            <td class='add_agent'>
                                <input type='hidden' class='target_url' value="<?php echo site_url('role/select_items/0/'.$role_id)?>">
                                <input name='add_agent'  type="button"  class='btnAdd' value="编辑">
                             </td>
                         </tr>
                        <tr><td>通话记录</td>
                        	<td><input name='look_record_check' type='checkbox'></td>
                            <td><input class='post_data' name='look_record_agnet_data' value='<?php echo $look_record_agent_data;?>' type='hidden' >
                            	<input class="view_data"  type='text' value='<?php echo $look_record_agent_data;?>'>
                            </td>
                            <td class='add_agent'>
                             	<input type='hidden' class='target_url' value="<?php echo site_url('role/select_items/1/'.$role_id)?>">
                             	<input type="button" name='add_agent' value="编辑" class='btnAdd'>
                             </td>
                         </tr>
                    </table>
			
                </center>
                </fieldset>
		</div>
        <?php echo form_close()?>
</div>

</body>
</html>