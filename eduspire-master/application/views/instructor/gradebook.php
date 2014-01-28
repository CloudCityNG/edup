<?php
// ********************************************************************************************************************************
//Page name			:- 			gradebook.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			File used user gradebook.  
//Date				:- 			01-10-2013
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
</script> 
<h2>Gradebook</h2>
<h3>
	<?php if(isset($course) && !empty($course)): ?>
    <div class="course_details eduspireNews courseFinalGrades">
        <div><?php echo $course->cdCourseID; ?>:<?php echo $course->cdCourseTitle; ?></div>
        <div><?php echo format_date($course->csStartDate,DATE_FORMAT);  ?>
            -
            <?php echo format_date($course->csEndDate,DATE_FORMAT);  ?>
        </div> <?php //echo anchor('user/switch_course','Switch Course','class="fancybox"'); ?>
    </div> 
    <?php endif; ?>
</h3> 

<div class="result_container">
	<?php if(isset($results) && count($results)>0): ?>
    <table class="table striped instructorTables"  id="grid" width="100%"> 
        <tr> 
        	<th>Image</th>
            <th>Name</th>
            <th>Completed <br />Assignments</th>
            <th>Points<br />Earned</th>
            <th>&nbsp;</th>
        </tr>
        <?php 
		$rowCss = 0;
		foreach($results as $result): 
		 $getUserDetails  = $this->assignment_model->show_assign_user_details($result->alUserID);
		 $getUserPointsDetails  = $this->assignment_model->get_total_user_assign($result->alUserID); 
		 $user            = $this->user_model->get_single_record($result->alUserID,'*',true);
		if(isset($getUserDetails[0]->firstName) && $getUserDetails[0]->firstName != '')
		{
		?>
        <?php $tr_class = ($rowCss++%2==0)?'even':'odd'; ?>
	    <tr class="<?php echo $tr_class; ?>">  
            <td> 
                <?php 
                $profile_image=( '' != $user->profileImage)?$user->profileImage:'default.jpg';
                $profile_image=base_url().'uploads/users/'.$profile_image;
                ?>
                <img src="<?php echo crop_image($profile_image); ?>" title="<?php echo $this->session->userdata('display_name'); ?>" 
                alt="<?php echo $this->session->userdata('display_name'); ?>"/> 
            </td>
            <td><?php echo '<a href="#">'.$getUserDetails[0]->lastName.', '.$getUserDetails[0]->firstName.'</a>'."<br>".  
            '<a href="mailto:'.$getUserDetails[0]->email.'">'.$getUserDetails[0]->email.'</a>';
            
            ?></td>
            <td><?php echo $getUserPointsDetails['completedAssign']; ?> </td>
            <td><?php echo $getUserPointsDetails['pointsGot'].'/'.$getUserPointsDetails['totalPoints']; ?> </td>
            <td><a href="<?php echo base_url().'instructor/gradebookdetails/'.$result->alUserID ;?>" class="submit">Details</a> </td>
        </tr>
        <?php 
		}
		endforeach; ?>  
    	<tr> 
        	<th>Image</th>
            <th>Name</th>
            <th>Completed <br />Assignments</th>
            <th>Points<br />Earned</th>
            <th>&nbsp;</th>
        </tr>
    </table>
   
    <?php else: ?>
        <p class="no_record_found">No record found</p>
    <?php endif; ?> 
		
</div>
 