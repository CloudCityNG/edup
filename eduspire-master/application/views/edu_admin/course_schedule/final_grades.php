<?php 
/**
@assignment/Module Name/Class: 	final_grades.php
@Author Name:			 		Alan Anil
@Date:					 		Nov, 20 2013
@Purpose:		        		display final grades of user to a selected course. 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  
  
//---------------------------------------------------------------------------------------- 
*/
?>  
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<!--Show user course details.-->
<h3>
	<?php if(isset($course) && !empty($course)): ?>
    <div class="course_details">
        <div>Course:<?php echo $course->cdCourseID; ?>:<?php echo $course->cdCourseTitle; ?></div>
        <div><?php echo format_date($course->csStartDate,DATE_FORMAT);  ?>
            -
            <?php echo format_date($course->csEndDate,DATE_FORMAT);  ?>
        </div>
    </div> 
    <?php endif; ?>
</h3> 
<h2>Final Grades</h2> 
<div class="result_container"> 
    <!--Final grade entry form.-->
    <form action="" method="post" name="finalGradeEntry" onsubmit="return checkAllTotalPointsVal();">
        <table class="table striped" id="grid"> 
        <tr>  
            <th>Registrant</th>
            <th></th>
            <th>Total Pts</th>
            <th>Current	Grade</th>
            <th>Comments for Administration</th>
            <th>Submit</th>
        </tr> 
        <?php
		$totalNoOfUser     = 0;
		$noOfUserSubmitted = 0; 
		$gardeValue        = '';
		$rowCss            = 0; 
		
			if(isset($getUserDetails[0]->firstName) && $getUserDetails[0]->firstName != '')
			{
				$totalNoOfUser++ ;	 
				?>
				<?php $tr_class = ($rowCss++%2==0)?'even':'odd'; ?>
				<tr class="<?php echo $tr_class; ?>"> 
                    <td>
						<?php   
						$profile_image=( '' != $user->profileImage)?$user->profileImage:'default.jpg';
						if('default.jpg' != $profile_image)
						{
							$image_path=UPLOADS.'/users/'.$profile_image;
							if(!file_exists($image_path))
							{
								$profile_image='default.jpg';
							}
						}	 
                        $profile_image=base_url().'uploads/users/'.$profile_image;
                        ?>
                        <img src="<?php echo crop_image($profile_image); ?>" 
                        title="<?php echo  $getUserDetails[0]->firstName.', '.$getUserDetails[0]->lastName ; ?>" 
                        alt="<?php echo  $getUserDetails[0]->firstName.', '.$getUserDetails[0]->lastName ; ?>"/> 
                    </td>
                    <td>    
                        <?php    
                        echo  $getUserDetails[0]->firstName.', '.$getUserDetails[0]->lastName ;  
                        ?> 
                    </td>
                    <td>
                    <?php
					// If user has no grade in database.
					if(!isset($getGradeNum['fgComputedGrade'])) {
						echo "NO GRADE";
					}
					else {
						?>
						<input type="text" name="fgCompGrade" class="gradeBox fgCompGrade" 
						id="fgCompGrade_<?php echo $totalNoOfUser; ?>"
						value="<?php if(isset($getGradeNum['fgGrade']) && $getGradeNum['fgGrade'] != '') { 
						echo $getGradeNum['fgGrade']; } ?>" maxlength="5" > /
						
						<input type="text" name="fgGrade"  class="gradeBox fgGrade" 
						id="fgGrade_<?php echo $totalNoOfUser; ?>"
						value="<?php if(isset($getGradeNum['fgComputedGrade']) && $getGradeNum['fgComputedGrade'] != '') { 
						echo $getGradeNum['fgComputedGrade']; } ?>" maxlength="5" >
						<?php 
					} ?>
                    </td>
                    <td>  
					<?php 
                    // Show grade got from user.
                    if(isset($getGradeNum['fgComputedGrade']) && $getGradeNum['fgComputedGrade'] != '') 
                    {
                      if($getGradeNum['fgComputedGrade'] == 0) {
					  	$percentage = 'A';
					  }
					  else {	
					    $percentage  = $this->assignment_model->percentage($getGradeNum['fgGrade'],
                                    $getGradeNum['fgComputedGrade'],0);   
                          
					  }	
					  if(isset($getGradeNum['fgComputedGrade']) && $getGradeNum['fgApproved'] == 0) {
					  	$percentage = 'Not Final';
					  }
					  echo $percentage;
                    } ?>  
                    </td> 
                    <td>
                    <?php
					// Show admin commment section.
					if(isset($getGradeNum['fgComputedGrade']) && $getGradeNum['fgComputedGrade'] != '') {
					 ?>
                     <textarea name="adminComment" rows="4" cols="40" placeholder="Enter comments here" maxlength="500" ><?php if(isset($getGradeNum['fgCommentAdmin']) && $getGradeNum['fgCommentAdmin'] != '') {echo $getGradeNum['fgCommentAdmin'] ; }?></textarea> 
					 <?php
					} ?>
                    </td> 
                    <td>
                     <?php
					 // Check checkbox if grade already submitted.
					if(isset($getGradeNum['fgComputedGrade']) && $getGradeNum['fgComputedGrade'] != '') {
					 ?>
                        <input type="checkbox" name="approved" class='checkall' id="checkAllApprovedId" value="1"
                        <?php if(isset($getGradeNum['fgComputedGrade']) && $getGradeNum['fgApproved'] == 1) { ?> checked="checked"  
                        <?php $noOfUserSubmitted++ ;} ?> />
                    <?php 
					} ?>
                    </td>
				</tr> 
				<?php 
			} else {?>
			<tr>  
            <td colspan="6" align="center">No record found</td> 
        </tr>	
			<?php
            } ?>
        <tr>  
            <th>Registrant</th>
            <th></th>
            <th>Total Pts</th>
            <th>Current	Grade</th>
            <th>Comments for Administration</th>
            <th>Submit</th>
        </tr> 
       </table> 
        <div class="top_links instructorSubmitButton">
      	  <input type="submit" value="Save Changes"  name="save" class="submit"/> 
        </div> 
        <div class="top_links instructorSubmitButton">
      	  <input type="submit" value="Publish Grades" name="submitToEduspire" class="submit"/> 
        </div>  
</div>
<script>
/**
		@Function Name:	checkAllTotalPointsVal
		@Author Name:	Alan Anil
		@Date:			Nov, 20 2013
		@Purpose:		For checking validations to enter only numbers.
	
*/ 
function checkAllTotalPointsVal()
{  
	var numCheckCounter = 0;
	var pointsVal1 = document.getElementByName("fgGrade").value;
	var pointsVal2 = document.getElementByName("fgCompGrade").value;
	if (isNaN(pointsVal1) || isNaN(pointsVal2)) { 
		numCheckCounter = 1;
	}     
	if(numCheckCounter == 1){
		alert("Please enter only numbers in Total Pts Fields"); 
		return false;
	}
	else {
		return true;
	} 
}	
</script>