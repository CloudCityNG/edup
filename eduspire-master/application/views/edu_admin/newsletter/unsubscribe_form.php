<?php 
/**
@Page/Module Name/Class: 	    unsubscribe_form.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display newsletter add/edit unsubscription form
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?> 
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="backButton">
	 <?php echo anchor('edu_admin/newsletter/unsubscribe/','Back','class="submit"'); ?> 
</div>
<div id="form">
				
	<div class="error_msg">
	  <?php echo validation_errors('<p>', '</p>');?>
	   <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" >
    	<ul class="updateForm">
        	<li>
            	<label>Email <span class="required">*</span></label>
                <label class="right"><input type="text" name="uEmail" value="<?php echo isset($result->uEmail)?$result->uEmail:$this->input->post('uEmail');?>" /></label>
               
            </li>
            <li>
            	 <label></label>
                <label class="right"><input type="submit" value="<?php echo (isset($result->uID))?'Save':'Add'; ?>" class="submit"/></label>
            </li>
        </ul>
	</form>
	
</div>