<?php 
/**
@Page/Module Name/Class: 		login.php
@Author Name:			 		janet rajani
@Date:					 		Sept 24, 2013
@Purpose:		        		display sinlge assignment details 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
***********************************************************************************
Chronological development
***********************************************************************************
| Ref No.  |   Author name	| Date		| Severity 	| Modification description
***********************************************************************************
RF1.       |  ben binesh   |  Oct,11 2013  | minor  |  add form action attribute and add support to the registeration to multiple course ids
RF2       |  Janet Rajani  |  Dec,24 2013  | minor  |  Added captcha */
?>
<script>
   
    jQuery(document).ready(function($) {
        $(document).on('change',"#iuUnitDropDown",function(){
			$.ajax({
				url: '<?php echo base_url().'ajax/iu_districts/';?>'+$(this).val(),
				success: function(data) {
					$("#iuBasedDistrictContainer").html(data);
				}	
			});
		});
    });
</script>
<div class="publicTitle"><h1>Course Registration</h1></div>
<?php if(isset($courses) && !empty($courses)): ?>
<?php foreach($courses as $course): ?>
	<h2><?php 
		 $course_location=$course->csCity.', '.$course->csState;
			if(COURSE_ONLINE == $course->csCourseType)
				$course_location='Online';
	echo $course->cdCourseID.' '.$course->cdCourseTitle.' ('.format_date($course->csStartDate,DATE_FORMAT).'-'.format_date($course->csEndDate,DATE_FORMAT).'-'.$course_location.')'; ?></h2>
	<?php endforeach; ?>
