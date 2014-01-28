<?php 
/**
@Page/Module Name/Class:            view_users.php
@Author Name:                       Janet Rajani
@Date:                              Nov, 6 2013
@Purpose:		            Display all the users who choses this answer
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>

<div class='result_container'>
    <div class="popupTitle"><h1>Respondents View</h1></div>
   <table class="table striped" cellspacing="0" width="100%" id="grid">
        <form method='post' action=''>
    <?php
    if(!empty($results)):
    //Display all comments for this question one by one
    foreach($results as $result)
    {
        ?>
        <tr><td> <a href='<?php echo base_url().'edu_admin/questionnaire_report/users_survey_answer/'.$qrAssignID.'/'.$result->qrUserID;?>' target='_blank'><?php echo $result->firstName.' '.$result->lastName;?></a></td></tr>
    <?php
    }
    else:
      ?>
        <tr><td class='no_recored_fount'>No response found</td></tr>
        <?php
    endif;
   
    ?>
        </form>
    </table>
</div>