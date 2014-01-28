<?php 
/**
@Page/Module Name/Class: 	    index.php
@Author Name:			 		ben binesh
@Date:					 		Oct 30, 2013
@Purpose:		        		display permission list 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="addRecord">
	<?php echo anchor('edu_admin/permission/create/','Add Permission','class="submit"'); ?>
	<span class="manageGroup"><?php echo anchor('edu_admin/permission/groups/','Manage Group','class="submit"'); ?></span>
	
</div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="result_container">
		<?php if(isset($results) && count($results)>0): ?>
			<form method="post" name="grid-form">
			<input type="hidden" name="mass_action" value="1"/>
			<table class="table striped" cellspacing="0" width="100%" id="grid">
			<tr>
				<th>ID</th>
				<th>permission	</th>
				<th>Key</th>
				<th>parent</th>
				<th>Action</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($result['parentName'])?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			
			<td>
			<?php echo $result['id'];  ?></td>
			<td><?php if(isset($result['level'])){
					echo $space = str_repeat('- ' , $result['level']);
				}?><?php echo $result['permission'];  ?></td>
			<td><?php echo $result['key'];  ?></td>
			<td><?php echo ($result['parentName'])?$result['parentName']:'None';?></td>
			<td>
			<?php echo anchor('edu_admin/permission/create/'.$result['id'],'<img src="/images/edit.png" title="edit" alt="edit"/>'); ?>
			</td>
			</tr>
			<?php endforeach; ?>
			
			</table>
			</form>	
		
		<?php else: ?>
			<p class="no_recored_fount">No record found</p>
		<?php endif; ?>
		
</div>