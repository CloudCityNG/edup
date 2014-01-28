<?php 
/**
@Page/Module Name/Class: 		view.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display single contact details
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<?php if(isset($result) && !empty($result)): ?>

<div class="adminTitle"><h1> Contact Details <?php echo $result->contFirstName; ?> <?php echo $result->contLastName; ?>( <?php echo$result->contEmail;  ?>) </h1></div>
<div class="backButton clearfix">
	 <?php echo anchor('edu_admin/contact/','Back','class="submit"'); ?> 
</div>

<div id="form" class="view">
		
        <ul class="updateForm">
          <li>
	   <label>ID </label>
	   <div class="formRight">
	   <?php echo $result->contID;?>
	   </div>
	</li>
	
	<li>
	   <label>Name </label>
	   <div class="formRight">
	   <?php echo $result->contFirstName; ?> <?php echo $result->contLastName; ?>
	   </div>
	</li>
	
	<li>
	   <label>Email</label>
	   <div class="formRight">
	  <?php echo $result->contEmail;  ?>
	   </div>
	</li>
	
	<li>
	   <label>Message</label>
	   <div class="formRight">
	  <?php echo $result->contMessage;  ?>
	   </div>
	</li>
	
	<li>
	   <label>Contact Date</label>
	   <div class="formRight">
		<?php echo format_date($result->contDate,DATE_FORMAT.' '.TIME_FORMAT); ?>
	   </div>
	</li>
    </ul>	
</div>
<?php else: ?>
<p class="no-record">No record found </p>
<?php endif; ?>