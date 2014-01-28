<?php 
/**
@Page/Module Name/Class: 		activate.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display user account activation form
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<script>
jQuery(document).ready(function($) {
	$("#username").blur(function(){
		var userName = $(this).val();
		elm = $(this);
		check_username(userName,elm);
	});
});
function check_username(userName,elm){
	var form_data= {
        username : userName
        };
	$("#message").removeClass("green"); 
	$("#message").removeClass("red")	
	$('#message').html('checking availability..');	
	$.ajax({
		type: "POST",
		url : '<?php echo base_url().'user/check_username/';?>',
		data : form_data,
		dataType :'json',
		success : function(result) {
			if(result.error){
				$("#message").removeClass("green"); 
				$("#message").addClass("red");
				$('#message').html(result.error_message);
			}else{
				$("#message").removeClass("red"); 
				$("#message").addClass("green");
				$('#message').html(result.response);
			}
		}
	});
}
</script>
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
		   <div class="left_area">Username <span class="required">*</span></div>
		   <div class="right_area">
		   <input type="text" name="username" id="username" value="<?php echo $this->input->post('username') ?>" maxlength="255" size="40"/>
		   <div id="message"></div>
		   <div class="error"><?php echo form_error('username','',''); ?></div>
		   <div class="hint">Length between 5 and 15 characters. Do not include white spaces</div>
		   </div>
		</div>
		
		<div class="row clearfix">
		   <div class="left_area">Password <span class="required">*</span></div>
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
			<input type="submit" value="Activate" class="submit"/>
			
		   </div>
		</div>
	</form>
	
</div>