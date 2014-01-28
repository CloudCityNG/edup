<?php 
/**
@Page/Module Name/Class:            index.php
@Author Name:                       Janet Rajani
@Date:                              Jan, 14 2014
@Purpose:		            display all testimonials so that admin can update them
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>

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
            url: '<?php echo base_url().'edu_admin/testimonials/update_comment/';?>'+qrID+'/'+qr,
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
<div class="top_form">
    <h3>Filters</h3>
   <!--Filter start--> 
    <form name="search-form" action="<?php echo base_url().'edu_admin/testimonials/index' ?>">
        <ul class="manageType">
            <li style="width:45%">
                <label>Testimonial</label><input type="text" style="width:60%" name="tTestimonial" value=""/>
            </li>
            
            <li>
        <label>Status</label>
        <?php echo form_dropdown('tStatus',$this->testimonials_model->get_status_array(true,array(''=>''),true),$tStatus); ?></li>
            
        </ul>
        <div class="formButton">
                <input type="submit" class="submit" value="Search"/>
                <?php echo anchor('edu_admin/testimonials/','Reset','class="submit"'); ?> 
        </div>
    </form>
    <!--Filter End--> 
</div>

<div class="result_container">
   <form method="post" action="">
        <table class="table striped" cellspacing="0" width="100%" id="grid">
    <?php 
    //Display all comments for this question one by one
    if(empty($all_comments))
    {
      ?>
        <tr><td colspan="3" class="no_recored_fount">No response found</td></tr>
        </table>
        <?php
    }
    else
    {
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
            $quest_number = explode('qr',$comments['tRefQr']);
            $qr = $quest_number[1];
            //Course detail
            $course_title ='';
            $course_title .=$comments['cdCourseID'].':'.$comments['cdCourseTitle']; 
            $course_title .='(';

            $course_title .=format_date($comments['csStartDate'],DATE_FORMAT);
                        $course_location = $comments['csCity'].', '.$comments['csState']; 
                        if(COURSE_ONLINE==$comments['csCourseType'])
                                $course_location='Online';

            $course_title .= '-'.$course_location;
            $course_title .=')';
            //End course detail
            //User who commented on this question
            //$qr is the field number in questionnaire_results table for answer. Like qr21
            //Show user name with profile link.  Email this user.?>

            <tr><td width="65%">
                <?php echo '<div>'.$course_title.'</div>'.$comments['firstName'].' '.$comments['lastName'];?>
                </td><td>
                    <?php
                echo '<img src="'.base_url().'images/edit.png" class="edit" onclick="javascript:edit_user_comment('.$comments['qrID'].','.$qr.')"> <img src="'.base_url().'images/success.png" class="save" onclick="javascript:save_user_comment('.$comments['qrID'].','.$qr.')">';?>

                <!--Admin can active/deactive this comment-->
                </td><td><?php
                echo '<input name="comment_status_['.$comments['tID'].']" '.$approve.' type="radio" value="2"  /> Approve<input name="comment_status_['.$comments['tID'].']" type="radio" value="1" '.$reject.'/> Reject <a href="'.base_url().'edu_admin/testimonials/instructor_list/'.$comments['tCourse'].'/'.$comments['tID'].'" class="fancybox"><input name="comment_status_['.$comments['tID'].']" type="radio" value="3" '.$inst_approve.' /></a>  Instr. Appr.';?>
                </td></tr>
            <!--Comments given by this user-->
            <tr><td colspan="3">
                    <?php
                    //Display comment as text
                    //qrID is the primary key of questionnaire_result table
            echo '<div id="user_comment_'.$comments['qrID'].'">'.$comments['tTestimonial'].'</div>';
            //Display comment in textarea after admin hit Edit icon
            echo '<div id="user_comment_edit_box_'.$comments['qrID'].'" class="comment_edit"><textarea name="comment'.$comments['qrID'].'" cols="90" rows="3" id="comment_textarea_'.$comments['qrID'].'">'.$comments['tTestimonial'].'</textarea></div>';?>
            </td></tr>
        <?php
        }
       
           //If there are some comments then show this button also
          ?>
            </table>
            <?php echo $pagination_links; ?>
            <div class="pagnination_summary">
                            <?php echo pagination_summary();?>
                    </div>
                <div class="addRecord">
                    <input type="submit" name="process_responses" class="submit" value="Process Responses">
                </div>
            <?php
    }
    ?>
        </form>
</div>