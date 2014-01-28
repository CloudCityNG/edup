<?php 
/**
@Page/Module Name/Class:            manage_questions.php
@Author Name:                       Janet Rajani
@Date:                              Oct, 2 2013
@Purpose:		            display all questions related to one questionnaire
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1>Manage Questions</h1></div>
<div class="backButton clearfix">
    <?php echo anchor('edu_admin/questionnaire/','Back','class="submit"'); ?> 
</div>

<div class="flash_message">
    <?php get_flash_message(); ?>
</div>

<div class="result_container">
    <div class="addRecord">
	 <?php echo anchor('edu_admin/questionnaire/update_questions/'.$questionnaire_id,'Add Questions','class="submit"'); ?> 
    </div>
        <?php if(isset($results) && count($results)>0): ?>
                <form method="post" name="grid-form">
                <input type="hidden" name="mass_action" value="1"/>
                <table class="table striped" cellspacing="0" width="100%" id="grid">
                    <tr>
                        <th >ID</th>
                        <th>Title/Section</th>
                        <th>Questions</th>
                        <th>Order</th>
                        <th></th>
                    </tr>
                    <?php $i=0; ?>
                    <?php foreach($results as $result): ?>
                    <?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
                    <tr class="<?php echo $tr_class; ?>">
                        <td ><?php echo $result->qID; ?></td>
                        <td>
                             <div><?php echo $result->qTitle; ?></div>
                        </td>
                        <td><?php echo $result->qQuestion; ?></td>
                        <td>
                            <input type="text" class="small_box" name="qOrder[<?php echo $result->qID; ?>]" value="<?php echo $result->qOrder;?>">
                        </td>
                        <td>
                            <?php echo anchor('edu_admin/questionnaire/update_questions/'.$questionnaire_id.'/'.$result->qID,'<img src="/images/edit.png" title="edit" alt="edit"/>'); ?>
                            <?php 
                            //If questionnaire is not submitted by any user
                            if($total_submission==0)
                            echo anchor('edu_admin/questionnaire/delete_question/'.$questionnaire_id.'/'.$result->qID,'<img src="/images/delete.png" title="delete" alt="delete"/>','onclick=\'return confirm("Do you want to delete this record?")\''); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                </table>
                <div class="addRecord">
                <input type="submit" class="submit" name="order_submit" value="Save Order" >
                </div>
                </form>	

        <?php else: ?>
                <p class="no_recored_fount">No question found</p>
        <?php endif; ?>
</div>