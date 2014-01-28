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
<div id="popupForm">
<div class="popupTitle"><h1>Update Profile</h1></div>

<div id="form">
				
	<div class="error">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" enctype="multipart/form-data" >
	
		
		<ul class="updateForm brdr_btm">
		<li>
				<div class="left_area"><span class="required">*</span>Required Fields</div>
				<div class="right_area">
				</div>
		</li>
		<h2>Account Info</h2>
			<li>
			  <label>Username <span class="required">*</span></label>
			  <div class="formRight">
			   <input type="text" name="userName" value="<?php echo isset($user->userName)?$user->userName:$this->input->post('userName');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('userName','',''); ?></div>
			   <div class="hint">Length between 5 and 15 characters. Do not include white spaces.</div>
			   </div>
			</li>
		
			<li>
			  <label>Email <span class="required">*</span></label>
			  <div class="formRight">
			   <input type="text" name="email" value="<?php echo isset($user->email)?$user->email:$this->input->post('email');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('email','',''); ?></div>
			   </div>
			</li>
					
		</ul>	
		
		<ul class="updateForm brdr_btm">
			<h2>General Info</h2>
			<li>
			  <label>First Name <span class="required">*</span></label>
			  <div class="formRight">
			   <input type="text" name="firstName" value="<?php echo isset($user->firstName)?$user->firstName:$this->input->post('firstName');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('firstName','',''); ?></div>
			   </div>
			</li>
		
			<li>
			  <label>Last Name<span class="required">*</span></label>
			  <div class="formRight">
			   <input type="text" name="lastName" value="<?php echo isset( $user->lastName ) ? $user->lastName:$this->input->post('lastName');?>" maxlength="255" size="40"/>
			     <div class="error"><?php echo form_error('lastName','',''); ?></div>
			   </div>
			</li>
			
			<li>
			  <label>Gender<span class="required">*</span></label>
			  <div class="formRight">
				<input type="radio" name="gender" id="role_M" <?php echo (isset( $user->gender ) && $user->gender=='M' )?'checked="checked"':"";?>  value="M" /><label for="role_M">Male</label>
				<input type="radio" name="gender" id="role_F" <?php echo (isset( $user->gender) && $user->gender=='F' )?'checked="checked"':"";?> value="F" /><label for="role_">Female</label>
				<div class="error"><?php echo form_error('gender','',''); ?></div>
			   </div>
			</li>
			
			<li>
				<label>Birth Date </label>
				<?php 
					$birth_date='';
					$birth_date=isset($user->birthDate)?$user->birthDate:'';
					$birth_date = explode('-',$birth_date);
					$birth_year=(isset($birth_date[0]) && ''!=$birth_date[0])?$birth_date[0]:$this->input->post('birth_year');
					if(''== $birth_year)
						$birth_year=1970;
					$birth_month=isset($birth_date[1])?$birth_date[1]:$this->input->post('birth_month');
					$birth_day=isset($birth_date[2])?$birth_date[2]:$this->input->post('birth_day');
				?>
				
				<div class="formRight">
				<span><?php  echo form_dropdown('birth_day',get_days_array(true,array(''=>'Day')),$birth_day,'class="inline-10"'); ?></span>
				<span><?php  echo form_dropdown('birth_month',get_months_array(true,array(''=>'Month')),$birth_month,'class="inline-10"'); ?></span>
				<span><?php  echo form_dropdown('birth_year',get_years_array(1940,date('Y')-10,true,array(''=>'Year')),$birth_year,'class="inline-10"'); ?></span>
				<div class="error"><?php echo form_error('birthDate','',''); ?></div>
				</div>
			</li>
			
			<li>
				<label>Secondary email</label>
				<div class="formRight">
				<input type="text" name="email2"  value="<?php echo isset( $user->email2 ) ? $user->email2:$this->input->post('email2');?>" maxlength="255" size="40"/>
				<div class="error"><?php echo form_error('email2','',''); ?></div>
				</div>
			</li>
							
			<li>
			  <label>Address <span class="required">*</span></label>
			  <div class="formRight">
				<textarea name="address"  rows="5"><?php echo isset( $user->address ) ? $user->address:$this->input->post('address');?></textarea>
				<div class="error"><?php echo form_error('address','',''); ?></div>
			   </div>
			</li>
			
			<li>
			  <label>City <span class="required">*</span></label>
			  <div class="formRight">
			   <input type="text" name="city" value="<?php echo isset( $user->city ) ? $user->city:$this->input->post('city');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('city','',''); ?></div>
			   </div>
			</li>
			
			<li>
			  <label>State <span class="required">*</span></label>
			  <div class="formRight">
			   <input type="text" name="state" value="<?php echo isset( $user->state ) ? $user->state:$this->input->post('state');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('state','',''); ?></div>
			   </div>
			</li>
			
			<li>
			  <label>Zip <span class="required">*</span></label>
			  <div class="formRight">
			   <input type="text" name="zip" value="<?php echo isset( $user->zip ) ? $user->zip:$this->input->post('zip');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('zip','',''); ?></div>
			   </div>
			</li>
			
			
			<li>
			  <label>Phone <span class="required">*</span></label>
			  <div class="formRight">
			   <input type="text" name="phone" value="<?php echo isset( $user->phone ) ? $user->phone:$this->input->post('phone');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('phone','',''); ?></div>
			   </div>
			</li>
			<li>
			  <label>Cell Phone</label>
			  <div class="formRight">
			   <input type="text" name="mobileCarrier" value="<?php echo isset( $user->mobileCarrier ) ? $user->mobileCarrier:$this->input->post('mobileCarrier');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('mobileCarrier','',''); ?></div>
			   </div>
			</li>
			
		</ul>	
			
			
			
			
		<ul class="updateForm brdr_btm">	
			<h2>Web Presence/Social Networking</h2>
			<li>
			  <label>Twitter ID</label>
			  <div class="formRight">
			   <input type="text" name="twitter" value="<?php echo isset( $user->twitter ) ? $user->twitter:$this->input->post('twitter');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('twitter','',''); ?></div>
			   </div>
			</li>
			
			<li>
			  <label>AIM ID</label>
			  <div class="formRight">
			   <input type="text" name="aim" value="<?php echo isset( $user->aim ) ? $user->aim:$this->input->post('aim');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('aim','',''); ?></div>
			   </div>
			</li>
			
			<li>
			  <label>MSN/Windows Live ID</label>
			  <div class="formRight">
			   <input type="text" name="msn" value="<?php echo isset( $user->msn ) ? $user->msn:$this->input->post('msn');?>" maxlength="255" size="40"/>
			     <div class="error"><?php echo form_error('msn','',''); ?></div>
			   </div>
			</li>
			
			<li>
			  <label>Facebook</label>
			  <div class="formRight">
			   <input type="text" name="facebook" value="<?php echo isset( $user->facebook ) ? $user->facebook:$this->input->post('facebook');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('facebook','',''); ?></div>
			   <div class="hint">Please enter the complete URL to your Facebook page. You can load it in another window/tab and then paste it back into here.</div>
			   </div>
			</li>
			
						
			<li>
			  <label>Site URL</label>
			  <div class="formRight">
			   <input type="text" name="siteURL" value="<?php echo isset( $user->siteURL ) ? $user->siteURL:$this->input->post('siteURL');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('siteURL','',''); ?></div>
			   </div>
			</li>
		</ul>	
		<ul class="updateForm">
		<li>  
		  <label>&nbsp;</label>
		  <div class="formRight">
			<input type="submit" value="<?php echo (isset($user->id))?'Save':'Create'; ?>" class="submit"/>
			<input type="button" value="Cancel" id="close_fancy" class="submit"/>
		   </div>
		 </li>  
		</ul>
	</form>
	</div>
</div>