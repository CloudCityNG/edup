<?php 
/**
@Page/Module Name/Class: 		edit_profile.php
@Author Name:			 		ben binesh
@Date:					 		Oct 17, 2013
@Purpose:		        		display user account edit form for personal info
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

<div class="popupTitle"><h1>Update Image</h1></div>

<div id="form">
				
	<div class="error_msg">
	  <?php echo validation_errors('<p>', '</p>');?>
	   <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" enctype="multipart/form-data" >
		<?php if(isset($image) && $image != ''): ?>
			<div class="row clearfix">
			   <div class="left_area">Uploaded Image </div>
			   <div class="right_area">
				<?php $img_path=  base_url().'uploads/users/'.$image; ?>
				<img src="<?php echo crop_image($img_path);?>" height="100" width="100"/>
				<input type="hidden" name="old_image" value="<?php echo $image; ?>" />
			   </div>
			</div>
			<?php endif; ?>
		
		
			<div class="row clearfix">
			   <div class="left_area">Profile Image </div>
			   <div class="right_area">
				<input type="file" name="profileImage"/>
				<span class="imageMessage">(only .jpg,.jpeg.png,.gif allowed)</span><br/>
			   </div>
			</div>
		
		<div class="row clearfix">  
		   <div class="left_area">&nbsp;</div>
		   <div class="right_area">
			<input type="submit" name="form_submit" value="<?php echo (isset($user->id))?'Save':'Create'; ?>" class="submit"/>
			<input type="button" value="Cancel" id="close_fancy" class="submit"/>
		   </div>
		</div>
	</form>
	
</div>