<?php 
/**
@Page/Module Name/Class:            index.php
@Author Name:                       Janet Rajani
@Date:                              Nov, 13 2013
@Purpose:		            testimonials approved by admin and instructor will be listed here for public
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1>Testimonials</h1></div>
<?php get_text(1); ?>
	<div class="testimonialForm">
		 
            <form name="search-form" action="<?php echo base_url().'testimonials/index' ?>" >
                <ul>
               <li>
                    Grade Level
                    
                        <?php 
                         $selected_grade_levels =  ($this->input->get('level' ))? $this->input->get('level'):$level;
                         $grade_level_array = get_grade_level_array(true,array(''=>'All'));
                         echo form_dropdown('level',$grade_level_array,$selected_grade_levels,'id="id_csGenreId"');
                         ?>
                    </li>
               <li>
                   Subject
                    
                         <?php 
                         $selected_grade_subject = ( $this->input->get('gradeSubject' ))?$this->input->get('gradeSubject'):$gradeSubject;
                         $grade_subject_array=get_dropdown_array('tracks',$where_condition=array('cnfID'=>STATUS_PUBLISH),$order_by='nestedMenuOrder',$order='ASC','trID','trName','',true,array(''=>'All'));	
                         echo form_dropdown('gradeSubject',$grade_subject_array,$selected_grade_subject,'id="id_csGenreId"');
                         ?>
                </li>
                <li>  
                   What course are you interested in?
                        <?php 
                        $selected_type = ($this->input->get('course_id'))?$this->input->get('course_id'):$course_id;
                        echo form_dropdown('course_id',$this->testimonials_model->get_course_array(),$selected_type,'id="id_csCourseType"');
					?>
                </li>
                </ul>
                <div class="testimonialButton"> <input type="submit" value="Read from people like me" class="button"></div>
            </form>
	</div>
 
<?php if(isset($results) && count($results)>0): ?>
       <?php foreach($results as $result):?>
		<div class="testimonialSummury">
				<p>
                <?php echo strip_slashes($result->tTestimonial);?></p>
                    <span>&mdash;
                        <?php echo substr($result->firstName,0,1).'. '.substr($result->lastName,0,1).'., '.anchor(get_seo_url('course',$result->cgID,$result->cgTitle),$result->cgTitle);?></span>
               
			</div>
            <?php endforeach;?>
                    <?php echo $pagination_links; ?>
                    <div class="pagnination_summary">
                        <?php echo pagination_summary();?>
                    </div>
<?php else: ?>
       <p class="no_recored_fount">No record found</p>
<?php endif; ?>