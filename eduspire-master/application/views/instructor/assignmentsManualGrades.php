<?php 
// ********************************************************************************************************************************
//Page name			:- 			assignmentsMaunaulGrades.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			File used for showing assignments and grades manually.  
//Date				:- 			05-09-2013
//Table Refered		:-  		N/A
//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  

//----------------------------------------------------------------------------------------  
?>
<h2>Assignment:&nbsp;<?php echo $courseDetails[0]->assignTitle; ?></h2>
<h3>
	<!--Show user course details-->
	<?php if(isset($course) && !empty($course)): ?>
    <div class="course_details">
        <div><?php echo $course->cdCourseID; ?>:<?php echo $course->cdCourseTitle; ?></div>
        <div><?php echo format_date($course->csStartDate,DATE_FORMAT);  ?>
            -
            <?php echo format_date($course->csEndDate,DATE_FORMAT);  ?>
        </div>
    </div>
    <?php endif; ?>
</h3>  
<!--Show user grades details-->
<div class="result_container">
<form class="form" action="" method="post">
	<?php if(isset($results) && count($results)>0): ?>
    <table class="table"> 
        <tr> 
            <th>Name</th>
            <th>Grade</th>
        </tr>
        <?php foreach($results as $result): 
		 $getUserDetails  = $this->assignment_model->show_assign_user_details($result->alUserID);
		?>
        <tr>  
        <td><?php if(isset($getUserDetails[0]->lastName) && $getUserDetails[0]->lastName != '') { echo $getUserDetails[0]->lastName; } else {} 
		          if(isset($getUserDetails[0]->firstName) && $getUserDetails[0]->firstName != '') { echo ', '.$getUserDetails[0]->firstName; } else {} ?></td>
        <td><?php if($result->alGrade != '') { echo $result->alGrade; } else { ?> <input type="text" name="grade_<?php 
		echo $result->alUserID ; ?>" value="" /> <?php } ?>  </td>
        </tr>
        <?php endforeach; ?>   
    </table>
    <div class="top_links clearfix">
    <input type="submit" value="Save" class="submit"/> 
    </div>
     <div class="top_links clearfix">
    <a href="<?php echo base_url(); ?>instructor/assignments" class="submit">Cancel</a> 
    </div>
    <?php else: ?>
        <p class="no_recored_fount">No record found</p>
    <?php endif; ?> 
</form>		
</div>
