<?php 
/**
@Page/Module Name/Class: 		user.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display user sidebar data 
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
			padding : 5,
                        margin: [115, 0, 10, 0] // top, right, bottom, left
		});
		return false;
	});

});
</script>
<?php /*

<div class="block clearfix">	
<h3 class="block-title">NAVIGATION</h3>
<div class="block-content">

<h4>Member Tools</h4>
<ul>
	<li><?php  echo anchor('user/profile','My Profile'); ?></li>
	<li><?php  echo anchor('user/edit','Edit Profile'); ?></li>
	<li><?php  echo anchor('user/receipt','My Receipts'); ?></li>
	<li><?php  echo anchor('user/change_password','Change Password'); ?></li>
	<?php if(MEMBER == $this->session->userdata('access_level')): ?>
	<li><?php  echo anchor('#','Final Grades'); ?></li>
	<?php  endif;?>
</ul>
<?php if(INSTRUCTOR == $this->session->userdata('access_level')): ?>
<h4>Instructor Tools</h4>
<ul>
	<li><?php  echo anchor('instructor/assignments','Assignments'); ?></li>
	<li><?php  echo anchor('instructor/gradeentry','Final Grade Entry'); ?></li>
	<li><?php  echo anchor('instructor/gradebook','Gradebook'); ?></li>
	<li><?php  echo anchor('instructor/questionnaire','Questionnaire Reporting'); ?></li>
	<li><?php  echo anchor('instructor/techimplementation','Tech Implementation Plans'); ?></li>
</ul>
<?php endif; ?>

<h4>Other Tools</h4>
<ul>
	<li><?php  echo anchor('faq','Frequently Asked Questions'); ?></li>
	<li><?php  echo anchor('user','Members Directory'); ?></li>
	<li><?php  echo anchor('events','News'); ?></li>
</ul>
</div><!--.block-content-->
</div><!--.block-->
*/?>
<div class="adminRight">
          <div class="eduspireNews">
            <h3>Eduspire News</h3>
		  <ul>

		<?php foreach($archives as $event): ?>
		 <li>
			<h4><a href="<?php echo get_seo_url('event',$event->nwID,$event->nwTitle); ?>"><?php echo $event->nwTitle; ?></a></h4>
			<span><?php echo format_date($event->nwDate,'M d, Y'); ?></span>
			<p><?php echo get_excerpt($event->nwDescription); ?></p>
		</li>
		<?php endforeach; ?>
		</ul>
          </div>
          
        </div>
