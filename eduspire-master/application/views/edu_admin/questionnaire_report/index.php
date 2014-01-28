<?php 
/**
@Page/Module Name/Class:            index.php
@Author Name:                       Janet Rajani
@Date:                              Sept, 30 2013
@Purpose:		            display all survey questions
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1>Survey Reports</h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
    <h3>Filters</h3>
   <!--Filter start--> 
    <form name="search-form" action="<?php echo base_url().'edu_admin/questionnaire_report/index' ?>">
        <ul class="manageType">
            <li style="width:45%">
                <label>Assignment Title</label><input type="text" style="width:60%" name="assignTitle" value="<?php echo $assignTitle; ?>"/>
            </li>
        </ul>
        <div class="formButton">
                <input type="submit" class="submit" value="Search"/>
                <?php echo anchor('edu_admin/questionnaire_report/','Reset','class="submit"'); ?> 
            </div>
    </form>
    <!--Filter End--> 
</div>
<div class="result_container">
        <?php if(isset($results) && count($results)>0): ?>
                <input type="hidden" name="mass_action" value="1"/>
                <table class="table striped" cellspacing="0" width="100%" id="grid">
                <tr>
                    <th>Assignment Title</th>
                    <th>Course</th>
                    <th>Due Date</th>
                </tr>
                <?php $i=0; 
                    foreach($results as $result): 
                    $tr_class = ($i++%2==0)?'even':'odd'; 
                    //Course title
                    $course_title ='<b>';
                        if($result->cdCourseID)
                        $course_title .=$result->cdCourseID.':'.$result->cdCourseTitle;
                        if($result->csStartDate)
                        {
                            $course_title .='(';
                            $course_title .=format_date($result->csStartDate,DATE_FORMAT);
                            $course_title .=')</b><br>';
                        }
                        //If offline course then show location,city and state
                        if(($result->csCity)||($result->csState))
                        $course_title .= $result->csLocation.'<br>'.$result->csCity.','.$result->csState;
                        //End course title
                        ?>
                <tr class="<?php echo $tr_class; ?>">
                    <td>
                            <div>
                                <?php echo anchor('edu_admin/questionnaire_report/report_question/'.$result->assignID.'/'.$result->assignQuestionnaire,$result->assignTitle);?>
                               </div>
                    </td>
                    <td><?php echo $course_title; ?></td>
                    <td><?php echo format_date($result->assignDueDate,DATE_FORMAT) ;?></td>
                </tr>
                <?php endforeach; ?>
                
                </table>
                <?php echo $pagination_links; ?>
                <div class="pagnination_summary">
                        <?php echo pagination_summary();?>
                </div>
        <?php else: ?>
                <p class="no_recored_fount">No record found</p>
        <?php endif; ?>
		
</div>