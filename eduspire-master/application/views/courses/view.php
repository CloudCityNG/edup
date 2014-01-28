<?php 
/**
@Page/Module Name/Class: 		view.php
@Author Name:			 		ben binesh
@Date:					 		Oct 14 2013
@Purpose:		        		display course definition page 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<?php if(!empty($course_definition)): ?>
<div class="popupTitle">	<h1><?php echo $course_definition->cdCourseTitle; ?></h1></div>
<?php endif; ?>
<?php if(!empty($course_definition)): ?>

	<?php if($course_definition->cdDescription): ?>
		<div class="content-row" class="course-description">
			<?php echo $course_definition->cdDescription;  ?>	
		</div>
	<?php endif;  ?>
	
	<?php if($course_definition->cdEvaluationMethod): ?>
		<div class="content-row" class="evaluation-method">
			<!--<h3>Evaluation Method</h3>-->
			<?php echo $course_definition->cdEvaluationMethod;  ?>	
		</div>
	<?php endif;  ?>

	<?php if($course_definition->cdGoals): ?>
		<div class="content-row" class="goals">
			<!--<h3>Goals</h3>-->
			<?php echo $course_definition->cdGoals;  ?>	
		</div>
	<?php endif;  ?>
	
	<?php if($course_definition->cdOutline): ?>	
		<div class="content-row" class="outline">
			<h3>Course Outline</h3>
			<?php echo $course_definition->cdOutline;  ?>	
		</div>
	<?php endif;  ?>
	
<?php endif; ?>



