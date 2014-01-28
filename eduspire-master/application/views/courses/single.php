<?php 

/**

@Page/Module Name/Class: 		index.php

@Author Name:			 		ben binesh

@Date:					 		Sept, 26 2013

@Purpose:		        		display course landing page 

@Table referred:				NIL

@Table updated:					NIL

@Most Important Related Files	NIL

 Chronological Development

***********************************************************************************

Ref No.  |   Author name	| Date		| Severity 	| Modification description

***********************************************************************************

RF1       | Janet Rajani         | 10 Dec, 2013   | made testimonial dynamic on course definition page 

***********************************************************************************/

?>
<script type="text/javascript">

	jQuery(document).ready(function($) {

		$(".tablesorter")

			.tablesorter({sortList:[[1,0]]})

			//.tablesorterPager({container: $("#pager")});

			

		$(".fancybox").click(function() {

			$.fancybox.open({

				href : jQuery(this).attr('href'),

				type : 'iframe',

				padding : 5

			});

			return false;

		});	

			

	});
	function navigate(e){ 
		window.location.href=e.value
	}
	</script>

	

<?php if(isset($this->bread_crumbs) &&  count($this->bread_crumbs)>1): ?>

<div class="breadcrumb">

<?php echo implode(' &raquo; ',$this->bread_crumbs); ?>

<select class="selectnav" id="selectnav-5" onchange="navigate(this)">	

</select>

</div>

<?php endif; ?>







<div class="courceContent">

<div class="middle">



<span class="thumbnail">

<?php 

$image=($course_genres->cgImage)?$course_genres->cgImage:'dummy.gif';

?>

<img src="<?php echo base_url().'uploads/course/'.$image ?>" alt="<?php echo $course_genres->cgTitle; ?>" class="fLeft"/>

</span>

<h2>

<?php if(BYOC_ID == $course_genres->cgID): ?>

	Build Your Own 3-Credit Course

<?php else: ?>

	<?php echo (isset($course_definition->cdCourseTitle))?$course_definition->cdCourseTitle:''; ?>

<?php endif; ?>

</h2>

<?php echo (isset($course_definition->cdCourseID))?'<h4>'.$course_definition->cdCourseID.'</h4>':''; ?>





<div class="upcomingClasses"><a href="#upcomming-courses">See Upcoming Offerings of this Course</a></div>

<div class="clear"></div>

<div class="error_msg">

	  <?php if(isset($errors) && count($errors)>0 ): 

			foreach($errors as $error){

				echo '<p>'.$error.'</p>';	

			}

		endif; ?>					

</div>

	

<?php if(!empty($course_genres)): ?>

	<?php if('' != $course_genres->cgDescription): ?>

		<div class="content-row course-landing_copy">

			<?php echo $course_genres->cgDescription ?>

		</div>

	<?php endif; ?>	

<?php endif; ?>



<?php if(!empty($course_definition)): ?>



	<?php if($course_definition->cdDescription): ?>

		<div class="content-row course-description">

			<?php echo $course_definition->cdDescription;  ?>	

		</div>

	<?php endif;  ?>

	

	<?php if($course_definition->cdEvaluationMethod): ?>

		<div class="content-row evaluation-method">

			<!--<h3>Evaluation Method</h3>-->

			<?php echo $course_definition->cdEvaluationMethod;  ?>	

		</div>

	<?php endif;  ?>



	<?php if($course_definition->cdGoals): ?>

		<div class="content-row goals">

			<!--<h3>Goals</h3>-->

			<?php echo $course_definition->cdGoals;  ?>	

		</div>

	<?php endif;  ?>

	

	

	

<?php endif; ?>

</div>

</div>

<div class="courceSidebar">

	<select class="selectnav" id="selectnav-4" onchange="navigate(this)">

	

	</select>

          <div class="sidebar">

             <?php if(isset($course_definition->cdOutline)): ?>	

			<div class="curriculum">

             

				<h4>Course Outline</h4>

              <?php echo $course_definition->cdOutline;  ?>	

			 

            </div>

			 <?php endif;  ?>

             <?php 

              //RF1

              //Display testimonial on home page

              if($testimonial)

              {

                  foreach($testimonial as $testimonial_view)

                  {?>

                <div class="testimonials"><p>

                  <?php  

                  echo strip_slashes($testimonial_view->tTestimonial); ?> 

                       <span class="arrow"><img src="/images/arrow.png"  alt="Testimonial"/></span> 

                     </p>   

                        <span>

                        <?php echo substr($testimonial_view->firstName,0,1).'. '.substr($testimonial_view->lastName,0,1).'.';?>

                            <br/>

                        <?php echo anchor(get_seo_url('course',$testimonial_view->cgID,$testimonial_view->cgTitle),$testimonial_view->cgTitle); ?>

                    </span>

                    </div>

                    <?php 

                  }

              }

              //End RF1?>

          </div>

