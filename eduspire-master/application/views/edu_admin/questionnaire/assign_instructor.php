<?php 
/**
@Page/Module Name/Class:            assign_instructor.php
@Author Name:                       Janet Rajani
@Date:                              Nov, 6 2013
@Purpose:		            display all testimonials with course and instructor name
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
<div class="publicTitle"><h1>Assign to Instructor</h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<!--Filter form-->
<div class="top_form">
    <h3>Filters</h3>
	<form name="search-form" action="<?php echo base_url().'edu_admin/questionnaire/assign_instructor' ?>">
            
            <ul class="manageType">
                <li>
                    <label>Course </label><input type="text" name="tCourse" value="<?php echo $tCourse;?>"/>
                </li>
            </ul>
            <div class="formButton">
                    <input type="submit" class="submit" value="Search"/>
                    <?php echo anchor('edu_admin/questionnaire/instructor_list','Reset','class="submit"'); ?> 
            </div>
	</form>
</div>
<!--End Filter form-->
<div class="result_container">
        <?php if(isset($results) && count($results)>0): ?>
                <input type="hidden" name="mass_action" value="1"/>
                <table class="table striped" cellspacing="0" width="100%" id="grid">
                <tr>
                    <th>ID</th>
                    <th >Course</th>
                    <th>Approved by</th>
                    <th>Testimonial</th>
                    <th>Assign</th>
                </tr>
                <?php $i=0; 
                 foreach($results as $result): 
                 $tr_class = ($i++%2==0)?'even':'odd'; ?>
                <tr class="<?php echo $tr_class; ?>">
                    <td><!--Course ID --->
                        <?php echo $result['csID']; ?>
                    </td>
                    <!--Course name--->
                    <td ><?php echo $result['cdCourseID'].'<br/> '.$result['cdCourseTitle']; ?></td>
                    <td>
                        <!--Name of admin who approved it--->
                            <?php echo $result['firstName'].' '.$result['lastName']; ?>
                    </td>
                    <td><!--echo testimonial--->
                        <?php echo $result['tTestimonial']; ?></td>
                    <td><!--click on this link to open instructor name in popup--->
                        <a href="<?php echo base_url(); ?>edu_admin/questionnaire/instructor_list/<?php echo $result['tCourse'].'/'.$result['tID'];?>" class="fancybox" id="comments_box">Instructors</a></td>
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