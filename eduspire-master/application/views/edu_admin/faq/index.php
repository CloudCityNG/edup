<?php 
/**
@Page/Module Name/Class: 		index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display faqs list 
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
	<form name="search-form" action="<?php echo base_url().'edu_admin/faq/index' ?>">
		<ul class="manageContent">
        	<li><label>Title</label><input type="text" name="title" value="<?php echo $title; ?>"/></li>
            <li><label>Intended Audience</label>
			<?php echo form_dropdown('audience',$this->faq_model->get_audience_array(true),$audience,'class="selectAudience"');?></li>
            <li><label>Status</label>
			<?php echo form_dropdown('status',$this->faq_model->get_status_array(true),$status); ?></li>
        
        </ul>
    	<div class="formButton">    
			<input type="submit" class="submit" value="Search"/>
			<?php echo anchor('edu_admin/faq/','Reset','class="submit"'); ?> 
	</div>
		
	</form>	
</div>
<div class="result_container">

<div class="addRecord">
	 <?php echo anchor('edu_admin/faq/create/','Add Record','class="submit"'); ?> 
</div>
		<?php if(isset($results) && count($results)>0): ?>
			<table id="grid" class="table striped" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<th>ID</th>
				<th>Title</th>
				<th>Audience</th>
				<th>Status</th>
				<th>Sort Order</th>
				<th>Action</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td><?php echo $result->faqID; ?></td>
			<td><?php echo $result->question; ?></td>
			<td><?php echo $this->faq_model->show_audience($result->intendedAudience); ?></td>
			<td><?php echo $this->faq_model->show_status($result->faqPublish); ?></td>
			<td><?php echo $result->nestedMenuOrder; ?></td>
			<td><?php echo anchor('edu_admin/faq/update/'.$result->faqID,'<img src="/images/edit.png" title="edit" alt="edit"/>'); ?>
			<?php echo anchor('edu_admin/faq/delete/'.$result->faqID,'<img src="/images/delete.png" title="delete" alt="delete"/>','onclick=\'return confirm("Do you want to delete this record?"
			)\''); ?></td>
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
