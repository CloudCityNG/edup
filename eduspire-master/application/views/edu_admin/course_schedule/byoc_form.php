<?php 
/**
@Page/Module Name/Class: 		byoc_form.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		displau form to add/edit course schedule 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>
	jQuery(document).ready(function($) {
		
		//load the date picker 
		loadDatePicker();
	});
	
	/**
		@Function Name:	loadDatePicker
		@Author Name:	binesh
		@Date:			Aug, 26 2013
		@Purpose:		load the date picker 
	
	*/
	
	function loadDatePicker(){
		$( ".datepicker" ).datepicker();
	}
</script>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="backButton clearfix">
	 <?php echo anchor('edu_admin/course_schedule/one_credit','Back','class="submit"'); ?> 
</div>

<div id="form">
				
	<div class="error_msg">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post">
		<input type="hidden" name="csCourseType" value="<?php echo isset( $result->csCourseType ) ? $result->csCourseType:COURSE_ONLINE; ?>">
		
		<input type="hidden" name="csGenreId" value="<?php echo isset( $result->csGenreId ) ? $result->csGenreId:BYOC_ID; ?>">
		<ul class="updateForm">
		
		<li>
		   <label>&nbsp;</label>
		   <div class="formRight">
                        <?php 
                        $default_status=($this->input->post('csPublish') != '')?$this->input->post('csPublish'):STATUS_PUBLISH;
                        $selectd_status =  isset( $result->csPublish )?$result->csPublish:$default_status;
                        ?>
			<input type="checkbox" name="csPublish" value="1" <?php echo (STATUS_PUBLISH==$selectd_status)?'checked="checked"':''; ?>/>
			Publish?
			</div>
		</li>
		<?php /* need this comment 	
		<li>
		   <label>&nbsp;</label>
		    <div class="formRight">
			<?php 
				$default_featured= ($this->input->post('csFeatured') != '')?$this->input->post('csFeatured'):0;
				$selected_featured =  isset( $result->csFeatured )?$result->csFeatured:$default_featured;
				
				
			?>
			<input type="checkbox" name="csFeatured" value="<?php echo FEATURED ?>" <?php echo (FEATURED==$selected_featured)?'checked="checked"':''; ?>/>
			Featured?
			</div>
		</li> */?>
		<li>
		   <label>Course Definition: <span class="required">*</span></label>
		   <div class="formRight">
			<div id="id_csCourseDefinitionId_container">
				<?php 
					$selected_definition =  isset( $result->csCourseDefinitionId ) ? $result->csCourseDefinitionId:$this->input->get_post('csCourseDefinitionId');
					$definition_array=get_dropdown_array('course_definitions',$where_condition=array('cdGenre'=>BYOC_ID),$order_by='cdCourseTitle',$order='ASC','cdID','cdCourseID','cdCourseTitle',true,array(''=>'Select'));	
					echo form_dropdown('csCourseDefinitionId',$definition_array,$selected_definition,'id="id_csCourseDefinitionId"');
				?>
			
			</div>
			<div class="error"><?php echo form_error('csCourseDefinitionId','',''); ?></div>
		   </div>
		</li> 
		
		<li>
		   <label>Instructor:</label>
		   <div class="formRight">
		   <div class="scroll">
			<?php 
				$selected_instructor =  isset( $result->instructor) && count($result->instructor) ?array_keys($result->instructor):$this->input->post('instructor');
				
				$instructor_array = get_dropdown_array('users',$where_condition=array('accessLevel'=>INSTRUCTOR),$order_by='lastName',$order='ASC','id','lastName','firstName',false);
				foreach($instructor_array as $fKey=>$fValue)
				{
					$selected = (is_array($selected_instructor) && in_array($fKey,$selected_instructor))?'checked="checked"':'';
				?>
					<div>
					<div><input id="instructor-<?php echo $fKey ?>" <?php echo $selected; ?>type="checkbox" name="instructor[]"  value="<?php echo $fKey ?>" /><label for="instructor-<?php echo $fKey ?>"><?php echo $fValue ?></label></div>
					</div>
				<?php }?>
			</div><!--scroll-->
		   </div>
		</li> 
		
		<li>
		   <label>Course Session <span class="required">*</span></label>
		   <div class="formRight">
			<?php 
					$selected_session =  isset( $result->csCourseSession ) ? $result->csCourseSession:$this->input->get_post('csCourseSession');
					echo form_dropdown('csCourseSession',$session_array,$selected_session);
				?>
				<div class="error"><?php echo form_error('csCourseSession','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>Registration Start Date: <span class="required">*</span></label>
		   <div class="formRight">
		   <input type="text" name="csRegistrationStartDate" value="<?php echo isset($result->csRegistrationStartDate)?format_date($result->csRegistrationStartDate,'m/d/Y'):$this->input->post('csRegistrationStartDate');?>" class="datepicker" maxlength="255" size="40"/>
		   <div class="error"><?php echo form_error('csRegistrationStartDate','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>Registration End Date :<span class="required">*</span></label>
		   <div class="formRight">
		   <input type="text" name="csRegistrationEndDate" value="<?php echo isset($result->csRegistrationEndDate)?format_date($result->csRegistrationEndDate,'m/d/Y'):$this->input->post('csRegistrationEndDate');?>" class="datepicker" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('csRegistrationEndDate','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>NON-GUARANTEED Payment Start Date :<span class="required">*</span></label>
		   <div class="formRight">
		   <input type="text" name="csPaymentStartDate" value="<?php echo isset($result->csPaymentStartDate)?format_date($result->csPaymentStartDate,'m/d/Y'):$this->input->post('csPaymentStartDate');?>" class="datepicker" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('csPaymentStartDate','',''); ?></div>
		   </div>
		</li>
                </ul>		
		<div class="course-schedule-dates">
                    <ul class="updateForm">
                        <li>
				<label><h3>Course Dates</h3></label>
                                <div class="formRight">
			<?php 
				//get the schedules dates 
				$course_schedule_id = isset($result->csID)?$result->csID:0;
				$course_schedule_dates=$this->course_schedule_model->get_schedule_dates($course_schedule_id); 
				
			?>
			
			<div id="course-schedule-online">
				<table>
					
					<tr>
						<td>
						<label>Start Date:<span class="required">*</span></label>
						  <input type="text" name="cs_start_date" value="<?php echo isset($course_schedule_dates[0]->csdStartDate)?format_date($course_schedule_dates[0]->csdStartDate,'m/d/Y'):$this->input->post('cs_start_date');?>" class="datepicker" maxlength="255" size="40"/>
						   <div class="error"><?php echo form_error('cs_start_date','',''); ?></div>
						</td>
						
						<td>
						<label>End Date: <span class="required">*</span></label>
						 <input type="text" name="cs_end_date" value="<?php echo isset($course_schedule_dates[0]->csdEndDate)?format_date($course_schedule_dates[0]->csdEndDate,'m/d/Y'):$this->input->post('cs_end_date');?>" class="datepicker" maxlength="255" size="40"/>
						   <div class="error"><?php echo form_error('cs_end_date','',''); ?></div>
						</td>
						
					</tr>
					
				</table>
			</div>
                    </div>
                        </li>
                    </ul>
		</div><!--.course-schedule-dates-->
		
		<ul class="updateForm">
                    <li>
                        <label>Maximum Enrollees: <span class="required">*</span></label>
                        <div class="formRight">
                        <input type="text" name="csMaximumEnrollees" value="<?php echo isset($result->csMaximumEnrollees)?$result->csMaximumEnrollees:$this->input->post('csMaximumEnrollees');?>" maxlength="255" size="40"/>
                         <div class="error"><?php echo form_error('csMaximumEnrollees','',''); ?></div>
                        </div>
                     </li>
		
		<li>
		  <label>Price: <span class="required">*</span></label>
		   <div class="formRight">
		   <input type="text" name="csPrice" value="<?php echo isset($result->csPrice)?$result->csPrice:$this->input->post('csPrice');?>" maxlength="255" size="40"/>
		     <div class="error"><?php echo form_error('csPrice','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>Non Credit Price: <span class="required">*</span></label>
		   <div class="formRight">
		   <input type="text" name="csNonCreditPrice" value="<?php echo isset($result->csNonCreditPrice)?$result->csNonCreditPrice:$this->input->post('csNonCreditPrice');?>" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('csNonCreditPrice','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>Non-credit comment : </label>
		    <div class="formRight">
			<textarea name="csNonCreditComment"   rows="3"><?php echo isset( $result->csNonCreditComment) ? $result->csNonCreditComment:$this->input->post('csNonCreditComment');?></textarea>
			 <div class="error"><?php echo form_error('csNonCreditComment','',''); ?></div>
		   </div>
		</li>
		
		<li>  
		    <label>&nbsp;</label>
		   <div class="formRight">
			<input type="submit" value="<?php echo (isset($result->csID))?'Save':'Create'; ?>" class="submit"/>
		   </div>
		</li>
                </ul>
	</form>
</div>