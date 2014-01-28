<?php 
// ********************************************************************************************************************************
//Page name			:- 			usergradebook.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			File used to show enrolled user grade details.  
//Date				:- 			05-09-2013
//Table Refered		:-  		N/A
//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  

//---------------------------------------------------------------------------------------- 
?>
<!--Show user profile image, type, role, district, email.-->
<div class="profile-image">
	<?php  
		$userTrack       = $this->assignment_model->get_track_user($assignDetails[0]->gradeSubject);
		$userId          = $getUserPointsDetails['alUserID'];
		$user            = $this->user_model->get_single_record($userId,'*',true);
		$profile_image=( '' != $user->profileImage)?$user->profileImage:'default.jpg';
		$profile_image=base_url().'uploads/users/'.$profile_image;
	?>
	<img src="<?php echo crop_image($profile_image); ?>" title="<?php echo $this->session->userdata('display_name'); ?>" 
    alt="<?php echo $this->session->userdata('display_name'); ?>"/>
</div>

<h3><?php echo $assignDetails[0]->firstName.'  '.$assignDetails[0]->lastName; ?></h3>
<h4><?php echo "Track: ".$userTrack; ?> </h4>
<h4><?php echo "Role: ".$assignDetails[0]->role;  ?> </h4>	
<h4><?php echo "District: ".$assignDetails[0]->districtAffiliation; ?> </h4> 
<h4><?php echo "Email:".'<a href="mailto:'.$assignDetails[0]->email.'">'.$assignDetails[0]->email.'</a>'; ?> </h4> 
<!--Show points earned by user and user grade.	-->	
<div>
    <h3>Points Earned</h3>
    <?php 
    $percentage  = $this->assignment_model->percentage($getUserPointsDetails['pointsGot'],$getUserPointsDetails['totalPoints'],0); 
    echo $getUserPointsDetails['pointsGot'].' / '.$getUserPointsDetails['totalPoints'].
          ' ( '.$percentage.' ) '; ?> 
</div>
