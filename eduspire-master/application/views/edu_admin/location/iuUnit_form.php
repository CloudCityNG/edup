<?php 
/**
@Page/Module Name/Class: 	   iuUnit_form.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display add/edit form for Intermediate units
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="backButton clearfix">
	 <?php echo anchor('edu_admin/location/iu_unit/','Back','class="submit"'); ?> 
</div>
<div id="form">
				
	<div class="error_msg error">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" >
		<ul class="updateForm">
            
			 <li>
		   <label>&nbsp;</label>
		  <div class="formRight">
                    <?php 
                    $default_status= ($this->input->post('iuPublish') != '')?$this->input->post('iuPublish'):STATUS_PUBLISH;
                    $selectd_status =  isset( $result->iuPublish )?$result->iuPublish:$default_status;
                    ?>
			<input type="checkbox" name="iuPublish" value="1" <?php echo (STATUS_PUBLISH==$selectd_status)?'checked="checked"':''; ?>/>
			Publish?
			</div>
		</li> 	 
				 <li>
		   <label>Name <span class="required">*</span></label>
		  <div class="formRight">
		   <input type="text" name="iuName" value="<?php echo isset($result->iuName)?$result->iuName:$this->input->post('iuName');?>" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('iuName','',''); ?></div>
		   </div>
		</li>
		
		
		
		<li> 
		   <label>&nbsp;</label>
		     <div class="formRight">
			<input type="submit" value="<?php echo (isset($result->iuID))?'Save':'Create'; ?>" class="submit"/>
			
		   </label>
		</li>
                </ul>
	</form>
	
</div>