<?php 
/**
@Page/Module Name/Class: 	    form.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display add/edit form for cms content 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?> 
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="backButton">
	 <?php echo anchor('edu_admin/page/','Back','class="submit"'); ?> 
</div>
<script>
    tinymce.init({
		selector: "textarea.tinymce_editor",
		height:175,
		plugins: [
         "advlist autolink link image lists charmap  preview hr anchor pagebreak ",
         "searchreplace wordcount visualblocks visualchars code   media nonbreaking",
         "jbimages code"
		],
		relative_urls : false,
		remove_script_host : true,
		convert_urls : true,
		theme: "modern",
		menubar: false,
		paste_auto_cleanup_on_paste : true,
		skin : "lightgray",
		toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image jbimages advlist |  preview media fullpage | code ", 
  
	}); 
</script>
<div id="form">
	<div class="error_msg error">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post"  enctype="multipart/form-data">
            <ul class="updateForm">
                <li>
                <label></label>
                <label class="right">
                    <?php 
                    $default_status= ($this->input->post('cpPublish') != '')?$this->input->post('cpPublish'):STATUS_PUBLISH;
                    $selectd_status =  isset( $result->cpPublish )?$result->cpPublish:$default_status;
                    ?>
                    <input type="checkbox" name="cpPublish" value="1" <?php echo (STATUS_PUBLISH==$selectd_status)?'checked="checked"':''; ?>/>
			Publish?
			</label>
		</li> 
		<li>
		   <label>Page Name <span class="required">*</span></label>
		   <label class="right">
		   <input type="text" name="cpName" value="<?php echo isset($result->cpName)?$result->cpName:$this->input->post('cpName');?>" maxlength="255" size="40"/>
		   <div class="hint">This name will use internally </div>
		    <div class="error"><?php echo form_error('cpName','',''); ?></div>
		   </label>
		</li>
		
		<li>
		   <label>Title <span class="required">*</span></label>
		   <label class="right">
		   <input type="text" name="cpTitle" value="<?php echo isset($result->cpTitle)?$result->cpTitle:$this->input->post('cpTitle');?>" maxlength="255" size="40"/>
		   <div class="error"><?php echo form_error('cpTitle','',''); ?></div>
		   </label>
		</li>
		
		<li>
		   <label>Url Key</label>
		    <label class="right">
                    <input type="text" name="cpUrlKey" value="<?php echo isset( $result->cpUrlKey ) ? $result->cpUrlKey:$this->input->post('cpUrlKey');?>" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('cpUrlKey','',''); ?></div>
		   </label>
		</li>
		
		<li>
		   <label>Description</label>
		  <label class="right">
			<textarea name="cpDescription" class="tinymce_editor" rows="5"><?php echo isset( $result->cpDescription ) ? $result->cpDescription:$this->input->post('cpDescription');?></textarea>
			 <div class="error"><?php echo form_error('cpDescription','',''); ?></div>
		   </label>
		</li>
		<?php if(isset($image) && $image != ''): ?>
			<li>
			   <label>Uploaded Image </label>
			   <label class="right">
				<?php $img_path=  base_url().'uploads/pages/'.$image; ?>
				<img src="<?php echo crop_image($img_path);?>" height="100" width="100"/>
				<input type="hidden" name="old_image" value="<?php echo $image; ?>" />
			   </label>
			</li>
		<?php endif; ?>
				
		<li>
		   <label>Banner Image </label>
		   <label class="right">
			<input type="file" name="cpImage"/>(only .jpg,.jpeg.png,.gif allowed)<br/>
		   </label>
		</li>
		
		<li>
		   <label>Meta Title </label>
		   <label class="right">
		   <input type="text" name="cpMetaTitle" value="<?php echo isset( $result->cpMetaTitle ) ? $result->cpMetaTitle:$this->input->post('cpMetaTitle');?>" maxlength="255" size="40"/>
		   <div class="error"><?php echo form_error('cpMetaTitle','',''); ?></div>
		   </label>
		</li>
		<li>
		   <label>Meta Description</label>
		   <label class="right">
			<textarea name="cpMetaDescription"  rows="3"><?php echo isset( $result->cpMetaDescription ) ? $result->cpMetaDescription:$this->input->post('cpMetaDescription');?></textarea>
			<div class="error"><?php echo form_error('cpMetaDescription','',''); ?></div>
		   </label>
		</li>
		
		<li> 
		   <label>&nbsp;</label>
		   <label class="right">
			<input type="submit" value="<?php echo (isset($result->cpID))?'Save':'Create'; ?>" class="submit"/>
			
		   </label>
		</li>
            </ul>
	</form>
</div>