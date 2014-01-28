<script>
	$(document).ready(function(e) {
		$('.bxslider').bxSlider({
		pagerCustom: '#bx-pager',
		auto:true
	
		});
	});
	
</script>

<?php if(isset($slides) && !(empty($slides))): ?>
	 <div class="banner"><!--banner start here-->

		<ul class="bxslider">

			<?php foreach($slides as $slide): ?>
				<li>
				<?php $link = ($slide->csUrl)?$slide->csUrl:'javascript:void(0)'; ?>
				<a href="<?php echo $link; ?>">
				<?php $slide_image= base_url().'/uploads/slide/'.$slide->csImage; ?>
				<?php /*<img src="<?php echo crop_image($slide_image,300,936); ?>" title="<?php echo $slide->csTitle;  ?>"> */?>
				<img src="<?php echo $slide_image; ?>" title="<?php echo $slide->csTitle;  ?>"> 
				
				</a>
				</li>
			<?php endforeach; ?>
		</ul>
		 <div id="bx-pager">
        <div class="section group">
          <div class="col span_4_of_4">
            <ul>
				<?php 
				$i=0;
				foreach($slides as $slide):  ?>
				<li><a data-slide-index="<?php echo $i++ ?>" href="#"><?php echo $slide->csTitle; ?></a></li>
            
			  <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>

	   </div>
    <!--banner ends here-->

<?php endif;?>
 
<div class="section group">
      <div class="col span_4_of_4">
        <ul class="homeImages">
          <li><img src="images/practical.jpg"  alt=""/></li>
          <li><img src="images/cuttingedge.jpg"  alt=""/></li>
          <li><img src="images/gradcource.jpg"  alt=""/></li>
          <li><img src="images/forteacher.jpg"  alt=""/></li>
          <li class="testimonial">
          <p class="home">
              <?php 
              //Janet | 28 Nov | made testimonial dymanic on home page
              //Display testimonial on home page
              if($home_page_testimonial)
              {
                  foreach($home_page_testimonial as $home_page_testimonial_view)
                  {
                    echo strip_slashes($home_page_testimonial_view->tTestimonial); ?> <span><img src="images/arrow.png"  alt=""/></span></p> <em>  &mdash; <?php echo substr($home_page_testimonial_view->firstName,0,1).'. '.substr($home_page_testimonial_view->lastName,0,1).'.'; ?>
                    <p><?php echo anchor(get_seo_url('course',$home_page_testimonial_view->cgID,$home_page_testimonial_view->cgTitle),$home_page_testimonial_view->cgTitle);
                    ?></p>
                    </em> 
          <?php 
                  }
              }
              //End dynamic testimonial?></li>
        </ul>
      </div>
    </div>
    <div class="section group"><!--content of home page div-->
      <div class="col span_1_of_3">
        <div class="featuredCource"><!--course div start here-->
			
		<?php if(isset($featured_courses) && !empty($featured_courses)):  ?>	
          <h1>Featured Courses</h1>
		  <ul>
		  <?php foreach($featured_courses as $course): ?>
				<li id="course-<?php $course->cdID; ?>">
			<?php 
				$image=($course->cgImage)?$course->cgImage:'default.png';
			?>
			<a href="<?php echo get_seo_url('course',$course->cdGenre,$course->cdCourseTitle); ?>">
			<img src="<?php echo base_url().'uploads/course/'.$image ?>" />
			<span><?php echo $course->cdCourseTitle ?></span></a>
			</li>
			
            <?php  endforeach; ?>
            
          </ul>
		  <?php endif; ?>
          <div class="btnCourses"><?php echo anchor('courses/','See More Courses &raquo;'); ?></div>
          <div class="universityLogo">
            <p>Graduate credits are awarded through our partner university:</p>
            <img src="<?php echo base_url(); ?>images/logo_university.gif"  alt=""/></div>
        </div>
        <!--course div ends here--> 
      </div>
      <div class="col span_1_of_3">
        <div class="aboutEduspire"><!--eduspire div start-->
			<?php if(isset($content) && (!empty($content))): ?>
			<h1><?php echo $content->cpTitle; ?></h1>
			<?php echo $content->cpDescription; ?>	
			<?php endif; ?>
		
        </div>
        <!--eduspire div ends--> 
      </div>
      <div class="col span_1_of_3" id="administrator_Right">
        <div class="rightColumn"><!--Right column div-->
          <?php if(isset($content_second) && (!empty($content_second))): ?>
			<h1><?php echo $content_second->cpTitle; ?></h1>
			<?php echo $content_second->cpDescription; ?>	
			<?php endif; ?>
          <div class="updates">
			<?php if(isset($events) && !empty($events)): ?>
            
			<h1><?php echo anchor('/events','Eduspire News','title="Eduspire News"'); ?></h1>
			<?php foreach($events as $event): ?>
            <div class="title">
              <p><strong><a href="<?php echo get_seo_url('event',$event->nwID,$event->nwTitle); ?>"><?php echo $event->nwTitle; ?></a> &mdash; </strong><?php echo format_date($event->nwDate,DATE_FORMAT); ?></p>
              <p><?php echo get_excerpt($event->nwDescription,20); ?></p>
            </div>
                        
			<?php endforeach; ?>
			<?php endif; ?>	
          </div>
                        
        </div>
        <!--right column div ends--> 
      </div>
        <?php 
              //Janet | 11 Dec | made testimonial dymanic on home page
              //Display testimonial on home page
              if($home_page_testimonial)
              {
                  foreach($home_page_testimonial as $home_page_testimonial_view)
                  {?>
                        
                      <div class="testimonials" id="responsive">
                       <p> <?php
                        echo strip_slashes(get_excerpt($home_page_testimonial_view->tTestimonial,175)).'..'; ?> 
                              <span class="arrow"> <img src="images/arrow.png"  alt=""/></span></p>
                       <span>
                        <?php echo substr($home_page_testimonial_view->firstName,0,1).'. '.substr($home_page_testimonial_view->lastName,0,1).'.'; ?>
                    <br><?php echo anchor(get_seo_url('course-registration',$home_page_testimonial_view->csID,$home_page_testimonial_view->cdCourseTitle),$home_page_testimonial_view->cdCourseTitle); ?></span>
                     
                      </div>
          <?php 
                  }
              }
              //End dynamic testimonial?>
    </div>
    <!--content of home page div ends--> 	