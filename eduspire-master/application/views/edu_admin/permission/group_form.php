<?php 
/**
@Page/Module Name/Class: 	    permission_form.php
@Author Name:			 		ben binesh
@Date:					 		Oct 30, 2013
@Purpose:		        		display add/edit form premission 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div> 
<div class="backButton">
	 <?php echo anchor('edu_admin/permission/','Back','class="submit"'); ?> 
</div>
<div id="form">
				
	<div class="error_msg">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post">
    	<ul class="updateForm">
        	<li>
            	<label>Group Name <span class="required">*</span></label>
                <div class="formRight"><input type="text" name="groupName" value="<?php echo isset($result->groupName)?$result->groupName:$this->input->post('groupName');?>" />
		    <div class="error"><?php echo form_error('groupName','',''); ?></div></div>
            </li>
            <li>
            	<label>Group Key<span class="required">*</span></label>
                <div class="formRight">
                	<input type="text" name="groupKey" value="<?php echo isset($result->groupKey)?$result->groupKey:$this->input->post('groupKey');?>" />
		   			<div class="hint">This is used by system in the code ,Key must be unique and contain no space and special characters</div>
		  			<div class="error"><?php echo form_error('groupKey','',''); ?></div>
                </div>
            </li>
            <li>
            	<label>Group Permission</label>
                <div class="formRight">
                	<?php if(!empty($permission)): ?>
				
			<ul class="updatepermission_list">
			<li><input type="checkbox" name="check_all" id="check_all" value="1" onclick="checkall(this.form)" /><span>All</span></li>
			<?php 
				$check_box_list='';
				$this->permission_model->get_permission_list($check_box_list,$permission,$selected_permission); 
				echo $check_box_list;
				?>
			</ul>
			<?php endif; ?>
                </div>
            </li>
            <li>
            	<label></label>
                <div class="formRight"><input type="submit" value="<?php echo (isset($result->groupID))?'Save':'Create'; ?>" class="submit"/></div>
            </li>
        </ul>
	</form>
	
</div>