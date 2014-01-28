<?php 
/**
@Page/Module Name/Class: 		view.php
@Author Name:			 		ben binesh
@Date:					 		Oct, 04 2013
@Purpose:		        		display sinlge assignment details 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<?php $result=$result[0]; ?>
<div class="publicTitle"><h1><?php echo $result->assignTitle ?></h1></div>
<?php if($result->assignDueDate): ?>	
	<div class="left"> Due:<?php 
		$dueTime = $result->assignDueDate.' '.$result->assignDueTime; 
		echo isset($result->assignDueDate)?format_date($dueTime,'M d, Y h:i A'):$dueTime; ?> 
	</div>
<?php endif;  ?>		
<div class="right">
Pts:<?php echo $result->assignPoints; ?>
</div>
<div id="form">

	<?php if($result->assignActiveDate): ?>	
	<div class="row clearfix">
	   <div class="left_area">Assigned</div>
	   <div class="right_area">
	  <?php 
		if($result->assignActiveTime == '')
		{ 
			$result->assignActiveTime = '00:00:00';
		}
		$actTime = $result->assignActiveDate.' '.$result->assignActiveTime; 
		echo isset($result->assignActiveDate)?format_date($actTime,'M d, Y h:i A'):$actTime;
				?>
	   </div>
	</div>
	<?php endif;  ?>
	<?php if($result->assignTopic): ?>	
	<div class="row clearfix">
	   <div class="left_area">Description</div>
	   <div class="right_area">
	   <?php echo $result->assignTopic; ?> 
	   </div>
	</div>
	<?php endif;  ?>
		
	<?php if($result->assignQuestionnaire): ?>	
	<div class="row clearfix">
	   <div class="left_area">Questionnaire</div>
	   <div class="right_area">
	   <?php echo get_single_value('questionnaire_defs','qTitle','qID = '.$result->assignQuestionnaire) ?> 
	   </div>
	</div>
	<?php endif; ?>
	<?php if(''!=$result->assignLinkName && ''!= $result->assignLinkUrl):  ?> 
		<div class="row clearfix">
		<div class="left_area"><a href="<?php echo $result->assignLinkUrl; ?>" target="_blank"><?php echo $result->assignLinkName; ?></a></div>
		
	  
		</div>
	<?php endif; ?>
	


</div>