</div>



<div class="section group">

<div id="upcomming-courses">	

	<h2>Upcoming Courses</h2>

	<?php get_text(6); ?>

	<?php if($this->byoc): ?>

		<form name="one_credit_form" method="post">

		<?php if(isset($sessions) && !empty($sessions)): ?>

		<?php foreach($sessions as $session): ?>

			<div>

				<b><?php echo format_date($session->bsStartDate,DATE_FORMAT); ?>-<?php echo format_date($session->bsEndDate,DATE_FORMAT) ?></b>

				<div class="courses">

				<?php 

					$one_credit_courses = $this->course_schedule_model->get_courses(BYOC_ID,'',0,0,-1,'',STATUS_PUBLISH, false , 0 , 0 , $session->bsID);

					if(!empty($one_credit_courses)):?>

						<div class="accordion">

						<?php foreach($one_credit_courses as $one_credit_course): ?>

								

								<h3>

								<input type="checkbox" name="course[<?php echo $one_credit_course->cdID; ?>]" value="<?php echo $one_credit_course->csID; ?>"/>

								<?php //endif; ?>

								<a href="<?php echo get_seo_url('courses/view',$one_credit_course->cdID,$one_credit_course->cdCourseTitle); ?>" class="fancybox" title="Click to see more info"><?php  echo $one_credit_course->cdCourseID; ?> :<?php echo $one_credit_course->cdCourseTitle; ?></a></h3>	

								<div>

								<div>	Registration Due:<?php echo format_date($one_credit_course->csRegistrationEndDate,DATE_FORMAT); ?></div>

								</div>

							

						<?php endforeach; ?>

						</div>

					<?php else: ?>			

					<p class="no-record border">There are currently no offerings of this course, please go to <?php echo anchor('contact/','this page','title="Contact Us"') ?> to be added for our announcements of future courses</p>

					<?php endif;	?>

				</div> 

			</div>

		<?php endforeach; ?>
		<input type="submit" class="submit" value="Register for Selected Course" name="one_credit_submit"/>	
		<?php else: ?>			

					<p class="no-record border">There are currently no offerings of this course, please go to <?php echo anchor('contact/','this page','title="Contact Us"') ?> to be added for our announcements of future courses</p>

		<?php endif;	?>

			

	</form>	

	<?php  else: ?>

<?php if(isset($courses) && !(empty($courses))): ?>

	<table id="grid" class="table striped tablesorter" cellspacing="0" cellpadding="0" width="100%">

	<thead>

	<tr>

		<th>Location</th>

		<th>Course Start Date</th>

		<th>Registration Deadline</th>

	</tr>

	</thead>

	<tbody>

	<?php $i=0; ?>

	<?php foreach($courses as $course): ?>

		<?php $tr_class = ($i++%2==0)?'odd':'even'; ?>

		<tr class="<?php echo $tr_class; ?>">

			<td>

				<a href="<?php echo get_seo_url('course-registration',$course->csID,$course->cdCourseTitle); ?>">

				<?php if(COURSE_ONLINE == $course->csCourseType): ?>

					Online

				<?php  else:?>

					<?php echo $course->csCity.', '.$course->csState; ?>

				<?php endif; ?>

				</a>

			</td>	

			<td><?php echo format_date($course->csStartDate,DATE_FORMAT); ?> <span class="hide">9:12 AM</span></td>

			<td><?php echo format_date($course->csRegistrationEndDate,'M d, Y'); ?> <span class="hide">9:12 AM</td>

			

		</tr>		

	<?php endforeach; ?>

	</tbody>

	</table>

	<?php else: ?>

	<p class="no-record border">There are currently no offerings of this course, please go to <?php echo anchor('contact/','this page','title="Contact Us"') ?> to be added for our announcements of future courses</p>

	<?php endif; ?>

	

	

	

	



<?php endif; ?>

</div><!--#upcomming-courses-->

</div>







