<?php 
/**
@Page/Module Name/Class: 		index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display contacts 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>

<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">

	<form name="search-form" action="<?php echo base_url().'edu_admin/contact/index' ?>">
    	<h3>Filters</h3>
		<ul class="manageContent">
        	<li><label>Name</label><input type="text" name="name" value="<?php echo $name; ?>"/></li>
            <li><label>Email</label><input type="text" name="email" value="<?php echo $email; ?>"/></li>
        </ul>
        <div class="formButton">
			<input type="submit" class="submit" value="Search"/>
			<?php echo anchor('edu_admin/contact/','Reset','class="submit"'); ?> 
			</div>
        <ul class="manageContent">
        	<li><label>Select</label><?php echo form_dropdown('export_type',get_report_type_array(),$this->input->get('export_type')); ?></li>
        </ul>
		<div class="formButton"><input type="submit" name="export" class="submit tooltip" title="Reports are also filtered" value="Export"  /> </div>
			
		
		
	</form>
	</div>
<div class="result_container">
<div class="addRecord">
	 <?php echo anchor('edu_admin/contact/create/','Add Record','class="submit"'); ?> 
</div>
		<?php if(isset($results) && count($results)>0): ?>
			<form method="post" name="grid-form">
			<input type="hidden" name="mass_action" value="1"/>
			<table id="grid" class="table striped" cellspacing="0" cellspacing="0" width="100%"> 
			
			<tr>
				<th><input type="checkbox" name="check_all" id="check_all" value="1" onclick="checkall(this.form)" /></th>
				<th>ID</th>
				<th>Name</th>
				<th>Email</th>
				<th>Contact Date</th>
				<th>Action</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td><input name="chk_ids[]" type="checkbox" class="checkbox" value="<?php echo $result->contID; ?>" /></td>
			<td><?php echo $result->contID; ?></td>
			<td>
				<?php echo $result->contFirstName.' '.$result->contLastName; ?>
			</td>
			<td><?php echo $result->contEmail; ?></td>
			<td><?php echo format_date($result->contDate,DATE_FORMAT.' '.TIME_FORMAT); ?></td>
			<td>
			<?php echo anchor('edu_admin/contact/view/'.$result->contID,'<img src="/images/view.png" title="view" alt="view"/>'); ?>
			<?php echo anchor('edu_admin/contact/delete/'.$result->contID,'<img src="/images/delete.png" title="delete" alt="delete"/>','onclick=\'return confirm("Do you want to delete this record?")\''); ?></td>
			</tr>
			<?php endforeach; ?>
			<tr>
			<td  class="mastaction">
				<input type="submit" class="submit" name="delete" value="Delete" onclick=" return check()"/>
			</td>	
			
			</tr>
			<tr>
				<td colspan="6" class="summary">
				<?php echo $pagination_links; ?>
				<div class="pagnination_summary">
					<?php echo pagination_summary();?>
				</div>
				</td>
			</tr>		
			
			</table>
			</form>	
		
		<?php else: ?>
			<p class="no_recored_fount">No recored found</p>
		<?php endif; ?>
		
</div>
