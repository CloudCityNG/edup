<?php 
/**
@Page/Module Name/Class: 		receipts .php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display user's transaction receipts 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<h1>My receipts</h1>
	<?php if(isset($results) && count($results)>0): ?>
			<form method="post" name="grid-form">
			<input type="hidden" name="mass_action"  value="1"/>
			<table class="table striped" cellspacing="0">
			<tr>
				<td colspan="7" class="summary">
				<?php echo $pagination_links; ?>
				<div class="pagnination_summary">
					<?php echo pagination_summary();?>
				</div>
				</td>
			</tr>	
			<tr>
				<th>Date / Transaction ID / Invoice</th>
				<th width="300">Item</th>
				<th>Amount</th>
				<th>Status</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td>
			<?php echo $result->payment_date; ?><br/>
			<?php echo $result->txn_id; ?><br/>
			<?php echo $result->ppID; ?>
			</td>
			<td><a href="<?php echo base_url().'user/view_receipt/'.$result->ppID; ?>"><?php echo $result->item_name1; ?></a></td>
			<td><?php echo $result->payment_gross; ?></td>
			<td><?php echo $result->payment_status; ?></td>
			</tr>
			<?php endforeach; ?>
			<tr>
				<td colspan="7" class="summary">
				<?php echo $pagination_links; ?>
				<div class="pagnination_summary">
					<?php echo pagination_summary();?>
				</div>
				</td>
			</tr>		
			
			</table>
			</form>	
		
	<?php else: ?>
		<p class="no_record">No receipts found</p>
	<?php endif; ?>