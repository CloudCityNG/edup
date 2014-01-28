<?php 
/**
@Page/Module Name/Class: 	    view.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display single newsletter subscription detials  
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?> 
<?php if(isset($result) && !empty($result)): ?>

<div class="adminTitle"><h1> Newsletter subscription Details of  <?php echo $result->newsFirstName; ?> <?php echo $result->newsLastName; ?>( <?php echo $result->newsEmail;  ?>) </h1></div>
<div class="backButton">
	 <?php echo anchor('edu_admin/newsletter/','Back','class="submit"'); ?> 
</div>

<div class="view">
	<ul class="details">
    	<li>ID</li>
        <li><?php echo $result->newsID;?></li>
        <li>Name</li>
        <li><?php echo $result->newsFirstName; ?> <?php echo $result->newsLastName; ?></li>
        <li>Email</li>
        <li><?php echo $result->newsEmail;  ?></li>
        <li>School District</li>
        <li>
		<?php
				if(is_numeric($result->newsSchoolDistrict)){
					echo get_single_value('district','disName','disID = '.$result->newsSchoolDistrict) ;
				}else{
					 echo $result->newsSchoolDistrict; 
				}
			?> 
		</li>
        <li>Teaches Subject</li>
        <li><?php echo $result->newsTeachesSubject;  ?></li>
		 <li>Grade level</li>
        <li><?php echo $result->newsGradeLevel;  ?></li>
        <li>IU</li>
        <li><?php echo $result->newsIU;  ?></li>
        <li>Hear About eduspire By</li>
        <li> <?php 
			if(4 == $result->newsReferralMethod){
				echo $result->newsReferralMethodOther;
			}else{
				 echo show_hearabout_text($result->newsReferralMethod);
			}		
	  		?>
      </li>
      <li>Contact Date</li>
      <li><?php echo format_date($result->newsSignupDate,DATE_FORMAT.' '.TIME_FORMAT); ?></li>
    </ul>
    <?php else: ?>
<p class="no-record">No record found </p>
<?php endif; ?>
 
</div>
