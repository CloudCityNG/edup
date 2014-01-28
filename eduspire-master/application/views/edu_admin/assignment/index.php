<?php 
/**
@assignment/Module Name/Class: 	    index.php
@Author Name:			 		ben binesh
@Date:					 		Oct, 03 2013
@Purpose:		        		display assignment  list 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  
  RF1      Alan Anil           Nov 19 2013     Normal          show assignments total grades and link to show grades details.
//---------------------------------------------------------------------------------------- 
 */
?> 
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<?php if(isset($course) && !empty($course)): ?>
	<h2>
	<?php 
		$course_location=$course->csCity.', '.$course->csState;
			if(COURSE_ONLINE == $course->csCourseType)
				$course_location='Online';
	echo $course->cdCourseID.' '.$course->cdCourseTitle.'('.format_date($course->csStartDate,DATE_FORMAT).'-'.format_date($course->csEndDate,DATE_FORMAT).'-'.$course_location.')'; ?>
	</h2>
	<?php endif; ?>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
<h3>Filters</h3>
	
	<form name="search-form" action="<?php echo base_url().'edu_admin/assignment/index' ?>">
		<ul class="manageContent">
        	<li><label>Title</label><input type="text" name="title" value="<?php echo $title; ?>"/></li>
            <li><label>Type</label><?php echo form_dropdown('type',$this->assignment_model->get_assignment_type_array(true),$type); ?></li>
        </ul>
    	<div class="formButton">
			<input type="submit" class="submit" value="Search"/>
			<?php echo anchor('edu_admin/assignment/','Reset','class="submit"'); ?> 
			</div>
	</form>
	
	
</div>
<div class="result_container">
<div class="addRecord">
	 <?php if(is_allowed('edu_admin/assignment/create')): ?>
	 <?php echo anchor('edu_admin/assignment/create/?assignCnfID='.$this->input->get('course_id'),'Add assignment','class="submit"'); ?>
	 <?php endif; ?>
</div>
		<?php if(isset($results) && count($results)>0): ?>
		<table id="grid" class="table striped" cellspacing="0" cellspacing="0" width="100%">
		<tr>
				<th>ID</th>
				<th>Date/Time Active</th>
				<th>Date/Time Due</th>
				<th>Title</th>
				<th>Type</th>
				<th>Completed/Graded</th>
				<th>Action</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td><?php echo $result->assignID; ?></td>
			<td><?php echo format_date($result->assignActiveDate,DATE_FORMAT).'<br/> '.format_date($result->assignActiveTime,TIME_FORMAT); ?></td>
			<td><?php echo format_date($result->assignDueDate,DATE_FORMAT).'<br/> '.format_date($result->assignDueTime,TIME_FORMAT); ?></td>
			<td><?php echo $result->assignTitle; ?></td>
			<td><?php echo $this->assignment_model->show_assignment_type($result->assignType); ?></td>
			<td>
			<?php 
			// RF1
			$grade = $this->assignment_model->show_assign_grades($result->assignID); 
			 
			if(!empty($grade)): 
				echo anchor('edu_admin/assignment/grades/'.$result->assignID,$grade['completed'].'/'.$grade['graded']);
			endif;
			//Rf1 end.
			?> 
            </td>
			<td>
			<?php
				if(is_allowed('edu_admin/assignment/update')):
					echo anchor('edu_admin/assignment/update/'.$result->assignID.'?assignCnfID='.$this->input->get('course_id'),'<img src="/images/edit.png" title="edit" alt="edit"/>'); 
				endif;
			?>
			<?php
			if(is_allowed('edu_admin/assignment/delete')):
				echo anchor('edu_admin/assignment/delete/'.$result->assignID.'?assignCnfID='.$this->input->get('course_id'),'<img src="/images/delete.png" title="delete" alt="delete"/>','onclick=\'return confirm("Do you want to delete this record?")\''); 
			endif;
			?>
			</td>
			</tr>
			<?php endforeach; ?>
			<tr>
				<td colspan="7" class="summary">
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
