<?php 
/**
@Page/Module Name/Class: 	    form.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display add/edit form for home page slides 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div> 
<div class="backButton">
	 <?php echo anchor('edu_admin/slide/','Back','class="submit"'); ?> 
</div>

	<div class="error_msg error">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" enctype="multipart/form-data">
    	<ul class="updateForm">
        	<li><label></label>
            	<?php 
				$default_status= ($this->input->post('csPublish') != '')?$this->input->post('csPublish'):STATUS_PUBLISH;
				$selectd_status =  isset( $result->csPublish )?$result->csPublish:$default_status;
				
				
			?>
			<input type="checkbox" name="csPublish" value="1" <?php echo (STATUS_PUBLISH==$selectd_status)?'checked="checked"':''; ?>/>
			Publish?
            </li>
            <li>
            	<label>Title <span class="required">*</span></label>
                <label class="right">
                <span class="error"><?php echo form_error('csTitle','',''); ?></span>
                <input type="text" name="csTitle" value="<?php echo isset($result->csTitle)?$result->csTitle:$this->input->post('csTitle');?>"/>
                </label>
            </li>
            <li>
            	<label>Url</label>
                <label class="right">
	                <span class="error"><?php echo form_error('csUrl','',''); ?></span>
    	            <input type="text" name="csUrl" value="<?php echo isset( $result->csUrl ) ? $result->csUrl:$this->input->post('csUrl');?>"/>
                </label>
		    </li>
            <li>
            	<label>Order</label>
                <label class="right">
                <span class="error"><?php echo form_error('csOrder','',''); ?></span>
                <input type="text" name="csOrder" value="<?php echo isset( $result->csOrder ) ? $result->csOrder:$this->input->post('csOrder');?>"/>
                </label>
		   
            </li>
            <li>
            	<?php if(isset($image) && $image != ''): ?>
				<label>Uploaded Image </label>
				<label class="right">
                	<img src="<?php echo base_url() ?>uploads/slide/<?php echo $image; ?>" height="75" width="75"/>
            		<input type="hidden" name="old_image" value="<?php echo $image; ?>" />
                 </label>
		 		<?php endif; ?>
            </li>
            <li>
            	<label>Upload Image <span class="required">*</span></label>
                <input type="file" name="csImage"/>	<span class="imageMessage">(only .jpg,.jpeg.png,.gif allowed)</span>
            </li>
            <li><label></label>
            <label class="right">
            <input type="submit" value="<?php echo (isset($result->csID))?'Save':'Create'; ?>" class="submit"/></label>
            </li>
        </ul>

	</form>
