<?php
// ********************************************************************************************************************************
//Page name			:- 			assignments.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			File used for showing/add all assignments regarding any course.  
//Date				:- 			05-09-2013
//Table Referred		:-  		N/A
//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  

//---------------------------------------------------------------------------------------- 
?>
<script language="javascript">
jQuery(document).ready(function($) {
	$(".fancybox").click(function() {
		$.fancybox.open({
			href : jQuery(this).attr('href'),
			type : 'iframe',
			padding : 5
		});
		return false;
	});
loadDatePicker();
}); 
	/**
		@Function Name:	loadDatePicker
		@Author Name:	Alan Anil
		@Date:			Aug, 26 2013
		@Purpose:		load the date picker 
	
	*/
	
	function loadDatePicker(){
		$( ".datepicker" ).datepicker({dateFormat: 'mm/dd/yy',minDate: 0}).attr('readonly', 'readonly');
	} 
/**
		@Function Name:	fillAll
		@Author Name:	Alan Anil
		@Date:			Oct, 24 2013
		@Purpose:		For filling all points.
	
	*/ 
function fillAll(formname)
{   
	var totalVal    = $("input[class='pointGot pointAval']").length; 
	var checktoggle = document.getElementById("hiddenPointId").value ; 
      for(var i = 0; i < totalVal; i++) { 
        document.getElementById("pointGotId_" + i).value = checktoggle;   
      }  
	
} 
function checkAllPointsVal()
{ 
	var totalVal1    = $("input[class='pointGot pointAval']").length; 
	var numCheckCounter = 0;  
	  for(var i = 0; i < totalVal1; i++) { 
      	var pointsVal = document.getElementById("pointGotId_" + i).value;
		if (isNaN(pointsVal)) { 
			numCheckCounter = 1;
		}
	       
      } 
	  if(numCheckCounter == 1){
		alert("Please enter only numbers in Pts Earned Field"); 
	  	return false;
	  }
	  else {
	  	return true;
	  }
		
} 	 
</script> 
<div class="publicTitle"><h1> </h1></div>
<!--Show course details-->
<h3>
  <?php if(isset($course) && !empty($course)): ?>
  <div class="course_details eduspireNews assignmentCourseCss">
    <div><?php echo $course->cdCourseID; ?>:<?php echo $course->cdCourseTitle; ?></div>
    <div><?php echo format_date($course->csStartDate,DATE_FORMAT);  ?> - <?php echo format_date($course->csEndDate,DATE_FORMAT);  ?> </div> 
  </div>
  
  <?php endif; ?>
</h3>
<div class="result_container leftDiv assignmentListDiv eduspireNews">
  <H3>Assignments</H3>
  <?php if(isset($results) && count($results)>0): ?>
  <?php foreach($results as $result):
		$getAssigmentLock = $this->assignment_model->check_assignment_lock($result->assignID);
		 ?>
  <h4>
    <?php
        $this->_current_request = $this->router->class.'/edit' ; 
		if(!is_allowed($this->_current_request)) {		
			echo $result->assignTitle;
		}
		else { ?>
    <a href="<?php echo base_url().'instructor/edit/'.$result->assignID ;?>"><?php echo $result->assignTitle; ?></a>
    <?php } ?>
  </h4>
  <?php endforeach; ?>
  <?php else: ?>
  <p class="no_recored_fount">No record found</p>
  <?php endif; ?>
