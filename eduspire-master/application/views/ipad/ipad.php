<?php 
/**
@Page/Module Name/Class: 		index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display ipad selection form with various options
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>
	jQuery(document).ready(function($) {
		$('#product_id').change(function(){
			product_id =$(this).val();
			if(''==product_id || 101 == product_id){
				$('#color_options_ipad, #ipad_options').show();
				$('#color_options_ipad_mini, #ipad_options_mini').hide();
				
			}else{
				$('#color_options_ipad, #ipad_options').hide();
				$('#color_options_ipad_mini, #ipad_options_mini').show()
			}
		});
	});
</script>
<?php if(isset($content) && !empty($content) ): ?>
<div class="publicTitle"><h1><?php echo $content->cpTitle; ?></h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>


<?php echo $content->cpDescription;?>
<?php endif; ?>

<?php if(isset($results) && !empty($results)): ?>
	<div id="form">
	
	<form action="" method="post">
	<div class="error_msg error">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<?php $product_id=$this->input->post('product_id'); ?>
		<ul class="updateForm">
		<li>
		<label>	iPad Choice:<span class="required">*</span></label>
		<div class="formRight">
		<select name="product_id" id="product_id">
		<option value="">Select</option>
		<?php foreach($results as $result): ?>
			<?php if(1 == $result->invSubcatID): ?>
				<?php $selected = ($product_id == $result->invID)?'selected="selected"':''; ?>
				<option <?php echo $selected; ?> value="<?php echo  $result->invID ?>"><?php echo  $result->invName ?></option>

			<?php endif; ?>
		<?php endforeach; ?>
		</select>
		<div class="error"><?php echo form_error('product_id','',''); ?></div>
		</div>
		</li>
		
		<li class="color-options">
		<label>iPad Color:<span class="required">*</span></label>
		<div class="formRight">
		<?php foreach($results as $result): ?>	
			<?php if(101==$result->invID && 1==$result->invSubcatID ):?>
					<?php if(isset($result->invVariant01)): ?>
						<?php $color_options=explode("\n",$result->invVariant01); ?>
							<select name="color_101" id="color_options_ipad" style="display:<?php echo ((''==$product_id) || (101==$product_id) )?'block':'none' ?>">
								<option value="">Select</option>
								<?php foreach($color_options as $color_option): 
										if($color_option){ ?>
									
								<option <?php echo (trim($color_option)==$this->input->post('color_101'))?'selected="selected"':''; ?> value="<?php echo trim($color_option) ?>"><?php echo $color_option ?></option>
								<?php }  
								endforeach; ?>
							</select>
							
						<?php endif; ?>
					
				<?php elseif(102==$result->invID && 1==$result->invSubcatID): ?>
				<?php if(isset($result->invVariant01)): ?>
					<?php $color_options=explode("\n",$result->invVariant01); ?>	
						<select name="color_102" id="color_options_ipad_mini" style="display:<?php echo ((102==$product_id) )?'block':'none' ?>">
							<option value="">Select</option>
							<?php foreach($color_options as $color_option): 
									if($color_option){ ?>
								
							<option <?php echo (trim($color_option)==$this->input->post('color_102'))?'selected="selected"':''; ?> value="<?php echo trim($color_option) ?>"><?php echo $color_option ?></option>
							<?php }  
							endforeach; ?>
						</select>
						<div class="error"><?php echo form_error('color_102','',''); ?></div>
						<div class="error"><?php echo form_error('color_101','',''); ?></div>
					<?php endif; ?>
				
				<?php endif; ?>
		<?php endforeach; ?>		
		</div>
		</li>
				
		
		<li class="upgrade_option">
			<label>Upgrade:<span class="required">*</span></label>
			<div class="formRight">
			<?php $i=0; $j=0; ?>
			<select name="option_id_101" id="ipad_options"  style="display:<?php echo ((''==$product_id) || (101==$product_id) )?'block':'none' ?>">
			<?php foreach($results as $result):?>	
				<?php if(2==$result->invSubcatID ): $i++;?>
					<!--add standard ipad -->
					<?php $selected= ($result->invID==$this->input->post('option_id_101'))?'selected="selected"':''; ?>
					<?php if($i==1):  ?>
					<option value="">Select</option>
					<option <?php echo $selected; ?> value="standard"><?php echo STANDARD_IPAD.'-'.CURRENCY.STANDARD_IPAD_PRICE; ?></option>
					<?php endif;?>
					<option <?php echo $selected; ?>   value="<?php echo $result->invID;  ?>"><?php echo  $result->invName ?>-<?php echo CURRENCY.$result->invPrice1 ?></option>
							
				<?php endif; ?>						
			<?php endforeach; ?>	
			</select>
			
			<select name="option_id_102" id="ipad_options_mini" style="display:<?php echo ((102==$product_id) )?'block':'none' ?>">
			<?php foreach($results as $result): ?>	
				<?php if( 3==$result->invSubcatID):   $j++; ?>
						<?php $selected= ($result->invID==$this->input->post('option_id_102'))?'selected="selected"':''; ?>
						
						<!--add standard ipad -->
						<?php if($j==1):  ?>
							<option value="">Select</option>
							<option <?php echo $selected; ?>   value="standard"><?php echo STANDARD_IPAD.'-'.CURRENCY.STANDARD_IPAD_PRICE; ?></option>
						<?php endif;?>
						<option  <?php echo $selected; ?> value="<?php echo $result->invID;  ?>"><?php echo  $result->invName ?>-<?php echo CURRENCY.$result->invPrice1 ?></option>
						
				<?php endif; ?>
			<?php endforeach; ?>
			</select>
			<div class="error"><?php echo form_error('option_id_102','',''); ?></div>
			<div class="error"><?php echo form_error('option_id_101','',''); ?></div>
		</div>
		</li>			
		
			
	
		<input type="submit" name="btn_submit" value="Submit" class="submit"/>
	</form>
	</div><!--#form-->
<?php endif; ?>