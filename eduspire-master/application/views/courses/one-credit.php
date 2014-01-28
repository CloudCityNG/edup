<?php 
/**
@Page/Module Name/Class: 		index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display course landing page 
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

<?php if(isset($results) && !empty($results)): ?>
	<div class="error_msg">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form name="one_credit_form">
	<?php foreach($results as $result): ?>
		<?php
			//fetch the courses inside these definitions ;
			$courses=$this->course_schedule_model->get_courses(BYOC_ID,	'',0,0,-1,			date('Y-m-d'),STATUS_PUBLISH,false,0,$result->cdID);
			if($courses):
			//if course are avaiblebe 
			//show only one course schedule block 		
			?>
				<div class="section">
				<h2><a href="<?php echo get_seo_url('courses/view',$result->cdID,$result->cdCourseTitle); ?>" class="fancybox"><?php  echo $result->cdCourseID; ?> :<?php echo $result->cdCourseTitle; ?></a></h2>
					<div class="schedule">
						<div><?php echo format_date($courses[0]->csStartDate,DATE_FORMAT); ?>-<?php echo format_date($courses[0]->csEndDate,DATE_FORMAT); ?>
						<span> <input type="checkbox" name="course[<?php echo $result->cdID; ?>]" value="<?php echo $courses[0]->csID; ?>"/> </span>
						</div>
					</div>
				
				<?php /* foreach($courses as $course): ?>
					<div class="schedule">
						<div><?php echo format_date($course->csStartDate,DATE_FORMAT); ?>-<?php echo format_date($course->csEndDate,DATE_FORMAT); ?>
						<span> <input type="radio" name="course[<?php echo $result->cdID; ?>]" value="<?php echo $course->csID; ?>"/> </span>
						</div>
					</div>
				<?php endforeach; */ ?>
				</div>
		<?php endif; ?>
			
	<?php endforeach; ?>
	<input type="submit" class="submit" value="Continue" name="one_credit_submit"/>
	</form>
	
<?php  else:?>
<p class="no-record">Course not Available</p>
<?php endif; ?>



	




