<?php 
/**
@Page/Module Name/Class: 		ipad.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display static block content  in sidebar
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<?php if(isset($block_first) && !empty($block_first)): ?>
<div class="block">	
	<h3 class="block-title"><?php echo $block_first->cpTitle ?></h3>
	<div  class="sidebar-block block-contnet">
		<?php echo $block_first->cpDescription; ?>
	</div>

</div><!--.block-->
<?php endif; ?>
<?php if(isset($block_second) && !empty($block_second)): ?>
<div class="block">	
	<h3 class="block-title"><?php echo $block_second->cpTitle ?></h3>
	<div  class="sidebar-block block-contnet">
		<?php echo $block_second->cpDescription; ?>
	</div>

</div><!--.block-->
<?php endif; ?>