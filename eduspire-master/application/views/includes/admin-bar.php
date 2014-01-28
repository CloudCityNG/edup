<?php 
/**
@Page/Module Name/Class: 		admin-bar.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display admin bar at the top for quick links to admin access 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<?php

	if(ADMIN == $this->session->userdata('access_level') || "" !=  $this->session->userdata('emulate')):?>
	<div id="eduadminbar" class="" role="navigation">
	<div class="admin-nav">
	<ul>
		
		
		<?php if(is_logged_in()):?>

			<?php if(ADMIN == $this->session->userdata('access_level')): ?>
				<li>
					<?php  echo anchor('edu_admin','Manage'); ?>
					<ul class="sub-menu">
						<li><?php  echo anchor('edu_admin/page','Manage Content'); ?></li>
						<li><?php  echo anchor('edu_admin/slide','Manage Slides'); ?></li>
						<li><?php  echo anchor('edu_admin/course_genres','Manage Course Types'); ?></li>
						<li><?php  echo anchor('edu_admin/course_definition','Manage Course Definition'); ?></li>
						<li><?php  echo anchor('edu_admin/course_schedule','Manage Course schedule'); ?></li>
					</ul>
				<li>
			<?php endif; ?>
		<?php if("" ==  $this->session->userdata('emulate')): ?>
		<li><?php echo anchor('edu_admin/home/emulate','Emulate User'); ?></li>
		<?php endif; ?>	
		<li>  <a href="<?php echo base_url() ?>login/logout">Logout</a>  </li>
		<?php endif; ?>

	</ul>
	
	<?php 
		if("" !=  $this->session->userdata('emulate')):
			echo anchor('user/switch_admin','Switch to admin');
		endif;
	?>
	</div>		
	</div><!--#eduadminbar-->
	
	<?php endif;?>