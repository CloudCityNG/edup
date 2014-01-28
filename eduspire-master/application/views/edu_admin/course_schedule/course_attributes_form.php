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
<div class="adminTitle"><h1>Update Attributes</h1></div>
<h2>Enter values only those attributes that are changing. All other attributes will remain unchanged.</h2>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="backButton">
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
   
	
	
	<form class="form" action="<?php echo base_url().'edu_admin/course_schedule/update_attributes/' ?>" method="post">
		
		<input type="hidden" value="<?php echo $ids; ?>" name="ids"/>
		
		 <ul class="updateForm">
            <li>
                <label>Registration Start Date: </label>
                <label class="right"><input type="text" name="csRegistrationStartDate" value="<?php echo $this->input->post('csRegistrationStartDate');?>" class="datepicker" />
				<div class="error"><?php echo form_error('csRegistrationStartDate','',''); ?></div>
				</label>
            </li>
            <li>
            	<label>Registration End Date :</label>
                <label class="right"><input type="text" name="csRegistrationEndDate" value="<?php echo $this->input->post('csRegistrationEndDate');?>" class="datepicker" />
				<div class="error"><?php echo form_error('csRegistrationEndDate','',''); ?></div>
				</label>
            </li>
            <li>
            	<label>NON-GUARANTEED Payment Start Date:</label>
                <label class="right"><input type="text" name="csPaymentStartDate" value="<?php echo $this->input->post('csPaymentStartDate');?>" class="datepicker" />
				<div class="error"><?php echo form_error('csPaymentStartDate','',''); ?></div>
				</label>
            </li>
			<li>
		   <label>Course Session </label>
		   <label class="right">
			<?php 
					$selected_session =$this->input->get_post('csCourseSession');
					echo form_dropdown('csCourseSession',$session_array,$selected_session);
				?>
				<div class="error"><?php echo form_error('csCourseSession','',''); ?></div>
		   </label>
		</li>
			
   		 </ul>
		 <h3>Course Dates</h3>
		<ul class="updateForm">
			<li>
            	<label>Start Date:</label>
                <label class="right"><input type="text" name="cs_start_date" value="<?php echo $this->input->post('cs_start_date');?>" class="datepicker" />
				<div class="error"><?php echo form_error('cs_start_date','',''); ?></div>
				</label>
            </li>   
            <li>
            	<label>End Date: </label>
                <label class="right"><input type="text" name="cs_end_date" value="<?php echo $this->input->post('cs_end_date');?>" class="datepicker"/>
				<div class="error"><?php echo form_error('cs_end_date','',''); ?></div>
				</label>
            </li>
            <li>
            <label>Publish Status</label>
            <label class="right"><?php 
				$selectd_status =  STATUS_PUBLISH;
				echo form_dropdown('csPublish',$this->course_schedule_model->get_status_array(),$selectd_status);
			?>
			<div class="error"><?php echo form_error('csPublish','',''); ?></div>
			</label>
            </li>
            <li>
            	<label></label>
                <label class="right"><input type="submit" value="Update" class="submit"/></label>
            </li>
        </ul>
		
	</form>
	
</div>