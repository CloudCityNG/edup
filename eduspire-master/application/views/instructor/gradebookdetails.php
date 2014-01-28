<?php
// ********************************************************************************************************************************
//Page name			:- 			gradebookdetailes.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			File used for showing all grades regarding any course.  
//Date				:- 			05-09-2013
//Table Refered		:-  		N/A
//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  

//---------------------------------------------------------------------------------------- 
?>
<h2>Gradebook:<?php echo $assignDetails[0]->firstName.'  '.$assignDetails[0]->lastName; ?></h2>
<h3>
	<!--Show user course details-->
	<?php if(isset($course) && !empty($course)): ?>
    <div class="course_details  eduspireNews courseFinalGrades gradebookDetailsTop">
        <div><?php echo $course->cdCourseID; ?>:<?php echo $course->cdCourseTitle; ?></div>
        <div><?php echo format_date($course->csStartDate,DATE_FORMAT);  ?>
            -
            <?php echo format_date($course->csEndDate,DATE_FORMAT);  ?>
        </div> 
    </div> 
    <?php endif; ?>
</h3> 
<!--Show user grade details-->
<div class="result_container gradebookDetailsTop"> 
	<?php if(isset($results) && count($results)>0): ?>
    <table class="table striped instructorTables" id="grid" width="100%"> 
        <tr> 
        	<th>Assignment</th>
            <th>Score</th> 
        </tr>
        <?php 
		$rowCss = 0;
		foreach($results as $result):  
		$tr_class = ($rowCss++%2==0)?'even':'odd'; ?>
	    <tr class="<?php echo $tr_class; ?>">   
        <td><?php echo $result->assignTitle ; ?> </td>
        <td><?php echo $result->alGrade.'/'.$result->assignPoints ; ?> </td> 
        </tr>
        <?php endforeach; ?>  
    </table>
   
    <?php else: ?>
        <p class="no_record_found">No record found</p>
    <?php endif; ?> 
		
</div>
 