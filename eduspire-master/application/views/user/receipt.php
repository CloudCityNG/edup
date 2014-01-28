<?php 
/**
@Page/Module Name/Class: 		receipt.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display the transaction receipt
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="letterInnerTop">
<span class="site_address"><?php echo EDUSPIRE_ADDRESS ?></span> <span class="site_email"><?php echo EDUSPIRE_EMAIL ?></span>
</div>
<?php if(isset($result) && !empty($result)): ?>
	<div class="publicTitle">
		<h1>Receipt</h1>
	</div>
 	<div id="form" class="view">
		<div class="row clearfix">
		   <div class="left_area">Name </div>
		   <div class="right_area">
		   <?php echo $result->first_name.' '.$result->last_name;?>
		  <?php if($result->address_street):?>
			   <br/><?php echo $result->address_street;?><br/>
			   <?php echo $result->address_city;?>, 
			   <?php echo ' '.$result->address_state; ?>
			   <?php echo $result->address_zip; ?>
		   <?php endif; ?>
		   </div>
		</div>
		
		
		<div class="row clearfix">
		   <div class="left_area">Item Purchased</div>
		   <div class="right_area">
		   <?php echo $result->item_name1;?>
		   </div>
		</div>
	
		
		<?php if( PAYMENT_MODE_PAYPAL == $result->payment_mode ): ?>
			<div class="row clearfix">
			   <div class="left_area">
			   <?php echo ($result->txn_id)?'Transaction ID':'';?> 
			   <?php echo ('' != $result->manual_comment)?'Receipt Note':'';?> 
			   </div>
			   <div class="right_area">
			   <?php echo $result->txn_id;?>
			   <div class="hint"><?php echo nl2br($result->manual_comment);?></div>
			   </div>
			</div>

		<?php else: ?>
			<div class="row clearfix">
			   <div class="left_area">Payment Mode </div>
			   <div class="right_area">
			   <?php echo $this->checkout_model->show_payment_mode($result->payment_mode); ?>
			   
			   <div class="hint"><?php echo nl2br($result->manual_comment);?></div>
			   </div>
			</div>
	
		<?php endif; ?>	
		<div class="row clearfix">
		   <div class="left_area">Transaction Date </div>
		   <div class="right_area">
		   <?php echo format_date($result->payment_date,DATE_FORMAT);?>
		   </div>
		</div>
		
		<div class="row clearfix">
		   <div class="left_area">Amount</div>
		   <?php 
			
			$credit=1;
			if(isset($course) && !empty($course))
				if($course->cgCourseCredits)
					$credit=$course->cgCourseCredits;
		   ?>
		   <div class="right_area">
			<?php if($result->payment_gross):
				//change the price into the number format 
				$gross_amount=str_replace(',','',$result->payment_gross); ?>
				<?php echo CURRENCY.number_format($gross_amount,2);?>
				<?php if(PRODUCT_TYPE_COURSE==$result->product_type): ?>
				<div class="hint">Costs above are for tuition alone and do not include any other fees.<br/>
				<?php echo CURRENCY.number_format(round(($gross_amount)/$credit,2),2)?> per credit</div>
				<?php endif; ?>
			<?php endif; ?>
		   </div>
		   
		</div>
		
		<?php 
		//display the paid stamp
		if(PAYMENT_ENROLLED == $result->payment_status || PAYMENT_COMPLETED == $result->payment_status ): ?>
			<div class="row clearfix">
			   <div class="left_area">&nbsp;</div>
			   <div class="right_area">
			   <img src="<?php echo base_url()?>images/paidStamp.png"/>
			   </div>
			</div>
		<?php endif; ?>
		
	</div><!--#form-->
			
<?php else: ?>
	<p class="no_record">No receipts found</p>
<?php endif; ?>