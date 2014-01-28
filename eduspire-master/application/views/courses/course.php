<?php 
/**
@Page/Module Name/Class: 		course.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display the single course data 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>
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
<?php if(isset($course) && (!empty($course))):  ?>
<div class="publicTitle">
<h1>
<?php echo $course->cdCourseTitle .' ('.$course->cdCourseID.')'; ?>
</h1>
</div>
<?php endif; ?>
<div class="flash-message">
	<?php get_flash_message(); ?>
</div>
<div class="course-details">

	<h3>Location:</h3>
	<div class="address">
	<?php if(COURSE_OFFLINE == $course->csCourseType): ?>
		<div><?php echo $course->csLocation; ?></div>
		<div><?php echo $course->csAddress; ?></div>
		<div><?php echo $course->csCity; ?>, <?php echo $course->csState; ?> <?php echo $course->csZIP; ?>
		</div>
	<?php else: ?>
	<div>Online</div>	
	<?php endif; ?>

	</div>
	
	<?php if(isset($instructors) && !empty($instructors)): ?>	
	<h3>Instructors:</h3>
	<div class="courcePrice">
		<?php  foreach($instructors as $user):?>
		<div>
		<a href="<?php echo base_url(); ?>userprofile/instructorbio/<?php echo $user->id; ?>"  class="fancybox">
        <?php echo $user->firstName.' '.$user->lastName; ?></a> 
		</div>	
		<?php  endforeach; ?>
	</div>
	<?php  endif;?>

	<h3>Price <span><?php echo $course->cgCourseCredits; ?></span>:</h3>
	<div class="courcePrice">
    	<div>Price (<?php echo $course->cgCourseCredits; ?>-credits): <span><?php echo CURRENCY; ?><?php echo number_format($course->csPrice, 2); ?></span></div>
		<div>Price (non-credit): <span><?php echo CURRENCY; ?><?php echo number_format($course->csNonCreditPrice, 2); ?></span></div>
	</div>
	
	<?php if($course->csNonCreditComment): ?>
	<h3>Notes:</h3>
	<p><?php echo $course->csNonCreditComment; ?></p>
	<?php endif; ?>
	

<?php if(count($course->course_dates)>0): ?>

<?php if(COURSE_OFFLINE == $course->csCourseType): ?>	
	<h3>Dates:</h3>
<?php endif;?>

<?php foreach($course->course_dates as $course_date): ?>

	<?php if(COURSE_OFFLINE == $course->csCourseType): ?>
		
		<div class="dates"><?php echo format_date($course_date->csdStartDate,DATE_FORMAT); ?>
		( 
			<?php echo format_date($course_date->csdStartTime,TIME_FORMAT); ?>
			-
			<?php echo format_date($course_date->csdEndTime,TIME_FORMAT); ?>
		)	
	

	
	<?php else: ?>
	
		<h3>Start Date:</h3>
		<div class="dates"><?php echo format_date($course_date->csdStartDate,DATE_FORMAT); ?></div>
	
	
	
		<h3>End Date:</h3>
		<div class="dates"><?php echo format_date($course_date->csdEndDate,DATE_FORMAT); ?></div>
	
	
	
	<?php endif; ?>

</div>
<?php endforeach; ?>

<?php endif; ?>
<div class="pTop20">

<?php 
//check whether to show the register button or not 
if(isset($is_register) && ($is_register)):
	echo anchor('login/index/'.$course->csID,'Register','class="submit" title="Register"');
endif;
 ?>
</div>
</div>
</div><!--.course-details-->



