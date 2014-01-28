<?php 
/**
@Page/Module Name/Class: 	   district_form.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display add/edit form for district
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="backButton clearfix">
	 <?php echo anchor('edu_admin/location','Back','class="submit"'); ?> 
</div>

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
                    $default_status= ($this->input->post('disPublish') != '')?$this->input->post('disPublish'):STATUS_PUBLISH;
                    $selectd_status =  isset( $result->disPublish )?$result->disPublish:$default_status;
                    ?>
			<input type="checkbox" name="disPublish" value="1" <?php echo (STATUS_PUBLISH==$selectd_status)?'checked="checked"':''; ?>/>
			Publish?
			</div>
		</li> 
		
		<li>
		   <label>IU <span class="required">*</span> </label>
		   <div class="formRight">
			<?php 
				$selected_iuUnit =  isset( $result->disIuUnit ) ? $result->disIuUnit:$this->input->post('disIuUnit');
				$iu_unit_array=get_dropdown_array('iu_unit',$where_condition=array(),$order_by='iuID',$order='ASC','iuID','iuName','',true,array(''=>'Select'));
				echo form_dropdown('disIuUnit',$iu_unit_array,$selected_iuUnit);
			?>
			 <div class="error"><?php echo form_error('disIuUnit','',''); ?></div>
		   </div>
		</li> 
		
		<li>
		   <label>Name <span class="required">*</span></label>
		   <div class="formRight">
		   <input type="text" name="disName" value="<?php echo isset($result->disName)?$result->disName:$this->input->post('disName');?>" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('disName','',''); ?></div>
		   </div>
		</li>
		
		<li>  
		   <label>&nbsp;</label>
		   <div class="formRight">
			<input type="submit" value="<?php echo (isset($result->disID))?'Save':'Create'; ?>" class="submit"/>
			
		   </div>
		</li>
             </ul>
	</form>
	
</div>