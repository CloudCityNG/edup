<?php 
/**
@Page/Module Name/Class: 		instructor.php
@Author Name:			 		Alan Anil
@Date:					 		Dec 11, 2013
@Purpose:		        		display user personal info
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
Chronological Development
*****************************************************************
RF  Developer Name      Date            Description
**************************************************************************************** 
RF1 ben binesh      30 Dec 2013     fix the empty image problem 
*/
?> 
<div id="popupForm">
<h2><?php  echo $user->firstName.' '.$user->lastName; ?></h2>
    <div>
        <div>
            <div class="profileImage">
                <p><?php 
                $profile_image=( '' != $user->profileImage)?$user->profileImage:'default.jpg';
				//Rf1
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
                 <img src="<?php echo crop_image($profile_image); ?>" 
                title="<?php echo $user->firstName.' '.$user->lastName; ?>" 
                alt="<?php echo $user->firstName.' '.$user->lastName; ?>" width="100px" height="100px"/> </p>
            </div>
            <div class="info"><span class="value"> 
				<?php 
                if(is_numeric($user->districtAffiliation)){
					echo get_single_value('district','disName','disID = '.$user->districtAffiliation) ;
				}else{
					 echo $user->districtAffiliation; 
				} ?>
            </div>
            <div class="info"><span class="value">
				<?php 
                if($user->gradeSubject):
                    echo get_single_value('tracks','trName','trID = '.$user->gradeSubject) ;
                endif;	
                ?> </span>
            </div> 
            <div>
           	 <?php echo nl2br($user->usrBio); ?>
        	</div>
        </div> 
      	<br />
        <div>  
            <?php if(isset($courses) && !empty($courses)): ?> <h3>Courses:</h3> 
				<?php  
					  $coursIdVal = array();	
					  foreach($courses as $cor):  
					   if (in_array($cor->csGenreId, $coursIdVal))  { }
					   else {
					  ?>
                <div> 
                  <a href="<?php echo get_seo_url('course',$cor->csGenreId,$cor->cdCourseTitle) ?>" target="_blank" 
                  title="<?php echo $cor->cdCourseTitle;  ?>"><?php echo  $cor->cdCourseID; ?>:<?php echo $cor->cdCourseTitle;  ?></a>
                </div>	 
                <?php 
					   $coursIdVal[] = $cor->csGenreId;
					   }
					endforeach; ?>
            <?php endif; ?>
            
        </div>
       
        <div class="popup_meetinstructor">
            <?php if(isset($testimonial) && !empty($testimonial)): ?>
			<br />
            <h3>Testimonials:</h3>

               <?php foreach($testimonial as $tests): ?>
                <div> 
					<h4>
                    <?php if($tests->tTestimonial != '') echo '"'.$tests->tTestimonial.'"'; ?> 
					</h4>
                </div>	 
                <?php endforeach; ?>
            <?php endif; ?> 
        </div>
    </div> 
</div>
 