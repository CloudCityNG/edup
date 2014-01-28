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

<h1> Contact Details <?php echo $result->contFirstName; ?> <?php echo $result->contLastName; ?>( <?php echo$result->contEmail;  ?>) </h2>
<div class="top_links clearfix">
	 <?php echo anchor('edu_admin/contact/','Back','class="submit"'); ?> 
</div>

<div id="form" class="view">
		
	<div class="row clearfix">
	   <div class="left_area">ID </div>
	   <div class="right_area">
	   <?php echo $result->contID;?>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Name </div>
	   <div class="right_area">
	   <?php echo $result->contFirstName; ?> <?php echo $result->contLastName; ?>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Email</div>
	   <div class="right_area">
	  <?php echo $result->contEmail;  ?>
	   </div>
	</div>
	
	
	
	<div class="row clearfix">
	   <div class="left_area">Message</div>
	   <div class="right_area">
	  <?php echo $result->contMessage;  ?>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Contact Date</div>
	   <div class="right_area">
		<?php echo format_date($result->contDate,DATE_FORMAT.' '.TIME_FORMAT); ?>
	   </div>
	</div>
		
</div>
<?php else: ?>
<p class="no-record">No record found </p>
<?php endif; ?>