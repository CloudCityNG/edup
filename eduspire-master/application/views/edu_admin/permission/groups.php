<?php 
/**
@Page/Module Name/Class: 	    groups.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display user list 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>
  jQuery(document).ready(function($) {
   $( ".fancybox" ).click(function() {
		$.fancybox.open({
			href : jQuery(this).attr('href'),
			type : 'iframe',
			padding : 5,
                        margin: [115, 0, 10, 0] // top, right, bottom, left
		});
		return false;
	});

});

</script>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="addRecord"><?php echo anchor('edu_admin/permission/create_group/','Add Group','class="submit"'); ?></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="result_container">
		<?php if(isset($results) && count($results)>0): ?>
			<form method="post" name="grid-form">
			<input type="hidden" name="mass_action" value="1"/>
			<table class="table striped" cellspacing="0" id="grid" cellpadding="0" width="100%">
			<tr>
				<th>ID</th>
				<th>Group</th>
				<th>Key</th>
				<th>Permissions</th>
				<th>Action</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			
			<td><?php echo $result->groupID;  ?></td>
			<td><?php echo $result->groupName;  ?></td>
			<td><?php echo $result->groupKey;  ?></td>
			<td><?php echo anchor('edu_admin/permission/group_permissions/'.$result->groupID,'Permissions','class="fancybox"'); ?></td>
			<td>
			<?php echo anchor('edu_admin/permission/create_group/'.$result->groupID,'<img src="/images/edit.png" title="edit" alt="edit"/>'); ?>
			</td>
			</tr>
			<?php endforeach; ?>
			
				
			
			</table>
			</form>	
		
		<?php else: ?>
			<p class="no_recored_fount">No record found</p>
		<?php endif; ?>
		
</div>


