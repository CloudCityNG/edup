<?php 
/**
@Page/Module Name/Class: 		from.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display add/edit form for inventory(ipad)
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="top_links clearfix">
	 <?php echo anchor('edu_admin/inventory/','Back','class="submit"'); ?> 
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
	
	<form class="form" action="" method="post"  enctype="multipart/form-data" >
		<ul class="updateForm">
        	<li>
            	<label></label>
                <div class="formRight"><?php 
				$default_status= ($this->input->post('invPublish') != '')?$this->input->post('invPublish'):STATUS_PUBLISH;
				$selectd_status =  isset( $result->invPublish )?$result->invPublish:$default_status;
			?>
			<input type="checkbox" name="invPublish" value="1" <?php echo (STATUS_PUBLISH==$selectd_status)?'checked="checked"':''; ?>/></div>
            </li>
            <li>
            	<label>Category <span class="required">*</span></label>
                <div class="formRight"><?php 
				$selectd_category =  isset( $result->invCatID )?$result->invCatID:$this->input->post('invCatID');
				echo form_dropdown('invCatID',$this->inventory_model->get_category_array(true,array(''=>'-select-')),$selectd_category);
			?>
			 <div class="error"><?php echo form_error('invCatID','',''); ?></div>
                </div>
            </li>
            <li><label>Subcategory <span class="required">*</span></label>
            	<div class="formRight"><?php 
				$selectd_subcategory =  isset( $result->invSubcatID )?$result->invSubcatID:$this->input->post('invSubcatID');
				echo form_dropdown('invSubcatID',$this->inventory_model->get_subcategory_array(true,array(''=>'-select-')),$selectd_subcategory);
			?>
			 <div class="error"><?php echo form_error('invSubcatID','',''); ?></div>
                </div>
             </li>
            <li><label>Product Name<span class="required">*</span></label>
            <div class="formRight"><input type="text" name="invName" value="<?php echo isset($result->invName)?$result->invName:$this->input->post('invName');?>" />
		   <div class="error"><?php echo form_error('invName','',''); ?></div>
            </div></li>
            <li><label>Product Description <span class="required">*</span></label>
            <div class="formRight"><textarea name="invDescription" class="tinymce_editor" ><?php echo isset( $result->invDescription) ? $result->invDescription:$this->input->post('invDescription');?></textarea>
			 <div class="error"><?php echo form_error('invDescription','',''); ?></div>
            </div>
             </li>
            <li>
            	<label>Variants</label>
            	<div class="formRight"><textarea name="invVariant01" ><?php echo isset( $result->invVariant01) ? $result->invVariant01:$this->input->post('invVariant01');?></textarea>
			<div class="hint">This is for variations in the product, such as colors. Enter each variant, separated by the enter or return key.</div>
			 <div class="error"><?php echo form_error('invVariant01','',''); ?></div>
		   </div>
           </li>
           <?php if(isset($image) && $image != ''): ?><li><label>Uploaded Image</label>
           <label class="formRight"><?php $img_path=  base_url().'uploads/ipads/'.$image; ?>
				<img src="<?php echo crop_image($img_path);?>" height="100" width="100"/>
				<input type="hidden" name="old_image" value="<?php echo $image; ?>" /></label></li>
               <?php endif; ?> 
            <li>
            	<label>Product Image</label>
                <div class="formRight"><input type="file" name="invImage"/> <span class="imageMessage">(only .jpg,.jpeg.png,.gif allowed)</span>
                </div>
            </li>
            <li>
            	<label>Product Price<span class="required">*</span></label>
                <div class="formRight"><input type="text" name="invPrice1" value="<?php echo isset($result->invPrice1)?$result->invPrice1:$this->input->post('invPrice1');?>" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('invPrice1','',''); ?></div>
                </div>
            </li>
            <li>
            	<label>Quantity Available</label>
                <div class="formRight"> <input type="text" name="invQuantity" value="<?php echo isset($result->invQuantity)?$result->invQuantity:$this->input->post('invQuantity');?>" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('invQuantity','',''); ?></div></div>
            </li>
            <li>
            	<label>Shipping</label>
                <div class="formRight"> <input type="text" name="invShip" value="<?php echo isset($result->invShip)?$result->invShip:$this->input->post('invShip');?>" maxlength="255" size="40"/>
		   <div class="hint">Specify the shipping cost per item unless there is none.</div>
		   <div class="error"><?php echo form_error('invShip','',''); ?></div></div>
            </li>
            <li>
            	<label>Sorting Order</label>
                <div class="formRight"> <input type="text" name="invSortOrder" value="<?php echo isset($result->invSortOrder)?$result->invSortOrder:$this->input->post('invSortOrder');?>" maxlength="255" size="40"/>
		   <div class="error"><?php echo form_error('invSortOrder','',''); ?></div></div>
            </li>
            <li>
            	<label></label>
                <div class="formRight"><input type="submit" value="<?php echo (isset($result->invID))?'Save':'Create'; ?>" class="submit"/></div>
            </li>
        </ul>
	
		
	</form>
	
</div>