</div>
<div id="form" class="rightDiv">
  <div class="error"> <?php echo validation_errors('<p>', '</p>');?>
    <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>
  </div>
  <!--Assigment add/edit form-->
  <form name="addEditAssignment" action="" method="post">
  
  	<div class="instructorRightBlock">
		<div class="instructorBlockInner">
			<div class="instructorTopLabel">
				<label>Title of Assignment:</label>
				<span class="required">*</span>
			</div>
			<input type="text" name="assignmentTitle" value="<?php echo htmlspecialchars(isset($assignData[0]->assignTitle)?($assignData[0]->assignTitle):$this->input->post('assignmentTitle'));?>" />
		</div>
		<div class="instructorBlockInner">
			<div class="instructorTopLabel">
				<label>Points Available:</label>
      			<span class="required">*</span>
			</div>
			<input type="text" name="pointValue" class="pointAval" value="<?php echo isset($assignData[0]->assignPoints)?($assignData[0]->assignPoints):$this->input->post('pointValue');?>" maxlength="255" size="40"/>
		</div>
	</div>
	<div class="instructorRightBlock">
		<div class="instructorBlockInner instructorBlockInnerInput">
			<div class="instructorTopLabel">
				<label>Date Due:</label>
			</div>
			<input type="text" name="dueDate" 
				value="<?php if(isset($assignData)) { 
													   $duDate        = $assignData[0]->assignDueDate;
													   $finalDuDate   = date("m/d/Y", strtotime($duDate));
												   } 
			   echo isset($finalDuDate)?($finalDuDate):$this->input->post('dueDate');?>" 
			   class="datepicker" maxlength="255" size="40"/>
		</div>
		<div class="instructorBlockInner instructorBlockInnerInput">
			<div class="instructorTopLabel">
				<label>Date Activated:</label>
			</div>
			<?php 
					if(isset($assignData)) {   
						$actDate        = $assignData[0]->assignActiveDate;
						$finalActDate   = date("m/d/Y", strtotime($actDate));
						echo isset($assignData)?($finalActDate):$this->input->post('activationDate'); 
					} 
					else
					{ 
					?>
			  <input type="text" name="activationDate" 
					value="<?php echo $this->input->post('activationDate'); ?>" class="datepicker" maxlength="255" size="40"/>
			  <?php 
					} ?>
		</div>
		<div class="instructorBlockInner">
			<div class="instructorTopLabel">
				<label>Last Modified:</label>
			</div> 
			<?php  
				  if(isset($assignData[0]->assignModifiedDate)) {
				  	  $finalLastDate   = date("m/d/Y", strtotime($assignData[0]->assignModifiedDate));
				  }
				echo isset($finalLastDate)?($finalLastDate):date("m/d/Y"); $finalLastDate = ''; ?> 
		</div>
	</div>
	<div class="instructorRightBlock instructorLeftMargin">
		<div class="instructorTopLabel">
			<label>Description:</label>
		</div>
		<textarea rows="6" cols="65" name="assignTopic" ><?php echo isset($assignData[0]->assignTopic)?($assignData[0]->assignTopic):$this->input->post('assignTopic');?></textarea>
	</div>
	<div class="instructorRightBlock">
		<div class="instructorBlockInner">
			<div class="instructorTopLabel">
				<label>Name of link:</label>
			</div>
			<input type="text" name="assignLinkName" 
            	value="<?php echo htmlspecialchars(isset($assignData[0]->assignLinkName)?($assignData[0]->assignLinkName):$this->input->post('assignLinkName'));?>" />
		</div>
		<div class="instructorBlockInner instructorBlockInner2">
			<div class="instructorTopLabel">
				<label>URL:</label>
			</div>
			<input type="text" name="assignLinkUrl" 
            	value="<?php echo htmlspecialchars(isset($assignData[0]->assignLinkUrl)?($assignData[0]->assignLinkUrl):$this->input->post('assignLinkUrl'));?>"  />
		</div>	
	</div>
	<div class="instructorRightBlock">
		<div class="instructorSaveButton">
		  <?php if(isset($assignData)) { ?>
		  <input type="hidden" name="assignId" value="<?php echo $assignData[0]->assignID;?>" />
		  <input type="submit" value="Edit" class="submit"/>
		  <?php 
			   } else {  ?>
		  <input type="submit" value="Save" class="submit"/>
		  <?php } ?>
		</div>
	</div> 
  </form>
