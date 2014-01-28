<?php 
/**
@Page/Module Name/Class:            view_responses.php
@Author Name:                       Janet Rajani
@Date:                              Nov, 25 2013
@Purpose:		            Display all the comments for this question
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="popupTitle"><h1>Responses</h1></div>
<div class="result_container">
<table id="grid" class="table striped" cellspacing="0" cellspacing="0" width="100%"> 
    <?php
    if(empty($all_comments))
    {
        ?>
        <tr><td class="no_recored_fount">No response found</td></tr>
        <?php
    }
    
    //Display all comments for this question one by one
    foreach($all_comments as $comments)
    {
        ?>
        <!--Comments given by this user-->
        <tr><td>
            <?php
            //Display comment 
            echo strip_slashes($comments['qr'.$qr]);
            ?>
        </td></tr>
        <?php
    }
    
    ?>
    </table>
</div>