<?php 
/**
@Page/Module Name/Class:            users_survey_answer.php
@Author Name:                       Janet Rajani
@Date:                              Nov, 19 2013
@Purpose:		            Display survey question and the answer given by this user
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
                                isDisabled:true,
                                onSuccess : function(){
                                  alert('Success : your rate has been saved :)');
                                },
                                onError : function(){
                                  alert('Error : please retry');
                                }
			});
                        
		});</script>
<div class="result_container">
    <div class="publicTitle"><h1><?php 
    //User who fill this questionnaire/survey
    echo $user_name;?></h1></div>
        <?php if(isset($results) && count($results)>0): ?>
                <?php 
                    foreach($results as $result):
                        foreach($result as $result_key=>$result_value): 
                            ?>
                           <div class="reportquestion">
                                    <?php 
                                    //Display question
                                    echo $result_key;?>
                                    </div>
                        <?php if($result_value)
                            {?>
                                <div class="reportanswer"> 
                                    <!--Answer given by this user-->
                                    <?php if(is_numeric(trim($result_value)))
                                        {?>
                                            <div class="star">
                                            <div class="star_rating" data-average="<?php echo $result_value;?>" data-id="<?php echo $result_value;?>">
                                            </div>
                                            </div>
                                    <?php }
                                    else
                                    {
                                        //Answer given by this user
                                        echo $result_value;
                                    }?>
                                </div>
                            <?php 
                            }
                        endforeach;
                    endforeach;?>
        <?php else: ?>
                <p class="no_recored_fount">No record found</p>
        <?php endif; ?>	
</div>