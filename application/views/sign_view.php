<html>
<body>
<?php echo $title?>
<?php echo form_open('login/add')?>
<p>�û�����<?php echo form_input('name')?></p>
<p>���룺<?php echo form_password('passwd')?></p>
<p>ȷ�ϣ�<?php echo form_password('passwd2')?></p>
<?php echo form_submit('submit','ע��')?>
<?php echo form_close()?>
</body>
</html>

