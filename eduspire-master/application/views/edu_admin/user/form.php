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
		relative_urls : false,
		remove_script_host : true,
		convert_urls : true,
		theme_modern_buttons1 : "",
		theme_modern_buttons2 : "",
		toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image jbimages advlist |  preview media fullpage | code ", 
  
	}); 
	
   
    jQuery(document).ready(function($) {
        $(document).on('change',"#iuUnitDropDown",function(){
			$.ajax({
				url: '<?php echo base_url().'login/iu_districts/';?>'+$(this).val(),
				success: function(data) {
					$("#iuBasedDistrictContainer").html(data);
				}	
			});
		});
    });

</script>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="backButton clearfix">
	 <?php echo anchor('edu_admin/user/','Back','class="submit"'); ?> 
</div>

<div id="form" class="createUser">
				
	<div class="error_msg">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" enctype="multipart/form-data" >
	<div class="profileInformation">
			<ul class="updateForm brdr_btm">
            <h2>Login Info</h2>
                            <li>
			   <label>Username <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="userName" value="<?php echo isset($result->userName)?$result->userName:$this->input->post('userName');?>" />
			   <div class="hint">Length between 5 and 15 characters. Do not include white spaces.</div>
			    <div class="error"><?php echo form_error('userName','',''); ?></div>
			   </div>
			</li>
		
			<li>
			   <label>Email <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="email" value="<?php echo isset($result->email)?$result->email:$this->input->post('email');?>" />
			     <div class="error"><?php echo form_error('email','',''); ?></div>
			   </div>
			</li>
			<?php if(!isset($result->id)): ?>
			<li>
			   <label>Password <span class="required">*</span></label>
			   <div class="formRight">
                            <input type="password" name="password" value="" />
                            <div class="hint">Password length must  between 5 to 12 characters</div>
			    <div class="error"><?php echo form_error('password','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Confirm Password <span class="required">*</span></label>
			   <div class="formRight">
                            <input type="password" name="c_password" value="" />
			    <div class="error"><?php echo form_error('c_password','',''); ?></div>
			   </div>
			</li>
			<?php endif; ?>
			<li>
			   <label>Access Level <span class="required">*</span></label>
			   <div class="formRight">
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
                       <div class="formRight">
                            <?php 
                                    $default_status= ($this->input->post('activationFlag') != '')?$this->input->post('activationFlag'):ACCOUNT_ACTIVE;
                                    $selectd_status =  isset( $result->activationFlag )?$result->activationFlag:$default_status;
                                    echo form_dropdown('activationFlag',$this->user_model->get_status_array(),$selectd_status);
                            ?>
                            <div class="error"><?php echo form_error('activationFlag','',''); ?></div>
                       </div>
                    </li>
					
			<li>
		   <label>&nbsp;</label>
		    <div class="formRight">
			<?php 
				$default_featured= ($this->input->post('isAboutUs') != '')?$this->input->post('isAboutUs'):0;
				$selected_featured =  isset( $result->isAboutUs )?$result->isAboutUs:$default_featured;
				
				
			?>
			<input type="checkbox" name="isAboutUs" value="<?php echo FEATURED ?>" <?php echo (FEATURED==$selected_featured)?'checked="checked"':''; ?>/>
			Visible on About us?
			</div>
		</li> 		
                   </ul>
                    
                    <ul class="updateForm brdr_btm">
                    <h2>General Info</h2>
                      <li>
                       <label>First Name <span class="required">*</span></label>
                       <div class="formRight">
                       <input type="text" name="firstName" value="<?php echo isset($result->firstName)?$result->firstName:$this->input->post('firstName');?>" />
                       <div class="error"><?php echo form_error('firstName','',''); ?></div>
                       </div>
                    </li>
		
			<li>
			   <label>Last Name<span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="lastName" value="<?php echo isset( $result->lastName ) ? $result->lastName:$this->input->post('lastName');?>" />
			   <div class="error"><?php echo form_error('lastName','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Gender<span class="required">*</span></label>
			   <div class="formRight">
				<input type="radio" name="gender" id="role_M" <?php echo (isset( $result->gender ) && $result->gender=='M' )?'checked="checked"':"";?>  value="M" /><label for="role_M">Male</label>
				<input type="radio" name="gender" id="role_F" <?php echo (isset( $result->gender) && $result->gender=='F' )?'checked="checked"':"";?> value="F" /><label for="role_">Female</label>
				<div class="error"><?php echo form_error('gender','',''); ?></div>
			   </div>
			</li>
			
			<?php if(isset($image) && $image != ''): ?>
			<li>
			   <label>Uploaded Image </label>
			   <div class="formRight">
				<?php $img_path=  base_url().'uploads/users/'.$image; ?>
				<img src="<?php echo crop_image($img_path);?>" height="100" width="100"/>
				<input type="hidden" name="old_image" value="<?php echo $image; ?>" />
			   </div>
			</li>
			<?php endif; ?>
		
			<li>
			   <label>Profile Image </label>
			   <div class="formRight">
				<input type="file" name="profileImage"/> <span class="imageMessage">(only .jpg,.jpeg.png,.gif allowed)</span><br/>
			   </div>
			</li>
			
			
			<li>
				<label>Birth Date<span class="required">*</span> </label>
				<?php 
					$birth_date='';
					$birth_date=isset($result->birthDate)?$result->birthDate:'';
					$birth_date = explode('-',$birth_date);
					$birth_year=(isset($birth_date[0]) && ''!=$birth_date[0])?$birth_date[0]:$this->input->post('birth_year');					if(''== $birth_year)						$birth_year=1970;
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
				<label>Secondary email	</label>
				<div class="formRight">
				<input type="text" name="email2"  value="<?php echo isset( $result->email2 ) ? $result->email2:$this->input->post('email2');?>" />
				<div class="error"><?php echo form_error('email2','',''); ?></div>
				</div>
			</li>
		
					
			<li>
			   <label>Receive System Emails</label>
			   <div class="formRight">
				<?php 
					$default_status= ($this->input->post('receiveSystemEmails') != '')?$this->input->post('receiveSystemEmails'):STATUS_YES;
					$selectd_status =  isset( $result->receiveSystemEmails )?$result->receiveSystemEmails:$default_status;
					echo form_dropdown('receiveSystemEmails',get_yesNO_array(),$selectd_status);
				?>
				<div class="error"><?php echo form_error('receiveSystemEmails','',''); ?></div>
			   </div>
			</li>
		</ul>
		
			
			<ul class="updateForm brdr_btm">
            <h2>Profile Info</h2>
                         <li>
			   <label>User Bio</label>
			   <div class="formRight">
				<textarea name="usrBio" class="tinymce_editor" rows="5"><?php echo isset( $result->usrBio ) ? $result->usrBio:$this->input->post('usrBio');?></textarea>
				<div class="error"><?php echo form_error('usrBio','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Address <span class="required">*</span></label>
			   <div class="formRight">
				<textarea name="address"  rows="5"><?php echo isset( $result->address ) ? $result->address:$this->input->post('address');?></textarea>
				<div class="error"><?php echo form_error('address','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>City <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="city" value="<?php echo isset( $result->city ) ? $result->city:$this->input->post('city');?>" />
			   <div class="error"><?php echo form_error('city','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>State <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="state" value="<?php echo isset( $result->state ) ? $result->state:$this->input->post('state');?>" />
			    <div class="error"><?php echo form_error('state','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Zip <span class="required">*</span></label>
			   <label class="formRight">
			   <input type="text" name="zip" value="<?php echo isset( $result->zip ) ? $result->zip:$this->input->post('zip');?>" />
			   <div class="error"><?php echo form_error('zip','',''); ?></div>
			   </label>
			</li>
			
			
			<li>
			   <label>Phone <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="phone" value="<?php echo isset( $result->phone ) ? $result->phone:$this->input->post('phone');?>" />
			    <div class="error"><?php echo form_error('phone','',''); ?></div>
			   </div>
			</li>
			<li>
			   <label>Cell Phone</label>
			   <div class="formRight">
			   <input type="text" name="mobileCarrier" value="<?php echo isset( $result->mobileCarrier ) ? $result->mobileCarrier:$this->input->post('mobileCarrier');?>" />
			    <div class="error"><?php echo form_error('mobileCarrier','',''); ?></div>
			   </div>
			</li>
                    </ul>	
			
			
			<ul class="updateForm brdr_btm">
            <h2>Web Presence/Social Networking</h2>	
                        <li>
			   <label>Twitter ID</label>
			   <div class="formRight">
			   <input type="text" name="twitter" value="<?php echo isset( $result->twitter ) ? $result->twitter:$this->input->post('twitter');?>" />
			    <div class="error"><?php echo form_error('twitter','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>AIM ID</label>
			   <div class="formRight">
			   <input type="text" name="aim" value="<?php echo isset( $result->aim ) ? $result->aim:$this->input->post('aim');?>" />
			     <div class="error"><?php echo form_error('aim','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>MSN/Windows Live ID</label>
			   <div class="formRight">
			   <input type="text" name="msn" value="<?php echo isset( $result->msn ) ? $result->msn:$this->input->post('msn');?>" />
			    <div class="error"><?php echo form_error('msn','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Facebook</label>
			   <div class="formRight">
			   <input type="text" name="facebook" value="<?php echo isset( $result->facebook ) ? $result->facebook:$this->input->post('facebook');?>" />
			   <div class="hint">Please enter the complete URL to your Facebook page. You can load it in another window/tab and then paste it back into here.</div>
			    <div class="error"><?php echo form_error('facebook','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Site URL</label>
			   <div class="formRight">
			   <input type="text" name="siteURL" value="<?php echo isset( $result->siteURL ) ? $result->siteURL:$this->input->post('siteURL');?>" />
			   <div class="error"><?php echo form_error('siteURL','',''); ?></div>
			   </div>
			</li>
		</ul>	
			
			
			<ul class="updateForm brdr_btm">
                <h2>Employment Info</h2> 
                <li>
			   <label>District Affiliation</label>
			   <div class="formRight">
			   
			   <?php 
				$school_district =isset( $result->districtAffiliation ) ? $result->districtAffiliation:$this->input->post('districtAffiliation');
				if(is_numeric($school_district)){
				$school_district= get_single_value('district','disName','disID = '.$school_district) ;
				}
		   ?>
			   <input type="text" name="districtAffiliation" value="<?php echo $school_district;?>" />
			   <div class="error"><?php echo form_error('districtAffiliation','',''); ?></div>
			   <div class="hint">Old Field</div>
			   </div>
			</li>
			
			<li>
                <label>Intermediate Unit<span class="required">*</span></label>
                <div class="formRight">
                    <?php 
                 $selected_iuUnit = isset( $result->iuID ) ? $result->iuID:$this->input->post('dis_iu_unit');
                 $iu_unit_array=get_dropdown_array('iu_unit',$where_condition=array(),$order_by='iuID',$order='ASC','iuID','iuName','',true,array(''=>'Select'));	
                 echo form_dropdown('dis_iu_unit',$iu_unit_array,$selected_iuUnit,'id="iuUnitDropDown"');
                     ?>
                <div class="error"><?php echo form_error('dis_iu_unit','',''); ?></div>
				</div>
            </li>
            <li>
                <label>School District <span class="required">*</span></label>
                <div class="formRight">
                     <div id="iuBasedDistrictContainer">
                     <?php 
                             $selected_dis_unit =   isset( $result->districtID ) ? $result->districtID:$this->input->post('school_district');
                             $where_condition=array('disPublish'=>STATUS_PUBLISH);
							 if($selected_iuUnit !=='')
								$where_condition['disIuUnit']=$selected_iuUnit;
							 $school_district_array=get_dropdown_array('district',$where_condition,$order_by='disName',$order='ASC','disID','disName','',true,array(''=>'Select'));
                             echo form_dropdown('school_district',$school_district_array,$selected_dis_unit); ?>
                     </div>
                     <div class="error"><?php echo form_error('school_district','',''); ?></div>
                </div>
            </li>
			
			<li>
			   <label>Building Assigned</label>
			   <div class="formRight">
			   <input type="text" name="buildingAssigned" value="<?php echo isset( $result->buildingAssigned ) ? $result->buildingAssigned:$this->input->post('buildingAssigned');?>" />
			    <div class="error"><?php echo form_error('buildingAssigned','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Building Address</label>
			   <label class="formRight">
				<textarea name="buildingAddress"  rows="5"><?php echo isset( $result->buildingAddress ) ? $result->buildingAddress:$this->input->post('buildingAddress');?></textarea>
				 <div class="error"><?php echo form_error('buildingAddress','',''); ?></div>
			   </label>
			</li>	
			
			<li>
			   <label>Building City	</label>
			   <div class="formRight">
				 <input type="text" name="buildingCity" value="<?php echo isset( $result->buildingCity ) ? $result->buildingCity:$this->input->post('buildingCity');?>" />
				  <div class="error"><?php echo form_error('buildingCity','',''); ?></div>
			   </div>
			</li>
			<li>
			   <label>Building State</label>
			   <label class="formRight">
				 <input type="text" name="buildingState" value="<?php echo isset( $result->buildingState ) ? $result->buildingState:$this->input->post('buildingState');?>" />
				  <div class="error"><?php echo form_error('buildingState','',''); ?></div>
			   </label>
			</li>
			
			<li>
			   <label>Building Zip</label>
			   <div class="formRight">
				 <input type="text" name="buildingZip" value="<?php echo isset( $result->buildingZip ) ? $result->buildingZip:$this->input->post('buildingZip');?>" />
				  <div class="error"><?php echo form_error('buildingZip','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Role</label>
			   <div class="formRight">
			   
				<div><input id="role-Administrator" type="radio" name="role" <?php echo ((isset( $result->role ) && $result->role=='Administrator') || ('Administrator'==$this->input->post('role')) )?'checked="checked"':"";?>  value="Administrator" /><label for="role-Administrator">Administrator</label></div>
				
				<div><input id="role-Teacher" type="radio" name="role" <?php echo ((isset( $result->role ) && $result->role=='Teacher') || ('Teacher'==$this->input->post('role')) )?'checked="checked"':"";?>  value="Teacher" /><label for="role-Teacher">Teacher</label></div>
				
				<div><input id="role-IU_Support" type="radio" name="role" <?php echo ((isset( $result->role ) && $result->role=='IU_Support') || ('IU_Support'==$this->input->post('role')) )?'checked="checked"':"";?>  value="IU_Support" /><label for="role-IU_Support">IU_Support</label></div>
				
				<div><input id="role-Volunteer" type="radio" name="role" <?php echo ((isset( $result->role ) && $result->role=='Volunteer') || ('Volunteer'==$this->input->post('role')) )?'checked="checked"':"";?>  value="Volunteer" /><label for="role-Volunteer">Volunteer</label></div>
			 <div class="error"><?php echo form_error('role','',''); ?></div>	
			</div>	
			</li>
			
			<li>
			   <label>Years in this role</label>
			   <div class="formRight">
				 <input type="text" name="yearsActive" value="<?php echo isset( $result->yearsActive ) ? $result->yearsActive:$this->input->post('yearsActive');?>" />
				  <div class="error"><?php echo form_error('yearsActive','',''); ?></div>	
			   </div>
			</li>
			
			
			<li>
			   <label>How many grad courses are you likely to take within the next two school years</label>
			   <div class="formRight">
				 <?php 
					$default_status= ($this->input->post('gradCoursesTaking') != '')?$this->input->post('gradCoursesTaking'):STATUS_YES;
					$selectd_status =  isset( $result->gradCoursesTaking )?$result->gradCoursesTaking:$default_status;
					echo form_dropdown('gradCoursesTaking',get_numbers_array($start=0,$end=5,true,array(''=>'Select')),$selectd_status);
				?>
				<div class="error"><?php echo form_error('gradCoursesTaking','',''); ?></div>
			   </div>
			</li>
			</ul>		
			
			<ul class="updateForm">
			   <h2>Subject Area</h2>	
				<li>
			   <label>What do you teach?</label>
			   <div class="formRight">
					<?php 
							$selected_grade_subject =  isset( $result->gradeSubject ) ? $result->gradeSubject:$this->input->post('gradeSubject');
							$grade_subject_array=get_dropdown_array('tracks',$where_condition=array('cnfID'=>STATUS_PUBLISH),$order_by='nestedMenuOrder',$order='ASC','trID','trName','',true,array(''=>'Select'));	
							echo form_dropdown('gradeSubject',$grade_subject_array,$selected_grade_subject,'id="id_csGenreId"');
					?>
					<div class="error"><?php echo form_error('gradeSubject','',''); ?></div>
					</div>
			</li> 

			<li>
			   <label>Grade Levels</label>
			   <div class="formRight">
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
			   <div class="formRight">
					<input type="submit" value="<?php echo (isset($result->id))?'Save':'Create'; ?>" class="submit"/>

			   </div>
			</li>
		 </ul>
	</form>
	</div>
</div>