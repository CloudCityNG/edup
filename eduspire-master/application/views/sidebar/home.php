<?php 
/**
@Page/Module Name/Class: 		home.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display static block content  in sidebar
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>

<?php if(isset($sidebar_data) && !(empty($sidebar_data))): ?>
<div class="block">
	<?php foreach($sidebar_data as $s_data): ?>
	<div id="available-courses" class="block-content">	
	<?php echo $s_data->cpDescription; ?>
	</div><!--.block-content-->
	<?php endforeach; ?>
		
</div><!--.block-->
<?php endif; ?>