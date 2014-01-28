<?php 
/**
@Page/Module Name/Class: 		two-column-right.tphp
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display data with in left sideber column
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="two-column-right" class="clearfix">
	<div class="col span_2_of_3">
		<?php $this->load->view($main); ?>
	</div>
	<div class="col span_1_of_3">
		<?php 
			if(isset($sidebar)){
				$this->load->view('sidebar/'.$sidebar); 
			}
		?>
	</div>
	
	

</div><!--.two-column-right-->


