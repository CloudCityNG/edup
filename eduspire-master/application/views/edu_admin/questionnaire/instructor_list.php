<?php 
/**
@Page/Module Name/Class:            instructor_list.php
@Author Name:                       Janet Rajani
@Date:                              Nov, 6 2013
@Purpose:		            All instructor
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="popupTitle"><h1>Instructors</h1></div>
<div class="result_container">
    <form method="post" action="">
    <table border="0" width="100%" id="grid">
        
    <?php 
    if(!empty($results)):
    //Display all comments for this question one by one
    foreach($results as $result)
    {
        ?>
        <tr>
        <td> <input type="checkbox" name="instructor[]" value="<?php echo $result->id;?>" <?php echo ($result->already_assigned_instructor)? 'checked="checked"':''?>><?php echo $result->firstName.' '.$result->lastName;?></td>
        </tr>
    <?php
    }?>
        <tr><td>
        <input type="submit" name="assign_instructor" value="Assign">
            </td></tr>
    <?php
    else:
      ?>
        <tr><td colspan="5" class="no_recored_fount">No record found</td></tr>
        <?php
    endif;
   
    ?>
    </table>
        </form>
</div>