<?php 
/**
@Page/Module Name/Class: 	    change_password.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display add/edit form for user 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?> 
<script>
    tinymce.init({
		selector: "textarea.tinymce_editor",
		height:175,
		plugins: [
         "advlist autolink link image lists charmap  preview hr anchor pagebreak ",
         "searchreplace wordcount visualblocks visualchars code   media nonbreaking",
         "jbimages code"
		],
		menubar: false,
		theme: "modern",
		paste_auto_cleanup_on_paste : true,
		skin : "lightgray",
		theme_modern_buttons1 : "",
		theme_modern_buttons2 : "",
		toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image jbimages advlist |  preview media fullpage | code ", 
  
	}); 
</script>

<div class="backButton clearfix">
	 <?php echo anchor('edu_admin/user/','Back','class="submit"'); ?> 
</div>

<div id="form">
				
	<div class="error_msg">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" enctype="multipart/form-data" >
                        <h3>Login Info</h3>
			<ul class="updateForm">
                            <li>
			   <label>Username <span class="required">*</span></label>
			   <div class="right">
			   <input type="text" name="userName" value="<?php echo isset($result->userName)?$result->userName:$this->input->post('userName');?>" maxlength="255" size="40"/>
			   <div class="hint">Contain no white spaces length between 5 to 12 characters</div>
			    <div class="error"><?php echo form_error('userName','',''); ?></div>
			   </div>
			</li>
		
			<li>
			   <label>Email <span class="required">*</span></label>
			   <div class="right">
			   <input type="text" name="email" value="<?php echo isset($result->email)?$result->email:$this->input->post('email');?>" maxlength="255" size="40"/>
			     <div class="error"><?php echo form_error('email','',''); ?></div>
			   </div>
			</li>
			<?php if(!isset($result->id)): ?>
			<li>
			   <label>Password <span class="required">*</span></label>
			   <div class="right">
                            <input type="password" name="password" value="" maxlength="255" size="40"/>
                            <div class="hint">Password length must  between 5 to 12 characters</div>
			    <div class="error"><?php echo form_error('password','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Confirm Password <span class="required">*</span></label>
			   <div class="right">
                            <input type="password" name="c_password" value="" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('c_password','',''); ?></div>
			   </div>
			</li>
			<?php endif; ?>
			<li>
			   <label>Access Level <span class="required">*</span></label>
			   <div class="right">
				<?php 
					$default_status= ($this->input->post('accessLevel') != '')?$this->input->post('accessLevel'):MEMBER;
					$selectd_status =  isset( $result->accessLevel )?$result->accessLevel:$default_status;
					echo form_dropdown('accessLevel',$this->user_model->get_access_level_array(true,array(''=>'Select')),$selectd_status);
				?>
				 <div class="error"><?php echo form_error('accessLevel','',''); ?></div>
			   </div>
			</li>
                    <li>
                       <label>Account Status</label>
                       <div class="right">
                            <?php 
                                    $default_status= ($this->input->post('activationFlag') != '')?$this->input->post('activationFlag'):ACCOUNT_ACTIVE;
                                    $selectd_status =  isset( $result->activationFlag )?$result->activationFlag:$default_status;
                                    echo form_dropdown('activationFlag',$this->user_model->get_status_array(),$selectd_status);
                            ?>
                            <div class="error"><?php echo form_error('activationFlag','',''); ?></div>
                       </div>
                    </li>
                   </ul>
                    <h3>General Info</h3>
                    <ul class="updateForm">
                      <li>
                       <label>First Name <span class="required">*</span></label>
                       <div class="right">
                       <input type="text" name="firstName" value="<?php echo isset($result->firstName)?$result->firstName:$this->input->post('firstName');?>" maxlength="255" size="40"/>
                       <div class="error"><?php echo form_error('firstName','',''); ?></div>
                       </div>
                    </li>
		
			<li>
			   <label>Last Name<span class="required">*</span></label>
			   <div class="right">
			   <input type="text" name="lastName" value="<?php echo isset( $result->lastName ) ? $result->lastName:$this->input->post('lastName');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('lastName','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Gender<span class="required">*</span></label>
			   <div class="right">
				<input type="radio" name="gender" id="role_M" <?php echo (isset( $result->gender ) && $result->gender=='M' )?'checked="checked"':"";?>  value="M" /><label for="role_M">Male</label>
				<input type="radio" name="gender" id="role_F" <?php echo (isset( $result->gender) && $result->gender=='F' )?'checked="checked"':"";?> value="F" /><label for="role_">Female</label>
				<div class="error"><?php echo form_error('gender','',''); ?></div>
			   </div>
			</li>
			
			<?php if(isset($image) && $image != ''): ?>
			<li>
			   <label>Uploaded Image </label>
			   <div class="right">
				<?php $img_path=  base_url().'uploads/users/'.$image; ?>
				<img src="<?php echo crop_image($img_path);?>" height="100" width="100"/>
				<input type="hidden" name="old_image" value="<?php echo $image; ?>" />
			   </div>
			</li>
			<?php endif; ?>
		
			<li>
			   <label>Profile Image </label>
			   <div class="right">
				<input type="file" name="profileImage"/>(only .jpg,.jpeg.png,.gif allowed)<br/>
			   </div>
			</li>
		
			<li>
			   <label>Legal Photo Release</label>
			   <div class="right">
				<?php 
					$default_status= ($this->input->post('legalPhotoRelease') != '')?$this->input->post('legalPhotoRelease'):STATUS_YES;
					$selectd_status =  isset( $result->legalPhotoRelease )?$result->legalPhotoRelease:$default_status;
					echo form_dropdown('legalPhotoRelease',get_yesNO_array(),$selectd_status);
				?>
				<div class="error"><?php echo form_error('legalPhotoRelease','',''); ?></div>
			   </div>
			</li>
			
			<li>
				<label>Birth Date </label>
				<?php 
					$birth_date='';
					$birth_date=isset($result->birthDate)?$result->birthDate:'';
					$birth_date = explode('-',$birth_date);
					$birth_year=isset($birth_date[0])?$birth_date[0]:$this->input->post('birth_year');
					$birth_month=isset($birth_date[1])?$birth_date[1]:$this->input->post('birth_month');
					$birth_day=isset($birth_date[2])?$birth_date[2]:$this->input->post('birth_day');
				?>
				
				<div class="right">
				<span><?php  echo form_dropdown('birth_day',get_days_array(true,array(''=>'Day')),$birth_day,'class="inline-10"'); ?></span>
				<span><?php  echo form_dropdown('birth_month',get_months_array(true,array(''=>'Month')),$birth_month,'class="inline-10"'); ?></span>
				<span><?php  echo form_dropdown('birth_year',get_years_array(1960,date('Y')-10,true,array(''=>'Year')),$birth_year,'class="inline-10"'); ?></span>
				
				</div>
			</li>
			
			<li>
				<label>Secondry email	</label>
				<div class="right">
				<input type="text" name="email2"  value="<?php echo isset( $result->email2 ) ? $result->email2:$this->input->post('email2');?>" maxlength="255" size="40"/>
				<div class="error"><?php echo form_error('email2','',''); ?></div>
				</div>
			</li>
		
			<li>
			   <label>Act48</label>
			   <div class="right">
				<?php 
					$default_status= ($this->input->post('act48') != '')?$this->input->post('act48'):STATUS_YES;
					$selectd_status =  isset( $result->act48 )?$result->act48:$default_status;
					echo form_dropdown('act48',get_yesNO_array(),$selectd_status);
				?>
				<div class="error"><?php echo form_error('act48','',''); ?></div>
			   </div>
			</li>
		
			<li>
			   <label>Receive System Emails</label>
			   <div class="right">
				<?php 
					$default_status= ($this->input->post('receiveSystemEmails') != '')?$this->input->post('receiveSystemEmails'):STATUS_YES;
					$selectd_status =  isset( $result->receiveSystemEmails )?$result->receiveSystemEmails:$default_status;
					echo form_dropdown('receiveSystemEmails',get_yesNO_array(),$selectd_status);
				?>
				<div class="error"><?php echo form_error('receiveSystemEmails','',''); ?></div>
			   </div>
			</li>
		</ul>
		
			<h3>Profile Info</h3>
			<ul class="updateForm">
                         <li>
			   <label>User Bio</label>
			   <div class="right">
				<textarea name="usrBio" class="tinymce_editor" rows="5"><?php echo isset( $result->usrBio ) ? $result->usrBio:$this->input->post('usrBio');?></textarea>
				<div class="error"><?php echo form_error('usrBio','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Address</label>
			   <div class="right">
				<textarea name="address"  rows="5"><?php echo isset( $result->address ) ? $result->address:$this->input->post('address');?></textarea>
				<div class="error"><?php echo form_error('address','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>City</label>
			   <div class="right">
			   <input type="text" name="city" value="<?php echo isset( $result->city ) ? $result->city:$this->input->post('city');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('city','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>State</label>
			   <div class="right">
			   <input type="text" name="state" value="<?php echo isset( $result->state ) ? $result->state:$this->input->post('state');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('state','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Zip</label>
			   <label class="right">
			   <input type="text" name="zip" value="<?php echo isset( $result->zip ) ? $result->zip:$this->input->post('zip');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('zip','',''); ?></div>
			   </label>
			</li>
			
			
			<li>
			   <label>Phone</label>
			   <div class="right">
			   <input type="text" name="phone" value="<?php echo isset( $result->phone ) ? $result->phone:$this->input->post('phone');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('phone','',''); ?></div>
			   </div>
			</li>
			<li>
			   <label>Mobile Carrier</label>
			   <div class="right">
			   <input type="text" name="mobileCarrier" value="<?php echo isset( $result->mobileCarrier ) ? $result->mobileCarrier:$this->input->post('mobileCarrier');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('mobileCarrier','',''); ?></div>
			   </div>
			</li>
                    </ul>	
			
			<h3>WEB PRESENCE/SOCIAL NETWORKING</h3>	
			<ul class="updateForm">
                        <li>
			   <label>Twitter ID</label>
			   <div class="right">
			   <input type="text" name="twitter" value="<?php echo isset( $result->twitter ) ? $result->twitter:$this->input->post('twitter');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('twitter','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>AIM ID</label>
			   <div class="right">
			   <input type="text" name="aim" value="<?php echo isset( $result->aim ) ? $result->aim:$this->input->post('aim');?>" maxlength="255" size="40"/>
			     <div class="error"><?php echo form_error('aim','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>MSN/Windows Live ID</label>
			   <div class="right">
			   <input type="text" name="msn" value="<?php echo isset( $result->msn ) ? $result->msn:$this->input->post('msn');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('msn','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Facebook</label>
			   <div class="right">
			   <input type="text" name="facebook" value="<?php echo isset( $result->facebook ) ? $result->facebook:$this->input->post('facebook');?>" maxlength="255" size="40"/>
			   <div class="hint">Please enter the complete URL to your Facebook page. You can load it in another window/tab and then paste it back into here.</div>
			    <div class="error"><?php echo form_error('facebook','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Site URL</label>
			   <div class="right">
			   <input type="text" name="siteURL" value="<?php echo isset( $result->siteURL ) ? $result->siteURL:$this->input->post('siteURL');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('siteURL','',''); ?></div>
			   </div>
			</li>
		</ul>	
			<h3>Employment Info</h3>
			
			<ul class="updateForm">
                          <li>
			   <label>District Affiliation</label>
			   <div class="right">
			   <input type="text" name="districtAffiliation" value="<?php echo isset( $result->districtAffiliation ) ? $result->districtAffiliation:$this->input->post('districtAffiliation');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('districtAffiliation','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Building Assigned</label>
			   <div class="right">
			   <input type="text" name="buildingAssigned" value="<?php echo isset( $result->buildingAssigned ) ? $result->buildingAssigned:$this->input->post('buildingAssigned');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('buildingAssigned','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Building Address</label>
			   <label class="right">
				<textarea name="buildingAddress"  rows="5"><?php echo isset( $result->buildingAddress ) ? $result->buildingAddress:$this->input->post('buildingAddress');?></textarea>
				 <div class="error"><?php echo form_error('buildingAddress','',''); ?></div>
			   </label>
			</li>	
			
			<li>
			   <label>Building City	</label>
			   <div class="right">
				 <input type="text" name="buildingCity" value="<?php echo isset( $result->buildingCity ) ? $result->buildingCity:$this->input->post('buildingCity');?>" maxlength="255" size="40"/>
				  <div class="error"><?php echo form_error('buildingCity','',''); ?></div>
			   </div>
			</li>
			<li>
			   <label>Building State</label>
			   <label class="right">
				 <input type="text" name="buildingState" value="<?php echo isset( $result->buildingState ) ? $result->buildingState:$this->input->post('buildingState');?>" maxlength="255" size="40"/>
				  <div class="error"><?php echo form_error('buildingState','',''); ?></div>
			   </label>
			</li>
			
			<li>
			   <label>Building Zip</label>
			   <div class="right">
				 <input type="text" name="buildingZip" value="<?php echo isset( $result->buildingZip ) ? $result->buildingZip:$this->input->post('buildingZip');?>" maxlength="255" size="40"/>
				  <div class="error"><?php echo form_error('buildingZip','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Role</label>
			   <div class="right">
			   
				<div><input id="role-Administrator" type="radio" name="role" <?php echo ((isset( $result->role ) && $result->role=='Administrator') || ('Administrator'==$this->input->post('role')) )?'checked="checked"':"";?>  value="Administrator" /><label for="role-Administrator">Administrator</label></div>
				
				<div><input id="role-Teacher" type="radio" name="role" <?php echo ((isset( $result->role ) && $result->role=='Teacher') || ('Teacher'==$this->input->post('role')) )?'checked="checked"':"";?>  value="Teacher" /><label for="role-Teacher">Teacher</label></div>
				
				<div><input id="role-IU_Support" type="radio" name="role" <?php echo ((isset( $result->role ) && $result->role=='IU_Support') || ('IU_Support'==$this->input->post('role')) )?'checked="checked"':"";?>  value="IU_Support" /><label for="role-IU_Support">IU_Support</label></div>
				
				<div><input id="role-Volunteer" type="radio" name="role" <?php echo ((isset( $result->role ) && $result->role=='Volunteer') || ('Volunteer'==$this->input->post('role')) )?'checked="checked"':"";?>  value="Volunteer" /><label for="role-Volunteer">Volunteer</label></div>
			 <div class="error"><?php echo form_error('role','',''); ?></div>	
			</div>	
			</li>
			
			<li>
			   <label>Years in this role</label>
			   <div class="right">
				 <input type="text" name="yearsActive" value="<?php echo isset( $result->yearsActive ) ? $result->yearsActive:$this->input->post('yearsActive');?>" maxlength="255" size="40"/>
				  <div class="error"><?php echo form_error('yearsActive','',''); ?></div>	
			   </div>
			</li>
			
			
			<li>
			   <label>How many grade courses are you likely to take within the next two school years</label>
			   <div class="right">
				 <?php 
					$default_status= ($this->input->post('gradCoursesTaking') != '')?$this->input->post('gradCoursesTaking'):STATUS_YES;
					$selectd_status =  isset( $result->gradCoursesTaking )?$result->gradCoursesTaking:$default_status;
					echo form_dropdown('gradCoursesTaking',get_numbers_array($start=0,$end=5,true,array(''=>'Select')),$selectd_status);
				?>
				<div class="error"><?php echo form_error('gradCoursesTaking','',''); ?></div>
			   </div>
			</li>
			</ul>		
			<h3>Tracks</h3>	
                            <ul class="updateForm">
                                <li>
                               <label>Track</label>
                               <div class="right">
                                    <?php 
                                            $selected_grade_subject =  isset( $result->gradeSubject ) ? $result->gradeSubject:$this->input->post('gradeSubject');
                                            $grade_subject_array=get_dropdown_array('tracks',$where_condition=array(),$order_by='nestedMenuOrder',$order='ASC','trID','trName','',true,array(''=>'Select'));	
                                            echo form_dropdown('gradeSubject',$grade_subject_array,$selected_grade_subject,'id="id_csGenreId"');
                                    ?>
                                    <div class="error"><?php echo form_error('gradeSubject','',''); ?></div>
                                    </div>
                            </li> 

                            <li>
                               <label>Grade Levels</label>
                               <div class="right">
                                    <?php 
                                    $selected_grade_levels = array();
                                    if(isset($result->level )&& $result->level != ''){
                                            $selected_grade_levels=explode(',',$result->level);
                                    }else{
                                            $selected_grade_levels=$this->input->post('level');
                                    }
                                    if(!is_array($selected_grade_levels)){
                                            $selected_grade_levels = array();
                                    }
                                    $grade_level_array = get_grade_level_array();
                                    foreach($grade_level_array as $grade_key=>$grade_value):	
                                    $selected=(in_array($grade_key,$selected_grade_levels))?'checked="checked"':'';
                                    ?>
                                    <div><input id="level-<?php echo $grade_key ?>" <?php echo $selected; ?>type="checkbox" name="level[]"  value="<?php echo $grade_key ?>" /><label for="role-<?php echo $grade_key ?>"><?php echo $grade_value ?></label></div>
                                    <?php endforeach; ?>
                                    <div class="error"><?php echo form_error('level','',''); ?></div>
                            </div>
                            </li> 

                            <li>  
                               <label>&nbsp;</label>
                               <div class="right">
                                    <input type="submit" value="<?php echo (isset($result->id))?'Save':'Create'; ?>" class="submit"/>

                               </div>
                            </li>
                         </ul>
	</form>
</div>