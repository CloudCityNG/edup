<?php 
/**
@Page/Module Name/Class:            index.php
@Author Name:                       Janet Rajani
@Date:                              Oct, 29 2013
@Purpose:		            display form with question of this questionnaire
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>
$(document).ready(function(){
			
			$('.star_rating').jRating({
				step:true,
				length : 5,
                                onSuccess : function(){
                                  alert('Success : your rate has been saved :)');
                                },
                                onError : function(){
                                  alert('Error : please retry');
                                }
			});
                        
		});
</script>
<div class="publicTitle"><h1>Questionnaires</h1></div>
<div class="result_container">
    <div class="flash_message">
    <?php get_flash_message();?>
    </div>

    <?php if(isset($results) && count($results)>0): ?>
            <form method="post" name="grid-form">
                <?php 
                $answer_array = array();
                foreach($results as $question_and_answer):
                    
                    if((0==$question_and_answer->qOptional) && ($question_and_answer->qType!='section'))
                    {
                        $optional = '<span class="required">*</span>';
                        $optional_value =0;
                    }
                    else
                    {
                        $optional = '';
                        $optional_value =1;
                    }
                    
                    $answer_type = $question_and_answer->qType;?>
                    
                    <div class="questionnaires">
                        <?php echo ($question_and_answer->qQuestion)?'<div class="question">'.$question_and_answer->qQuestion.$optional.'</div>':'<h2>'.$question_and_answer->qTitle.'</h2>';?> 
                    <div class="hint">
                        <?php echo $question_and_answer->qHelp;?>
                    </div>
                    <div>
                                <?php 
                                
                                    switch ($answer_type) 
                                    {
                                        //If the question is drop down type
                                        case 'select':
                                            $answer_exploded = explode("\n",$question_and_answer->qAnswers);
                                            //To force exploded array to start from 1 and not 0
                                            $answer_array = array(''=>'Select')+array_combine(range(1, count($answer_exploded)), $answer_exploded);
                                            echo form_dropdown($question_and_answer->qID,$answer_array);?>
                                            <div class="error"><?php echo form_error($question_and_answer->qID,'','');?></div>
                                <?php
                                            break;
                                            //If the question is a section/heading
                                        case 'section':
                                            ?>
                                            <input type='hidden' name='<?php echo $question_and_answer->qID;?>' value=''>
                                            <?php
                                        break;
                                        //If the question is a text type
                                        case 'text':
                                            ?>
                                            <input type='text' name='<?php echo $question_and_answer->qID;?>' value='<?php echo ($this->input->post($question_and_answer->qID)?$this->input->post($question_and_answer->qID):'');?>'>
                                            <div class="error"><?php echo form_error($question_and_answer->qID,'',''); ?></div>
                                            <?php
                                        break;
                                         //If the question is a rating type
                                        case 'starRating':
                                                ?>
                                               <input type='hidden' name='<?php echo $question_and_answer->qID;?>' id="datas_<?php echo $question_and_answer->qID;?>" >
                                            <div class="star">
                                               <div class="star_rating" data-average="5" data-id="<?php echo $question_and_answer->qID;?>"></div>
                                            </div>
                                            <div class="error"><?php echo form_error($question_and_answer->qID,'',''); ?></div>
                                                <?php 
                                        break;
                                         //If the question is a check-box type
                                        case 'checkboxGroup':
                                            $answer_array = explode("\n",$question_and_answer->qAnswers);
                                            $i =1;
                                            foreach($answer_array as $answer_options):
                                                ?>
                                                 <input type='checkbox' name='<?php echo $question_and_answer->qID;?>' value='<?php echo $i;?>'>
                                                <?php
                                                echo $answer_options;
                                                $i++;
                                            endforeach;?>
                                            <div class="error"><?php echo form_error($question_and_answer->qID,'','');?></div><?php
                                        break;
                                         //If the question is a textarea type    
                                        case 'textarea':?>
                                             <textarea name="<?php echo $question_and_answer->qID;?>"><?php echo ($this->input->post($question_and_answer->qID)?$this->input->post($question_and_answer->qID):'')?></textarea>
                                             <div class="error"><?php echo form_error($question_and_answer->qID,'','');?></div>
                                            <?php
                                        break;
                                         //If the question is a radio buttons type
                                        case 'radioSection':
                                            $answer_array = explode("\n",$question_and_answer->qAnswers);
                                            $i =1;
                                            foreach($answer_array as $answer_options):
                                                ?>
                                            <input type='radio' name='<?php echo $question_and_answer->qID;?>' value='<?php echo $i;?>'>
                                                <?php
                                                echo $answer_options;
                                                $i++;
                                            endforeach;?>
                                            <div class="error"><?php echo form_error($question_and_answer->qID,'','');?></div>
                                                 <?php
                                        break;
                                        default:
                                            ?>
                                            <input type='hidden' name='<?php echo $question_and_answer->qID;?>' value=''>
                                            <?php
                                        break;
                                    }
                               ?>
                            </div>
                       </div>
                    <?php
                endforeach;
                ?>
                <div class="questionnaires">  
                    <input type="submit" value="Submit" class="submit" name="submit_questionnaire"/>
                    <input type="hidden" value="<?php echo $question_and_answer->qCnfID;?>" name="course_id"/>
		</div>
            </form>	
    <?php else: ?>
            <p class="no_recored_fount">No record found</p>
    <?php endif; ?>
</div>