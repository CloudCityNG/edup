<?php 
/**
@Page/Module Name/Class: 		form.php
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
		
		$('#id_csGenreId').change(function(){
			$.ajax({
				url: '<?php echo base_url().'edu_admin/ajax/course_definition/';?>'+$(this).val(),
				success: function(data) {
					$("#id_csCourseDefinitionId_container").html(data);
				}	
			});
		});
		
		$('#id_csCourseType').change(function(){
			if(<?php echo COURSE_ONLINE ?> == $(this).val()){
				$('#offline-location').hide();
				$('#course-schedule-online').show();
				$('#course-schedule-offline').hide();
			}else if(<?php echo COURSE_OFFLINE ?> == $(this).val()){
				$('#offline-location').show();
				$('#course-schedule-offline').show();
				$('#course-schedule-online').hide();
			}
		});
		
		$('#add_more_date_btn').click(function(){
			$('table#schedules_dates tr').length;
			var $clone = $('table#schedules_dates tr:last').clone();
			$clone.find(':text').val('');
			$clone.find('.day_count').text($('table#schedules_dates tr').length+1);
			$clone.find('.datepicker').removeClass('hasDatepicker').removeAttr('id');
			
			$('table#schedules_dates').append($clone);
			loadDatePicker();
			
		});
		
		$(document).on('click','.remove_date_anchor',function(){
			if(confirm('Do you want to delete this date ?')){
				$(this).parent().parent().remove();
			}
		});
		
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
	 <?php echo anchor('edu_admin/course_schedule/','Back','class="submit"'); ?> 
</div>
<?php 
	$offline = true;
	$course_type =  isset( $result->csCourseType ) ? $result->csCourseType:$this->input->post('csCourseType');
	if($course_type==COURSE_ONLINE){
		$offline = false;
	}
?>

<div id="form">
				
	<div class="error_msg">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	
	<form class="form" action="" method="post">
		<ul class="updateForm brdr_btm">
		<li>
		   <label>&nbsp;</label>
		    <div class="formRight">
			<?php 
				$default_status= ($this->input->post('csPublish') != '')?$this->input->post('csPublish'):STATUS_PUBLISH;
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
		</li> 
		*/?>
		<li>
		   <label>Course Genre <span class="required">*</span></label>
		   <div class="formRight">
			<?php 
				$selected_genre =  isset( $result->csGenreId ) ? $result->csGenreId:$this->input->get_post('csGenreId');
				$genres_array=get_dropdown_array('course_genres','cgID !='.BYOC_ID,$order_by='cgTitle',$order='ASC','cgID','cgTitle','',true,array(''=>'Select'));	
				echo form_dropdown('csGenreId',$genres_array,$selected_genre,'id="id_csGenreId"');
			?>
			 <div class="error"><?php echo form_error('csGenreId','',''); ?></div>
		   </div>
		</li> 
		
		<li>
		  <label>Course Definition: <span class="required">*</span></label>
		   <div class="formRight">
			<div id="id_csCourseDefinitionId_container">
				<?php 
					$selected_genre = ($selected_genre)?$selected_genre:0;
					$selected_definition =  isset( $result->csCourseDefinitionId ) ? $result->csCourseDefinitionId:$this->input->get_post('csCourseDefinitionId');
					$definition_array=get_dropdown_array('course_definitions',$where_condition=array('cdGenre'=>$selected_genre),$order_by='cdCourseTitle',$order='ASC','cdID','cdCourseID','cdCourseTitle',true,array(''=>'Select'));	
					echo form_dropdown('csCourseDefinitionId',$definition_array,$selected_definition,'id="id_csCourseDefinitionId"');
				?>
			
			</div>
			 <div class="error"><?php echo form_error('csCourseDefinitionId','',''); ?></div>
		   </div>
		</li> 
		
		<li>
		   <label>Course Type <span class="required">*</span></label>
		   <div class="formRight">
			<?php 
				$selected_type =  isset( $result->csCourseType ) ? $result->csCourseType:$this->input->post('csCourseType');
				echo form_dropdown('csCourseType',$this->course_schedule_model->get_coursetype_array(true,array('0'=>'Select')),$selected_type,'id="id_csCourseType"');
			?>
			 <div class="error"><?php echo form_error('csCourseType','',''); ?></div>
		   </div>
		</li> 
		
		<li>
		   <label>Instructor:</label>
		   <div class="formRight">
		   <div class="scroll">
			<?php 
				$selected_instructor =  isset( $result->instructor) && count($result->instructor) ?array_keys($result->instructor):$this->input->post('instructor');
				//$selected_instructor =  isset( $result->instructor ) ? $result->instructor:
				
				$instructor_array = get_dropdown_array('users',$where_condition=array('accessLevel'=>INSTRUCTOR),$order_by='lastName',$order='ASC','id','lastName','firstName',false);
				
				//echo form_dropdown('cdFacilitator[]',$facilitator_array,$selected_facilitator,'multisele');
				foreach($instructor_array as $fKey=>$fValue){
				
				
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
		<div id="offline-location" style="display:<?php echo ($offline)?'block':'none'; ?>">
		<h3>Course Location</h3>
                <ul class="updateForm brdr_btm">
		<li>
		   <label>Location Name: <span class="required">*</span></label>
		   <div class="formRight">
			<textarea name="csLocation"   rows="3"><?php echo isset( $result->csLocation) ? $result->csLocation:$this->input->post('csLocation');?></textarea>
			<div class="error"><?php echo form_error('csLocation','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>Address: <span class="required">*</span></label>
		   <div class="formRight">
			<textarea name="csAddress" rows="3"><?php echo isset( $result->csAddress) ? $result->csAddress:$this->input->post('csAddress');?></textarea>
			<div class="error"><?php echo form_error('csAddress','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>City : </label>
		   <div class="formRight">
		   <input type="text" name="csCity" value="<?php echo isset($result->csCity)?$result->csCity:$this->input->post('csCity');?>" maxlength="255" size="40"/>
		   <div class="error"><?php echo form_error('csCity','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>State : </label>
		   <div class="formRight">
		   <input type="text" name="csState" value="<?php echo isset($result->csState)?$result->csState:$this->input->post('csState');?>" maxlength="255" size="40"/>
		   <div class="error"><?php echo form_error('csState','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>Zip : </label>
		   <div class="formRight">
		   <input type="text" name="csZIP" value="<?php echo isset($result->csZIP)?$result->csZIP:$this->input->post('csZIP');?>" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('csZIP','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>IU Region : <span class="required">*</span></label>
		    <div class="formRight">
		   <?php 
				$selected_iu = isset($result->csIURegion)?$result->csIURegion:$this->input->post('csIURegion');
				$iu_array=get_dropdown_array('iu_unit',$where_condition=array('iuPublish'=>STATUS_PUBLISH),$order_by='iuID',$order='ASC','iuID','iuName','',true,array(''=>'Select'));	
				echo form_dropdown('csIURegion',$iu_array,$selected_iu);
			?>
			 <div class="error"><?php echo form_error('csIURegion','',''); ?></div>
		   </div>
		
		  
		</li>
        </ul>
		</div><!--#course-type-location-->
		
		<div class="course-schedule-dates">
        <h3>Course Dates</h3>
                    <ul class="updateForm brdr_btm">
                        <li>	
                      
                        
			<?php 
				//get the schedules dates 
				$course_schedule_id = isset($result->csID)?$result->csID:0;
				$course_schedule_dates=$this->course_schedule_model->get_schedule_dates($course_schedule_id); 
			?>
			
			<div id="course-schedule-offline" style="display:<?php echo ($offline)?'block':'none'; ?>">
				
				<table id="schedules_dates" width="100%">
					<?php 
					$show_empty=true;
					
					if(!empty($course_schedule_dates) && count($course_schedule_dates)>0):
						$show_empty = false;
						$i=0;
                                                    foreach($course_schedule_dates as $course_schedule_date): $i++; ?>
							<tr>
							<td>
							<label>Day <span class="day_count"><?php echo $count=$i; ?><span></label>
							<input type="text" name="cs_start_date_multiple[]" value="<?php echo format_date($course_schedule_date->csdStartDate,'m/d/Y'); ?>" class="datepicker" maxlength="255" size="40"/>
							</td>
							
							<td>
							<label>Start Time  <span class="day_count"><?php echo $count=$i; ?><span></label>
							
							<?php $start_time = explode(':',$course_schedule_date->csdStartTime);
								$ampm='am';
								if($start_time[0] > 12 || $start_time[0]=='00' ){
									$ampm='pm';
								}
								if($start_time[0] > 12){
									$start_time[0]=$start_time[0]-12;
								}
							?>
							<?php  echo form_dropdown('cs_start_hour[]',get_hours_array(true,array('0'=>'-hh-')),$start_time[0]); ?>
							<?php  echo form_dropdown('cs_start_minute[]',get_minute_array(true,array('0'=>'-mm-')),$start_time[1]); ?>
							<?php  echo form_dropdown('cs_start_ampm[]',get_ampm_array(true,array('0'=>'-a/p-')),$ampm); ?>
							</td>
							<td>
							
							<?php $end_time = explode(':',$course_schedule_date->csdEndTime);
								$ampm = 'am';
								if($end_time[0] > 12 || $end_time[0]=='00' ){
									$ampm='pm';
								}
								if($end_time[0] > 12){
									$end_time[0]=$end_time[0]-12;
								}
							?>
							<label>End Time  <span class="day_count"><?php echo $count=$i; ?><span></label>
							<?php  echo form_dropdown('cs_end_hour[]',get_hours_array(true,array('0'=>'-hh-')),$end_time[0]); ?>
							<?php  echo form_dropdown('cs_end_minute[]',get_minute_array(true,array('0'=>'-mm-')),$end_time[1]); ?>
							<?php  echo form_dropdown('cs_end_ampm[]',get_ampm_array(true,array('0'=>'-a/p-')),$ampm); ?>
							</td>
							<td><label>&nbsp;</label>
							<a href="javascript:void(0)" class="remove_date_anchor"><img src="/images/delete.png" title="delete" alt="delete"/ ></a></td>
						   
						</tr>
						
						<?php endforeach; ?>
					
					
					<?php else: ?>
						<?php
						
						$schedule_date_postback = $this->input->post('cs_start_date_multiple');
						if(!empty($schedule_date_postback) && count($schedule_date_postback)>0):
						$show_empty=false;
						?>
						<?php for( $i = 0 ; $i < count( $schedule_date_postback ) ; $i++ ): ?>
							
						<tr>
							<td>
							<label>Day <span class="day_count"><?php echo $count=$i+1; ?><span></label>
							<input type="text" name="cs_start_date_multiple[]" value="<?php echo format_date($schedule_date_postback[$i],'m/d/Y'); ?>" class="datepicker" maxlength="255" size="40"/>
							</td>
							
							<td>
							<label>Start Time  <span class="day_count"><?php echo $count=$i+1; ?><span></label>
							
							<?php  echo form_dropdown('cs_start_hour[]',get_hours_array(true,array('0'=>'-hh-')),$_POST['cs_start_hour'][$i]); ?>
							<?php  echo form_dropdown('cs_start_minute[]',get_minute_array(true,array('0'=>'-mm-')),$_POST['cs_start_minute'][$i]); ?>
							<?php  echo form_dropdown('cs_start_ampm[]',get_ampm_array(true,array('0'=>'-a/p-')),$_POST['cs_start_ampm'][$i]); ?>
							</td>
							<td>
							<label>End Time  <span class="day_count"><?php echo $count=$i+1; ?><span></label>
							<?php  echo form_dropdown('cs_end_hour[]',get_hours_array(true,array('0'=>'-hh-')),$_POST['cs_end_hour'][$i]); ?>
							<?php  echo form_dropdown('cs_end_minute[]',get_minute_array(true,array('0'=>'-mm-')),$_POST['cs_end_minute'][$i]); ?>
							<?php  echo form_dropdown('cs_end_ampm[]',get_ampm_array(true,array('0'=>'-a/p-')),$_POST['cs_end_ampm'][$i]); ?>
							</td>
							<td><label>&nbsp;</label>
							<a href="javascript:void(0)" class="remove_date_anchor"><img src="/images/delete.png" title="delete" alt="delete"/ ></a></td>
						   
						</tr>
						
						<?php endfor; ?>
						<?php endif; ?>
						
					
					<?php endif;?>
					
					
					<?php if($show_empty): ?>
					<tr>
						<td>
						<label>Day <span class="day_count">1<span></label>
						<input type="text" name="cs_start_date_multiple[]" class="datepicker" maxlength="255" size="40"/>
						</td>
						
						<td>
						<label>Start Time  <span class="day_count">1<span></label>
						<?php  echo form_dropdown('cs_start_hour[]',get_hours_array(true,array('0'=>'-hh-'))); ?>
						<?php  echo form_dropdown('cs_start_minute[]',get_minute_array(true,array('0'=>'-mm-'))); ?>
						<?php  echo form_dropdown('cs_start_ampm[]',get_ampm_array(true,array('0'=>'-a/p-'))); ?>
						</td>
						<td>
						<label>End Time  <span class="day_count">1<span></label>
						<?php  echo form_dropdown('cs_end_hour[]',get_hours_array(true,array('0'=>'-hh-'))); ?>
						<?php  echo form_dropdown('cs_end_minute[]',get_minute_array(true,array('0'=>'-mm-'))); ?>
						<?php  echo form_dropdown('cs_end_ampm[]',get_ampm_array(true,array('0'=>'-a/p-'))); ?>
						</td>
						<td><label>&nbsp;</label>
						<a href="javascript:void(0)" class="remove_date_anchor"><img src="/images/delete.png" title="delete" alt="delete"/ ></a></td>
					   
					</tr>
				 
				<?php endif; ?>
				</table>
				<input type="button" id="add_more_date_btn" class="submit" value="add more">
				
			</div><!--#course-schedule-offline-->
			
			<div id="course-schedule-online" style="display:<?php echo ($offline)?'none':'block'; ?>"  >
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
			
                       </li>
                    </ul>
		</div><!--.course-schedule-dates-->
                <ul class="updateForm">
                <li><label></label></li>
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