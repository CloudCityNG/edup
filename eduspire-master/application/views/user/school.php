<?php 
/**
@Page/Module Name/Class: 		school.php
@Author Name:			 		ben binesh
@Date:					 		Oct 17, 2013
@Purpose:		        		display user account edit form for school info
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
	$(document).on('change',"#iuUnitDropDown",function(){
			$.ajax({
				url: '<?php echo base_url().'login/iu_districts/';?>'+$(this).val(),
				success: function(data) {
					$("#iuBasedDistrictContainer").html(data);
				}	
			});
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
<div class="popupTitle">
	<h1>Update School Info</h1>
</div>
<div id="form">
				
	<div class="error">
		<?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" enctype="multipart/form-data" >
		<ul class="updateForm">
		<li>
			<label><span class="required">*</span>Required Fields</label>
			<div class="formRight">
			</div>
		</li>
		
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
                            $where_condition=array('disPublish'=>STATUS_PUBLISH);							if($selected_iuUnit !==''){								$where_condition['disIuUnit']=$selected_iuUnit;							}	
							 $school_district_array=get_dropdown_array('district',$where_condition,$order_by='disName',$order='ASC','disID','disName','',true,array(''=>'Select'));
                             echo form_dropdown('school_district',$school_district_array,$selected_dis_unit); ?>
                     </div>
                     <div class="error"><?php echo form_error('school_district','',''); ?></div>
                </div>
            </li>
			
			<li>
			   <label>School Building Assigned<span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="buildingAssigned" value="<?php echo isset( $user->buildingAssigned ) ? $user->buildingAssigned:$this->input->post('buildingAssigned');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('buildingAssigned','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>School Address<span class="required">*</span></label>
			   <div class="formRight">
				<textarea name="buildingAddress"  rows="5"><?php echo isset( $user->buildingAddress ) ? $user->buildingAddress:$this->input->post('buildingAddress');?></textarea>
				<div class="error"><?php echo form_error('buildingAddress','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>City<span class="required">*</span></label>
			   <div class="formRight">
				 <input type="text" name="buildingCity" value="<?php echo isset( $user->buildingCity ) ? $user->buildingCity:$this->input->post('buildingCity');?>" maxlength="255" size="40"/>
				 <div class="error"><?php echo form_error('buildingCity','',''); ?></div>
			   </div>
			</li>
			<li>
			   <label>State<span class="required">*</span></label>
			   <div class="formRight">
				 <input type="text" name="buildingState" value="<?php echo isset( $user->buildingState ) ? $user->buildingState:$this->input->post('buildingState');?>" maxlength="255" size="40"/>
				  <div class="error"><?php echo form_error('buildingState','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>ZIP<span class="required">*</span></label>
			   <div class="formRight">
				 <input type="text" name="buildingZip" value="<?php echo isset( $user->buildingZip ) ? $user->buildingZip:$this->input->post('buildingZip');?>" maxlength="255" size="40"/>
				  <div class="error"><?php echo form_error('buildingZip','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Role<span class="required">*</span></label>
			   <div class="formRight">
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
				 <input type="text" name="yearsActive" value="<?php echo isset( $user->yearsActive ) ? $user->yearsActive:$this->input->post('yearsActive');?>" maxlength="255" size="40"/>
				  <div class="error"><?php echo form_error('yearsActive','',''); ?></div>
			   </div>
			</li>
			
			
			<li>
			   <label>How many grad courses are you likely to take within the next two school years <span class="required">*</span></label>
			   <div class="formRight">
				 <?php 
					$default_status= ($this->input->post('gradCoursesTaking') != '')?$this->input->post('gradCoursesTaking'):STATUS_YES;
					$selectd_status =  isset( $user->gradCoursesTaking )?$user->gradCoursesTaking:$default_status;
					echo form_dropdown('gradCoursesTaking',get_numbers_array($start=0,$end=5,true,array(''=>'Select')),$selectd_status);
				?>
				 <div class="error"><?php echo form_error('gradCoursesTaking','',''); ?></div>
			   </div>
			</li>
			
				<li>
				   <label>Subject Area <span class="required">*</span></label>
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
                   <div class="gradelevels">
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
                    </div>
				</li>
		 
		  <li>
			<label>&nbsp;</label>
			 <div class="formRight">
			<input type="submit" name="profile-submit" value="<?php echo (isset($user->id))?'Save':'Create'; ?>" class="submit"/>
			<input type="button" value="Cancel" id="close_fancy" class="submit"/>
			</div>
			</li>
		</ul>   
		</div>
	</form>
	
</div>