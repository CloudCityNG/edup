<?php 
/**
@Page/Module Name/Class: 		comment.php
@Author Name:			 		Alan Anil
@Date:					 		Dec 18, 2013
@Purpose:		        		display grade comment.
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
?> 
<h2>Comments:-</h2>
<div> 
	<?php 
	 if(isset($getAssignPointsDetails['alCommentStudent']) && $getAssignPointsDetails['alCommentStudent'] != '') { 
	    echo $getAssignPointsDetails['alCommentStudent']; 
	 } ?>
</div>
<div class="publicTitle"> </div>
