<?php 
/**
@Page/Module Name/Class: 		events.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display latest events list in sidebar
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="sidebar"><!-- sidebar start here-->
	
<?php if(isset($archives) && !empty($archives)): ?>
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

</div><!--.archive-->

	<div class="registrationEnrollment">
		<h3>Registrations / Enrollments</h3>
		<ul>
			<li><span>Today</span><?php echo $today_registered ?>/<?php echo $today_enrolled; ?></li>
			<li><span>This Week</span><?php echo $this_week_registered ?> / <?php echo $this_week_enrolled ?></li>
			<li><span>Last Week</span><?php echo $last_week_registered; ?>/<?php echo $last_week_enrolled; ?></li>
			<!--<li><span>Week of</span>9/30</li>-->
			<li><span>This Month</span> <?php echo $this_month_registered; ?>/<?php echo $this_month_enrolled; ?></li>
			<li><span><?php echo format_date($lastmonth_date,'F'); ?></span> <?php echo $last_month_registered; ?>/<?php echo $last_month_enrolled; ?></li>
			<!--
			<li><span>September</span><a href="#"> 121/20</a></li>
			<li><span>October</span><a href="#"> 30/20</a></li>-->
		</ul>
  </div>

</div><!--admin right -->
<?php endif; ?>
</div><!--.sidebar-->
