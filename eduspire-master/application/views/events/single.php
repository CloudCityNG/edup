<?php 
/**
@Page/Module Name/Class: 		single.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display the single event data 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:'News'; ?></h1></div>
<?php if(isset($event)&& !(empty($event))): ?>
<div class="newsEvent">
	<div class="posted-date">Posted Date: <?php echo format_date($event->nwDate,DATE_FORMAT); ?></div>
	<div class="entry-content"><?php echo $event->nwDescription; ?></div>
	
</div>
<?php endif; ?>

