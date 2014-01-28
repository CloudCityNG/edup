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
<div class="publicTitle"><h1>Final Grades </h1></div>

<!--Show user final grades of selected subject.-->
<div class="result_container"> 
	<?php  
    if(isset($courses) && !empty($courses)): ?>
    <?php foreach($courses as $cor):
			// check course matched with current selected course.
            if($courseId == $cor->csID): ?>
            <div class="course-div clearfix">
            <!--show selected course details.-->
                <h3><?php //echo $cor->csID; ?><?php echo $cor->cdCourseID; ?>:<?php echo $cor->cdCourseTitle;  ?></h3>	
                <div><?php echo $cor->csCity; ?>, <?php echo $cor->csState; ?></div>
                <div><?php echo format_date($cor->csStartDate,DATE_FORMAT); ?>-<?php echo format_date($cor->csEndDate,DATE_FORMAT); ?>
                </div>
            </div>
            <table class="table striped" id="grid" width="100%"> 
            <tr>    
                <th>Grade</th> 
                <th>Total Pts</th>
                <th>Comments for Student</th>
                <th>Submit</th>
            </tr> 
            <?php 
			
			// fetch user assignments and thier grades/points detaiils.
            $getUserDetails        = $this->assignment_model->show_assign_user_details($userId);
            $getGradeSum           = $this->assignment_model->show_user_grade_sum($userId,$cor->csID);
            $getGradeNum           = $this->assignment_model->get_user_grade($userId,$cor->csID);
            $user                  = $this->user_model->get_single_record($userId,'*',true);
            $getUserDetails        = $this->assignment_model->show_assign_user_details($userId);
            $getUserPointsDetails  = $this->assignment_model->get_total_user_assign($userId); 
            ?>
            <tr> 
                <td>  
                    <?php  
                    // Show grade got from user.
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
                    
                </td>
                 
                 <td>
                <?php
                // If user has no grade in database.
                if(!isset($getGradeNum['fgComputedGrade'])) {
                    echo "NO GRADE";
                }
                else { 
                    echo $getGradeNum['fgGrade'].'/'.$getGradeNum['fgComputedGrade']; ?> 
                <?php 
                } ?>
                </td>
                
                <td>
                <?php
                    // Show student commment section. 
                     if(isset($getGradeNum['fgCommentStudent']) && $getGradeNum['fgCommentStudent'] != '') { 
                     echo $getGradeNum['fgCommentStudent'] ; } ?>
                </td>
                <td> 
                <?php 
                    if(isset($getGradeNum['fgApproved']) && $getGradeNum['fgApproved'] == 1) {
                        echo "Submitted";
                    }
                    else if(isset($getGradeNum['fgApproved']) && $getGradeNum['fgApproved'] == 0) {
                        echo "Not Submitted";
                    }
                    else { echo "Not Submitted"; }
                ?>
                </td>
            </tr> 
            <tr>    
                <th>Grade</th> 
                <th>Total Pts</th>
                <th>Comments for Student</th>
                <th>Submit</th>
            </tr> 
            </table>  
           <?php endif; ?>
    <?php endforeach; ?> 
    <?php else: ?>
            <p class="no_recored_fount">No record found</p> 
    <?php endif; ?> 
    
</div>    