<?php 
/**
@Page/Module Name/Class: 		session_form.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display form to add/edit course session
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
		@Author Name:	ben binesh
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
	 <?php echo anchor('edu_admin/course_definition/sessions','Back','class="submit"'); ?> 
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
		
		<div class="course-schedule-dates">
			
			<ul class="updateForm">
			<div id="course-schedule-online">
					<li>	
						
						<label>Start Date:<span class="required">*</span></label>
						  <div class="formRight"> <input type="text" name="bsStartDate" value="<?php echo isset($result->bsStartDate)?format_date($result->bsStartDate,'m/d/Y'):$this->input->post('bsStartDate');?>" class="datepicker" maxlength="255" size="40"/>
						  <div class="error"><?php echo form_error('bsStartDate','',''); ?></div>
						  </div>
					</li>
						
						<li>
						<label>End Date: <span class="required">*</span></label>
						 <div class="formRight"> <input type="text" name="bsEndDate" value="<?php echo isset($result->bsEndDate)?format_date($result->bsEndDate,'m/d/Y'):$this->input->post('bsEndDate');?>" class="datepicker" maxlength="255" size="40"/>
						 <div class="error"><?php echo form_error('bsEndDate','',''); ?></div>
						</div>
						</li>
						
						
				
			</div>
			<li>
				<label></label>
				<div class="formRight"><input type="submit" value="<?php echo (isset($result->bsID))?'Save':'Create'; ?>" class="submit"/></div>
			</li>
			</ul>
			
			
		</div><!--.course-schedule-dates-->
		
		
		
		
		
	</form>
	
</div>