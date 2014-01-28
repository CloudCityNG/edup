<?php 
/**
@Page/Module Name/Class: 		edit_profile.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display user account edit form
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
		theme: "modern",
		menubar: false,
		paste_auto_cleanup_on_paste : true,
		skin : "lightgray",
		theme_modern_buttons1 : "",
		theme_modern_buttons2 : "",
		toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink  advlist |  preview  fullpage | code ", 
  
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
<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>	
<div id="form">
				
	<div class="error_msg error">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" enctype="multipart/form-data" >
		<div class="profileInformation">
			<h3>Account Info</h3>
            <ul class="updateForm brdr_btm">
                <li>
			   <label>Username <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="userName" value="<?php echo empty( $user->userName ) ? $this->input->post('userName'):$user->userName;?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('userName','',''); ?></div>
			   <div class="hint">Length between 5 and 15 characters. Do not include white spaces</div>
			   </div>
			</li>
		
			<li>
			   <label>Email <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="email" value="<?php echo empty( $user->email ) ? $this->input->post('email'):$user->email;?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('email','',''); ?></div>
			   </div>
			</li>
		</ul>	
			<h3>General Info</h3>
			<ul class="updateForm brdr_btm">
                         <li>
			   <label>First Name <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="firstName" value="<?php echo empty( $user->firstName ) ? $this->input->post('firstName'):$user->firstName;?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('firstName','',''); ?></div>
			   </div>
			</li>
		
			<li>
			   <label>Last Name<span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="lastName" value="<?php echo empty( $user->lastName ) ? $this->input->post('lastName'):$user->lastName;?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('lastName','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Gender<span class="required">*</span></label>
			   <div class="formRight">
				<?php $gender=empty( $user->gender ) ? $this->input->post('gender'):$user->gender; ?>
				<input type="radio" name="gender" id="role_M" <?php echo ($gender=='M' )?'checked="checked"':"";?>  value="M" /><label for="role_M">Male</label>
				<input type="radio" name="gender" id="role_F" <?php echo ($gender=='F' )?'checked="checked"':"";?> value="F" /><label for="role_">Female</label>
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
				<input type="file" name="profileImage"/><span class="imageMessage">(only .jpg,.jpeg.png,.gif allowed)</span>
				<div class="error"><?php echo form_error('file','',''); ?></div>
				
			   </div>
			</li>
			<?php /* need the comment for future
			<li>
			   <label>Legal Photo Release</label>
			   <div class="formRight">
				<?php 
					$default_status= ($this->input->post('legalPhotoRelease') != '')?$this->input->post('legalPhotoRelease'):STATUS_YES;
					$selectd_status =  isset( $user->legalPhotoRelease )?$user->legalPhotoRelease:$default_status;
					echo form_dropdown('legalPhotoRelease',get_yesNO_array(),$selectd_status);
				?>
				<?php  ?>
			   </div>
			</li>
			*/?>
			<li>
				<label>Birth Date <span class="required">*</span></label>
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
				<span><?php  echo form_dropdown('birth_day',get_days_array(true,array(''=>'Day')),$birth_day,'class="inline-10 date_drop_down"'); ?></span>
				<span><?php  echo form_dropdown('birth_month',get_months_array(true,array(''=>'Month')),$birth_month,'class="inline-10 date_drop_down"'); ?></span>
				<span><?php  echo form_dropdown('birth_year',get_years_array(1940,date('Y')-10,true,array(''=>'Year')),$birth_year,'class="inline-10 date_drop_down"'); ?></span>
				<div class="error"><?php echo form_error('birthDate','',''); ?></div>
				</div>
			</li>
			
			<li>
				<label>Secondary email	</label>
				<div class="formRight">
				<input type="text" name="email2"  value="<?php echo empty( $user->email2 ) ? $this->input->post('email2'):$user->email2;?>" maxlength="255" size="40"/>
				<div class="error"><?php echo form_error('email2','',''); ?></div>
				</div>
			</li>
			
			<!--<li>
			   <label>Receive System Emails</label>
			   <div class="formRight">
				<?php 
					//$default_status= ($this->input->post('receiveSystemEmails') != '')?$this->input->post('receiveSystemEmails'):STATUS_YES;
					//$selectd_status =  isset( $user->receiveSystemEmails )?$user->receiveSystemEmails:$default_status;
					//echo form_dropdown('receiveSystemEmails',get_yesNO_array(),$selectd_status);
				?>
				<div class="error"><?php //echo form_error('receiveSystemEmails','',''); ?></div>
			   </div>
			</li>-->
		</ul>
		
			<h3>Profile Info</h3>
			<ul class="updateForm brdr_btm">
                
				<li>
			   <label>User Bio</label>
			   <div class="formRight">
				<textarea name="usrBio" class="tinymce_editor" rows="5"><?php echo empty( $user->usrBio ) ? $this->input->post('usrBio'):$user->usrBio;?></textarea>
				<div class="error"><?php echo form_error('usrBio','',''); ?></div>
			   </div>
			</li>
		
			<li>
			   <label>Address <span class="required">*</span></label>
			   <div class="formRight">
				<textarea name="address"  rows="5"><?php echo empty( $user->address ) ? $this->input->post('address'):$user->address;?></textarea>
				<div class="error"><?php echo form_error('address','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>City <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="city" value="<?php echo empty( $user->city ) ? $this->input->post('city'):$user->city;?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('city','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>State <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="state" value="<?php echo empty( $user->state ) ? $this->input->post('state'):$user->state;?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('state','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Zip <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="zip" value="<?php echo empty( $user->zip ) ? $this->input->post('zip'):$user->zip;?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('zip','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Phone <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="phone" value="<?php echo empty( $user->phone ) ? format_phone_number($this->input->post('phone')): format_phone_number($user->phone);?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('phone','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Cell Phone</label>
			   <div class="formRight">
			   <input type="text" name="mobileCarrier" value="<?php echo empty( $user->mobileCarrier ) ? format_phone_number($this->input->post('mobileCarrier')): format_phone_number($user->mobileCarrier);?>" maxlength="255" size="40"/>
			   </div>
			</li>
			
		</ul>	
			
                <h3>Web Presence/Social Networking </h3>	
                    <ul class="updateForm brdr_btm">
                        <li>
			   <label>Twitter ID</label>
			   <div class="formRight">
			   <input type="text" name="twitter" value="<?php echo empty( $user->twitter ) ? $this->input->post('twitter'):$user->twitter;?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('twitter','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>AIM ID</label>
			   <div class="formRight">
			   <input type="text" name="aim" value="<?php echo empty( $user->aim ) ? $this->input->post('aim'):$user->aim;?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('aim','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>MSN/Windows Live ID</label>
			   <div class="formRight">
			   <input type="text" name="msn" value="<?php echo empty( $user->msn ) ? $this->input->post('msn'):$user->msn;?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('msn','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Facebook</label>
			   <div class="formRight">
			   <input type="text" name="facebook" value="<?php echo empty( $user->facebook ) ? $this->input->post('facebook'):$user->facebook;?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('facebook','',''); ?></div>
			   <div class="hint">Please enter the complete URL to your Facebook page. You can load it in another window/tab and then paste it back into here.</div>
			   </div>
			</li>
			
			<li>
			   <label>Site URL</label>
			   <div class="formRight">
			   <input type="text" name="siteURL" value="<?php echo empty( $user->siteURL ) ? $this->input->post('siteURL'):$user->siteURL;?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('siteURL','',''); ?></div>
			   </div>
			</li>
		</ul>	
			<h3>Employment Info</h3>
			<ul class="updateForm brdr_btm">
			<li>
                <label>Intermediate Unit<span class="required">*</span></label>
                <div class="formRight">
                    <?php 
                 $selected_iuUnit = isset( $user->iuID ) ? $user->iuID:$this->input->post('dis_iu_unit');
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
                             $selected_dis_unit =   isset( $user->districtID ) ? $user->districtID:$this->input->post('school_district');
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
			   <label>School Building Assigned<span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="buildingAssigned" value="<?php echo empty( $user->buildingAssigned ) ? $this->input->post('buildingAssigned'):$user->buildingAssigned;?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('buildingAssigned','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>School Address<span class="required">*</span></label>
			   <div class="formRight">
				<textarea name="buildingAddress"  rows="5"><?php echo empty( $user->buildingAddress ) ? $this->input->post('buildingAddress'):$user->buildingAddress;?></textarea>
				<div class="error"><?php echo form_error('buildingAddress','',''); ?></div>
			   </div>
			</li>	
			
			<li>
			   <label>City<span class="required">*</span></label>
			   <div class="formRight">
				 <input type="text" name="buildingCity" value="<?php echo empty( $user->buildingCity ) ? $this->input->post('buildingCity'):$user->buildingCity;?>" maxlength="255" size="40"/>
				 <div class="error"><?php echo form_error('buildingCity','',''); ?></div>
			   </div>
			</li>
			<li>
			   <label>State<span class="required">*</span></label>
			   <div class="formRight">
				 <input type="text" name="buildingState" value="<?php echo empty( $user->buildingState ) ? $this->input->post('buildingState'):$user->buildingState;?>" maxlength="255" size="40"/>
				 <div class="error"><?php echo form_error('buildingState','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>ZIP<span class="required">*</span></label>
			   <div class="formRight">
				 <input type="text" name="buildingZip" value="<?php echo empty( $user->buildingZip ) ? $this->input->post('buildingZip'):$user->buildingZip;?>" maxlength="255" size="40"/>
				 <div class="error"><?php echo form_error('buildingZip','',''); ?></div>
			   </div>
			   
			</li>
			
			<li>
			   <label>Role<span class="required">*</span></label>
			   <div class="formRight">
			   <?php echo $this->input->post('role'); ?>
				<div><input id="role-Administrator" type="radio" name="role" <?php echo ((isset( $user->role ) && $user->role=='Administrator') || ('Administrator'==$this->input->post('role')) )?'checked="checked"':"";?>  value="Administrator" /><label for="role-Administrator">Administrator</label></div>
				
				<div><input id="role-Teacher" type="radio" name="role" <?php echo ((isset( $user->role ) && $user->role=='Teacher') || ('Teacher'==$this->input->post('role')) )?'checked="checked"':"";?>  value="Teacher" /><label for="role-Teacher">Teacher</label></div>
				
				<div><input id="role-IU_Support" type="radio" name="role" <?php echo ((isset( $user->role ) && $user->role=='IU_Support') || ('IU_Support'==$this->input->post('role')) )?'checked="checked"':"";?>  value="IU_Support" /><label for="role-IU_Support">IU_Support</label></div>
				
				<div><input id="role-Volunteer" type="radio" name="role" <?php echo ((isset( $user->role ) && $user->role=='Volunteer') || ('Volunteer'==$this->input->post('role')) )?'checked="checked"':"";?>  value="Volunteer" /><label for="role-Volunteer">Volunteer</label></div>
				<div class="error"><?php echo form_error('role','',''); ?></div>
			</div>	
			</li>
			
			<li>
			   <label>Years in this role<span class="required">*</span></label>
			   <div class="formRight">
				 <input type="text" name="yearsActive" value="<?php echo empty( $user->yearsActive ) ? $this->input->post('yearsActive'):$user->yearsActive;?>" maxlength="255" size="40"/>
				 <div class="error"><?php echo form_error('yearsActive','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>
                            How many grad courses are you likely to take within the next two school years 
                            <span class="required">*</span></label>
			   <div class="formRight">
				 <?php 
					$default_status= ($this->input->post('gradCoursesTaking') != '')?$this->input->post('gradCoursesTaking'):STATUS_YES;
					$selectd_status =  isset( $user->gradCoursesTaking )?$user->gradCoursesTaking:$default_status;
					echo form_dropdown('gradCoursesTaking',get_numbers_array($start=0,$end=5,true,array(''=>'Select')),$selectd_status);
				?>
				 <div class="error"><?php echo form_error('gradCoursesTaking','',''); ?></div>
			   </div>
			</li>
			</ul>
                        
			<h3>Teaching Discipline</h3>
                        <ul class="updateForm">
                            <li>
                               <label>What do you teach? <span class="required">*</span></label>
                               <div class="formRight">
                                    <?php 
                                            $selected_grade_subject =  isset( $user->gradeSubject ) ? $user->gradeSubject:$this->input->post('gradeSubject');
                                            $grade_subject_array=get_dropdown_array('tracks',$where_condition=array('cnfID'=>STATUS_PUBLISH),$order_by='nestedMenuOrder',$order='ASC','trID','trName','',true,array(''=>'Select'));
                                            echo form_dropdown('gradeSubject',$grade_subject_array,$selected_grade_subject,'id="id_csGenreId"');
                                    ?>
									<div class="error"><?php echo form_error('gradeSubject','',''); ?></div>
									
                                    </div>
                            </li> 
				
				<li>
				   <label>Grade Levels<span class="required">*</span></label>
				   <div class="formRight">
					<?php 
					$selected_grade_levels = array();
					if(isset($user->level )&& $user->level != ''){
						$selected_grade_levels=explode(',',$user->level);
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
                                <input type="submit" value="<?php echo (isset($user->id))?'Save':'Create'; ?>" class="submit"/>
                               </div>
                            </li>
             </ul>
			 </div>
	</form>
</div>