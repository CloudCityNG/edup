<?php 
/**
@Page/Module Name/Class: 		        two-column-right-samll.tphp
@Author Name:			 		Janet Rajani
@Date:					 	Nov, 26 2013
@Purpose:		        		display data with in right small sidebar
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="col span_4_of_4">
        <div class="col span_3_of_4">
		<?php $this->load->view($main); ?>
	</div>
	<div class="col span_1_of_4">
		<?php 
			if(isset($sidebar)){
				$this->load->view('sidebar/'.$sidebar); 
			}
		?>
	</div>
	
	

</div><!--.span_4_of_4-->