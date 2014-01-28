<?php 
/**
@Page/Module Name/Class:            report_question.php
@Author Name:                       Janet Rajani
@Date:                              Nov, 25 2013
@Purpose:		            display report for the questions related to a questionnaire
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>
jQuery(document).ready(function($) {
   
        $(".fancybox").click(function() {
		$.fancybox.open({
			href : jQuery(this).attr('href'),
			type : 'iframe',
			padding : 5,
                        margin: [115, 0, 10, 0] // top, right, bottom, left
		});
		return false;
                });
 	
});
</script>
<div class="publicTitleQuestionnarie"><?php echo $heading;?></div>
<div class="flash_message">
<?php get_flash_message(); 

    //to save answer choice and total no of times answer chosen
    $question_ans_records=''; ?>
</div>
<div class="result_container">
        <?php if(isset($results) && count($results)>0): ?>
                <form method="post" name="grid-form">
                <input type="hidden" name="mass_action" value="1"/>
               
                <?php $j=0;$k=1;
                
                    foreach($report_answer_responses as $report_answer_response): 
                      
                            $question_ans_records   = array();
                            //Sum of total answer submission for a particular question
                            $total_sum_count        = 0;
                            //Var to save options of answer in case of multi-choice
                            $key_first  ='';
                            //Var for total count of ans given by users for one choice (multi-choice question case)
                            $key_sec    ='';
                            //Var to save the % out of total ansswer submitted for one question
                            $key_third  ='';
                            foreach($report_answer_response as $key=>$value)
                            {
                                    $m=1;
                                    //answer_array contains exploded values of answers field from questionnaire_defs table
                                    //As the answer option were saving as a text record in db, we have to explode those values
                                    foreach($answer_array[$j] as $response_answers)
                                    {
                                       
                                        foreach($value as $ans)
                                        {
                                            if(!empty($response_answers))
                                            {
                                               if(intval($ans['answer']) == intval($m))
                                               {
                                                      $question_ans_records[$response_answers][] =$ans['total_answer_count']; 
                                                     //Sum of all answers count, given by users
                                                     $total_sum_count +=$ans['total_answer_count'];
                                               }
                                               elseif(intval($ans['answer']) != intval($m))
                                               {
                                                   //If no one chooses this answer option
                                                     $question_ans_records[$response_answers][] = ''; 
                                               }
                                            }
                                            else
                                            {
                                                //Check if its not a section then show View Responses only
                                                if(isset($ans['answer']))
                                                {
                                                    //$k is the answer number from question_result table
                                                    $question_ans_records[$response_answers][] =  '<a href="'.base_url().'questionnaire_report/view_responses/'.$ans['qrAssignID'].'/'.$k.'/'.$ans['qrID'].'" class="fancybox" id="comments_box">View Responses</a>';break;
                                                }
                                            }
                                        }
                                        $m++;
                                    }
                                    ?>
                                    <div class="reportquestion">
                                        <?php
                                        echo $key;
                                        ?>
                                    </div><div class="reportanswer"> 
                                 <?php
                                 
                                   //This array contains key=choices of answers in questionnaire_defs table,
                                   //value=the answers count given by students from questionnaire_result table
                                    $ans_opt_position=1; // Answer option position 
                                    foreach($question_ans_records as $ans_key=>$ans_value)
                                    {
                                        //If question is multi choice then display all choices corresponding to that question
                                       $key_first .= '<td>'.$ans_key.'</td>';
                                       if(!traverse_array($ans_value))
                                       {
                                           //check if there is only key and no value in the array
                                            $key_sec .= '<td>0</td>';
                                            $key_third .= '<td>0%</td>';
                                            $ans_opt_position++;
                                       }
                                       else
                                       {
                                           
                                                //Total no of students who choses this answer
                                           
                                                $key_sec .= '<td>'.traverse_array($ans_value).'</td>';
                                                //if a multi-choice answer then calculate the %
                                                if(!array_key_exists('',$question_ans_records))
                                                {
                                                    //percentage of how many users vote this answer
                                                    $percentage_answer = (traverse_array($ans_value) * 100)/$total_sum_count;
                                                    $key_third .= '<td>'.round($percentage_answer).'%</td>';
                                                }
                                                $ans_opt_position++;
                                        }
                                    }
                                    echo '<table width="100%" cellspacing="0" cellpadding="0" border ="0">';
                                    //Print options of answer in case of multi-choice
                                    echo '<tr>'.$key_first.'</tr>';
                                    //Print the count of answers voted by students
                                    echo '<tr>'.$key_sec.'</tr>';
                                    //Print the % of total answers voted
                                    echo '<tr>'.$key_third.'</tr>';
                                    $j++;$k++;   
                            } 
                            ?>	
                            </table> 
                        </div>
            <?php  
                endforeach;  ?>
                </form>	
        <?php else: ?>
                <p class="no_recored_fount">No record found</p>
        <?php endif; ?>		
</div>