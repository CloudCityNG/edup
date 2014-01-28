<?php 
/**
@Page/Module Name/Class: 	    view.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display sing euser details 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="adminTitle"><h1>View  <?php echo $user->lastName.' '.$user->firstName; ?>( <?php echo $user->email; ?>) </h2></div>
<div class="backButton clearfix">
	 <?php echo anchor('edu_admin/user/','Back','class="submit"'); ?> 
	 <?php echo anchor('edu_admin/user/update/'.$user->id,'Edit user','class="submit"'); ?> 
</div>

<div id="form" class="createUser">
	
	<form class="form" action="" method="post" enctype="multipart/form-data" >
		<ul class="updateForm brdr_btm">
			<h2>Login Info</h2>
			<li>
			  <label>Username</label>
			   <div class="formRight">
			    <?php echo $user->userName; ?> 
				</div>
			</li>
		
			<li>
			  <label>Email</label>
			   <div class="formRight">
			   <?php echo $user->email; ?> 
			   </div>
			</li>
			
			<li>
				 <label>Access Level</label>
				<div class="formRight">
					<?php echo $this->user_model->show_access_level($user->accessLevel); ?>
				</div>
			</li>
			<li>
		  <label>Account Status</label>
		   <div class="formRight">
			<?php echo $this->user_model->show_status($user->activationFlag); ?>
		   </div>
		</li>
		</ul>	
		
		<ul class="updateForm brdr_btm">
			<h2>General Info</h2>
			<li>
			  <label>First Name </label>
			   <div class="formRight">
			  <?php echo $user->firstName;?>
			   </div>
			</li>
		
			<li>
			  <label>Last Name</label>
			   <div class="formRight">
			   <?php echo $user->lastName;?>
			   </div>
			</li>
			
			<li>
			  <label>Gender</label>
			   <div class="formRight">
				 <?php echo $user->gender;?>
				
			   </div>
			</li>
			
			<?php if(isset($user->profileImage) && $user->profileImage != ''): ?>
			<li>
			  <label>Uploaded Image </label>
			   <div class="formRight">
				<?php $img_path=  base_url().'uploads/users/'.$user->profileImage; ?>
				<img src="<?php echo crop_image($img_path);?>" height="100" width="100"/>
				</div>
			</li>
			<?php endif; ?>
		
			
			<li>
				<div class="left_area">Birth Date </label>
				
				<div class="formRight">
				<?php echo format_date($user->birthDate,DATE_FORMAT); ?>
				</div>
			</li>
			
			<li>
				<div class="left_area">Secondary email	</label>
				<div class="formRight">
				<?php echo $user->email2;?>
				</div>
			</li>
			
			<li>
			  <label>Receive System Emails</label>
			   <div class="formRight">
				<?php echo show_yesNo_text($user->receiveSystemEmails);?>
			   </div>
			</li>
		
		
		</ul>
		
		
		<ul class="updateForm brdr_btm">
			<h2>Profile Info</h2>
			<li>
			  <label>User Bio</label>
			   <div class="formRight">
				<?php echo $user->usrBio;?>
			   </div>
			</li>
		
			
			
			<li>
			  <label>Address</label>
			   <div class="formRight">
				<?php echo $user->address;?>
			   </div>
			</li>
			
			<li>
			  <label>City</label>
			   <div class="formRight">
			   <?php echo $user->city;?>
			   </div>
			</li>
			
			<li>
			  <label>State</label>
			   <div class="formRight">
			   <?php echo  $user->state; ?>
			   </div>
			</li>
			
			<li>
			  <label>Zip</label>
			   <div class="formRight">
			   <?php echo $user->zip; ?>
			   </div>
			</li>
			
			
			<li>
			  <label>Phone</label>
			   <div class="formRight">
			 <?php echo $user->phone;?>
			   </div>
			</li>
			<li>
			  <label>Cell Phone</label>
			   <div class="formRight">
			  <?php echo $user->mobileCarrier;?>
			   </div>
			</li>
			
		</ul>	
			
			
			
			
		<ul class="updateForm brdr_btm">	
			<h2>Web Presence/Social Networking</h2>	
			<li>
			  <label>Twitter ID</label>
			   <div class="formRight">
			  <?php echo  $user->twitter;?>
			   </div>
			</li>
			
			<li>
			  <label>AIM ID</label>
			   <div class="formRight">
			   <?php echo $user->aim ?>
			   </div>
			</li>
			
			<li>
			  <label>MSN/Windows Live ID</label>
			   <div class="formRight">
			   <?php echo $user->msn;?>
			   </div>
			</li>
			
			<li>
			  <label>Facebook</label>
			   <div class="formRight">
			   <?php echo $user->facebook;?>
			   </div>
			</li>
			
			
			
			<li>
			  <label>Site URL</label>
			   <div class="formRight">
			  <?php echo $user->siteURL ;?>
			   </div>
			</li>
		</ul>	
		<ul class="updateForm brdr_btm">
			<h2>Employment Info</h2>
			
			<li>
			  <label>IU</label>
			   <div class="formRight">
			   <?php
				if($user->iuID){
					echo  get_single_value('iu_unit','iuName','iuID = '.$user->iuID) ;;
				}
			?> 
			   </div>
			</li>
			
			<li>
			  <label>District Affiliation(OLD)</label>
			   <div class="formRight">
			   <?php
				if(is_numeric($user->districtAffiliation)){
					echo get_single_value('district','disName','disID = '.$user->districtAffiliation) ;
				}else{
					 echo $user->districtAffiliation; 
				}
			?> 
			   </div>
			</li>
			
			<li>
			  <label>District Affiliation(New)</label>
			   <div class="formRight">
			   <?php
				if($user->districtID){
					echo get_single_value('district','disName','disID = '.$user->districtID) ;
				}
			?> 
			   </div>
			</li>
			
			
			
			<li>
			  <label>Building Assigned</label>
			   <div class="formRight">
			   <?php echo $user->buildingAssigned;?>
			   </div>
			</li>
			
			<li>
			  <label>Building Address</label>
			   <div class="formRight">
				<?php echo  $user->buildingAddress;?>
			   </div>
			</li>	
			
			<li>
			  <label>Building City	</label>
			   <div class="formRight">
				 <?php echo $user->buildingCity;?>
			   </div>
			</li>
			<li>
			  <label>Building State</label>
			   <div class="formRight">
				 <?php echo $user->buildingState;?>
			   </div>
			</li>
			
			<li>
			  <label>Building Zip</label>
			   <div class="formRight">
				 <?php echo $user->buildingZip;?>
			   </div>
			</li>
			
			<li>
			  <label>Role</label>
			   <div class="formRight">
			   <?php echo $user->role;?>
	
				
				
			</div>	
			</li>
			
			<li>
			  <label>Years in this role</label>
			   <div class="formRight">
				 <?php echo $user->yearsActive;?>
			   </div>
			</li>
			
			
			<li>
			  <label>How many grad courses are you likely to take within the next two school years</label>
			   <div class="formRight">
				 <?php echo $user->gradCoursesTaking ;?>
			   </div>
			</li>
			</ul>		
			<ul class="updateForm brdr_btm">
			<h2>Subject Area</h2>	
				<li>
				  <label>Track </label>
				   <div class="formRight">
					<?php if($user->gradeSubject):
							echo get_single_value('tracks','trName','trID = '.$user->gradeSubject) ;
						else:
							echo 'Not Available';
						endif;
					?>
					
					</div>
				</li> 
				
				
				<li>
				  <label>Grade Levels</label>
				   <div class="formRight">
					<?php echo $user->level;?> 
					</div>
				</li> 
			
			</ul>
		
		</form>
	
</div>