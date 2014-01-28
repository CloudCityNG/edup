<?php 
/**
@Page/Module Name/Class:            update_questions.php
@Author Name:                       Janet Rajani
@Date:                              Oct, 1 2013
@Purpose:		            Add/Edit questions in a Questionnaire
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1>Update Questions</h1></div>
<div id="form">
	<form class="form" action="" method="post">
		
		<ul class="updateForm">
                 <li>
		   <label>Question type <span class="required">*</span></label>
		   <div class="formRight">
			<?php 
                                $default_status= ($this->input->post('qType') != '')?$this->input->post('qType'):'';
                                $selectd_status =  isset( $result->qType )?$result->qType:$default_status;
                                echo form_dropdown('qType',$this->questionnaire_model->get_question_type(array(''=>'Select')),$selectd_status);
			?>
                       <div class="error"><?php echo form_error('qType','','');?></div>
		   </div>
		</li> 
	
            <li>
		   <label>Title <span class="required">*</span></label>
		   <div class="formRight">
				<input type="text" name="qTitle" value="<?php echo isset($result->qTitle)?$result->qTitle:$this->input->post('qTitle');?>" />
                                <div class="hint">Mandatory in case of section question type</div>
                                <div class="error"><?php echo form_error('qTitle','','');?></div>
		   </div>
                   
		</li> 
		<li>
		   <label>Question <span class="required">*</span></label>
		   <div class="formRight">
			<input type="text" name="qQuestion" value="<?php echo isset($result->qQuestion)?$result->qQuestion:$this->input->post('qQuestion');?>" />
                        <div class="error"><?php echo form_error('qQuestion','','');?></div>
		   </div>
		</li> 
		
		<li>
		   <label>Help Text </label>
		   <div class="formRight">
                            <textarea name="qHelp" rows="3"><?php echo isset( $result->qHelp ) ? $result->qHelp:$this->input->post('qHelp');?></textarea>
		   </div>
		</li> 
		
		<li>
		   <label>Answers <span class="required">*</span></label>
		   <div class="formRight">
                            <textarea name="qAnswers" rows="3"><?php echo isset( $result->qAnswers )? $result->qAnswers:$this->input->post('qAnswers');?></textarea>
                            <div class="hint">Mandatory in case of check-box, drop-down, star-rating and radio question type</div>
                            <div class="error"><?php echo form_error('qAnswers','','');?></div>
		   </div>
		</li> 
                
            <li>
                <label>&nbsp;</label>
		   <div class="formRight">
                           <?php  
                           //If editing question, then check if it was optional or not
                           echo form_checkbox(array('name' => 'qOptional', 'id' => 'userCreditsId'),'1', (isset($result->qOptional) && $result->qOptional==1)?TRUE:FALSE);?> Optional?
		   </div>
            </li>
            <li>
                <label>&nbsp;</label>
		   <div class="formRight">
                          <input type="submit" value="Submit" class="submit"/>
                          <?php echo anchor('edu_admin/questionnaire/manage_questions/'.$questionnaire_id,'Cancel','class="submit"'); ?>
		   </div>
		</li>
              </ul>
	</form>
</div><!--End id=form -->