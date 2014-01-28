<?php 
/**
@Page/Module Name/Class: 	    form.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display add/edit form for cms content 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?> 

<div class="backButton clearfix">
	 <?php echo anchor('edu_admin/assignment/','Back','class="submit"'); ?> 
</div>
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
		toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink  advlist |  preview  fullpage | code ", 
  
	});

	jQuery(document).ready(function($) {
		$('#id_assignType').change(function(){
			if(<?php echo ASGN_QUESTIONNAIRE  ?> ==$(this).val()){
				$('#questionaire').show();
			}else{
				$('#questionaire').hide();
			}
		});
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

<div id="form">
				
	<div class="error_msg">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" >
		
            <ul class="updateForm">
        	<li>
		   <label>Assignment Type<span class="required">*</span></label>
		   <div class="formRight">
		  <?php 
                        $default_type= ($this->input->post('assignType') != '')?$this->input->post('assignType'):ASGN_TYPE_COURSE;
                        $selected_type =  isset( $result->assignType )?$result->assignType:$default_type;
                        echo form_dropdown('assignType',$this->assignment_model->get_assignment_type_array(),$selected_type,'id="id_assignType"');
			?>
		   <div class="hint">If this questionnaire pertains to a session, please select it here. This field enables questionnaire viewing by instructors.
			</div>
			<div class="error"><?php echo form_error('assignType','',''); ?></div>
		   </div>
		</li>
		
		<?php if(isset($courses) && !empty($courses)): ?>
			<li>
			   <label>Select Course<span class="required">*</span></label>
			   <div class="formRight">
			   <select name="assignCnfID">
			<?php 
				$selected_course='';
				$course_id = isset( $result->assignCnfID )?$result->assignCnfID:$this->input->get_post('assignCnfID');
				foreach($courses as $course){
				$selected=($course->csID==$course_id)?'selected="selected"':'';
				?>
				
					<option  <?php echo $selected; ?> value="<?php echo $course->csID ?>" /><?php echo $course->cdCourseID.' '.$course->cdCourseTitle.'('.format_date($course->csStartDate,DATE_FORMAT).'-'.$course->csCity.', '.$course->csState.')'; ?></option>
				<?php }?>
				</select>
				<div class="error"><?php echo form_error('assignCnfID','',''); ?></div>
			</div>
			</li>
			<?php endif; ?>
		
		<?php $q_style=(ASGN_QUESTIONNAIRE==$selected_type)?'block':'none'; ?>
                        <li id="questionaire" style="display:<?php echo $q_style ?>">
                            <label>Questionnaire <span class="required">*</span></label>
                            <div class="formRight">
                                 <?php 
                                         $selected_questionaire =  isset( $result->assignQuestionnaire ) ? $result->assignQuestionnaire:$this->input->post('assignQuestionnaire');
                                         $questionaire_array=get_dropdown_array('questionnaire_defs',"qParent = ".SURVEY_PARENT." AND qTitle  != ''",$order_by='qTitle',$order='ASC','qID','qTitle','',true,array(''=>'Select'));	
                                         echo form_dropdown('assignQuestionnaire',$questionaire_array,$selected_questionaire,'id="id_csGenreId"');
                                 ?>
                                 <div class="error"><?php echo form_error('assignQuestionnaire','',''); ?></div>
                            </div>
                        </li> 
		
		<li>
		   <label>Assignment Title <span class="required">*</span></label>
		   <div class="formRight">
		   <textarea  name="assignTitle" rows="3"><?php echo isset($result->assignTitle)?$result->assignTitle:$this->input->post('assignTitle');?></textarea>
		   <div class="error"><?php echo form_error('assignTitle','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>Assignment Description/Instructions</label>
		   <div class="formRight">
			<textarea name="assignTopic" class="tinymce_editor" rows="5"><?php echo isset( $result->assignTopic ) ? $result->assignTopic:$this->input->post('assignTopic');?></textarea>
			 <div class="error"><?php echo form_error('assignTopic','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>Activation Date</label>
		   <div class="formRight">
		   <input type="text" name="assignActiveDate" value="<?php echo isset($result->assignActiveDate)?format_date($result->assignActiveDate,'m/d/Y'):$this->input->post('assignActiveDate');?>" class="datepicker" maxlength="255" size="40"/>
           <div class="hint">Activation Date and Activation Time define when the assignment becomes visible to the students.</div>
		   	 <div class="error"><?php echo form_error('assignActiveDate','',''); ?></div>
		   </div>
		</li>
         
                <li>
		   <label>Activation Time</label>
		   <div class="formRight">
            <?php
			$startHours = $this->input->post('activationTime');
			$ampm = $this->input->post('activationMin');
			$startMin = $this->input->post('activationAP');
			if(isset($result->assignActiveTime)) 
			{
				$start_time = explode(':',$result->assignActiveTime);
				$ampm='am';
				if($start_time[0] > 12 || $start_time[0]=='00' ){
					$ampm='pm';
				}
				if($start_time[0] > 12){
					$start_time[0]=$start_time[0]-12;
				}
				$startHours = $start_time[0];
				$startMin   = $start_time[1];
			}
				
			echo form_dropdown('activationTime',get_hours_array(true,array('0'=>'-hh-')),$startHours ); ?>
            <?php  echo form_dropdown('activationMin',get_minute_array(true,array('0'=>'-mm-')) ,$startMin); ?>
            <?php  echo form_dropdown('activationAP',get_ampm_array(true,array('0'=>'-a/p-')),$ampm ); ?>
		   </div>
		</li>
		
        
		<li>
		   <label>Due Date </label>
		   <div class="formRight">
		   <input type="text" name="assignDueDate" 
           value="<?php echo isset($result->assignDueDate)?format_date($result->assignDueDate,'m/d/Y'):$this->input->post('assignDueDate');?>" 
           class="datepicker" maxlength="255" size="40"/>
		   	 <div class="error"><?php echo form_error('assignDueDate','',''); ?></div>
		   </div>
		</li>
		
                <li>
		   <label>Due Time</label>
		   <div class="formRight">
                    <?php  
			$dueHours = $this->input->post('dueTime');
			$dampm    = $this->input->post('dueMin');
			$dueMin   = $this->input->post('dueAP');
			if(isset($result->assignDueTime)) {
				$end_time = explode(':',$result->assignDueTime);
				$dampm='am';
				if($end_time[0] > 12 || $end_time[0]=='00' ){
					$dampm='pm';
				}
				if($end_time[0] > 12){
					$end_time[0]=$end_time[0]-12;
				}
				$dueHours = $end_time[0];
				$dueMin   = $end_time[1];
			}

			echo form_dropdown('dueTime',get_hours_array(true,array('0'=>'-hh-')),$dueHours ); ?>
			<?php  echo form_dropdown('dueMin',get_minute_array(true,array('0'=>'-mm-')) ,$dueMin); ?>
			<?php  echo form_dropdown('dueAP',get_ampm_array(true,array('0'=>'-a/p-')),$dampm ); ?>
		   </div>
		</li>
				
		<li>
		   <label>Point Value <span class="required">*</span></label>
		   <div class="formRight">
		   <input type="text" name="assignPoints" value="<?php echo isset($result->assignPoints)?$result->assignPoints:$this->input->post('assignPoints');?>" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('assignPoints','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>Late Points Deducted Per Day </label>
		   <div class="formRight">
		   <input type="text" name="assignDeductLatePoints" value="<?php echo isset($result->assignDeductLatePoints)?$result->assignDeductLatePoints:$this->input->post('assignDeductLatePoints');?>" maxlength="255" size="40"/>
		   </div>
		</li>
		
		<li>
		   <label>Link Name</label>
		   <div class="formRight">
		   <input type="text" name="assignLinkName" value="<?php echo isset($result->assignLinkName)?$result->assignLinkName:$this->input->post('assignLinkName');?>" maxlength="255" size="40"/>
		   </div>
		</li>
		
		<li>
		   <label>Link Url</label>
		   <div class="formRight">
		   <input type="text" name="assignLinkUrl" value="<?php echo isset($result->assignLinkUrl)?$result->assignLinkUrl:$this->input->post('assignLinkUrl');?>" maxlength="255" size="40"/>
		   </div>
		</li>
		
		<li>  
		   <label>&nbsp;</label>
		   <div class="formRight">
			<input type="submit" value="<?php echo (isset($result->assignID))?'Save':'Create'; ?>" class="submit"/>
		   </div>
		</li>
	</form>
</div>