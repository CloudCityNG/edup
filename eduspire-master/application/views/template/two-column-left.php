<?php 
/**
@Page/Module Name/Class: 		two-column-left.tphp
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display data with in right sideber column
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="section group">
	<div class="col span_1_of_4" id="single-course">
			<?php $this->load->view('sidebar/'.$sidebar); ?>
	</div>
	<div class="col span_3_of_4">
			<?php $this->load->view($main); ?>
	</div>
	
</div><!--.two-column-left-->