<?php endif; ?>
<div id="form">
	
	<div class="error_msg error">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="<?php echo isset($action)?$action:''; ?>" method="post">
        <?php 
		
		//Rf1
		if(isset($ids))
                {?>
                    <input type="hidden" name="ids" value="<?php echo $ids; ?>">  
		<?php 
                }
		//end rf1
		?>
            <ul class="updateForm">
                <li>
                    <label><span class="required">*</span> All fields are required.</label>
                    <div class="formRight"></div>
                </li>
                <li>
                       <label>E-mail <span class="required">*</span></label>
		   <div class="formRight">
			<input type="text" name="userEmail" value="<?php echo isset($user->email)?$user->email:$this->input->post('userEmail'); ?>" maxlength="255" size="40"/>
                        <div class="error"><?php echo form_error('userEmail','',''); ?></div>
                        <div class="hint">
                           <?php get_text(2); ?>
                         </div>
		   </div>
		</li> 
		
		<li>
		   <label>First Name <span class="required">*</span></label>
		   <div class="formRight">
			<input type="text" name="userFirstName" value="<?php echo isset($user->firstName)?$user->firstName:$this->input->post('userFirstName');?>" maxlength="255" size="40"/>
			 <div class="error"><?php echo form_error('userFirstName','',''); ?></div>
		   </div>
		</li> 
		
		<li>
		   <label>Last Name <span class="required">*</span></label>
		   <div class="formRight">
                       <input type="text" name="userLastName" value="<?php echo isset($user->lastName)?$user->lastName:$this->input->post('userLastName');?>" maxlength="255" size="40"/>
                        <div class="error"><?php echo form_error('userLastName','',''); ?></div>
		   </div>
		</li>
		
            <li>
                <label>Phone <span class="required">*</span></label>
                <div class="formRight">
                    <input type="text" name="userPhone" value="<?php echo isset($user->phone)?$user->phone:$this->input->post('userPhone');?>" maxlength="255" size="40"/>
                     <div class="error"><?php echo form_error('userPhone','',''); ?></div>
                </div>
            </li>
		
            <li>
                <label>Intermediate Unit Your School Is In <span class="required">*</span></label>
                <div class="formRight">
                    <?php 
                 $selected_iuUnit =  isset($user->iuID)?$user->iuID:$this->input->post('dis_iu_unit');
                 $iu_unit_array=get_dropdown_array('iu_unit',$where_condition=array(),$order_by='iuID',$order='ASC','iuID','iuName','',true,array(''=>'Select'));	
                 echo form_dropdown('dis_iu_unit',$iu_unit_array,$selected_iuUnit,'id="iuUnitDropDown"');
                     ?>
                     <div class="error"><?php echo form_error('dis_iu_unit','',''); ?></div>
					 <div class="hint">Not sure what IU you are in...<?php echo anchor('http://www.dot.state.pa.us/Internet/Bureaus/pdPlanRes.nsf/infoBPR_Education_PA_Intermediate_unit','click','target="_blank"'); ?> to see the IU map</div>
                </div>
            </li>
            <li>
                <label>School District <span class="required">*</span></label>
                <div class="formRight">
                     <div id="iuBasedDistrictContainer">
                     <?php 
                             $selected_dis_unit =   isset($user->districtID)?$user->districtID:$this->input->post('school_district');
                             $school_district_array=get_dropdown_array('district',$where_condition=array('disPublish'=>STATUS_PUBLISH),$order_by='disName',$order='ASC','disID','disName','',true,array('0'=>'Other',''=>'Select'));
                             echo form_dropdown('school_district',$school_district_array,$selected_dis_unit); ?>
                     </div>
                     <div id='insert_new_district'>
                        <input type='text' name='new_district' value="<?php echo $this->input->post('new_district');?>">
                     </div>

                     <div class="error"><?php echo form_error('school_district','',''); ?></div>
                     <div class="error"><?php echo form_error('new_district','',''); ?></div>
                </div>
            </li>
		
            <li>
                <label>
                    My school district will reimburse me for the cost of this course <span class="required">*</span>
                </label>
                <div class="formRight"> 
                     <?php echo form_radio(array('name' => 'userDistrictReimburse','id' =>'userDistrictReimburseId'),'1', FALSE);?> Yes <?php echo form_radio(array('name' => 'userDistrictReimburse','id' => 'userDistrictReimburseId'), '0', FALSE);?> No
                     <div class="error"><?php echo form_error('userDistrictReimburse','',''); ?></div>
                </div>
            </li> 
		
            <li>   
                <label>I am taking this course for credit <span class="required">*</span></label>
                <div class="formRight">
                    <?php echo form_radio(array('name' => 'userCredits', 'id' => 'userCreditsId'), '1', FALSE);?> Yes <?php echo form_radio(array('name' => 'userCredits', 'id' => 'userCreditsId'), '0', FALSE);?> No
                    <div class="error"><?php echo form_error('userCredits','',''); ?></div>
                </div>
            </li>
         
            <li>   
                <label>Verification Code <span class="required">*</span></label>
                <div class="formRight">
                    <?php
                    //RF2
                    $vals = array(
                            'word'          => random_string('alnum', 5),
                            'img_path'      => UPLOADS.'/captcha/',
                            'img_url'       => base_url().'uploads/captcha/',
                            'font_path'     => base_url().'system/fonts/texb.ttf',
                            'img_width'     => '120',
                            'img_height'    => '28',
                            'expiration'    => '3600'
                    );
                    $cap = create_captcha($vals);
                    $this->session->set_userdata('captcha_word',$cap['word']);
                    
                    ?>
                     <input id="captcha" name="captcha" type="text" />
                     <span class="captcha">
                     <?php echo $cap['image']; ?>
                     </span>
                    <div class="error"><?php echo form_error('captcha','',''); ?></div>
                    <!--End RF2--> 
                </div>
            </li>
           
            <li>
                <label>&nbsp;</label>
                <div class="formRight">
                     <input type="submit" value="Register" name="register" class="submit"/>
                     <input type="submit" value="Register & Pay" name="registerAndPay" class="submit"/>
                </div>
            </li> 
           </ul>
	</form>
</div><!--End id form-->