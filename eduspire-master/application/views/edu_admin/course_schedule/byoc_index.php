<?php 
/**
@Page/Module Name/Class: 		index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display course schedules
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<?php if( isset( $definition ) && $definition ): ?>
<div class="adminTitle"><h1 class="p_b10">
<?php echo $definition->cdCourseID ?>:<?php echo $definition->cdCourseTitle; ?></h1></div>
<?php $new_class_link= anchor('edu_admin/course_schedule/byoc_add/?csGenreId='.$definition->cdGenre.'&csCourseDefinitionId='.$definition->cdID.'&redirect='.urlencode(get_current_url()),'New Class','class="submit"');?>  
<?php else: ?>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
    <?php $new_class_link= anchor('edu_admin/course_schedule/byoc_add/','New Class','class="submit"'); ?>
<?php endif;  ?>


<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
<h3>Filter</h3>
	<form name="search-form" action="<?php echo base_url().'edu_admin/course_schedule/one_credit' ?>">
		<input type="hidden" name="definition_id" value="<?php echo $this->input->get('definition_id'); ?>">
        
         <ul class="coursemanageSchedule">
        	<li><label>Course Title:</label><input type="text" name="title"value="<?php echo $title; ?>"/></li>
            <li><label>Status:</label>
			<?php echo form_dropdown('status',$this->course_schedule_model->get_status_array(true),$status); ?></li>
            <li><label>Course ID:</label><input type="text" name="course_id" value="<?php echo $course_id; ?>"/></li>
			
		 <li><label>Course Date:</label>
			<?php echo form_dropdown('course_date',$this->course_schedule_model->get_course_date_array(true,array('all'=>'All')),$course_date); ?>
		</li>
		  </ul>
        <div class="formButton">
        	<input type="submit" class="submit" value="Search"/>
			<?php echo anchor('edu_admin/course_schedule/one_credit','Reset','class="submit"'); ?> 
			<input type="submit" name="export" class="submit tooltip" title="Reports are also filtered" value="CSV Export"  /> 
        </div>
		
	</form>	
	</div>

<div class="result_container">

		<?php if(isset($results) && count($results)>0): ?>
			<form method="post" name="grid-form">
			<input type="hidden" name="mass_action" value="1"/>
            <div class="addRecord">
			<input type="submit" class="submit" name="update_attributes" value="Update Attributes"  onclick=" return check()"/>
			<?php if(is_allowed('edu_admin/course_schedule/byoc_add')):
					echo $new_class_link; 
					endif; 
 			?>
			</div>
			<div>
	<?php echo $pagination_links; ?>
	
   </div>
			<table id="grid" class="table striped" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<th><input type="checkbox" name="check_all" id="check_all" value="1" onclick="checkall(this.form)" /></th>
				<th>Course</th>
				<th>Instructor</th>
				<th>Session</th>
				<th>Registration Date</th>
				<th>Reg/Enroll</th>
				<th>Status</th>
				<th>Action</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td ><input name="chk_ids[]" type="checkbox" class="checkbox" value="<?php echo $result->csID; ?>" /></td>
			 <td width="200"><?php echo $result->cdCourseID; ?>:<?php echo $result->cdCourseTitle; ?></td>
			<td><?php
				if(count($result->instructor)){
				echo implode(',',$result->instructor);
				
				}
				?>
			</td>
			<td><?php 
				if($result->bsStartDate):
					echo format_date($result->bsStartDate,DATE_FORMAT).'-'.format_date($result->bsEndDate,DATE_FORMAT);
				endif;
			?></td>
			 <td width="150">
				<div><b>Start : </b><?php echo format_date($result->csStartDate,DATE_FORMAT); ?></div>
				<div><b>End : </b><?php echo format_date($result->csEndDate,DATE_FORMAT); ?></div>
				<div><b>Reg : </b><?php echo format_date($result->csRegistrationEndDate,DATE_FORMAT); ?></div>
				<div><b>Guar : </b><?php echo format_date($result->csPaymentStartDate,DATE_FORMAT); ?></div>
				
			
			</td>
						
			<td><?php echo anchor('edu_admin/course_reservation/index?course_id='.$result->csID.'&ref=courses',$result->registered_count); ?>/
			<?php echo anchor('edu_admin/course_schedule/enrollees?course_id='.$result->csID.'&ref=courses',$result->enrollee_count); ?></td>
			<td><?php echo substr($this->course_schedule_model->show_status($result->csPublish),0,1); ?></td>
			
			<td>
			<?php 
				if(is_allowed('edu_admin/assignment/index')):
					echo anchor('edu_admin/assignment/index/?course_id='.$result->csID,'<img src="/images/assignment.png" title="assignments" alt="assignment"/>');
				endif;
			?>
			<?php 
			if(is_allowed('edu_admin/course_schedule/byoc_add')):
				echo anchor('edu_admin/course_schedule/byoc_add/'.$result->csID.'?redirect='.urldecode(get_current_url()),'<img src="/images/edit.png" title="edit" alt="edit"/>');
			endif
			?>
			<?php 
			if(is_allowed('edu_admin/course_schedule/copy')):
				echo anchor('edu_admin/course_schedule/copy/'.$result->csID.'?ref=byoc','<img src="/images/copy.png" title="copy" alt="copy"/>','onclick=\'return confirm("Do you want to copy this course?")\''); 
			endif;
			?>
			<?php 
			if(is_allowed('edu_admin/course_schedule/delete')):
				echo anchor('edu_admin/course_schedule/delete/'.$result->csID.'?redirect='.urlencode(get_current_url()),'<img src="/images/delete.png" title="delete" alt="delete"/>','onclick=\'return confirm("Do you want to delete this record?")\''); 
			endif;
			?>
			 <?php 
				if(is_allowed('edu_admin/order/index')):
					echo anchor('edu_admin/order/index/?course_id='.$result->csID,'Orders');
				endif;
				?>
			</td>
			</tr>
			<?php endforeach; ?>
			<tr>
			<td colspan="10" class="massaction">
				<input type="submit" class="submit" name="update_dates" value="update dates"  onclick="return check()"/>
			</td>	
			</tr>
			</table>
            <?php echo $pagination_links; ?>
				<div class="pagnination_summary">
					<?php echo $pagination_summary= pagination_summary();?>
				</div>
			</form>	
            
		<?php else: ?>
			<p class="no_recored_fount">No record found</p>
		<?php endif; ?>
		
</div>
