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
<script>
   
    jQuery(document).ready(function($) {
        $(document).on('change',"#iuUnitDropDown",function(){
			$.ajax({
				url: '<?php echo base_url().'login/iu_districts/';?>'+$(this).val(),
				success: function(data) {
					$("#iuBasedDistrictContainer").html(data);
				}	
			});
		});
    });
</script>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="backButton clearfix">
	<ul class="admin-top-links">
	<?php if(isset($course)): ?>
	<li><?php echo anchor('edu_admin/course_schedule/enrollees?course_id='.$course->csID,'Back to Enrollees','class="submit"'); ?> </li>
	<?php else: ?>
	<li><?php echo anchor('edu_admin/user/','Back','class="submit"'); ?> </li>
	<?php endif; ?>
	</ul> 
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
	
			<ul class="updateForm  brdr_btm">
               <li>
			   <label>Email <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="email" value="<?php echo isset($result->email)?$result->email:$this->input->post('email');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('email','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>First Name <span class="required">*</span></label>
			    <div class="formRight">
			   <input type="text" name="firstName" value="<?php echo isset($result->firstName)?$result->firstName:$this->input->post('firstName');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('firstName','',''); ?></div>
			   </div>
			</li>
		
			<li>
			   <label>Last Name<span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="lastName" value="<?php echo isset( $result->lastName ) ? $result->lastName:$this->input->post('lastName');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('lastName','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>School District</label>
			   <div class="formRight">
			   <?php 
				$school_district =isset( $result->districtAffiliation ) ? $result->districtAffiliation:$this->input->post('districtAffiliation');
				if(is_numeric($school_district)){
				$school_district= get_single_value('district','disName','disID = '.$school_district) ;
				}
		   ?>
			   
			   <input type="text" name="districtAffiliation" value="<?php echo $school_district;?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('districtAffiliation','',''); ?></div>
				<div class="hint">Old field</div>
			   </div>
			</li>
			
			<li>
                <label>Intermediate Unit<span class="required">*</span></label>
                <div class="formRight">
                    <?php 
                 $selected_iuUnit = isset( $result->iuID ) ? $result->iuID:$this->input->post('dis_iu_unit');
                 $iu_unit_array=get_dropdown_array('iu_unit',$where_condition=array(),$order_by='iuID',$order='ASC','iuID','iuName','',true,array(''=>'Select'));	
                 echo form_dropdown('dis_iu_unit',$iu_unit_array,$selected_iuUnit,'id="iuUnitDropDown"');
                     ?>
                <div class="error"><?php echo form_error('dis_iu_unit','',''); ?></div>
				</div>
            </li>
            <li>
                <label>School District <span class="required">*</span></label>
                <div class="formRight">
                     <div id="iuBasedDistrictContainer">
                     <?php 
                             $selected_dis_unit =   isset( $result->districtID ) ? $result->districtID:$this->input->post('school_district');
                             $where_condition=array();
							 if($selected_iuUnit !=='')
								$where_condition=array('disIuUnit'=>$selected_iuUnit);
							 $school_district_array=get_dropdown_array('district',$where_condition,$order_by='disID',$order='DESC','disID','disName','',true,array(''=>'Select'));
                             echo form_dropdown('school_district',$school_district_array,$selected_dis_unit); ?>
                     </div>
                     <div class="error"><?php echo form_error('school_district','',''); ?></div>
                </div>
            </li>
			
			
			<li>
			   <label>Telephone<span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="phone" value="<?php echo isset( $result->phone ) ? $result->phone:$this->input->post('phone');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('phone','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>Address <span class="required">*</span></label>
			   <div class="formRight">
			   <textarea name="address"  rows="5"><?php echo isset( $result->address ) ? $result->address:$this->input->post('address');?></textarea>
			   <div class="error"><?php echo form_error('address','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>City <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="city" value="<?php echo isset( $result->city ) ? $result->city:$this->input->post('city');?>" maxlength="255" size="40"/>
			   <div class="error"><?php echo form_error('city','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>State <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="state" value="<?php echo isset( $result->state ) ? $result->state:$this->input->post('state');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('state','',''); ?></div>
			   </div>
			</li>
			
			<li>
			   <label>ZIP <span class="required">*</span></label>
			   <div class="formRight">
			   <input type="text" name="zip" value="<?php echo isset( $result->zip ) ? $result->zip:$this->input->post('zip');?>" maxlength="255" size="40"/>
			    <div class="error"><?php echo form_error('zip','',''); ?></div>
			   </div>
			</li>
			
			
			
            </ul>
			
			<?php if(!$this->new_record): ?>
			<ul class="updateForm">
				<?php if(isset($courses) && !empty($courses)): ?>
					
					<li>
					   <label>Course Membership</label>
					   <div class="formRight">
					   <div class="scroll">
					<?php 
						
						$course_id = $this->input->post('course');
						foreach($courses as $cor){
						$selected = ($cor->csID==$course_id)?'checked="checked"':'';
						$course_location=$cor->csCity.', '.$cor->csState;
						if(COURSE_ONLINE==$cor->csCourseType)
							$course_location='Online';
						?>
							
							<div><input id="course_<?php echo $cor->csID; ?>" <?php echo $selected; ?>type="checkbox" name="courses[]" checked="checked"   value="<?php echo $cor->csID ?>" /><label for="course_<?php echo $cor->csID ?>"><?php echo $cor->cdCourseID.' '.$cor->cdCourseTitle.'('.$cor->csStartDate.'-'.$course_location.')'; ?></label></div>
							
						<?php }?>
					</div><!--scroll-->
					<div class="error"><?php echo form_error('courses','',''); ?></div>
					</div>
					</li>
					
					
				<?php endif; ?>	
				
				<li>
				   <label>Credit</label>
				   <div class="formRight">
				   <?php $credit=isset($result->act48)?(!check_credit($result->act48,$current_course->csID)):$this->input->post('act48'); ?>
				   <div><input id="credit" type="checkbox" name="act48"  <?php echo ($credit)?'checked="checked"':''; ?> value="1" />
				   <label for="credit">Non-Credit ( for course <?php
						if(COURSE_ONLINE==$current_course->csCourseType)
							$course_location='Online';
				    echo $current_course->cdCourseID.' '.$current_course->cdCourseTitle.'('.$current_course->csStartDate.'-'.$course_location.')'; ?>
				   )</label></div>
					<div class="error"><?php echo form_error('credit','',''); ?></div>
				   </div>
				</li>
				</ul>
			<?php else: ?>
							
				<h2>Course Details </h2>		
				<ul class="updateForm">
					<?php if(isset($courses) && !empty($courses)): ?>
				<li>
				   <label>Select Membership</label>
				   <div class="formRight">
				   <div class="scroll">
				<?php 
					
					$course_id = $this->input->post('course');
					foreach($courses as $cor){
					$selected = ($cor->csID==$course_id)?'checked="checked"':'';
					$course_location=$cor->csCity.', '.$cor->csState;
					if(COURSE_ONLINE==$cor->csCourseType)
						$course_location='Online';
					?>
						
						<div><input id="course_<?php echo $cor->csID; ?>" <?php echo $selected; ?>type="radio" name="course"  value="<?php echo $cor->csID ?>" /><label for="course_<?php echo $cor->csID ?>"><?php echo $cor->cdCourseID.' '.$cor->cdCourseTitle.'('.$cor->csStartDate.'-'.$course_location.')'; ?></label></div>
						
					<?php }?>
				</div><!--scroll-->
				 <div class="error"><?php echo form_error('course','',''); ?></div>
				</div>
				</li>
				<li>
				   <label>Credit</label>
				   <div class="formRight">
				   <?php $credit=$this->input->post('act48'); ?>
				   <div><input id="credit" type="checkbox" name="act48"  <?php echo ($credit)?'checked="checked"':''; ?> value="1" />
				   <label for="credit">Non-Credit</label></div>
					<div class="error"><?php echo form_error('credit','',''); ?></div>
				   </div>
				</li>
				
				</ul>
				<?php endif; ?>	
			<?php endif; ?>		
			<ul class="updateForm">
			<?php if(isset($course)):?>
				<li>
				   <label>Courser Membership</label>
				   <div class="formRight">
					<?php 
					$course_location=$course->csCity.', '.$course->csState;
					if(COURSE_ONLINE==$course->csCourseType)
						$course_location='Online';
					echo $course->cdCourseID.' '.$course->cdCourseTitle.'('.$course->csStartDate.'-'.$course_location.')'; ?>
					<input type="hidden" name="course" value="<?php echo $course->csID; ?>"/>
					</div>
				</li>
				
				<li>
				   <label>Credit</label>
				   <div class="formRight">
				   <?php $credit=$this->input->post('act48'); ?>
				   <div><input id="credit" type="checkbox" name="act48"  <?php echo ($credit)?'checked="checked"':''; ?> value="1" />
				   <label for="credit">Non-Credit</label></div>
					<div class="error"><?php echo form_error('credit','',''); ?></div>
				   </div>
				</li>
				
			<?php endif; ?>		
			
			
			
			<?php $show_transaction_form=true; 
				if(!$this->new_record)
				{
					//check the transaction details 
					if(!(isset($transaction) && !empty($transaction)))
						$show_transaction_form=false;
				}
				
			?>
			
			<?php if($show_transaction_form): ?>
				<?php if(isset($transaction->item_name1) && $transaction->item_name1):?>
				<li>
				  
						<h2>Update payment  details for <?php echo $transaction->item_name1; ?>	</h2>
				  
				 </li>  
				<?php endif; ?>
				<li>
				   <label>
							Form of payment
				   </label>
				   <div class="formRight">
							<?php
								$payment_mode_array=$this->checkout_model->get_payment_mode_array(false);
								$payment_mode_selected=isset($transaction->payment_mode)?$transaction->payment_mode:$this->input->post('payment_mode');
								foreach($payment_mode_array as $payment_mode_key=>$payment_mode_value){?>
										<div> <input type="radio" <?php echo ($payment_mode_key== $payment_mode_selected)?'checked="checked"':''; ?> name="payment_mode" value="<?php echo $payment_mode_key  ?>"><?php echo  $payment_mode_value;?> </div>
								<?php } ?>
					<div>
				   Check Number<br/>
				   <input type="text" name="check_number" value="<?php echo isset($transaction->check_number)?$transaction->check_number:$this->input->post('check_number') ?>" maxlength="255" size="40"/>
				   </div>
				</div>
				</li>
				
				<li>
							<label>&nbsp;</label>
					<label class="formRight">
					<?php $show_receipt = isset($transaction->show_receipt)?$transaction->show_receipt:$this->input->post('show_receipt') ?>
					<input type="checkbox" <?php echo ($show_receipt==0)?'checked="checked"':''; ?>    name="show_receipt" value="1"> Do not create member receipt
				   </label>	
				</li>
				<li>
					<label>Notes for Self</label>
				   <div class="formRight">
					<textarea name="manual_comment_self" rows="5"><?php echo isset($transaction->manual_comment_self)?$transaction->manual_comment_self:$this->input->post('manual_comment_self') ?></textarea>
				   </div>
							</li>
							<li>
				   <label>Notes for Receipt</label>
				   <div class="formRight">
					<textarea name="manual_comment" rows="5"><?php echo isset($transaction->manual_comment)?$transaction->manual_comment:$this->input->post('manual_comment') ?></textarea>
							</div>
				</li>
			<?php endif; ?>
			<ul class="updateForm">
			<li>  
		   <label>&nbsp;</label>
		    <div class="formRight">
			<input type="submit"  value="Submit" class="submit"/>
		   </div>
		</li>
             </ul>	
	</form>
</div>