<?php 
/**
@Page/Module Name/Class: 	    instructor.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display form to add instructor
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>

<div class="backButton clearfix">
	<ul class="admin-top-links">
	<?php if(isset($course)): ?>
	<li><?php echo anchor('edu_admin/course_schedule/enrollees?course_id='.$course->csID,'Back to Enrollees','class="submit"'); ?> </li>
	<?php else: ?>
	<li><?php echo anchor('edu_admin/user/','Back','class="submit"'); ?> </li>
	<?php endif; ?>
	<ul> 
</div>

<div id="form">
	<div class="error_msg">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" enctype="multipart/form-data" >
	
			<ul class="updateForm">
                          <li>
			   <label>Email <span class="required">*</span></label>
			   <div class="right">
			   <input type="text" name="email" value="<?php echo isset($result->email)?$result->email:$this->input->post('email');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('email','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>First Name <span class="required">*</span></label>
			    <div class="right">
			   <input type="text" name="firstName" value="<?php echo isset($result->firstName)?$result->firstName:$this->input->post('firstName');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('firstName','',''); ?></div>
			   </div>
			</li>
		
			<li>
			   <label>Last Name<span class="required">*</span></label>
			   <div class="right">
			   <input type="text" name="lastName" value="<?php echo isset( $result->lastName ) ? $result->lastName:$this->input->post('lastName');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('lastName','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>School District<span class="required">*</span></label>
			   <div class="right">
			   <input type="text" name="districtAffiliation" value="<?php echo isset( $result->districtAffiliation ) ? $result->districtAffiliation:$this->input->post('districtAffiliation');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('districtAffiliation','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Telephone<span class="required">*</span></label>
			   <div class="right">
			   <input type="text" name="phone" value="<?php echo isset( $result->phone ) ? $result->phone:$this->input->post('phone');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('phone','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Address</label>
			   <div class="right">
			   <input type="text" name="address" value="<?php echo isset( $result->address ) ? $result->address:$this->input->post('address');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('address','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>City</label>
			   <div class="right">
			   <input type="text" name="city" value="<?php echo isset( $result->city ) ? $result->city:$this->input->post('city');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('city','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>State</label>
			   <div class="right">
			   <input type="text" name="state" value="<?php echo isset( $result->state ) ? $result->state:$this->input->post('state');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('state','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>ZIP</label>
			   <div class="right">
			   <input type="text" name="zip" value="<?php echo isset( $result->zip ) ? $result->zip:$this->input->post('zip');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('zip','',''); ?></div>
			   </div>
			</li>
                     </ul>
			<h3>Course Details </h3>		
			<ul class="updateForm">
                          <li>
			   <label>Credit</label>
			   <div class="right">
			   <div><input id="credit" type="checkbox" name="act48"  <?php echo ($this->input->post('act48'))?'checked="checked"':''; ?> value="1" />
			   <label for="credit">Credit</label></div>
			    <div class="error"><?php echo form_error('credit','',''); ?></div>
			   </div>
			</li>
			<?php if(isset($courses) && !empty($courses)): ?>
			<li>
			   <label>Select Membership</label>
			   <div class="right">
			   <div class="scroll">
			<?php 
				
				$course_id = $this->input->post('course');
				foreach($courses as $cor){
				$selected = ($cor->csID==$course_id)?'checked="checked"':'';
				?>
					
					<div><input id="course_<?php echo $cor->csID; ?>" <?php echo $selected; ?>type="radio" name="course"  value="<?php echo $cor->csID ?>" /><label for="course_<?php echo $cor->csID ?>"><?php echo $cor->cdCourseID.' '.$cor->cdCourseTitle.'('.$cor->csStartDate.'-'.$cor->csCity.', '.$cor->csState.')'; ?></label></div>
					
				<?php }?>
			</div><!--scroll-->
			 <div class="error"><?php echo form_error('course','',''); ?></div>
			</div>
			</li>
			<?php endif; ?>	
		
		<?php if(isset($course)):?>
			<li>
			   <label>Courser Membership</label>
			   <div class="right">
				<?php echo $course->cdCourseID.' '.$course->cdCourseTitle.'('.$course->csStartDate.'-'.$course->csCity.', '.$course->csState.')'; ?>
				<input type="hidden" name="course" value="<?php echo $course->csID; ?>"/>
				</div>
			</li>
		<?php endif; ?>		
		
		<li>
		   <label>
                    Form of payment
		   </label>
		   <div class="right">
                    <?php
                        $payment_mode_array=$this->checkout_model->get_payment_mode_array(false);
                        foreach($payment_mode_array as $payment_mode_key=>$payment_mode_value){?>
                                <div> <input type="radio" <?php echo ($payment_mode_key== $this->input->post('payment_mode'))?'checked="checked"':''; ?> name="payment_mode" value="<?php echo $payment_mode_key  ?>"><?php echo  $payment_mode_value;?> </div>
                        <?php } ?>
		    <div>
		   Check Number<br/>
		   <input type="text" name="check_number" value="<?php echo $this->input->post('check_number') ?>" maxlength="255" size="40"/>
		   </div>
		</div>
		</li>
		
		<li>
                    <label>&nbsp;</label>
			<label class="right">
			<input type="checkbox" name="show_receipt" value="1"> Do not create member receipt
		   </label>	
		</li>
		<li>
                    <label>Notes for Self</label>
		   <div class="right">
			<textarea name="manual_comment_self" rows="5"><?php echo $this->input->post('manual_comment_self');?></textarea>
		   </div>
                    </li>
                    <li>
		   <label>Notes for Receipt</label>
		   <div class="right">
			<textarea name="manual_comment" rows="5"><?php echo $this->input->post('manual_comment');?></textarea>
                    </div>
		</li>
			
		<li>  
		   <label>&nbsp;</label>
		    <div class="right">
			<input type="submit"  value="Submit" class="submit"/>
		   </div>
		</li>
             </ul>	
	</form>
</div>