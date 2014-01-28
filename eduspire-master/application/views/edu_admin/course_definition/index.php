<?php 
/**
@Page/Module Name/Class: 		index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display course definitions 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="addRecord">
	<?php 
	if(is_allowed('edu_admin/course_definition/create')):
		echo anchor('edu_admin/course_definition/create/','New Course','class="submit"'); 
	endif;	
	?> 
	 
</div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<?php /* need this comment 
<div class="top_form">
	<form name="search-form" action="<?php echo base_url().'edu_admin/course_definition/index' ?>">
		<div class="row clearfix">
		<div class="col"><label>Title:</label><input type="text" name="title"value="<?php echo $title; ?>"/></div>
		<div class="col"><label>Status:</label>
			<?php echo form_dropdown('status',$this->course_definition_model->get_status_array(true),$status); ?>
		</div>
		<div class="col"><label>Course ID:</label><input type="text" name="course_id" value="<?php echo $course_id; ?>"/></div>
		</div>
		<div class="row clearfix">
			<div class="col"><label>Course Genres:</label><?php
			$genres_array=get_dropdown_array(' course_genres',$where_condition=array(),$order_by='cgTitle',$order='ASC','cgID','cgTitle','',true,array(''=>'Select'));	
			echo form_dropdown('genre',$genres_array,$genre); 
			?>
			
			</div>
		</div>
		<div class="row clearfix">
			<div class="col">
			<input type="submit" class="submit" value="Search"/>
			<?php echo anchor('edu_admin/course_definition/','Reset','class="submit"'); ?> 
			</div>
		</div>
		
	</form>	
	</div>
</div>*/?>
<div class="result_container">
		<!--<?php if(isset($results) && count($results)>0): ?>
                     <?php echo $pagination_links; ?>
                        <div class="pagnination_summary">
                                <?php echo pagination_summary();?>
                        </div>-->
			<table class="table striped" cellspacing="0" width="100%" id="grid">
			
			<tr>
				<th>ID</th>
				<th>Course</th>
				<th>Credits</th>
				<th>Reg/Enroll</th>
				<th>Featured</th>
				<th>Status</th>
				<th>&nbsp;</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td><?php echo $result->cdCourseID; ?></td>
			<td width="450"><?php if(BYOC_ID == $result->cdGenre ): ?>
				<?php echo anchor('edu_admin/course_schedule/one_credit?definition_id='.$result->cdID,$result->cdCourseTitle); ?>
			<?php else: ?>
				<?php echo anchor('edu_admin/course_schedule/index?definition_id='.$result->cdID,$result->cdCourseTitle); ?>
			<?php  endif; ?>
			</td>
			<td><?php echo $result->cgCourseCredits; ?></td>
			<td><?php echo $result->registered_count ?>/<?php echo (!empty($result->enrolees_count))?$result->enrolees_count:0;?></td>
			<td><?php echo show_yesNo_text($result->cdFeatured); ?></td>
			<td><?php echo substr($this->course_definition_model->show_status($result->cdPublish),0,1); ?></td>
			
			<td>
			<?php
			if(is_allowed('edu_admin/course_definition/update')):
				echo anchor('edu_admin/course_definition/update/'.$result->cdID,'<img src="/images/edit.png" title="edit" alt="edit"/>'); 
			endif;	
			?>
			
			<?php 
			if(is_allowed('edu_admin/course_definition/delete')):
				echo anchor('edu_admin/course_definition/delete/'.$result->cdID,'<img src="/images/delete.png" title="delete" alt="delete"/>','onclick=\'return confirm("Do you want to delete this record?")\'');
			endif;	
			?>
			</td>
			</tr>
			<?php endforeach; ?>
			
			</table>
                        <?php echo $pagination_links; ?>
                            <div class="pagnination_summary">
                                    <?php echo pagination_summary();?>
                            </div>
		<?php else: ?>
			<p class="no_record_found">No record found</p>
		<?php endif; ?>
		
</div>
