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
<?php if(isset($definition) && $definition): ?>
<div class="adminTitle"><h1><?php echo $definition->cdCourseID ?>:<?php echo $definition->cdCourseTitle; ?></h1></div>
<?php $new_class_link= anchor('edu_admin/course_schedule/create/?csGenreId='.$definition->cdGenre.'&csCourseDefinitionId='.$definition->cdID.'&redirect='.urlencode(get_current_url()),'New Class','class="submit"');?>
<?php else: ?>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<?php $new_class_link= anchor('edu_admin/course_schedule/create/','New Class','class="submit"'); ?>
<?php endif;  ?>
<div class="flash_message">
  <?php get_flash_message(); ?>
</div>
<div class="top_form">
  <form name="search-form" action="<?php echo base_url().'edu_admin/course_schedule/index' ?>">
    <h3>Filters</h3>
    <input type="hidden" name="definition_id" value="<?php echo $this->input->get('definition_id'); ?>">
    <ul class="coursemanageSchedule">
      <li>
        <label>Course Title:</label>
        <input type="text" name="title"value="<?php echo $title; ?>"/>
      </li>
      <li>
        <label>Status:</label>
        <?php echo form_dropdown('status',$this->course_schedule_model->get_status_array(true),$status); ?></li>
      <li>
        <label>Course ID:</label>
        <input type="text" name="course_id" value="<?php echo $course_id; ?>"/>
      </li>
      <li><label>Course Genres:</label>
        <?php
			$genres_array=get_dropdown_array(' course_genres','cgID !='.BYOC_ID,$order_by='cgTitle',$order='ASC','cgID','cgTitle','',true,array(''=>'Select'));	
			echo form_dropdown('genre',$genres_array,$genre); 
			?></li>
      <li><label>Course Type:</label>
        <?php echo form_dropdown('course_type',$this->course_schedule_model->get_coursetype_array(true),$course_type); ?></li>
		<li><label>Course Date:</label>
			<?php echo form_dropdown('course_date',$this->course_schedule_model->get_course_date_array(true,array('all'=>'All')),$course_date); ?>
		</li>
		
		
    </ul>
    
    
    <div class="formButton">
      
        <input type="submit" class="submit" value="Search"/>
        <?php echo anchor('edu_admin/course_schedule/','Reset','class="submit"'); ?> </div>
    
  </form>
</div>
</div>
<div class="result_container"> 
<div class="addRecord">
      <?php
if(is_allowed('edu_admin/course_schedule/create')):
	echo $new_class_link; 
endif;

?>
    </div>
  <?php if(isset($results) && count($results)>0): ?>
                        
                           
  <div class="adminGrid">
    
	<div>
	<?php echo $pagination_links; ?>
	
   </div>
  
    <table class="table striped" cellspacing="0" width="100%" id="grid">
      <tr>
        <th>Course</th>
        <th>Location</th>
        <th>Instructor</th>
        <th>Registration Date</th>
        <th>Reg/Enroll</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <?php $i=0; ?>
      <?php foreach($results as $result): ?>
      <?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
      <tr class="<?php echo $tr_class; ?>">
        <td width="200"><?php echo $result->cdCourseID; ?>:<?php echo $result->cdCourseTitle; ?></td>
        <td width="120">
		
		<?php
		if(COURSE_ONLINE==$result->csCourseType)
			echo 'Online';
		else
			echo $result->csCity.', '.$result->csState;
		
		?>
		</td>
        <td><?php
				if(count($result->instructor)){
					echo implode(',',$result->instructor);
				}
				?></td>
        <td width="150">
			<div><b>Start : </b><?php echo format_date($result->csStartDate,DATE_FORMAT); ?></div>
			<div><b>End : </b><?php echo format_date($result->csEndDate,DATE_FORMAT); ?></div>
			<div><b>Reg : </b><?php echo format_date($result->csRegistrationEndDate,DATE_FORMAT); ?></div>
			<div><b>Guar : </b><?php echo format_date($result->csPaymentStartDate,DATE_FORMAT); ?></div>
		</td>
        <td><?php echo anchor('edu_admin/course_reservation/index?course_id='.$result->csID.'&ref=courses',$result->registered_count); ?>/ <?php echo anchor('edu_admin/course_schedule/enrollees?course_id='.$result->csID.'&ref=courses',$result->enrollee_count); ?></td>
        <td><?php echo substr($this->course_schedule_model->show_status($result->csPublish),0,1); ?></td>
        <td><?php 
					if(is_allowed('edu_admin/assignment/index')):
						echo anchor('edu_admin/assignment/index/?course_id='.$result->csID,'<img src="/images/assignment.png" title="Assignments" alt="assignment"/>'); 
					endif;	
					?>
          <?php 
				if(is_allowed('edu_admin/course_schedule/update')):
					echo anchor('edu_admin/course_schedule/update/'.$result->csID.'?redirect='.urldecode(get_current_url()),'<img src="/images/edit.png" title="edit" alt="edit"/>'); 
				endif;	
					?>
          <?php 
				if(is_allowed('edu_admin/course_schedule/copy')):
					echo anchor('edu_admin/course_schedule/copy/'.$result->csID,'<img src="/images/copy.png" title="copy" alt="copy"/>','onclick=\'return confirm("Do you want to copy this course?")\'');
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
				?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
  <?php echo $pagination_links; ?>
  <div class="pagnination_summary"> <?php echo $pagination_summary= pagination_summary();?> </div>
  <?php else: ?>
  <p class="no_recored_fount">No record found</p>
  <?php endif; ?>
</div>
