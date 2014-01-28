<script>
jQuery(document).ready(function($) {
	$('#id_district').change(function(){
		window.location.href="<?php echo base_url().'courses/index?district='?>"+$(this).val();
	});
});
</script>

<div class="publicTitle"><h1>Course offerings</h1>
<?php /*
<div class="top-form find-course">
<span>Find Course Near Me:</span>
<span>
<?php 
	$district_array=get_dropdown_array('district',$where_condition=array(),$order_by='disName',$order='ASC','disID','disName','',true,array(''=>'District'));	
	echo form_dropdown('district',$district_array,$district,'id="id_district"');
?></span>
</div>
*/?>
</div>
<?php get_text(6); ?>

<?php if(isset($results) && !empty($results)): ?>
<div class="section group">
<?php $i=0; $horizon_line=0;?>
     <div class="courceOffering">
<?php foreach($results as $course): $horizon_line++; ?>
	<div class="cources">
			<?php $course_title=  (BYOC_ID==$course->cdGenre)?'Build Your Own 3-Credit Course':$course->cdCourseTitle;
				
			?>
			<a href="<?php echo get_seo_url('course',$course->cdGenre,$course_title); ?>">
			<?php 
				$image=($course->cgImage)?$course->cgImage:'courceoffering.gif';
			?>
			<div class="image"><img src="<?php echo base_url().'uploads/course/'.$image ?>"/></div>
		<div class="title"><span>
		<?php echo $course_title; ?>
		</span></div></a>
    </div>
     <?php if(6==$horizon_line):?>
         <div class="line"></div>
      <?php $horizon_line=0; ?>
    <?php  endif; ?>
	
<?php 

endforeach; ?>
            </div>
</div>
<div class="section group">
<?php echo $pagination_links; ?>
</div >
<?php else: ?>
<p class="no-record">No record found</p>
</div>
<?php  endif;?>