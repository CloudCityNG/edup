<?php 
/**
@Page/Module Name/Class: 		enroll.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display enroll form
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>
jQuery(document).ready(function($) {
	$('#close_fancy').click(function(){
		parent.$.fancybox.close();
	});
	
});
 <?php 
	if(isset($url)){
		echo('top.location.href ="'.$url.'";');
	}	
	if(isset($reload)){
		echo('parent.location.reload(true);');
	}
	
 ?>
</script>
<div class="popupTitle"><h1> Manual Enroll</h1></div>
<div id="form">
	<div class="row clearfix">			
	<div class="error_msg">
	  <?php echo validation_errors('<p>', '</p>');?>
	   <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	</div>
	<form class="form" action="" method="post" >
		
		<div class="row clearfix">
		   <div class="left_area">&nbsp;</div>
		   <div class="right_area">
			<b><div>Price:<?php echo CURRENCY.$course->csPrice; ?></div>
			<div>Non Credit Price:<?php echo CURRENCY.$course->csNonCreditPrice; ?></div></b>
			<?php 
				$price=$course->csNonCreditPrice;
				if($data->urCredits)
					$price=$course->csPrice;
			?>
			
		   </div>
		</div>
		
		<div class="row clearfix">
		   <div class="left_area">
		   Form of payment<span class="required">*</span>
			<?php 
				$payment_mode_array=$this->checkout_model->get_payment_mode_array(false);
				foreach($payment_mode_array as $payment_mode_key=>$payment_mode_value){?>
					<div> <?php echo form_radio('payment_mode',$payment_mode_key);?><?php echo  $payment_mode_value;?> </div>
				<?php } ?>
			
			
		   </div>
		   <div class="right_area">
		   <div>
		   Price<span class="required">*</span><br/>
		   <input type="text" name="manual_price" value="<?php echo $price;?>" maxlength="255" size="40"/>
			</div>
		    <div>
		   Check Number<br/>
		   <input type="text" name="check_number" value="<?php echo $this->input->post('check_number') ?>" maxlength="255" size="40"/>
		   </div>
		    </div>
		  
		  
		</div>
				
		<div class="row clearfix">
			<div class="left_area">
			<input type="checkbox" name="show_receipt" value="1"> Do not create member receipt
		   </div>	
		</div>
		<div class="row clearfix">
		   <div class="left_area">Notes for Self<br/>
			<textarea name="manual_comment_self" rows="5"><?php echo $this->input->post('manual_comment_self');?></textarea>
		   </div>
		   <div class="right_area">
		   Notes for Receipt<br/>
			<textarea name="manual_comment" rows="5"><?php echo $this->input->post('manual_comment');?></textarea>
			</div>
		   
		</div>
		
				
		<div class="row clearfix">  
		   <div class="left_area">&nbsp;</div>
		   <div class="right_area">
				<input type="submit" name="enroll_submit" value="Enroll" class="submit"/>
				<input type="button" value="Cancel" id="close_fancy" class="submit"/>
		   </div>
		</div>
	</form>
	
</div>