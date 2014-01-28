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
	<form class="form" action="" method="post" >
    	<ul class="updateForm">
        	<li>
            	<label>Permission <span class="required">*</span></label>
                <div class="formRight">
                	<input type="text" name="permission" value="<?php echo isset($result->permission)?$result->permission:$this->input->post('permission');?>" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('permission','',''); ?></div>
                </div>
            </li>
            <li>
            	<label>Permission Key<span class="required">*</span></label>
                <div class="formRight">
                	<input type="text" name="key" value="<?php echo isset($result->key)?$result->key:$this->input->post('key');?>" maxlength="255" size="40"/>
		   <div class="hint">This is used by system in the code ,Key must be unique and contain no space and special characters</div>
		   <div class="error"><?php echo form_error('key','',''); ?></div>
                </div>
            </li>
            <li>
            	<label>Permission Parent<span class="required">*</span></label>
                <div class="formRight">
                	<?php 
					$default_parent= ($this->input->post('parentID') != '')?$this->input->post('parentID'):0;
					$selected_parent =  isset( $result->parentID )?$result->parentID:$default_parent;
					echo form_dropdown('parentID',$permissions_list,$selected_parent);
					?>
					<div class="error"><?php echo form_error('parentID','',''); ?></div>
                </div>
            </li>
            <li>
            	<label></label>
                <div class="formRight"><input type="submit" value="<?php echo (isset($result->permissionID))?'Save':'Create'; ?>" class="submit"/></div>
            </li>
        </ul>
		</form>
	</div>