<?php 
/**
@Page/Module Name/Class: 		about.php
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
	<div class="popupTitle"><h1>Update About</h1></div>
	
	<form class="form" action="" method="post" enctype="multipart/form-data" >
    	<div class="row clearfix">
			<div class="left_area">User Bio <span class="required">*</span></div>
			<div class="right_area">
            <div class="error">
	  <?php echo validation_errors('<p>', '</p>');?>
	   <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
			<textarea name="usrBio" class="tinymce_editor updateabout"><?php echo isset( $user->usrBio ) ? $user->usrBio:$this->input->post('usrBio');?></textarea>
		   </div>
		</div>
		<div class="row clearfix">  
        <div class="left_area">&nbsp;</div>
		  <div class="right_area">
			<input type="submit" name="profile-submit" value="<?php echo (isset($user->id))?'Save':'Create'; ?>" class="submit"/>
			<input type="button" value="Cancel" id="close_fancy" class="submit"/>
			
		   </div>
		</div>
	</form>
	
</div>