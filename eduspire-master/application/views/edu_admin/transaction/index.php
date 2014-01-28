<?php 
/**
@Page/Module Name/Class: 	    index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display transaction list  
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?> 
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
<h3>Filters</h3>
	<form name="search-form" action="<?php echo base_url().'edu_admin/transaction/index' ?>">
    	<ul class="manageContent1">
        	<li><label>Name</label><input type="text" name="name" value="<?php echo $name; ?>"/></li>
            <li><label>Email</label><input type="text" name="email" value="<?php echo $email; ?>"/></li>
            <li><label>Txn Id </label><input type="text" name="txn_id" value="<?php echo $txn_id; ?>"/></li>
			<!--<li><label>Payment Date</label><input type="text" name="payment_date" value="<?php echo $payment_date; ?>"/></li>-->
            <li><label>Status</label><?php echo form_dropdown('status',$this->checkout_model->get_status_array(true,array(''=>'')),$status); ?></li>
        </ul>
		<div class="formButton">
			<input type="submit" class="submit" value="Search"/>
			<?php echo anchor('edu_admin/transaction/','Reset','class="submit"'); ?> 
			</div>

		<!--<div class="row clearfix"><input type="submit" name="Export to CSV" value="" class="submit tooltip" title="Reports are also filtered" value="Export"  /> 
			</div>-->
				
	</form>
</div>
<div class="result_container">
		<?php if(isset($results) && count($results)>0): ?>
			<form method="post" name="grid-form">
			<input type="hidden" name="mass_action"  value="1"/>
			<table class="table striped" cellspacing="0" cellpadding="0" id="grid" width="100%">
						<tr>
				<th>Date / Transaction ID / Invoice</th>
				<th width="200">Name Address</th>
				<th width="300">item</th>
				<th>Amount</th>
				<th>Status</th>
				<th>&nbsp;</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td>
			<?php echo format_date($result->payment_date,DATE_FORMAT); ?><br/>
			<?php echo $result->txn_id; ?><br/>
			<?php echo $result->ppID; ?>
			</td>
			<td>
				<b><?php echo $result->last_name.' '.$result->first_name; ?></b><br/>
				<?php if($result->address_street): ?>
				<?php echo $result->address_street; ?><br/>
				<?php echo $result->address_city ?>, <?php echo $result->address_state ?> <?php echo $result->address_zip ?><br/>
				<?php endif; ?>
				<?php echo $result->payer_email; ?>
			</td>
			<td><?php echo $result->item_name1; ?></td>
			<td><?php echo ($result->payment_gross)?CURRENCY.$result->payment_gross:''; ?></td>
			<td><?php echo ($result->payment_status)?$result->payment_status:PAYMENT_REVERSED; ?></td>
			<td>
			<?php echo anchor('edu_admin/transaction/view/'.$result->ppID,'<img src="/images/view.png" title="view" alt="view"/>'); ?>
			</td> 
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
			<p class="no_recored_fount">No record found</p>
		<?php endif; ?>
		
</div>
