<?php 
/**
@Page/Module Name/Class: 	   district_index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display district list 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>
jQuery(document).ready(function($) {
	$(document).on('click','.btn-approve',function(){
		alink=jQuery(this).attr('href');
		edu_popup_msg(jQuery(this).attr('popup-text'));
		edu_show_popup();
		$(document).on('click','#ok-btn',function(){
			edu_close_popup();
			location.href=alink;
		});
	
		return false;
			
	});
});
				
</script>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
	<h3>Filters</h3>
	<form name="search-form" action="<?php echo base_url().'edu_admin/location/index' ?>">
		<ul class="manageContent">
        	<li><label>Name</label><input type="text" name="name" value="<?php echo $name; ?>"/></li>
            <li><label>IU</label>
			<?php $iu_unit_array=get_dropdown_array('iu_unit',$where_condition=array(),$order_by='iuID',$order='ASC','iuID','iuName','',true,array(''=>'Select'));	
			echo form_dropdown('iu_unit',$iu_unit_array,$iu_unit); 
			?></li>
            <li><label>User Added</label><?php echo form_dropdown('user_added',$this->location_model->get_user_added_array(true),$user_added); ?></li>
            <li><label>Status</label><?php echo form_dropdown('status',$this->location_model->get_status_array(true),$status); ?></li>
        </ul>
    	<div class="formButton">
			<input type="submit" class="submit" value="Search"/>
			<?php echo anchor('edu_admin/location/','Reset','class="submit"'); ?> 
			</div>
	</form>
	</fieldset>	
</div>
<div class="result_container">
<div class="addRecord">
	 <?php echo anchor('edu_admin/location/district/','Add Record','class="submit"'); ?> 
</div>
		<?php if(isset($results) && count($results)>0): ?>
			<table class="table striped" cellspacing="0" cellpadding="0" width="100%" id="grid">
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>IU</th>
				<th>User Added</th>
				<th>Status</th>
				<th>Action</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td><?php echo $result->disID; ?></td>
			<td><?php echo $result->disName; ?></td>
			<td><?php echo $result->iuName; ?></td>
			<td>
			<div><?php echo $this->location_model->show_user_added($result->disUserAdded); ?></div>
			<?php if(ADDED_BY_USER == $result->disUserAdded): ?>
			<div>
			<?php echo anchor('edu_admin/location/district_approve/'.$result->disID,'Approve','class="editButton btn-success btn-approve " popup-text="Do you want to approve this district"'); ?>
			<?php echo anchor('edu_admin/location/district_merge/'.$result->disID,'Merge','class=" editButton btn-warning"'); ?>
			<?php endif; ?>
			</div>
			</td>
			<td><?php echo $this->location_model->show_status($result->disPublish); ?></td>
			<td><?php echo anchor('edu_admin/location/district/'.$result->disID,'<img src="/images/edit.png" title="edit" alt="edit"/>'); ?>
			<?php echo anchor('edu_admin/location/delete_district/'.$result->disID,'<img src="/images/delete.png" title="delete" alt="delete"/>','onclick=\'return confirm("Do you want to delete this record?"
			)\''); ?>
			</td>
			</tr>
			<?php endforeach; ?>
			<tr>
				<td colspan="6" class="summary">
				<?php echo $pagination_links; ?>
				<div class="pagnination_summary">
					<?php echo pagination_summary();?>
				</div>
				</td>
			</tr>	
			</table>
		
		<?php else: ?>
			<p class="no_recored_fount">No record found</p>
		<?php endif; ?>
		
</div>
