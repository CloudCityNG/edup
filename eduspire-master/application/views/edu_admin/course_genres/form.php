<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="backButton">
	 <?php echo anchor('edu_admin/course_genres/','Back','class="submit"'); ?> 
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
		menubar: false,
		relative_urls : false,
		remove_script_host : false,
		convert_urls : false,
		paste_auto_cleanup_on_paste : true,
		skin : "lightgray",
		toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image jbimages advlist |  preview media fullpage | code ", 
  
	}); 
</script>
	<div class="error_msg">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" enctype="multipart/form-data">
    	<ul class="updateForm">
        	<li>
            	<label></label>
                <?php 
				$default_status= ($this->input->post('cgPublish') != '')?$this->input->post('cgPublish'):STATUS_PUBLISH;
				$selectd_status =  isset( $result->cgPublish )?$result->cgPublish:$default_status;
				?>
			<input type="checkbox" name="cgPublish" value="1" <?php echo (STATUS_PUBLISH==$selectd_status)?'checked="checked"':''; ?>/>
			Publish?
            </li>
            <li>
            	<label>Genre Title <span class="required">*</span></label>
                <div class="formRight">
                	<input type="text" name="cgTitle" value="<?php echo isset($result->cgTitle)?$result->cgTitle:$this->input->post('cgTitle');?>" />
		   			<div class="error"><?php echo form_error('cgTitle','',''); ?></div>
                </div>
                
            </li>
            <li>
            	<label>Course Credits<span class="required">*</span></label>
                <div class="formRight">
                	<input type="text" name="cgCourseCredits" value="<?php echo isset( $result->cgCourseCredits ) ? $result->cgCourseCredits:$this->input->post('cgCourseCredits');?>" maxlength="255" size="40"/>
		  			 <div class="error"><?php echo form_error('cgCourseCredits','',''); ?></div>
                </div>
            </li>
            <li>
            	<label>Course Notes</label>
                <div class="formRight">
                	<textarea name="cgCourseNotes"><?php echo isset( $result->cgCourseNotes ) ? $result->cgCourseNotes:$this->input->post('cgCourseNotes');?></textarea>
					<div class="error"><?php echo form_error('cgCourseNotes','',''); ?></div>
                </div>
            </li>
            <li>
            	<label>Landing Page Copy</label>
                <div class="formRight">
                    <textarea name="cgDescription" class="tinymce-editor"><?php echo isset( $result->cgDescription ) ? $result->cgDescription:$this->input->post('cgDescription');?></textarea>
				<div class="error"><?php echo form_error('cgDescription','',''); ?></div>
            	</div>       
            </li>
            <li>
            	<label>Uploaded Image</label>
                <div class="formRight">
                <?php if(isset($image) && $image != ''): ?>
				<?php $img_path=  base_url().'uploads/course/'.$image; ?>
				<img src="<?php echo crop_image($img_path);?>" height="100" width="100"/>
				<input type="hidden" name="old_image" value="<?php echo $image; ?>" />
                </div>
                <?php endif; ?>
            </li>
            <li>
            	<label>Course Image</label>
                <div class="formRight">
                    <input type="file" name="cgImage"/><span class="imageMessage">(only .jpg,.jpeg.png,.gif allowed)</span>
                </div>
            </li>
            <li>
            	<label>Display Order</label>
                <div class="formRight">
                <input type="text" name="cgDisplayOrder" value="<?php echo isset( $result->cgDisplayOrder ) ? $result->cgDisplayOrder:$this->input->post('cgDisplayOrder');?>" maxlength="255" size="40"/>
			 	<div class="error"><?php echo form_error('cgDisplayOrder','',''); ?></div>
                </div>
            </li>
            <li>
            	<label></label>
                <div  class="formRight">
                	<input type="submit" value="<?php echo (isset($result->cgID))?'Save':'Create'; ?>" class="submit"/>
                </div>
            </li>
        </ul>
	
	</form>
	
</div>