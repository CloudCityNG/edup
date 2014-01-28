<?php 
/**
@Page/Module Name/Class: 	    instructor.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display form to add instructor
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<h1>Add Instructor</h1>
<div class="top_links clearfix">
	 <?php echo anchor('edu_admin/user/','Back','class="submit"'); ?> 
</div>
</script>

<div id="form">
				
	<div class="error_msg">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" enctype="multipart/form-data" >
	
			
		
			<div class="row clearfix">
			   <div class="left_area">Email <span class="required">*</span></div>
			   <div class="right_area">
			   <input type="text" name="email" value="<?php echo isset($result->email)?$result->email:$this->input->post('email');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('email','',''); ?></div>
			   </div>
			</div>
			
			<div class="row clearfix">
			   <div class="left_area">Access Level <span class="required">*</span></div>
			   <div class="right_area">
				
				<div><input id="access_instructor" type="radio" name="accessLevel" <?php echo (INSTRUCTOR==$this->input->post('accessLevel')) ?'checked="checked"':"";?>  value="<?php echo INSTRUCTOR ?>" /><label for="access_instructor"><?php echo $this->user_model->show_access_level(INSTRUCTOR) ?></label></div>
				
				<div><input id="access_instructor_assistant" type="radio" name="accessLevel" <?php echo (INSTRUCTOR_ASSISTANT==$this->input->post('accessLevel')) ?'checked="checked"':"";?>  value="<?php echo INSTRUCTOR_ASSISTANT ?>" /><label for="access_instructor_assistant"><?php echo $this->user_model->show_access_level(INSTRUCTOR_ASSISTANT) ?></label></div>
				<div class="error"><?php echo form_error('accessLevel','',''); ?></div>
			   </div>
			</div>
		
		
		
			<div class="row clearfix">
			   <div class="left_area">First Name <span class="required">*</span></div>
			   <div class="right_area">
			   <input type="text" name="firstName" value="<?php echo isset($result->firstName)?$result->firstName:$this->input->post('firstName');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('firstName','',''); ?></div>
			   </div>
			</div>
		
			<div class="row clearfix">
			   <div class="left_area">Last Name<span class="required">*</span></div>
			   <div class="right_area">
			   <input type="text" name="lastName" value="<?php echo isset( $result->lastName ) ? $result->lastName:$this->input->post('lastName');?>" maxlength="255" size="40"/>
			     <div class="error"><?php echo form_error('lastName','',''); ?></div>
			   </div>
			</div>
			
			
			<div class="row clearfix">
			   <div class="left_area">Select Membership</div>
			   <div class="right_area">
			   <div class="scroll">
			<?php 
				
				$course_id = $this->input->post('course');
				foreach($courses as $course){
				$selected = ($course->csID==$course_id)?'checked="checked"':'';
				?>
					
					<div><input id="course_<?php echo $course->csID; ?>" <?php echo $selected; ?>type="checkbox" name="course[]"  value="<?php echo $course->csID ?>" /><label for="course_<?php echo $course->csID ?>"><?php echo $course->cdCourseID.' '.$course->cdCourseTitle.'('.$course->csStartDate.'-'.$course->csCity.', '.$course->csState.')'; ?></label></div>
					
				<?php }?>
			</div><!--scroll-->
			</div>
			</div>
			
				
		<div class="row clearfix">  
		   <div class="left_area">&nbsp;</div>
		   <div class="right_area">
			<input type="submit"  value="<?php echo (isset($result->id))?'Save':'Create'; ?>" class="submit"/>
			
		   </div>
		</div>
	</form>
	
</div>