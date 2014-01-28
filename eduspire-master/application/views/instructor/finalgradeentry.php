<?php 
// ********************************************************************************************************************************
//Page name			:- 			finalgradeentry.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			File used for showing user grades and comment from admintrator.  
//Date				:- 			05-09-2013
//Table Refered		:-  		N/A
//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  

//---------------------------------------------------------------------------------------- 
?>
<script language="JavaScript">
jQuery(document).ready(function($) {
	$(".fancybox").click(function() {
		$.fancybox.open({
			href : jQuery(this).attr('href'),
			type : 'iframe',
			padding : 5
		});
		return false;
	});

});
 
function checkAll(formname)
{  
	var checked = $("input[id='checkAllSubmit']:checked").length; 
  	var checktoggle = '';
	  if (checked == 0) {
		checktoggle = false ;
	  } else {
		checktoggle = true;
	  } 
	 var checkboxes = new Array();
      checkboxes = document.forms[formname].getElementsByTagName('input');
      for (var i = 0; i < checkboxes.length; i++) {
          if (checkboxes[i].type === 'checkbox') {
               checkboxes[i].checked = checktoggle;
          }
      }
}
function checkAllFinalPointsVal()
{ 
	var totalVal1    = $("input[class='gradeBox fgCompGrade']").length;  
	var numCheckCounter = 0;  
	  for(var i = 1; i <= totalVal1; i++) { 
      	var pointsVal = document.getElementById("fgCompGrade_" + i).value; 
		if (isNaN(pointsVal)) { 
			numCheckCounter = 1;
		}
	       
      } 
	  var totalVal2    = $("input[class='gradeBox fgGrade']").length;   
	  for(var i1 = 1; i1 <= totalVal2; i1++) { 
      	var pointsVal1 = document.getElementById("fgGrade_" + i1).value;
		if (isNaN(pointsVal1)) { 
			numCheckCounter = 1;
		}
	       
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
<div class="publicTitle"><h1> </h1></div>
<!--Show user course details.-->
<h2>Final Grades</h2>
<h3>
	<?php if(isset($course) && !empty($course)): ?>
    <div class="course_details eduspireNews courseFinalGrades">
        <div>Course:<?php echo $course->cdCourseID; ?>:<?php echo $course->cdCourseTitle; ?></div>
        <div><?php echo format_date($course->csStartDate,DATE_FORMAT);  ?>
            -
            <?php echo format_date($course->csEndDate,DATE_FORMAT);  ?>
        </div> 
    </div> 
    <?php endif; ?>
</h3> 
 
<div class="result_container">
<!--Check result exist or not.-->
	<?php if(isset($results) && count($results)>0): ?>
    <!--Final grade entry form.-->
    <form action="" method="post" name="finalGradeEntry" onsubmit="return checkAllFinalPointsVal();">
        <table class="table striped instructorTables" id="grid" width="100%"> 
        <tr>  
            <th colspan="2">Registrant</th> 
            <th>Total Pts</th>
            <th>Current	Grade</th>
            <th>Comments for Administration</th>
            <th><input type="checkbox" id="checkAllSubmit" onclick="checkAll('finalGradeEntry');" />Submit</th>
        </tr> 
        <?php
		$totalNoOfUser     = 0;
		$noOfUserSubmitted = 0; 
		$gardeValue        = '';
		$rowCss            = 0;
		// Fetch multiple records.
		 foreach($results as $result)
		 { 
			$getUserDetails        = $this->assignment_model->show_assign_user_details($result->alUserID);
			$getGradeSum           = $this->assignment_model->show_user_grade_sum($result->alUserID,$activeCourse);
			$getGradeNum           = $this->assignment_model->get_user_grade($result->alUserID,$activeCourse);
			$user                  = $this->user_model->get_single_record($result->alUserID,'*',true);
			$getUserDetails        = $this->assignment_model->show_assign_user_details($result->alUserID);
			$getUserPointsDetails  = $this->assignment_model->get_total_user_assign($result->alUserID); 
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
						echo "NON-CREDIT - NO GRADE";
					}
					else {
					?>
                     <input type="text" name="fgGrade_<?php echo $result->alUserID; ?>"  class="gradeBox fgGrade" 
                    id="fgGrade_<?php echo $totalNoOfUser; ?>"
                    value="<?php if(isset($getGradeNum['fgGrade']) && $getGradeNum['fgGrade'] != '') { 
					echo $getGradeNum['fgGrade']; } ?>" maxlength="5" >
                    /                                       
                    <input type="text" name="fgCompGrade_<?php echo $result->alUserID; ?>" class="gradeBox fgCompGrade" 
                    id="fgCompGrade_<?php echo $totalNoOfUser; ?>"
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
							
						    if(isset($getGradeNum['fgComputedGrade']) && $getGradeNum['fgApproved'] == 0) {
								$percentage = 'Not Final';
							}
							else {  
								if($getGradeNum['fgComputedGrade'] == 0) {
									$percentage = 'A';
								}
								else {
									$percentage  = $this->assignment_model->percentage($getGradeNum['fgGrade'],
									$getGradeNum['fgComputedGrade'],0);
								}   
							}			
							echo $percentage;  
						} ?> 
                        
                    </td>
                    
                    <td>
                    <?php
					// Show admin commment section.
					if(isset($getGradeNum['fgComputedGrade']) && $getGradeNum['fgComputedGrade'] != '') {
					 ?>
                     <textarea name="adminComment_<?php echo $result->alUserID; ?>" rows="4" cols="40" placeholder="Enter comments here" maxlength="500" ><?php if(isset($getGradeNum['fgCommentAdmin']) && $getGradeNum['fgCommentAdmin'] != '') {echo $getGradeNum['fgCommentAdmin'] ; }?></textarea> 
					 <?php
					} ?>
                    </td>
                    
                    <td>
                     <?php
					 // Check checkbox if grade already submitted.
					if(isset($getGradeNum['fgComputedGrade'])) {
					 ?>
                        <input type="checkbox" name="approved" class='checkall' id="checkAllApprovedId"
                        <?php if(isset($getGradeNum['fgComputedGrade']) && $getGradeNum['fgApproved'] == 1) { ?> checked="checked"  
                        <?php $noOfUserSubmitted++ ;} ?> />
                    <?php 
					} ?>
                    </td>
				</tr>
				
				<?php 
			}
		} ?>
         <tr>  
            <th colspan="2">Registrant</th> 
            <th>Total Pts</th>
            <th>Current	Grade</th>
            <th>Comments for Administration</th>
            <th>Submit</th>
        </tr> 
       </table>
       <input type="hidden" name="totalNoOfUsers" value="<?php echo $totalNoOfUser?>" />
       <input type="hidden" name="noOfUserSubmitted" value="<?php echo $noOfUserSubmitted?>" />
        <div class="top_links instructorSubmitButton">
      	  <input type="submit" value="Save Changes"  name="save" class="submit"/> 
        </div>
         <?php
			// Check permisson to publish user grades.
        if(is_allowed('instructor/finalGrades'))  { ?>
        <div class="top_links instructorSubmitButton">
      	  <input type="submit" value="Publish Grades" name="submitToEduspire" class="submit"/> 
        </div>
        <?php 
		} ?>
        <!--If no record found-->
    <?php else: ?>
        <p class="no_record_found">No record found</p>
    </form>
    <?php endif; ?> 
		
</div>
 