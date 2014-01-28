<?php 
/**
@Page/Module Name/Class: 		unregister.php
@Author Name:			 		ben binesh
@Date:					 		Oct 17 2013
@Purpose:		        		display user account edit form for about content
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
<div id="popupForm">
	<div class="error_msg">
	  <?php echo validation_errors('<p>', '</p>');?>
	   <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" enctype="multipart/form-data" >
		<div class="popupTitle"><h1>Unregister</h1></div>
		<div class="row clearfix">
			<div class="full-width">Please take a moment to help us understand why you decided to unregister from this course:</div>
			<div class="full-width">
			<textarea name="reason"  class="checkouttext"><?php echo $this->input->post('reason');?></textarea>
		   </div>
		</div>
		<div class="row clearfix">  
		  <div class="right_area">
			<input type="submit" name="reason-submit" value="Submit" class="submit"/>
			<input type="button" value="Cancel" id="close_fancy" class="submit"/>
			
		   </div>
		</div>
	</form>
	
</div>