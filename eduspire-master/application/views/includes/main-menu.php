<?php if(is_logged_in()): ?>
<script>
jQuery(document).ready(function($) {
	selectnav('top_menu'); 
});	

</script>
<?php endif; ?>
	<ul class="clearfix" id="top_menu">
		<?php if(is_logged_in()):?>
		<li class="name">
			<?php echo ($this->session->userdata('display_name'))?'Welcome, '.$this->session->userdata('display_name'):''; ?>	
			
		</li>
		<?php endif; ?>
		<li <?php echo ($this->uri->segment(1)=='courses' )? 'class="active"':'';?>>  <?php echo anchor('courses/','Courses'); ?>
		<?php 
			$course_types = get_content('course_genres','cgID,cgTitle','cgPublish = '.STATUS_PUBLISH,'cgDisplayOrder','ASC'); 
		?>
		<?php if($course_types): ?>
			
			<ul class="sub-menu">
			<?php foreach($course_types as $course_type): ?>
				<li>
				<a href="<?php echo get_seo_url('course',$course_type->cgID,$course_type->cgTitle) ?>" title="<?php echo $course_type->cgTitle; ?>"  ><?php echo $course_type->cgTitle; ?></a>
				</li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		</li>
		<li <?php echo ($this->uri->segment(1)=='about-us' )? 'class="active"':'';?>>  <a href="<?php echo base_url() ?>about-us" >About Us</a>  </li>
		<li <?php echo ($this->uri->segment(1)=='testimonials' )? 'class="active"':'';?>>  <a href="<?php echo base_url() ?>testimonials" >Testimonials</a>  </li>
		<li <?php echo ($this->uri->segment(1)=='events' )? 'class="active"':'';?>>  <a href="<?php  echo base_url()?>events/" <?php echo base_url() ?>testimonials" >News</a>  </li>
		<li <?php echo ($this->uri->segment(1)=='faq' )? 'class="active"':'';?>>  <a href="<?php echo base_url(); ?>faq" <?php echo base_url() ?>testimonials" >FAQ</a>  </li>
		<li <?php echo ($this->uri->segment(1)=='contact' )? 'class="active"':'';?>>  <a href="<?php echo base_url(); ?>contact/" >Contact</a>  </li>
		<?php if(!is_logged_in()): ?>
			<li class="login <?php echo ($this->uri->segment(1)=='login' )? ' active':'';?>"> <?php echo anchor('login/signin','Log In'); ?></li>
		<?php endif; ?>
		<?php if(is_logged_in()): ?>
        <li><?php echo anchor('login/logout','Log Out'); ?></li>
		<?php endif; ?>

	</ul>
	