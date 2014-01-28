<?php if(isset($permission) && !empty($permission)):?>
<div class="permissinPopup">
<div class="popupTitle">
	<h1><?php echo $result->groupName; ?> Permission</h1>
</div>
<ul class="permission_list">
			<?php 
				$check_box_list='';
				$this->permission_model->get_permission_list($check_box_list,$permission,$selected_permission,false); 
				echo $check_box_list;
				?>
</ul>
</div>
<?php else: ?>
<p>No Permission Defined <p>
<?php endif; ?>