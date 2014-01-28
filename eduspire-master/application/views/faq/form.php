<?php 
/**
@Page/Module Name/Class: 		inedx.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display add/edit form for faq
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>

<div class="backButton clearfix">
	 <?php echo anchor('edu_admin/faq/','Back','class="submit"'); ?> 
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
		menubar: false,
		paste_auto_cleanup_on_paste : true,
		skin : "lightgray",
		toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink image jbimages advlist |  preview media fullpage | code ", 
  
	}); 
</script>

<div id="form">
	
	<form class="form" action="" method="post" >
                <ul class="updateForm">	
                   <li>
                    <label>&nbsp;</label>	
                     <label class="right">
                        <div class="error_msg">

                        <?php if(isset($errors) && count($errors)>0 ): 
                                    foreach($errors as $error){
                                            echo '<p>'.$error.'</p>';	
                                    }
                            endif; ?>					
                        </div>
                        </label>
                    </li>	
				
		<li>
		   <label>&nbsp;</label>
		   <label class="right">
			<?php 
				$default_status= ($this->input->post('faqPublish') != '')?$this->input->post('faqPublish'):STATUS_PUBLISH;
				$selectd_status =  isset( $result->faqPublish )?$result->faqPublish:$default_status;
				
				
			?>
			<input type="checkbox" name="faqPublish" value="1" <?php echo (STATUS_PUBLISH==$selectd_status)?'checked="checked"':''; ?>/>
			Publish?
                    </label>
		</li> 
		<li>
		   <label>Question <span class="required">*</span></label>
		   <label class="right">
		   <input type="text" name="question" value="<?php echo isset($result->question)?$result->question:$this->input->post('question');?>" maxlength="255" size="40"/> 
		   <div class="error"><?php echo form_error('question','',''); ?></div>
		   </label>
		</li>
		
		<li>
		   <label>Answer <span class="required">*</span></label>
		   <label class="right">
			<textarea name="answer" class="tinymce_editor" rows="5"><?php echo isset( $result->answer ) ? $result->answer:$this->input->post('answer');?></textarea> 
			<div class="error"><?php echo form_error('answer','',''); ?></div>
		   </label>
		</li>
		
		<li>
		   <label>Display Order <span class="required">*</span></label>
		   <label class="right">
		   <input type="text" name="nestedMenuOrder" value="<?php echo isset($result->nestedMenuOrder)?$result->nestedMenuOrder:$this->input->post('nestedMenuOrder');?>" maxlength="255" size="40"/>
		   <div class="error"><?php echo form_error('nestedMenuOrder','',''); ?></div>
		   </label>
		</li>
		
		<li>
		   <label>Intended Audience</label>
		   <label class="right">
			<?php 
				$selectd_audience =  isset( $result->intendedAudience ) ? $result->intendedAudience:$this->input->post('intendedAudience');
				echo form_dropdown('intendedAudience',$this->faq_model->get_audience_array(),$selectd_audience);
			?>
			<div class="error"><?php echo form_error('intendedAudience','',''); ?></div>
		   </label>
		</li>
		
		<li>
		   <label>&nbsp;</label>
		   <label class="right">
			<input type="submit" value="<?php echo (isset($result->cpID))?'Save':'Create'; ?>" class="submit"/>
			
		   </label>
		</li>
                </ul>
	</form>
</div>