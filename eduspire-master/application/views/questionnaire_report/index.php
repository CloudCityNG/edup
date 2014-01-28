<?php 
/**
@Page/Module Name/Class:            index.php
@Author Name:                       Janet Rajani
@Date:                              Nov, 25 2013
@Purpose:		            display all survey questions of an Instructor
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>

<div class="result_container">
        <?php if(isset($results) && count($results)>0): ?>
                <form method="post" name="grid-form">
                <input type="hidden" name="mass_action" value="1"/>
                <table id="grid" class="table striped" cellspacing="0" cellspacing="0" width="100%">
                <tr>
                    <th>Assignment Title</th>
                    <th>Course</th>
                    <th>Due Date</th>
                </tr>
                <?php $i=0; 
                    foreach($results as $result): 
                    $tr_class = ($i++%2==0)?'even':'odd'; 
                    $course_title ='<b>';
                        if($result->cdCourseID)
                        $course_title .=$result->cdCourseID.':'.$result->cdCourseTitle;
                        if($result->csStartDate)
                        {
                            $course_title .='(';
                            $course_title .=format_date($result->csStartDate,DATE_FORMAT);
                            $course_title .=')</b><br>';
                        }
                        if(($result->csCity)||($result->csState))
                        $course_title .= $result->csLocation.'<br>'.$result->csCity.','.$result->csState;
                        ?>
                <tr class="<?php echo $tr_class; ?>">
                    <td>
                            <div>
                                <?php echo anchor('questionnaire_report/report_question/'.$result->assignID.'/'.$result->assignQuestionnaire,$result->assignTitle);?>
                               </div>
                    </td>
                    <td><?php echo $course_title; ?></td>
                    <td><?php echo date('d M Y',strtotime($result->assignDueDate)) ;?></td>
                </tr>
                <?php endforeach; ?>
                </table>
                </form>	

        <?php else: ?>
                <p class="no_recored_fount">No record found</p>
        <?php endif; ?>
		
</div>


