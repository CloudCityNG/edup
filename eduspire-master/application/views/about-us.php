<?php 
/**
@Page/Module Name/Class: 		about-us.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display user profile for public view 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
Chronological Development
*****************************************************************
RF  Developer Name      Date            Description
**************************************************************************************** 
RF1 alan anil       16 Dec 2013     show popup on click on images and names of staff members.
RF2 ben binesh      30 Dec 2013     fix the empty image problem 
 */
?>
<script>
  jQuery(document).ready(function($) { 
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

<?php  if(isset($content->cpImage) && ''!=$content->cpImage):?>
<div class="bannerInner"><!--banner start here-->
    <ul class="bxslider">
    <li> <?php $img_path=  base_url().'uploads/pages/'.$content->cpImage; ?>
				<img src="<?php echo $img_path;?>" ></li>
    </ul>
</div>
<?php endif; ?>
<div class="aboutus">

<div class="section group">
    <div class="col span_2_of_4">
		<?php if(isset($content->cpDescription))
			echo $content->cpDescription
			
	?>
		 
    </div>
    <div class="col span_2_of_4 fRight">
		<?php if(isset($content_second) && !empty($content_second)): ?>
		<h2><?php echo $content_second->cpTitle; ?></h2>
		<?php echo $content_second->cpDescription; ?>
		<?php endif; ?>
    </div>
</div>
		<?php if(isset($users) && !empty($users)){ $tony=''; $nathan=''; ?>
    	<?php foreach($users  as $user){
			if(INSTRUCTOR !=$user->accessLevel){
				
				if(2==$user->id){
					$nathan=$user;
				}	
				
				
				if(563==$user->id){
					$tony=$user;
				}	
				
			}
		}
		}	
		?>
		
 
 <div class="staffMember"><!--staff member div start-->
  	<?php if(isset($users) && !empty($users)): ?>
	<h2 class="sub-heading">Meet Our Staff</h2>
	<ul>
    	<?php foreach($users  as $user):
			if(INSTRUCTOR !=$user->accessLevel):
		?>
		<?php if(!empty($nathan)): ?>
		<li>
			<p><?php //Rf1
					$profile_image=( '' != $nathan->profileImage)?$nathan->profileImage:'default.jpg';
					//Rf2
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
				<a href="<?php echo base_url(); ?>userprofile/instructorbio/<?php echo $nathan->id; ?>"  class="fancybox">
					<img src="<?php echo crop_image($profile_image); ?>" title="<?php echo $nathan->firstName.' '.$nathan->lastName; ?>" 
					alt="<?php echo $nathan->firstName.' '.$nathan->lastName; ?>"/>
					<p><?php echo $nathan->firstName.' '.$nathan->lastName; ?></p>
				</a>  <!--show popup on click on images and names of staff members end.-->
			</p>
			</li>
		<?php unset($nathan); ?>
		<?php endif; ?>	
		<?php if(!empty($tony)): ?>
		<li>
			<p><?php //Rf1
					$profile_image=( '' != $tony->profileImage)?$tony->profileImage:'default.jpg';
					//Rf2
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
				<a href="<?php echo base_url(); ?>userprofile/instructorbio/<?php echo $tony->id; ?>"  class="fancybox">
					<img src="<?php echo crop_image($profile_image); ?>" title="<?php echo $tony->firstName.' '.$tony->lastName; ?>" 
					alt="<?php echo $tony->firstName.' '.$tony->lastName; ?>"/>
					<p><?php echo $tony->firstName.' '.$tony->lastName; ?></p>
				</a>  <!--show popup on click on images and names of staff members end.-->
			</p>
			</li>
		<?php unset($tony); ?>
		<?php endif; ?>	
		<?php if(2 != $user->id && 563!=$user->id): ?>
			<li>
			<p><?php //Rf1
					$profile_image=( '' != $user->profileImage)?$user->profileImage:'default.jpg';
					//Rf2
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
				<a href="<?php echo base_url(); ?>userprofile/instructorbio/<?php echo $user->id; ?>"  class="fancybox">
					<img src="<?php echo crop_image($profile_image); ?>" title="<?php echo $user->firstName.' '.$user->lastName; ?>" 
					alt="<?php echo $user->firstName.' '.$user->lastName; ?>"/>
					<p><?php echo $user->firstName.' '.$user->lastName; ?></p>
				</a>  <!--show popup on click on images and names of staff members end.-->
			</p>
			</li>
		<?php endif; ?>
		<?php endif; ?>
		<?php endforeach; ?>
    </ul>
	<?php endif; ?>
  </div> 
  
<div class="meetInstructer"><!--Instructor member div start-->
  	<?php if(isset($users) && !empty($users)): ?>
	<h2 class="sub-heading">Meet Our Instructors</h2>
	<ul>
    	<?php foreach($users  as $user):
			if(INSTRUCTOR ==$user->accessLevel):
		?>
		<li>
        	<p><?php 
				//Rf1
				$profile_image=( '' != $user->profileImage)?$user->profileImage:'default.jpg';
				//Rf2
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
            <a href="<?php echo base_url(); ?>userprofile/instructorbio/<?php echo $user->id; ?>"  class="fancybox">
            <img src="<?php echo crop_image($profile_image); ?>" title="<?php echo $user->firstName.' '.$user->lastName; ?>" alt="<?php echo $user->firstName.' '.$user->lastName; ?>"/>
            <p><?php echo $user->firstName.' '.$user->lastName; ?></p>
            </a>
            </p> <!--show popup on click on images and names of Instructor members end.-->
        </li>
		<?php endif; ?>
		<?php endforeach; ?>
    </ul>
	<?php endif; ?>
  </div>
	
  <div class="aboutusbtmContent">
      
      <?php 
              //Display testimonial editable from admin 
               get_text(5); 
              //End  testimonial?>
  </div>
</div>