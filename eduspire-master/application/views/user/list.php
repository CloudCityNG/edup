<?php 
/**
@Page/Module Name/Class: 		list.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display members list 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:'Members'; ?></h1></div>
<?php if(isset($course) && !empty($course)): ?>
	<h2>
	<?php 
		$course_location=$course->csCity.', '.$course->csState;
			if(COURSE_ONLINE == $course->csCourseType)
				$course_location='Online';
	echo $course->cdCourseID.' '.$course->cdCourseTitle.'('.format_date($course->csStartDate,DATE_FORMAT).'-'.format_date($course->csEndDate,DATE_FORMAT).'-'.$course_location.')'; ?>
	</h2>
	<?php endif; ?>	
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
    <h3>Filters</h3>
	<form name="search-form" action="<?php echo $action ?>">
		
		<input type="hidden" name="course_id" value="<?php echo $this->input->get('course_id'); ?>">
		<input type="hidden" name="ref" value="<?php echo $this->input->get('ref'); ?>">
		<ul class="manageContent">
                    <li><label>User</label>
                            <input type="text" name="name" value="<?php echo $name; ?>"/>
                    </li>
                    <li>
                        <input type="submit" class="submit" value="Search"/>
                        <?php echo anchor($action,'Reset','class="submit"'); ?> 
                    </li>
		</ul>	
	</form>
</div>
<div class="result_container">
        <?php if(isset($results) && count($results)>0): ?>

            <input type="hidden" name="mass_action" value="1"/>
            <table id="grid" class="table striped" cellspacing="0" cellspacing="0" width="100%">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>School District</th>
                    <th>Grade Level/Teaching Discipline</th>
                </tr>
                <?php $i=0; ?>
                <?php foreach($results as $result): ?>
                <?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
                <tr class="<?php echo $tr_class; ?>">
                <td>
                        <?php 
                        $profile_image=( '' != $result->profileImage)?$result->profileImage:'default.jpg';
						if('default.jpg' != $profile_image)
						{
							$image_path=UPLOADS.'/users/'.$profile_image;
							if(!file_exists($image_path))
							{
								$profile_image='default.jpg';
							}
						}	
                        $profile_image=base_url().'uploads/users/'.$profile_image;
						
						
                        ?>
                        <img src="<?php echo crop_image($profile_image); ?>" title="<?php echo $result->firstName.' '.$result->lastName; ?>" alt="<?php echo $result->firstName.' '.$result->lastName; ?>"/>
                </td>
                <td>
                        <div><a href="<?php echo get_seo_url('profile',$result->id,$result->firstName.' '.$result->lastName); ?>"><?php echo $result->firstName.' '.$result->lastName; ?></a></div>
                        <?php echo $this->user_model->show_access_level($result->accessLevel); ?>

                </td>
                <td><?php
				if(is_numeric($result->districtAffiliation)){
					echo get_single_value('district','disName','disID = '.$result->districtAffiliation) ;
				}else{
					 echo $result->districtAffiliation; 
				}
			?></td>

                <td><?php echo ($result->level)?$result->level.'/':'Not applicable/';?>
                        <?php 
						if($result->gradeSubject):
                            echo get_single_value('tracks','trName','trID = '.$result->gradeSubject) ;
                        else:
                                echo 'Not Available';
                        endif;	 ?>
                </td>
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