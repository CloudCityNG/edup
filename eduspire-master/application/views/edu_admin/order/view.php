<?php 
/**
@Page/Module Name/Class: 	    view.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display order details 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?> 
<?php if(isset($result) && !empty($result)): ?>

<div class="adminTitle"><h1> View Order# <?php echo $result->orderID; ?></h1></div>
<div class="top_links clearfix">
	 <?php echo anchor('edu_admin/order/','Back','class="submit"'); ?> 
</div>
<br>
<div id="form" class="view">
		
	<div class="row clearfix">
	   <div class="left_area">ID </div>
	   <div class="right_area">
	   <?php echo $result->orderID;?>
	   </div>
	</div>
	<div class="row clearfix">
	   <div class="left_area">Order Number </div>
	   <div class="right_area">
	   <?php echo $result->orderNumber;?>
	   </div>
	</div>
	
	
	
	<div class="row clearfix">
	   <div class="left_area">Order Date/Time </div>
	   <div class="right_area">
	   <?php echo $result->orderDate.'/'.$result->orderTime;?>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Name </div>
	   <div class="right_area">
	   <?php echo $result->orderName; ?>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Email</div>
	   <div class="right_area">
	  <?php echo $result->orderEmail;  ?>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Product</div>
	   <div class="right_area">
		<?php 
				echo  $result->oiProdName.'('.$result->oiProdVariantValue1.')';
				if($result->upgrade_info){
					echo '-'.$result->upgrade_info;
				}
				
			?>
	   </div>
	</div>
	
	
	<div class="row clearfix">
	   <div class="left_area">Product Price </div>
	   <div class="right_area">
	  <?php echo ($result->upgrade_price)?CURRENCY.$result->upgrade_price:''; ?>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Order Status  </div>
	   <div class="right_area">
	 <?php echo $result->orderStatus; ?>
	   </div>
	</div>
	
	<div class="row clearfix">
	   <div class="left_area">Telephone</div>
	   <div class="right_area">
	  <?php echo $result->orderTelephone;  ?>
	   </div>
	</div>
	<div class="row clearfix">
	   <div class="left_area">Address</div>
	   <div class="right_area">
	  <?php echo $result->orderCCStreet;  ?><br/>
	  <?php echo $result->orderCCCity;  ?>,<?php echo $result->orderCCState ?> <?php echo $result->orderCCZIP ?>
	  </div>
	</div>
	
		
</div>
<?php else: ?>
<p class="no-record">No record found </p>
<?php endif; ?>