<?php 
/**
@Page/Module Name/Class: 		form.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display the add/edit form for course definition
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="top_links clearfix">
	 <?php echo anchor('edu_admin/course_definition/','Back','class="submit"'); ?> 
</div>
<script>
     tinymce.init({
		selector: "textarea.tinymce-editor",
		height:175,
		plugins: [
         "advlist autolink link image lists charmap  preview hr anchor pagebreak ",
         "searchreplace wordcount visualblocks visualchars code   media nonbreaking",
         "jbimages code"
		],
		theme: "modern",
		relative_urls : false,
		remove_script_host : false,
		convert_urls : false,
		menubar: false,
		paste_auto_cleanup_on_paste : true,
		skin : "lightgray",
		toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image jbimages advlist |  preview media fullpage | code ", 
  
	}); 
</script>


<div id="form">
				
	<div class="error_msg">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post">
    
    <ul class="updateForm">
    	<li>
        <label></label>
        <div class="formRight"><?php 
				$default_status= ($this->input->post('cdPublish') != '')?$this->input->post('cdPublish'):STATUS_PUBLISH;
				$selectd_status =  isset( $result->cdPublish )?$result->cdPublish:$default_status;
				
				
			?>
		<input type="checkbox" name="cdPublish" value="1" <?php echo (STATUS_PUBLISH==$selectd_status)?'checked="checked"':''; ?>/>Publish?
        </div>
        </li>
		
		<li>
		   <label>&nbsp;</label>
		    <div class="formRight">
			<?php 
				$default_featured= ($this->input->post('cdFeatured') != '')?$this->input->post('cdFeatured'):0;
				$selected_featured =  isset( $result->cdFeatured )?$result->cdFeatured:$default_featured;
				
				
			?>
			<input type="checkbox" name="cdFeatured" value="<?php echo FEATURED ?>" <?php echo (FEATURED==$selected_featured)?'checked="checked"':''; ?>/>
			Featured?
			</div>
		</li> 
		
        <li>
        	<label>Genre <span class="required">*</span></label>
            <div class="formRight"><?php 
				$selected_genre =  isset( $result->cdGenre ) ? $result->cdGenre:$this->input->post('cdGenre');
				$genres_array=get_dropdown_array(' course_genres',$where_condition=array(),$order_by='cgTitle',$order='ASC','cgID','cgTitle','',true,array(''=>'Select'));	
				echo form_dropdown('cdGenre',$genres_array,$selected_genre);
			?>
			<div class="error"><?php echo form_error('cdGenre','',''); ?></div>
            </div>
        </li>
        <li>
        	<label>Course ID: <span class="required">*</span></label>
            <div class="formRight"> <input type="text" name="cdCourseID" value="<?php echo isset($result->cdCourseID)?$result->cdCourseID:$this->input->post('cdCourseID');?>" maxlength="255" size="40"/>
		   <div class="error"><?php echo form_error('cdCourseID','',''); ?></div>
           </div>
        </li>
        <li>
        	<label>Title :<span class="required">*</span></label>
            <div class="formRight"> <input type="text" name="cdCourseTitle" value="<?php echo isset($result->cdCourseTitle)?$result->cdCourseTitle:$this->input->post('cdCourseTitle');?>" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('cdCourseTitle','',''); ?></div>
            </div>
        </li>
        <li>
        	<label>Description:</label>
            <div class="formRight"><textarea name="cdDescription" class="tinymce-editor" id="id_cdDescription" rows="5"><?php echo isset( $result->cdDescription ) ? $result->cdDescription:$this->input->post('cdDescription');?></textarea>
			<div class="error"><?php echo form_error('cdDescription','',''); ?></div>
            </div>
        </li>
        <li>
        	<label>Goals</label>
            <div class="formRight"><textarea name="cdGoals"  class="tinymce-editor" rows="5"><?php echo isset( $result->cdGoals ) ? $result->cdGoals:$this->input->post('cdGoals');?></textarea>
			<div class="error"><?php echo form_error('cdGoals','',''); ?></div>
            </div>
        </li>
        <li>
        	<label>Course Outline</label>
            <div class="formRight">
            	<textarea name="cdOutline"  class="tinymce-editor" rows="5"><?php echo isset( $result->cdOutline ) ? $result->cdOutline:$this->input->post('cdOutline');?></textarea>
			<div class="error"><?php echo form_error('cdOutline','',''); ?></div>
            </div>
        </li>
        <li>
        	<label>Evaluation Method</label>
            <div class="formRight"><textarea name="cdEvaluationMethod"  class="tinymce-editor" rows="5"><?php echo isset( $result->cdEvaluationMethod ) ? $result->cdEvaluationMethod:$this->input->post('cdEvaluationMethod');?></textarea>
			<div class="error"><?php echo form_error('cdEvaluationMethod','',''); ?></div>
            </div>
        </li>
        <li>
        	<label></label>
            <div class="formRight">
                <input type="submit" value="<?php echo (isset($result->cdID))?'Save':'Create'; ?>" class="submit"/>
            </div>
        </li>
    </ul>
    
	</form>
	
</div>