</div>
<?php if(isset($assignData)) { ?>
<div class="instructorBottomSection">
  <?php if(isset($userList) && count($userList)>0): ?>
  <!--Form for showing user lists-->
  <form method="post" name="userLists" action="/instructor/saveUsersPoints" onsubmit="return checkAllPointsVal();">
    <table class="table striped instructorTables"  id="grid" width="100%">
      <tr>
        <th colspan="2">Registrant</th> 
        <th>Submitted</th>
        <th> Pts Earned <br />
          <a onclick="fillAll('userLists');" style="cursor:pointer">Fill All Pts</a> </th>
        <th>Total Pts</th>
        <th>Comments</th>
      </tr>
      <?php  
			$pointsValCounter = 0;
			$rowCss = 0;
			foreach($userList as $getUserList)
		    {
				$getUserDetails        = $this->assignment_model->show_assign_user_details($getUserList->alUserID);
				$getUserPointsDetails  = $this->assignment_model->get_total_user_assign($getUserList->alUserID);
				$user                  = $this->user_model->get_single_record($getUserList->alUserID,'*',true);
				$getAssignPointsDetails= $this->assignment_model->get_points_earned($getUserList->alUserID,
										 $assignData[0]->assignID);
				if(isset($getUserDetails[0]->firstName)) 
				{ 						 
			 ?>
      <?php $tr_class = ($rowCss++%2==0)?'even':'odd'; ?>
      <tr class="<?php echo $tr_class; ?>">
        <td><?php   
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
                        title="<?php 
						if(isset($getUserDetails[0]->firstName)){
							echo  $getUserDetails[0]->firstName.', '.$getUserDetails[0]->lastName ; 
						} ?>" 
                        alt="<?php 
						if(isset($getUserDetails[0]->firstName)){
							echo  $getUserDetails[0]->firstName.', '.$getUserDetails[0]->lastName ; 
						} ?>"/> </td>
        <td><?php if(isset($getUserDetails[0]->firstName)){ echo  $getUserDetails[0]->firstName.', '.$getUserDetails[0]->lastName; }?>
        </td>
        <td><?php echo isset($getAssignPointsDetails['alDateSubmitted'])?format_date($getAssignPointsDetails['alDateSubmitted'],'m/d/Y h:i A'):$getAssignPointsDetails['alDateSubmitted']; ?> </td>
        <td><input type="text" class="pointGot pointAval" name="pointGot_<?php echo $getUserList->alUserID;?>" 
                        id="pointGotId_<?php echo $pointsValCounter;?>" class="pointAval" 
                    	value="<?php echo $getAssignPointsDetails['pointsGot'];  ?>" />
          <?php echo ' / ' .$getAssignPointsDetails['totalPoints'];?> </td>
        <td><?php echo $getUserPointsDetails['pointsGot'].' / '.$getUserPointsDetails['totalPoints']; ?> </td>
        <td><textarea name="studentComment_<?php echo $getUserList->alUserID;?>" rows="4" cols="30" placeholder="Comments for student"><?php if(isset($getAssignPointsDetails['alCommentStudent']) && $getAssignPointsDetails['alCommentStudent'] != '') { 
					echo $getAssignPointsDetails['alCommentStudent']; } ?>
                    </textarea>
        </td>
      </tr>
      <?php 
		   $pointsValCounter++;
		   }
		}?>
      <input type="hidden" name="hiddenPoint" id="hiddenPointId" value="<?php echo $getAssignPointsDetails['totalPoints'];?>"  />
      <tr>
        <th colspan="2">Registrant</th> 
        <th>Submitted</th>
        <th> Pts Earned </th>
        <th>Total Pts</th>
        <th>Comments</th>
      </tr>
    </table>
    <?php 
		   // Check permisson to save changes user grades.
		   if(is_allowed('instructor/saveGrades'))  { 
			   ?>
    <div class="top_links  instructorSubmitButton">
      <input type="submit" value="Save Changes"  name="save" class="submit"/>
    </div>
    <?php 
			}  ?>
    <?php
			// Check permisson to publish user grades.
            if(is_allowed('instructor/publishGrades'))  { ?>
    <div class="top_links  instructorSubmitButton">
      <input type="submit" value="Publish Grades" name="submitToEduspire" class="submit"/>
    </div>
    <?php 
			}?>
    <?php 
		   // Check permisson to save changes user grades.
		   if(is_allowed('instructor/saveGrades'))  { 
				?>
    <div class="top_links  instructorSubmitButton">
      <input type="submit" value="Final Grade Entry" name="submitAndFinalGradeEntry" class="submit"/>
    </div>
    <?php
	}
	?>
    <input type="hidden" name="assignIdVal" value="<?php echo $assignData[0]->assignID;?>" />
  </form>
  <?php endif;?>
</div>
<?php } ?>
 