<?php 
/**
@Page/Module Name/Class: 	    form.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		displau add/edit form for news 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?> 
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="backButton clearfix">
	 <?php echo anchor('edu_admin/news/','Back','class="submit"'); ?> 
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
		theme: "modern",
		menubar:false,
		relative_urls : false,
		remove_script_host : true,
		convert_urls : true,
		paste_auto_cleanup_on_paste : true,
		skin : "lightgray",
		theme_modern_buttons1 : "",
		theme_modern_buttons2 : "",
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
	<form class="form" action="" method="post" >
		<ul class="updateForm">
                 <li>
		   <label>&nbsp;</label>
		   <div class="formRight">
			<?php 
				$default_status= ($this->input->post('nwPublish') != '')?$this->input->post('nwPublish'):STATUS_PUBLISH;
				$selectd_status =  isset( $result->nwPublish )?$result->nwPublish:$default_status;
			?>
			<input type="checkbox" name="nwPublish" value="1" <?php echo (STATUS_PUBLISH==$selectd_status)?'checked="checked"':''; ?>/>
			Publish?
			</div>
		</li> 
		
		<li>
		   <label>Title <span class="required">*</span></label>
		   <div class="formRight">
                        <input type="text" name="nwTitle" value="<?php echo isset($result->nwTitle)?$result->nwTitle:$this->input->post('nwTitle');?>" maxlength="255" size="40"/>
		   <div class="error"><?php echo form_error('nwTitle','',''); ?></div>
		   </div>
		</li>
		
		<li>
		   <label>Description</label>
		   <div class="formRight">
			<textarea name="nwDescription" class="tinymce_editor" rows="5"><?php echo isset( $result->nwDescription ) ? $result->nwDescription:$this->input->post('nwDescription');?></textarea>
			<div class="error"><?php echo form_error('nwDescription','',''); ?></div>
		   </div>
		</li>
		
		<li>  
		   <label>&nbsp;</label>
		   <div class="formRight">
			<input type="submit" value="<?php echo (isset($result->nwID))?'Save':'Create'; ?>" class="submit"/>
		   </div>
		</li>
	</form>
</div>