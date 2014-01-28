<?php 
/**
@Page/Module Name/Class: 		forgot_credential.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display forgot passowrd/username form 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1>Forgot <?php echo ('username'==$this->input->get('ref'))?'Username':'Password'; ?></h1></div>	
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div id="form">
<div class="row clearfix">
	<div class="left_area">&nbsp;</div>
	<div class="right_area">
		<div class="error">
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
		   <div class="left_area">Email <span class="required">*</span></div>
		   <div class="right_area">
		   <input type="email" name="email" value="<?php echo $this->input->post('email');?>" maxlength="255" size="40"/>
		   </div>
		</div>
		
		
		
		<div class="row clearfix">  
			<div class="left_area">&nbsp;</div>
			<div class="right_area">
			<input type="submit" value="Send" class="submit"/>
			</div>
		</div>
	</form>
</div><!--#form-->