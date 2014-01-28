<?php 
/**
@Page/Module Name/Class: 		index.php
@Author Name:			 		ben binesh
@Date:					 		Ove 06, 2013
@Purpose:		        		display course sessions
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="addRecord">
	<?php 
	if(is_allowed('edu_admin/course_definition/session')):
		echo anchor('edu_admin/course_definition/session','Add Session','class="submit"'); 
	endif;	
	?> 
	 
</div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>

<div class="result_container">
		<?php if(isset($results) && count($results)>0): ?>
			<table class="table striped" cellspacing="0" cellpadding="0" width="100%" id="grid">
			<tr>
				<th>ID</th>
				<th>Course Session</th>
				<th>&nbsp;</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td><?php echo $result->bsID; ?></td>
			<td><?php echo format_date($result->bsStartDate,DATE_FORMAT); ?>-<?php echo format_date($result->bsEndDate,DATE_FORMAT) ?></td>
			<td>
			<?php
			if(is_allowed('edu_admin/course_definition/session')):
				echo anchor('edu_admin/course_definition/session/'.$result->bsID,'<img src="/images/edit.png" title="edit" alt="edit"/>'); 
			endif;	
			?>
			
			<?php 
			if(is_allowed('edu_admin/course_definition/delete_session')):
				echo anchor('edu_admin/course_definition/delete_session/'.$result->bsID,'<img src="/images/delete.png" title="delete" alt="delete"/>','onclick=\'return confirm("Do you want to delete this record?")\'');
			endif;	
			?>
			</td>
			</tr>
			<?php endforeach; ?>
			<tr>
				<td colspan="3" class="summary">
				<?php echo $pagination_links; ?>
				<div class="pagnination_summary">
					<?php echo $pagination_summary= pagination_summary();?>
				</div>
				</td>
			</tr>	
			</table>
		
		<?php else: ?>
			<p class="no_recored_fount">No record found</p>
		<?php endif; ?>
		
</div>
