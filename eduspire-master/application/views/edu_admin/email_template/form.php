<?php 
/**
@Page/Module Name/Class: 		form.php
@Author Name:			 		janet rajani
@Date:					 		Sept, 26 2013
@Purpose:		        		display add/edit form for email template
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="backButton clearfix">
	 <?php echo anchor('edu_admin/email_template/','Back','class="submit"'); ?> 
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
	  <?php echo validation_errors('<p>', '</p>');?>
	   <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" > 
		<ul class="updateForm">
		<li>
		   <label>Title <span class="required">*</span></label>
                   <div class="formRight">
		   <input type="text" name="etTitle" value="<?php echo isset($result->etTitle)?$result->etTitle:$this->input->post('etTitle');?>" />
                   </div>
		</li>
                
		<li>
		   <label>Subject<span class="required">*</span></label>
		   <div class="formRight">
		   <input type="text" name="etSubject" value="<?php echo isset( $result->etSubject ) ? $result->etSubject:$this->input->post('etSubject');?>" />
		   </div>
		</li>
                
                <li>
                    <label>Description<span class="required">*</span></label> 
                    <div class="formRight">
                        <div class="emailEditorContent">
                        <textarea name="etCopy"  class="tinymce_editor" rows="20"><?php echo isset( $result->etCopy ) ? $result->etCopy:$this->input->post('etCopy');?></textarea>
                        </div> 
                    </div>
                </li>
           
           <!--Show list of variables for user can add those variables in email content.-->
                <li>
                <label>
                    Copy and paste the following variables into <br>your email to make it more relevant to your <br>recipients.
                </label>
                    <div class="formRight">
                       <div class="emailContentVariable">
                            [CourseTitle] = Course Title (EDKU 9036: iPads in Education)<br/>
                            [CourseLocation] = Course Location (Central York High School)<br/>
                            [CourseAddress] = Course Address (123 Main St)<br/>
                            [CourseCity] = Course City<br/>
                            [CourseState] = Course State<br/>
                            [CourseZIP] = Course ZIP<br/>
                            [CourseDates] = Course Dates/Times<br/>
                            [CourseEndGuaranteedDate] = Guaranteed End Date<br/>
                            [CourseRegistrationDateStart] = Registration/Guaranteed Payment Window Start<br/>
                            [CourseRegistrationDateEnd] = Registration/Payment Window End<br/>
                            [CoursePaymentDateStart] = Waiting List Payment Window Start<br/>
                            [CoursePaymentLink] = Link to Checkout Page<br/>
                            [UserEmail] = registrant e-mail<br/>
                            [UserName] = registrant name<br/>
							[AccountActivationUrl] = Account Activation url <br/>
							[maximumResponse]  = Response time Limit after which link is expired<br/>
							[RecoverPasswordURL]=Password recovery url		<br/>
							[userName] = User log-in name 	<br/>
							[ExportFinalGradesURL]	= export final grade url <br/>
							[Ipad]= Ipad selected<br/>
							[UpgradeInfo]=Ipad upgrade selected	<br/>						
							[Price]=upgrade price	<br/>
							[CoursePaymentLink]	=Course payment link(when link is sent to  <br/>individuals by admin) 
							[CurrentDate]=Current date	<br/>
							[CourseRegistrant] = Daily registrant details 	<br/>	
                        </div>    
                   </div>
                </li>
		<li>  
		   <label>&nbsp;</label>
		   <div class="formRight">
			<input type="submit" value="<?php echo (isset($result->etID))?'Save':'Create'; ?>" class="submit"/>
		   </div>
		</li>
            </ul>
           
	</form>
</div>
