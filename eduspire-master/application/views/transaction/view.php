<?php 
/**
@Page/Module Name/Class: 	    view.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display single transaction details
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?> 
<?php if(isset($result) && !empty($result)): ?>
<h1> Transaction Details #<?php echo $result->ppID; ?></h1>
<div class="top_links clearfix">
	 <?php echo anchor('edu_admin/newsletter/','Back','class="submit"'); ?> 
</div>

<div id="form" class="view">
	
	<div class="row clearfix">
	   <div class="left_area">Name </div>
	   <div class="right_area">
	   <?php echo $result->first_name; ?> <?php echo $result->last_name; ?>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Email</div>
	   <div class="right_area">
	  <?php echo $result->payer_email;  ?>
	   </div>
	</div>
	
	
	<div class="row clearfix">
	   <div class="left_area">Address</div>
	   <div class="right_area">
		<?php echo $result->address_street; ?><br/>
				<?php echo $result->address_city ?>, <?php echo $result->address_state ?> <?php echo $result->address_zip ?><br/>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Transaction ID </div>
	   <div class="right_area">
	  <?php echo $result->txn_id;  ?>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Amount</div>
	   <div class="right_area">
	  <?php echo ($result->payment_gross)?CURRENCY.$result->payment_gross:''; ?>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Date</div>
	   <div class="right_area">
	  <?php echo format_date($result->payment_date,DATE_FORMAT); ?>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Product Name</div>
	   <div class="right_area">
	  <?php echo $result->item_name1; ?>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Payment Mode</div>
	   <div class="right_area">
	  <?php echo $this->checkout_model->show_payment_mode($result->payment_mode); ?>
	   </div>
	</div>
	
	<?php if($result->check_number): ?>
	<div class="row clearfix">
	   <div class="left_area">Notes for receipt</div>
	   <div class="right_area">
	  <?php echo $result->check_number; ?>
	   </div>
	</div>
	<?php endif;?>
	
	<div class="row clearfix">
	   <div class="left_area">Notes for self</div>
	   <div class="right_area">
	  <?php echo $result->manual_comment_self; ?>
	   </div>
	</div>
	
		
</div>
<?php else: ?>
<p class="no-record">No record found </p>
<?php endif; ?>