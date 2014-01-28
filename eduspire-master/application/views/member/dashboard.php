<?php 
/**
@Page/Module Name/Class: 		dashboard.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display the member dashboard
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>
  jQuery(document).ready(function($) {
    $( "#tabs" ).tabs();
	$( "#accordion" ).accordion({
		heightStyle: "content",
		active: 0
    });
	$( ".fancybox" ).click(function() {
		$.fancybox.open({
			href : jQuery(this).attr('href'),
			type : 'iframe',
			padding : 5
		});
		return false;
	});

});

</script>
<!--<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>-->
<div class="flash_message push-20">
<?php get_flash_message(); ?>
</div>

<div class="profileAbout">
<div class="profileImage">
	<?php 
		$profile_image=( '' != $user->profileImage)?$user->profileImage:'default.jpg';
		if('default.jpg' != $profile_image)
		{
			$image_path=UPLOADS.'/users/'.$profile_image;
			if(!file_exists($image_path))
			{
				$profile_image='default.jpg';
			}
		}	
		$profile_image=base_url().'uploads/users/'.$profile_image;
		$image_path = '<img src="'.crop_image($profile_image).'" title="'.$this->session->userdata('display_name').'" alt="'.$this->session->userdata('display_name').'"/>';?>
	<p><?php echo anchor('user/image/',$image_path,'class="fancybox"'); ?></p>
</div>
<div id="tabs" class="profileDiscription">
  
   <div class="instructorName">
       <?php echo $user->firstName.' '.$user->lastName; ?>
   <ul class="tab-nav clearfix">
    
    <li><a href="#tabs-1">School <?php echo anchor('user/school/','<img src="/images/edit.png" title="edit" alt="edit"/>','class="fancybox"'); ?></a></li>
    <li><a href="#tabs-2">Contact<?php echo anchor('user/contact/','<img src="/images/edit.png" title="edit" alt="edit"/>','class="fancybox"'); ?></a></li>
  </ul>
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
		<div class="info"><span class="value"><?php echo format_phone_number($user->phone); ?></div>
		<div class="info"><span class="value"><?php echo nl2br($user->address); ?>
		</div>
		<div class="info"><span class="value">
		<?php echo ($user->city)?$user->city.',':''; ?> <?php echo $user->state; ?> <?php echo $user->zip; ?> </span>
                </div>
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
 
</div><!--.section-->
</div><!--.profileabout-->

<div class="instructorDashboard">
<div class="instructorGrid">
    
<div class="section clearfix" id="accordion"> 
    
	<?php if(isset($courses) && !empty($courses)): ?>
		<?php foreach($courses as $cor):?>
		<div class="course-div clearfix">
                    <div class="head">
			<h2><?php echo $cor->cdCourseID; ?>:<?php echo $cor->cdCourseTitle;  ?></h2>	
			<span class="location">
				<?php $course_location=$cor->csCity.', '.$cor->csState; 
					if(COURSE_ONLINE==$cor->csCourseType)
						$course_location='Online';
					echo $course_location;
				?> 
						 
            </span>
			<span class="date">
                            <?php echo format_date($cor->csStartDate,DATE_FORMAT); ?>-<?php echo format_date($cor->csEndDate,DATE_FORMAT); ?>
                        </span>
                    </div>
                </div>
    <div class="assignment-div clearfix">
        <table id="instructor" cellspacing="0" cellpadding="0" width="100%">
		<?php
		//assignments 
		$assignments = $this->assignment_model->geUserAllAssignments(0,$cor->csID,0,-1,$user->id,true);
		if(!empty($assignments)):
		?>
                    
                            <tr>
                            <th>Assignment</th>
                            <th>Due</th>
                            <th>Points</th>
                            </tr>
                            <?php foreach($assignments as $assignment):  ?>
                            <?php if(is_show_assignment($assignment,$cor,$user)): ?>
                            <tr>
                                <td><?php echo get_assignment_url($assignment,$user->id); ?></td>
                                <td>
                               <?php echo format_date($assignment->assignDueDate,DATE_FORMAT).' '.format_date($assignment->assignDueTime,TIME_FORMAT); ?>
                                </td>
                                <td> 
                                        <?php echo $assignment->assignPoints;?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>

                    
				
		<?php  endif; ?>
                            <tr><td colspan='3'>
                                    <span>
		<?php echo anchor('courses/members/?course_id='.$cor->csID.'&ref=instructor','Instructors') ?>
                </span>
                                    <span>
		<?php echo anchor('courses/members/?course_id='.$cor->csID.'&ref=member','Directory') ?>
                </span>
                                    <span>
		<?php echo anchor('/member/grade_sheet/'.$cor->csID,'Grade sheet') ?>
                </span>
                                    <span>
		<?php echo anchor('/member/grades/'.$cor->csID,'Final Grade') ?>
                </span>
                                    <span>
		<?php 
			if($receipt_url=get_receipt_url($cor->csID,$user->email)){
				echo anchor( $receipt_url,'Receipt');
			}

		?></span>
                  </td></tr>
                </table>
            </div>
	<?php endforeach; ?>
	<?php  endif; ?>
	
</div><!--.section-->
</div>
</div>