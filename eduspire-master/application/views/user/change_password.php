<?php 
/**
@Page/Module Name/Class: 		change_password.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display change password form 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1>Change Password</h1></div>

<div id="form">
				
	<div class="error_msg">
	  
	   <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" enctype="multipart/form-data" >
		
		<div class="row clearfix">
		   <div class="left_area">Old Password <span class="required">*</span></div>
		   <div class="right_area">
		   <input type="password" name="old_password" value="" maxlength="255" size="40"/>
		   <div class="error"><?php echo form_error('old_password','',''); ?></div>
		   <div class="hint">Enter you valid old password</div>
		   </div>
		</div>
		
		<div class="row clearfix">
		   <div class="left_area">New Password <span class="required">*</span></div>
		   <div class="right_area">
		   <input type="password" name="password" value="" maxlength="255" size="40"/>
		   <div class="error"><?php echo form_error('password','',''); ?></div>
		   <div class="hint">Password length must  between 5 to 15 characters</div>
		   </div>
		</div>
		
		<div class="row clearfix">
		   <div class="left_area">Confirm Password <span class="required">*</span></div>
		   <div class="right_area">
		   <input type="password" name="c_password" value="" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('c_password','',''); ?></div>
		   </div>
		</div>
			
		<div class="row clearfix">  
		   <div class="left_area">&nbsp;</div>
		   <div class="right_area">
			<input type="submit" value="Change" class="submit"/>
			
		   </div>
		</div>
	</form>
	
</div>