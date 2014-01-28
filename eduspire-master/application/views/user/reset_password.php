<?php 
/**
@Page/Module Name/Class: 		reset_password.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display reset password form 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<h1>Reset Password</h1>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div id="form">
<div class="row clearfix">
	<div class="left_area">&nbsp;</div>
	<div class="right_area">
		<div class="error_msg">
			  <?php echo validation_errors('<p>', '</p>');?>
			   <?php if(isset($errors) && count($errors)>0 ): 
					foreach($errors as $error){
						echo '<p>'.$error.'</p>';	
					}
				endif; ?>					
		</div>
	</div>
</div>

	<form class="form" action="" method="post" >
		<div class="row clearfix">
		   <div class="left_area">Username <span class="required">*</span></div>
		   <div class="right_area">
		   <input type="text" name="username" value="<?php echo $this->input->post('username');?>" maxlength="255" size="40"/>
		   </div>
		</div>
		
		<div class="row clearfix">
		   <div class="left_area">New Password <span class="required">*</span></div>
		   <div class="right_area">
		   <input type="password" name="password" value="" maxlength="255" size="40"/>
		   </div>
		</div>
		
		<div class="row clearfix">
		   <div class="left_area">Confirm Password <span class="required">*</span></div>
		   <div class="right_area">
		   <input type="password" name="c_password" value="" maxlength="255" size="40"/>
		   </div>
		</div>
		
		<div class="row clearfix">  
			<div class="left_area">&nbsp;</div>
			<div class="right_area">
			<input type="submit" value="Change" class="submit"/>
			</div>
		</div>
	</form>
</div><!--#form-->