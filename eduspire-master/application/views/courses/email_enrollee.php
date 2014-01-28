<?php 
/**
@Page/Module Name/Class: 		enroll.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display enroll form
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>
jQuery(document).ready(function($) {
	$('#close_fancy').click(function(){
		parent.$.fancybox.close();
	});
	
});
 <?php 
	if(isset($url)){
		echo('top.location.href ="'.$url.'";');
	}	
	if(isset($reload)){
		echo('parent.location.reload(true);');
	}
	
 ?>
</script>
<h1>Email Enrollees</h1>
<div id="form">
	<div class="row clearfix">			
	<div class="error_msg">
	  <?php echo validation_errors('<p>', '</p>');?>
	   <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	</div>
	<form class="form" action="" method="post" >
		
		<div class="row clearfix">
		   <div class="left_area">To</div>
		   <div class="right_area">
			 <input type="text" name="enrollee" value="Enrollees" maxlength="255" size="40"/>
		   </div>
		</div>
		
		
		<div class="row clearfix">
		   <div class="left_area">Subject <span class="required">*</span></div>
		   <div class="right_area">
			 <input type="text" name="subject" value="<?php echo $this->input->post('subject');?>" maxlength="255" size="40"/>
		   </div>
		</div>
		
		<div class="row clearfix">
		   <div class="left_area">Message<span class="required">*</span></div>
		   <div class="right_area">
		   <textarea name="message" rows="5"><?php echo $this->input->post('message');?></textarea>
			</div>
		   
		</div>
		
				
		<div class="row clearfix">  
		   <div class="left_area">&nbsp;</div>
		   <div class="right_area">
				<input type="submit" name="email_submit" value="Send" class="submit"/>
				<input type="button" value="Cancel" id="close_fancy" class="submit"/>
		   </div>
		</div>
	</form>
	
</div>