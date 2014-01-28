<?php 
/**
@assignment/Module Name/Class: 	    index.php
@Author Name:			 		ben binesh
@Date:					 		Oct, 03 2013
@Purpose:		        		display assignment  list 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?> 
<div class="publicTitle"><h1>Import Assignments</h1></div>
<script>
jQuery(document).ready(function($) {
	$('#id_csCourseDefinitionId').change(function(){
			$.ajax({
				url: '<?php echo base_url().'/ajax/course_schedule/';?>'+$(this).val(),
				success: function(data) {
					$("#id_course_id_container").html(data);
				}	
			});
		});
});
</script>
<h3>
<div class="eduspireNews courseFinalGrades"> 
        Course: <?php echo $course->cdCourseTitle ?>(<?php echo (COURSE_OFFLINE==$course->csCourseType)?$course->csLocation:'Online'; 
        ?>)  
    	<?php echo "<br>".format_date($course->csStartDate,DATE_FORMAT).'-'.format_date($course->csEndDate,DATE_FORMAT); ?> 
</div>
</h3>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
	<fieldset class="fieldset">
	<h3>Filters</h3>
	<form name="search-form" action="<?php echo base_url().'assignment/index' ?>">
		<input type="hidden" value="<?php echo $for_course ?>" name="for_course"/>
		<ul class="manageContent import-assignment">
		<li>
		<label>Choose a Course</label>
		<?php 
			
			$selected_definition =$this->input->get_post('definition_id');
			$definition_array=get_dropdown_array('course_definitions',array('cdPublish'=>STATUS_PUBLISH),$order_by='cdCourseTitle',$order='ASC','cdID','cdCourseID','cdCourseTitle',true,array(''=>'Select'));	
			echo form_dropdown('definition_id',$definition_array,$selected_definition,'id="id_csCourseDefinitionId"');
			?>
		</li>
		<li>
		<label>Choose an Instructor</label>
		<?php 
			$instructor_array = get_dropdown_array('users',$where_condition=array('accessLevel'=>INSTRUCTOR,'id != '=>$this->session->userdata('user_id')),$order_by='lastName',$order='ASC','id','lastName','firstName',true,array(''=>'select'));
			echo form_dropdown('author_id',$instructor_array,$this->input->get_post('author_id'));
		?>
		</li>
		
		<li>
		<label>Choose a class</label>
		<div id="id_course_id_container">
		<?php 
			$selected_definition = ($selected_definition)?$selected_definition:0;
			$course_array=get_course_dropdown('course_schedule',$where_condition=array('csCourseDefinitionId'=>$selected_definition),$order_by='csID',$order='ASC',
			$select='csID,csCity,csState,csStartDate,csCourseType',true,array(''=>'Select'));	
			echo form_dropdown('course_id',$course_array,$this->input->get_post('course_id'));
		?>
		</div>
		
		</li>
		
		</ul>
		<div class="row clearfix">
			<div class="formButton">
			<input type="submit" class="submit" value="Search"/>
			</div>
		</div>
		
	</form>
	</fieldset>	
	</div>
</div>
<div class="result_container">
		<?php if(isset($results) && count($results)>0): ?>
		<form method="post" name="grid-form">
		<input type="hidden" name="mass_action" value="1"/>
		<table class="table striped" cellspacing="0" width="100%" id="grid">
			
			<tr>
				<th><input type="checkbox" name="check_all" id="check_all" value="1" onclick="checkall(this.form)" /></th>
				<th>Title</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td ><input name="chk_ids[]" type="checkbox" class="checkbox" value="<?php echo $result->assignID; ?>" /></td>
			<td><?php echo $result->assignTitle; ?></td>
			</tr>
			<?php endforeach; ?>
			<td colspan="7" class="massaction">
				<input type="submit" class="submit" name="import" value="Import" onclick=" return check()"/>
			</td>
			<tr>
				<td colspan="7" class="summary">
				<?php echo $pagination_links; ?>
				<div class="pagnination_summary fRight">
					<?php echo $pagination_summary = pagination_summary();?>

				</div>
				</td>
			</tr>	
			</table>
		</form>
		<?php else: ?>
			<p class="no_record_found">No record found</p>		<?php endif; ?>
		
</div>
