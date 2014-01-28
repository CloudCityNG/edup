<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>	
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div id="form">
<div class="row clearfix">
	<div class="left_area">&nbsp;</div>
	<div class="right_area">
		<div class="error_msg error">
			  <?php if(isset($errors) && count($errors)>0 ): 
					foreach($errors as $error){
						echo '<p>'.$error.'</p>';	
					}
				endif; ?>					
		</div>
	</div>
</div>

	<form class="form" id="loginForm" action="" method="post" >
		<div class="row clearfix">
		   <div class="left_area">Username <span class="required">*</span></div>
		   <div class="right_area">
		   <input type="text" name="username" value="<?php echo $this->input->post('username');?>" maxlength="255" size="40"/>
		   <div class="error"><?php echo form_error('username','',''); ?></div>
		   </div>
		</div>
		
		<div class="row clearfix" style="padding-bottom:5px;">
		   <div class="left_area">Password <span class="required">*</span></div>
		   <div class="right_area">
		   <input type="password" name="password" value="" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('password','',''); ?></div>
		   </div>
		</div>
		
		<div class="row clearfix">  
		   <div class="left_area">&nbsp;</div>
		   <div class="right_area">
		   <div class="left">
			<?php echo anchor('user/forgot_credential?ref=password','Forgot password'); ?> |
			<?php echo anchor('user/forgot_credential?ref=username','Forgot username'); ?>
			</div>
			<div class="left"><input type="submit" value="Login" class="submit"/></div>
			
		   </div>
		</div>
		
	</form>
</div><!--#form-->