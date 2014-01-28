<?php 
/**
@Page/Module Name/Class: 		courses.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display the upcomming courses in the sidebar 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>
jQuery(document).ready(function($) {
	selectnav('sidenav'); 
	navigation = $('#sidenav_course .selectnav').html();
	$('#selectnav-4').html(navigation);
	$('#selectnav-5').html(navigation);
	var location = window.location.href;
	$("#selectnav-4 option[value='"+location+"']").attr("selected", "selected");
	$("#selectnav-5 option[value='"+location+"']").attr("selected", "selected");
});	

</script>

<?php 
$course_types = get_content('course_genres','cgID,cgTitle','cgPublish = '.STATUS_PUBLISH,'cgDisplayOrder','ASC'); 

if(isset($course_types) && !(empty($course_types))): ?>
<div class="block">	
	<div id="sidenav_course">
	<div class="aside">
	
	<ul id="sidenav">
	<?php foreach($course_types as $course_type): ?>
			<li class="<?php echo (isset($this->current_genre) && $this->current_genre == $course_type->cgID )?'active':'' ?>">
			<a href="<?php echo get_seo_url('course',$course_type->cgID,$course_type->cgTitle) ?>" title="<?php echo $course_type->cgTitle; ?>"><?php echo $course_type->cgTitle; ?></a>
			</li>
	<?php endforeach; ?>	
	
	</ul>	
	</div><!--.block-content-->
	</div>
</div><!--.block-->
<?php endif; ?>