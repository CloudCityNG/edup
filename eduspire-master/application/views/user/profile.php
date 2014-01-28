<?php 
/**
@Page/Module Name/Class: 		profile.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display user profile 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
 
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  
// Ref1    ben.binesh		Oct 11, 2013       major 		add course registeration and payment consistency check

																
//----------------------------------------------------------------------------------------  
?>
<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<?php get_flash_message(); ?>

<div class="employmentDetails">
	<h2  class="section-title">Account</h2>
	<div class="section-info">
		<div><?php echo $user->userName?></div>
		<div><?php echo $user->email?></div>
		</div><!--section-info-->
</div><!--.section-->

<div class=" employmentDetails">
	<h2  class="section-title">Personal</h2>
	<div class="section-info infoDetails">
		<div class="personalImage">
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
		<img src="<?php echo crop_image($profile_image); ?>" title="<?php echo $this->session->userdata('display_name'); ?>" alt="<?php echo $this->session->userdata('display_name'); ?>"/></p>
		</div>
		<div class="address">
		<div><?php echo $user->firstName.' '.$user->lastName; ?></div>
		<div>
		
		<?php echo ($user->address)?$user->address.'<br/>':''; ?>
		
		<?php echo ($user->city)?$user->city.',':''; ?> <?php echo $user->state; ?> <?php echo $user->zip; ?><br/>
		<?php echo ($user->phone)?$user->phone:''; ?><?php echo ($user->mobileCarrier)?'('.$user->mobileCarrier.')':''; ?><br/>
		</div>
		<?php if($user->birthDate): ?>
		<div>Bith Date: <?php echo format_date($user->birthDate,DATE_FORMAT); ?></div>
		<?php endif; ?>
		
		<?php if($user->email2): ?>
		<div>Secondry Email: <?php echo $user->email2; ?></div>
		<?php endif; ?>
		</div>
		<div class="social-media-info">
			<h2  class="section-title">Web Presence/Social Networking</h2>
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
		
	</div><!--section-info-->
</div><!--.section-->


<div class="employmentDetails">
	<h2  class="section-title">Employment</h2>
	<div class="section-info">
	<div class="info"><span class="value"><?php echo $user->role  ?>
	<?php if($user->yearsActive): ?>
	( <?php echo $user->yearsActive ; ?></span> years ) 
	<?php endif; ?> </div>
	
	<?php if($user->iuID): ?>
		<div class="info"><span class="label">IU:</span><span class="value"><?php echo  get_single_value('iu_unit','iuName','iuID = '.$user->iuID) ; ?></span></div>
	<?php endif; ?>
	<div class="info"><span class="label">District:</span><span class="value"><?php
				if(is_numeric($user->districtAffiliation)){
					echo get_single_value('district','disName','disID = '.$user->districtAffiliation) ;
				}else{
					 echo $user->districtAffiliation; 
				}
			?> 
			</span></div>
	<div class="info"><span class="label"></span><span class="label"><?php echo $user->buildingAssigned; ?></span></div>
	<div class="info"><span class="label"></span><span class="label"><?php echo $user->buildingAddress; ?></span></div>
	<div class="info"><span class="label"><?php echo $user->buildingCity; ?>, <?php echo $user->buildingState; ?> <?php echo $user->buildingZip; ?>
	</span></div>
	
	</div><!--section-info-->
</div><!--.section-->


<div class="employmentDetails">
	<h2  class="section-title">Teaching Discipline</h2>
	<div class="section-info">
	<div class="info"><span class="label"></span><span class="label">
	<?php 
		if($user->gradeSubject):
			echo get_single_value('tracks','trName','trID = '.$user->gradeSubject) ;
		else:
			echo 'Not Available';
		endif;
	?>
	
	</div>
	<div class="info"><span class="label">Grade Levels Taught:</span>
	<span class="label"><?php echo ($user->level)?$user->level:'Not Available'; ?></span></div>
	</div><!--section-info-->
</div><!--.section-->