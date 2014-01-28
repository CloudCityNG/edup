<?php 
// ********************************************************************************************************************************
//Page name			:- 			dashboard.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			Instructor dashboard.  
//Date				:- 			05-09-2013
//Table Refered		:-  		N/A
//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  
//	Ref 1  ben.binesh          oct 08          minor 			work on dashboard   	
//---------------------------------------------------------------------------------------- 
?>
<script>
  jQuery(document).ready(function($) {
    $( "#tabs" ).tabs();
	$( "#accordion" ).accordion({
      heightStyle: "content",
	  active: 0
    });
  


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
<div class="publicTitle"><h1> </h1></div>
<div class="profileAbout">
<div class="flash_message">
<?php get_flash_message(); ?>
</div> 
<div class="section clearfix"> 
<div class="profileImage">
	<p><?php 
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
	?>
	<a href="/user/image"  class="fancybox"><img src="<?php echo crop_image($profile_image); ?>" title="<?php echo $this->session->userdata('display_name'); ?>" alt="<?php echo $this->session->userdata('display_name'); ?>" width="153px" height="151px"/></a></p>
</div>
<div class="profileDiscription"> 
<div id="tabs" class="dashboard-tabs">
	  <div class="instructorName">
      <?php echo $user->firstName.' '.$user->lastName; ?>
	  <ul>
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
			
			<div class="info"><span class="label">District-</span><span class="value"><<?php
				if(is_numeric($user->districtAffiliation)){
					echo get_single_value('district','disName','disID = '.$user->districtAffiliation) ;
				}else{
					 echo $user->districtAffiliation; 
				}
			?> </span></div>
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
				<?php if($user->aim	): ?>
				<li class="msn"><a href="https://msn.com/<?php echo $user->msn;?>" target="_blank">msn</a></li>	
				<?php endif; ?>
				<?php if($user->facebook	): ?>
				<li class="facebook"><a href="<?php echo $user->facebook;?>" target="_blank"><img src="<?php echo base_url().'/images/icon_facebook.png'; ?>" title="Facebook" alt="Facebook"/></a></li>	
				<?php endif; ?>
				<?php if($user->facebook	): ?>
				<li class="site"><a href="<?php echo $user->siteURL;?>" target="_blank"><img src="<?php echo base_url().'/images/web_icon.png'; ?>" title="Site" alt="Site"/></a></li>	
				<?php endif; ?>
			</ul>
	</div>
  </div>
  </div>
</div>
</div><!--.section-->
</div><!--.profileAbout-->

<div class="instructorDashboard">
<div class="instructorGrid">
<div class="section clearfix" id="accordion"> 

	<?php if(isset($courses) && !empty($courses)): ?>
		<?php foreach($courses as $cor): ?>
		<div class="course-div clearfix">
        <div class="head">
			<h2><?php echo  $cor->cdCourseID; ?>:<?php echo $cor->cdCourseTitle;  ?></h2>	
			<span class="location">
			<?php $course_location=$cor->csCity.', '.$cor->csState;	
				if(COURSE_ONLINE == $cor->csCourseType)
					$course_location='Online';
				echo $course_location;
			?>
			</span>
			<span class="date"><?php echo format_date($cor->csStartDate,DATE_FORMAT); ?>-<?php echo format_date($cor->csEndDate,DATE_FORMAT); ?></span>
		</div>
        </div>	
		<div class="assignment-div clearfix">
        <table id="instructor" cellspacing="0" cellpadding="0" width="100%">
			<?php 
				$assignments = $cor->assignments( $this->session->userdata('user_id') );
				if( !empty( $assignments ) ):
			?>	
			 
				<tr>
				<th>Assignment</th>
				<th>Activated</th>
				<th>Due</th>
				<th>Points</th>
				</tr>
				
				<?php foreach( $assignments as $assignment ): ?>
				<tr>
					<td><?php //echo anchor(get_seo_url('assignment/view',$assignment->assignID,$assignment->assignTitle),$assignment->assignTitle)?>
                    <a href="<?php echo base_url().'instructor/edit/'.$assignment->assignID ;?>">
						<?php echo $assignment->assignTitle; ?>
                    </a>
                    </td>
					<td>
					<?php 
					if($assignment->assignActiveTime == '')
					{ 
						$assignment->assignActiveTime = '00:00:00';
					}
					$actTime = $assignment->assignActiveDate.' '.$assignment->assignActiveTime; 
					echo isset($assignment->assignActiveDate)?format_date($actTime,'M d, Y h:i A'):$actTime; ?>
					</td>
					<td>
					<?php 
						$dueTime = $assignment->assignDueDate.' '.$assignment->assignDueTime; 
						echo isset($assignment->assignDueDate)?format_date($dueTime,'M d, Y h:i A'):$dueTime; ?> 
					</td>
					<td><?php echo $assignment->assignPoints; ?></td>
				</tr>	
				<?php endforeach; ?> 
				<?php endif; ?>	 
                <tfoot>
                <tr>
                    <td colspan="4">
                        <span class="import">
						<?php				
						echo anchor('assignment/index?for_course='.$cor->csID.'&course_id='.$cor->csID.'&definition_id='.
						$cor->csCourseDefinitionId,'Import Assignments'); ?>
                        </span>  
                        <span class="newAssignment">
						<?php 
						echo anchor('instructor/assignments/'.$cor->csID ,'New Assignment'); 	
						?>
                        </span>  
                        <span>
						<?php echo anchor(get_seo_url('courses/enrollees',$cor->csID,$cor->cdCourseTitle),'Class Roster'); ?>
                        </span>  
                        <span class="finalGrade"><?php 
						if( !empty( $assignments ) ){	
							echo anchor('instructor/gradeentry/'.$cor->csID,'Final Grade Entry');  
						} ?>
                        </span>
						<span class="QuestionnaireReports">
						<?php 
						if( !empty( $assignments ) ){	
							if(is_allowed('questionnaire_report/index')): 
						echo anchor('questionnaire_report/index/'.$this->session->userdata('user_id').'/'.$cor->csID,'Questionnaire Reports'); 
						endif; 
						} ?>
                        </span>
                    </td>
                </tr>	
				</tfoot>
			</table> 
		</div>	
		<?php endforeach; ?>
	<?php endif; ?>
	
</div><!--.section-->
</div>
</div> 