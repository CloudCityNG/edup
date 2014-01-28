<?php 
/**
@Page/Module Name/Class: 	    index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display order list 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?> 

<!--<h1>
<?php if(isset($course) && !empty($course)):?>
Orders :<?php echo $course->cdCourseTitle.'('.$course->csLocation.')'.format_date($course->csStartDate,DATE_FORMAT);?>
<?php  else: ?>
Order Management
<?php endif; ?>
</h1>-->
<p>Use this tool to delete and reset errant (incomplete) iPad orders. Use caution as deletions are irreversible.</p>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
	<h3>Filters</h3>
	<form name="search-form" action="<?php echo base_url().'edu_admin/order/index' ?>">
    	<ul class="manageContent1">
        	<li><label>Name</label><input type="text" name="name" value="<?php echo $name; ?>"/></li>
            <li><label>Email</label><input type="text" name="email" value="<?php echo $email; ?>"/></li>
            <li><label>Order Date</label><input type="text" name="order_date" value="<?php echo $order_date; ?>"/></li>
        </ul>
		
		<?php /*
		<div class="col"><label>Status</label><?php echo form_dropdown('status',$this->order_model->get_status_array(true,array(''=>'')),$status); ?>
		</div>
		*/?>
		
			<div class="formButton">
			<input type="submit" class="submit" value="Search"/>
			<?php echo anchor('edu_admin/order/','Reset','class="submit"'); ?> 
			</div>
		<ul class="manageContent">
        	<li><label>CSV</label><?php echo form_dropdown('export_type',get_report_type_array(),$this->input->get('export_type')); ?></li>
        </ul>
			<div class="formButton">
				<input type="submit" name="export" class="submit tooltip" title="Reports are also filtered" value="Export"  /> 
			</div>
		
	</form>
	</div>

<div class="result_container">
		<?php if(isset($results) && count($results)>0): ?>
			
			<form method="post" name="grid-form">
			<input type="hidden" name="mass_action"  value="1"/>
			<table class="table striped" cellspacing="0" cellpadding="0" width="100%" id="grid">
			<tr>
				<th>Date / Time</th>
				<th width="100">Order Number</th>
				<th width="200">Name</th>
				<th width="200">Product</th>
				<th>Cost</th>
				<th>Action</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td>
			<?php echo $result->orderDate; ?><br/>
			<?php echo $result->orderTime; ?>
			</td>
			<td><?php echo $result->orderNumber; ?></td>
			<td>
				<?php echo $result->orderName; ?><br/>
				<?php echo $result->orderEmail; ?>
			</td>
			
			<td>
			<?php 
				echo  $result->oiProdName.'('.$result->oiProdVariantValue1.')';
				if($result->upgrade_info){
					echo '+'.$result->upgrade_info;
				}
			?>
			</td>
			<td><?php echo ($result->upgrade_price)?CURRENCY.$result->upgrade_price:''; ?></td>
			<td>
			<?php 
			if(is_allowed('edu_admin/order/view')):
				echo anchor('edu_admin/order/view/'.$result->orderID,'<img src="/images/view.png" title="view" alt="view"/>');
			endif;

			?>
			<?php 
			if(is_allowed('edu_admin/order/delete')):
				echo anchor('edu_admin/order/delete/'.$result->orderID,'<img src="/images/delete.png" title="delete" alt="delete"/>','onclick=\'return confirm("Do you want to delete this record?")\''); 
			endif;	
				?>
				
				</td> 
			</tr>
			<?php endforeach; ?>
			<tr>
				<td colspan="6" class="summary">
				<?php echo $pagination_links; ?>
				<div class="pagnination_summary">
					<?php echo $pagination_summary;?>
				</div>
				</td>
			</tr>		
			
			</table>
			</form>	
		
		<?php else: ?>
			<p class="no_recored_fount">No recored found</p>
		<?php endif; ?>
		
</div>
