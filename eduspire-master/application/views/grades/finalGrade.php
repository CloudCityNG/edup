<?php
	/**
	@Page/Module Name/Class: 		grades.php
	@Author Name:			 		Alan Anil
	@Date:					 		Nov, 14 2013
	@Purpose:		        		Contain all data related to grades of users 
	@Table referred:				users, users_profiles,final_grades
	@Table updated:					NA
	@Most Important Related Files	NA
	 */
  
?>   
<div class="letterInnerTop">
<?php echo EDUSPIRE_ADDRESS.', '.EDUSPIRE_EMAIL;  ?>
</div> 
<div class="lettterInnerSection"></div>
<div class="publicTitle"><h1> </h1></div> 
<div class="result_container"> 
<?php  
// User assignment grade sheet.
if(isset($courses) && !empty($courses)): ?>
<?php foreach($courses as $cor):
		// check course id to current selected course id.	
		if($courseId == $cor->csID): ?>
		<div class="course-div clearfix">
        	<h2>Student: <?php echo $user->firstName.' '.$user->lastName;?> </h2>
			<h2>Course: <?php echo $cor->cdCourseID; ?>:<?php echo $cor->cdCourseTitle;  ?></h2>	 
			<h3>Date: 
				<?php echo format_date($cor->csStartDate,DATE_FORMAT); ?>-<?php echo format_date($cor->csEndDate,DATE_FORMAT); ?>
            </h3><br>
		</div>
        <div>
        	<h3>Grade: 
        		  <?php $getGradeNum           = $this->assignment_model->get_user_grade($userId,$cor->csID);
				  if(isset($getGradeNum['fgComputedGrade']) && $getGradeNum['fgComputedGrade'] != '') 
                    {
                        if($getGradeNum['fgComputedGrade'] == 0){
							echo 'A';
						}
						else {
							$percentage  = $this->assignment_model->percentage($getGradeNum['fgGrade'],
										$getGradeNum['fgComputedGrade'],0);   
							echo $percentage;
						}  
                    } ?> 
        	</h3>
        </div> <br>
        <div>
        	<h3>Credits: <?php 
				if(isset($course) && !empty($course)):  
				 echo $course->cgCourseCredits;
                endif; ?>                 
			</h3>
        </div> 
       <?php endif; ?>
<?php endforeach; ?>  
<?php endif; ?> 
    
</div>
<script> 
window.print();
</script> 