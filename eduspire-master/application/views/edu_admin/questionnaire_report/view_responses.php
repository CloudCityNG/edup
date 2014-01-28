<?php 
/**
@Page/Module Name/Class:            view_responses.php
@Author Name:                       Janet Rajani
@Date:                              Oct, 16 2013
@Purpose:		            Display all the comments with details of user
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>
    <?php 
    //Refresh popup
	if(isset($url)){
		echo('top.location.href ="'.$url.'";');
	}	
	if(isset($reload)){
		echo('parent.location.reload(true);');
	}
	
 ?>
    $(document).ready(function(){
             
        $(".fancybox").click(function() {
		$.fancybox.open({
			href : jQuery(this).attr('href'),
			type : 'iframe',
                        padding:5,
                        minHeight: 300
		});
		return false;
        });
                 $('.comment_edit').hide();
    });

    function edit_user_comment(qrID,qr)
    {
        $("#user_comment_"+qrID).hide();
        $("#user_comment_edit_box_"+qrID).show();
    }
    function save_user_comment(qrID,qr)
    {
        $("#user_comment_"+qrID).show();
        $("#user_comment_edit_box_"+qrID).hide();
        $.ajax({
            type:'POST', 
            url: '<?php echo base_url().'edu_admin/questionnaire_report/update_comment/';?>'+qrID+'/'+qr,
            data:$('#comment_textarea_'+qrID).serialize(),
            success: function(data) {
           
                $("#user_comment_"+qrID).html(data);
                    }	
        });
    }
	</script>
<div class="popupTitle"><h1>Manage testimonials</h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="result_container">
   <form method="post" action="">
        <table class="table striped" cellspacing="0" width="100%" id="grid">
    <?php
    //Display all comments for this question one by one
    foreach($all_comments as $comments)
    {
        $approve ='';
        $reject='';
        $inst_approve='';
        $status =  $comments['tStatus'];
        if($status==2):
            $approve = 'checked="checked"';
        elseif($status==1):
            $reject = 'checked="checked"';
        elseif($status==3):
            $inst_approve = 'checked="checked"';
        endif;
        //User who commented on this question
        //$qr is the field number in questionnaire_results table for answer. Like qr21
        //Show user name with profile link.  Email this user.?>
            
        <tr><td>
            <?php echo '<a href="'. get_seo_url('profile',$comments['id'],$comments['firstName'].' '.$comments['lastName']).'" target="_blank">'.$comments['firstName'].' '.$comments['lastName'].'</a>';?>
            </td><td>View Questionnaire</td><td>
                <?php echo '<a href="'.base_url().'edu_admin/questionnaire_report/view_response_email/'.$comments['id'].'" class="fancybox" id="email_box">Email</a>';
            ?>
            </td><td>
                <?php
            echo '<img src="'.base_url().'images/edit.png" class="edit" onclick="javascript:edit_user_comment('.$comments['qrID'].','.$qr.')"> <img src="'.base_url().'images/success.png" class="save" onclick="javascript:save_user_comment('.$comments['qrID'].','.$qr.')">';?>
   
            <!--Admin can active/deactive this comment-->
            </td><td><?php
            echo '<input name="comment_status_['.$comments['qrID'].']" '.$approve.' type="radio" value="2"  /> Approve<input name="comment_status_['.$comments['qrID'].']" type="radio" value="1" '.$reject.'/> Reject <a href="'.base_url().'edu_admin/questionnaire_report/instructor_list/'.$comments['tCourse'].'/'.$comments['tID'].'" class="fancybox"><input name="comment_status_['.$comments['qrID'].']" type="radio" value="3" '.$inst_approve.' /></a>  Instr. Appr.';?>
            </td></tr>
        <!--Comments given by this user-->
        <tr><td colspan="6">
                <?php
                //Display comment as text
                //qrID is the primary key of questionnaire_result table
        echo '<div id="user_comment_'.$comments['qrID'].'">'.$comments['qr'.$qr].'</div>';
        //Display comment in textarea after admin hit Edit icon
        echo '<div id="user_comment_edit_box_'.$comments['qrID'].'" class="comment_edit"><textarea name="comment'.$comments['qrID'].'" cols="90" rows="3" id="comment_textarea_'.$comments['qrID'].'">'.$comments['qr'.$qr].'</textarea></div>';?>
        </td></tr>
    <?php
    }
    if(empty($all_comments))
    {
      ?>
        <tr><td colspan="6" class="no_recored_fount">No response found</td></tr>
        </table>
        <?php
    }
   else
    {
       //If there are some comments then show this button also
      ?>
        </table>
            <div class="addRecord">
                <input type="submit" name="process_responses" class="submit" value="Process Responses">
            </div>
        <?php
    }
    ?>
        </form>
</div>