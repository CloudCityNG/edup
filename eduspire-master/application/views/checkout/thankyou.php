<?php
/**
@Page/Module Name/Class:                        checkout.php
@Author Name:			 		Janet Rajani
@Date:					 	Dec, 02 2013
@Purpose:		        		Display thanks message after payment done
*/?>
<?php if($content): ?>
<div class="publicTitle"><h1><?php echo $content->cpTitle; ?></h1></div>
<div class="page-content">
		<?php echo $content->cpDescription; ?>
</div>
	
<?php endif; ?>