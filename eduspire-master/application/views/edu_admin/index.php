<?php 
/**
@Page/Module Name/Class: 	    index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display the admin dashboard
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<script>
  jQuery(document).ready(function($) {
    $( "#tabs" ).tabs();
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
<?php //print_r($this->session->all_userdata()); ?>

<div class="profileAbout">
<div class="profileImage">
	<?php 
		$profile_image=( '' != $user->profileImage)?$user->profileImage:'default.jpg';
		$profile_image=base_url().'uploads/users/'.$profile_image;
	
	$update_image = '<img src="'.crop_image($profile_image).'" title="'.$this->session->userdata('display_name').'" alt="'. $this->session->userdata('display_name').'"/>'; ?>
	<p><?php echo anchor('user/image/',$update_image,'class="fancybox"'); ?></p>
</div>

<div class="profileDiscription">


<div id="tabs" class="dashboard-tabs">
  <div class="instructorName">
  <?php echo $user->firstName.' '.$user->lastName; ?>
  <ul class="tab-nav clearfix">
    <li><a href="#tabs-3">About</a><?php echo anchor('user/about/','<img src="/images/edit.png" title="edit" alt="edit"/>','class="fancybox"'); ?></li>
    <li><a href="#tabs-1">School</a><?php echo anchor('user/school/','<img src="/images/edit.png" title="edit" alt="edit"/>','class="fancybox"'); ?></li>
    <li><a href="#tabs-2">Contact<?php echo anchor('user/contact/','<img src="/images/edit.png" title="edit" alt="edit"/>','class="fancybox"'); ?></a></li>
  
  </ul>
  </div>
	<div id="tabs-3" class="tab-content">
		<?php echo nl2br($user->usrBio); ?>
	</div>
	<div id="tabs-1" class="tab-content">
		<div class="dashboard-left">
			<div class="info"><span class="value"><?php echo $user->role  ?> ( <?php echo $user->yearsActive ; ?></span>Years ) </div>
			<div class="info"><span class="value">
			<?php 
			if($user->gradeSubject):
				echo get_single_value('tracks','trName','trID = '.$user->gradeSubject) ;
			endif;	
			?>
			</span>
			</div>
			<div class="info">
			<span class="label">Grade(s)-</span><span class="value"><?php echo ($user->level)?$user->level:'Not Available'; ?></span>
			</div>
			<?php if($user->iuID): ?>
			<div class="info"><span class="label">IU-</span><span class="value"><?php echo  get_single_value('iu_unit','iuName','iuID = '.$user->iuID) ; ?></span></div>
			<?php endif; ?>
			<div class="info"><span class="label">District-</span><span class="value"><?php
				if(is_numeric($user->districtAffiliation)){
					echo get_single_value('district','disName','disID = '.$user->districtAffiliation) ;
				}else{
					 echo $user->districtAffiliation; 
				}
			?> 
			</span></div>
		</div>
		<div class="dashboard-right">
			<div class="info"><span class="value"><?php echo $user->buildingAssigned; ?></div>
			<div class="info"><span class="value"><?php echo nl2br($user->buildingAddress); ?></div>
			<div class="info"><span class="value"><?php echo $user->buildingCity; ?>, <?php echo $user->buildingState; ?> <?php echo $user->buildingZip; ?>
			</span>
			</div>
		</div>
	
	</div>
  <div id="tabs-2" class="tab-content">
	<div class="dashboard-left">
		<div class="info"><span class="value"><?php echo $user->email; ?></div>
		<div class="info"><span class="value"><?php echo $user->phone; ?></div>
		<div class="info"><span class="value"><?php echo nl2br($user->address); ?>
		</div>
		<div class="info"><span class="value">
		<?php echo ($user->city)?$user->city.',':''; ?> <?php echo $user->state; ?> <?php echo $user->zip; ?> </span></div>
	</div>
    <div class="dashboard-right">
	<ul class="icons">
				<?php if($user->twitter): ?>
				<li class="twitter"><a href="https://twitter.com/<?php echo $user->twitter; ?>" target="_blank"><img src="<?php echo base_url().'/images/icon_twitter.png'; ?>" title="Twitter" alt="Twitter"/></a></li>	
				<?php endif; ?>
				<?php if($user->aim	): ?>
				<li class="aim	"><a href="https://aim.com/<?php echo $user->aim;?>" target="_blank">aim</a></li>	
				<?php endif; ?>
				<?php if($user->msn	): ?>
				<li class="msn"><a href="https://msn.com/<?php echo $user->msn;?>" target="_blank">msn</a></li>	
				<?php endif; ?>
				<?php if($user->facebook	): ?>
				<li class="facebook"><a href="<?php echo $user->facebook;?>" target="_blank"><img src="<?php echo base_url().'/images/icon_facebook.png'; ?>" title="Facebook" alt="Facebook"/></a></li>	
				<?php endif; ?>
				<?php if($user->siteURL	): ?>
				<li class="site"><a href="<?php echo $user->siteURL;?>" target="_blank"><img src="<?php echo base_url().'/images/web_icon.png'; ?>" title="Site" alt="Site"/></a></li>	
				<?php endif; ?>
			</ul>
	</div>
  </div>
  </div>
</div>
</div><!--."profileAbout"-->



<div class="section group"> 
<div class="cfCources">
	<h2>Current and Future Courses <span class="seeAll"><?php echo anchor('edu_admin/course_definition/','See All &raquo;'); ?></span>
</div>
<?php  if(isset($courses) && count($courses)>0):?>
		<table width="100%" border="0" cellspacing="0" cellpadding="0"  class="table" id="grid">

			<?php $i=0; ?>
			<?php foreach($courses as $result): ?>
			<?php $tr_class = ($i++%2==0)?'odd':'even'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<!--<td><?php //echo $result->cdCourseID; ?></td>-->
			<td>
			<?php if(BYOC_ID == $result->cdGenre ): ?>
				<?php echo anchor('edu_admin/course_schedule/one_credit?definition_id='.$result->cdID,$result->cdCourseTitle); ?>
			<?php else: ?>
				<?php echo anchor('edu_admin/course_schedule/index?definition_id='.$result->cdID,$result->cdCourseTitle); ?>
			<?php  endif; ?>
			</td>
			<td><b>Reg/Enroll : </b><?php echo $result->registered_count ?>/<?php echo $result->enrolees_count;?></td>
			
			</tr>
			<?php endforeach; ?>
		</table>
<?php endif; ?>
	

</div><!--.section--->