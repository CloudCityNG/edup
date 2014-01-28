<?php 
/**
@Page/Module Name/Class: 		cart.php
@Author Name:			 		Ben binesh
@Date:					 		Sept, 25 2013
@Purpose:		        		display the ipad cart 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
?>

<?php if(isset($content) && !empty($content) ): ?>
<div class="publicTitle">	<h1><?php echo $content->cpTitle; ?></h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<?php endif; ?>
<?php echo $content->cpDescription;?>

 
<div id="form" class="listBody">

<?php if(isset($cart) && !empty($cart)): ?>
<?php 
	$total=0;
?>
<form action="" method="post">	
<table cellspacing="0" id="grid" class="table striped" width="100%">
    <thead>
        <tr class="colHdrs">
            <th class="colHdrs">Item</th>
            <th class="colHdrs">Options</th>
            <th class="colHdrs">Price</th> 
            
        </tr>
    </thead>
    <tbody>
		<?php foreach($cart as $item): ?>
			 <tr>
				<td nowrap class="odd">
				  <?php echo $item['product_name'] ?>
				</td>
				<td nowrap class="odd">  
					 <?php echo $item['option'] ?>
					
					
				</td>
				<td nowrap class="odd">
				 <?php 
				 $total=$total+$item['price'];
				 echo CURRENCY.$item['price'] ?>
				 
				</td>
				
			</tr>
		
		
        <?php endforeach; ?>
		<tr><td></td><td></td><td><b> Total:<?php echo CURRENCY.$total; ?></b></td></tr>
		
    </tbody>
</table>
 <div><input type="submit" name="checkout"  class="submit" value="Checkout">&nbsp;&nbsp;<?php echo anchor('/ipad/cancel/','Cancel','class="submit"'); ?></div>
</form>
<?php else: ?>
<p class="no-record">Cart is empty </p>
<?php endif; ?>

</div> 
