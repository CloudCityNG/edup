<?php 
/**
@Page/Module Name/Class: 		cronform.php
@Author Name:			 		Alan Anil
@Date:					 		Sept, 07 2013
@Purpose:		        		display add/edit form for cron email template
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
 
?>
<script>
<!--Code for adding editor.-->
    tinymce.init({
		selector: "textarea.tinymce_editor",
		width:630,
		height:375,
		plugins: [
         "advlist autolink link image lists charmap  preview hr anchor pagebreak ",
         "searchreplace wordcount visualblocks visualchars code   media nonbreaking",
         "jbimages code"
		],
		theme: "modern",
		relative_urls : false,
		remove_script_host : true,
		convert_urls : true,
		menubar: false,
		paste_auto_cleanup_on_paste : true,
		skin : "lightgray",
		toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image jbimages advlist |  preview media fullpage | code ", 
  
	}); 
</script> 
<!--Check if title exist for email template.-->
<?php if(isset($result->aeTitle)): ?>
<div class="adminTitle"><h1>Update:<?php echo $result->aeTitle; ?></h1> </div>
<?php endif; ?>
 
<div id="form"> 	
	<div class="error_msg">
    <!--Show validation errors if occured.-->
	  <?php echo validation_errors('<p>', '</p>');?>
	   <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
    <!--Form for showing content of email template.-->
	<form class="form" action="" method="post" >
    	<div class="row clearfix">
        	<input type="checkbox" name="active" value="1"
			<?php if(isset($result->aeActive) && $result->aeActive == 1) {
						echo "checked='checked'";
					}	
			?>><span>Active?</span> 
        </div>
        <div class="row clearfix">
		   <div>Subject<span class="required">*</span></div>
		   <div>
               <input type="text" name="aeSubject"  class="adminEmailSubject"
               value="<?php echo isset( $result->aeSubject ) ? $result->aeSubject:$this->input->post('aeSubject');?>" 
               maxlength="255" size="40"/>
		   </div>
		</div>
        <div  class="row clearfix">
		   <div>Trigger Date <span class="required">*</span></div>
		   <div>
		  	<select name="aeTriggerDateField" id="aeTriggerDateFieldId" class="listMenu">
          		<option value="coRegistrationDateStart" 
                <?php if(isset($result->aeTriggerField) && $result->aeTriggerField == 'coRegistrationDateStart') 
					echo "selected='selected'";?>>
                Registration/Payment Start (Guaranteed Only)
                </option>
                <option value="coPaymentDateStart"
				<?php if(isset($result->aeTriggerField) && $result->aeTriggerField == 'coPaymentDateStart') 
					echo "selected='selected'";?>>
                Registration/Payment Start (All)
                </option>
                <option value="coRegistrationDateEnd"
				<?php if(isset($result->aeTriggerField) && $result->aeTriggerField == 'coRegistrationDateEnd') 
					echo "selected='selected'";?>>
                Registration/Payment End
                </option>
            </select>
		   </div>
		</div>
        <div  class="row clearfix">
		   <div>Trigger Date Offset <span class="required">*</span></div>
		   <div>
		  	 <input type="text" name="aeTriggerDateOffset" 
             value="<?php echo isset( $result->aeTriggerDays ) ? $result->aeTriggerDays:$this->input->post('aeTriggerDateOffset');?>" 
             maxlength="255" size="40"/>
		   </div>
		</div>
        <div  class="row clearfix">
		   <div >Time</div> 
           <div>  
           <?php   
		   //Handle three different fields of time to show selected values otherwise user selected values.
		   $dueMin = '';
           if(isset($result->aeTime)) {
				$end_time = explode(':',$result->aeTime);
				$dampm='am';
				if($end_time[0] > 12 || $end_time[0]=='00' ){
					$dampm='pm';
				}
				if($end_time[0] > 12){
					$end_time[0]=$end_time[0]-12;
				}
				$dueHours = $end_time[0];
				if(isset($end_time[1]) && $end_time[1] != '')
				$dueMin   = $end_time[1];
		   }
		   else { $dueHours = '';$dampm = '';$dueMin = ''; }	
				   echo form_dropdown('aeEmailTime',get_hours_array(true,array('0'=>'-hh-')),$dueHours ); ?>
			<?php  echo form_dropdown('aeEmailMin',get_minute_array(true,array('0'=>'-mm-')) ,$dueMin); ?>
			<?php  echo form_dropdown('aeEmailAP',get_ampm_array(true,array('0'=>'-a/p-')),$dampm ); ?>
		   </div>   
		</div>
        <div  class="row clearfix">
		   <div>E-mail Target <span class="required">*</span></div>
		   <div>
		  	<select name="aeTarget" id="aeTarget" class="listMenu">
                <option value="all" <?php if(isset($result->aeTarget) && $result->aeTarget == 'all') 
					echo "selected='selected'";?>>All
                </option>
                <option value="guaranteed" <?php if(isset($result->aeTarget) && $result->aeTarget == 'guaranteed') 
					echo "selected='selected'";?> >Guaranteed
                </option>
                <option value="waiting" <?php if(isset($result->aeTarget) && $result->aeTarget == 'waiting') 
					echo "selected='selected'";?>>Waiting
                </option>
            </select>
		   </div>
		</div>
		 
        <div class="row clearfix mainCronMessageDiv"> 
		   <div class="emailCronEditorContent">
            <div>Message<span class="required">*</span></div> 
            <textarea name="aeCopyMessage" class="tinymce_editor" rows="5"><?php 
				echo isset( $result->aeCopy ) ? $result->aeCopy:$this->input->post('aeCopyMessage');?>
            </textarea>
		   </div> 
		   <div>
           <!--Show list of variables for user can add those variables in email content.-->
           <div class="emailCronContentVariableDiv">
           Copy and paste the following variables into your email to make it more relevant to your recipients.
           </div>
               <div class="emailCronContentVariable emailContentVariable" style="height:460px;">
                    [Field:coTitle] = Course Title (EDKU 9036: iPads in Education)<br/>
                    [Field:coLocation] = Course Location (Central York High School)<br/>
                    [Field:coAddress] = Course Address (123 Main St)<br/>
                    [Field:coCity] = Course City<br/>
                    [Field:coState] = Course State<br/>
                    [Field:coZIP] = Course ZIP<br/>
                    [Field:coDates] = Course Dates/Times<br/> 
                    [Field:coRegistrationDateStart] = Registration/Guaranteed Payment Window Start<br/>
                    [Field:coRegistrationDateEnd] = Registration/Payment Window End<br/>
                    [Field:coPaymentDateStart] = Waiting List Payment Window Start<br/> 
                    [Field:urEmail] = registrant e-mail<br/>
                    [Field:urName] = registrant name<br />
                    [Field:paymentLink] = payment link
                </div>    
		   </div>
		</div> 
		<div class="row clearfix adminEmailSubmitButton">  
		   <div class="left_area">&nbsp;</div>
		   <div class="right_area">
           <!--Show submit button and cancel link.-->
           <?php echo anchor('edu_admin/email_template/','cancel','class="submit"'); ?>
			<input type="submit" value="Save" class="submit"/> 
		   </div>
		</div>
	</form>
</div> 