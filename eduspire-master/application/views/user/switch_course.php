<script>
 <?php 
	if(isset($url)){
		echo('top.location.href ="'.$url.'";');
	}	
	if(isset($reload)){
		echo('parent.location.reload(true);');
	}
	
 ?>
</script>
<div>
	<h1>Switch Course</h1>
	<?php if(isset($courses) && !empty($courses)): ?>
	<ul>
		<?php foreach($courses as $course): ?>
		<li>
			<?php echo anchor('user/switch_course/'.$course->csID,$course->cdCourseID.' '.$course->cdCourseTitle.'('.$course->csStartDate.'-'.$course->csCity.', '.$course->csState.')'); ?>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>