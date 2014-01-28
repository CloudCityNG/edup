
<?php 
/**
@Page/Module Name/Class: 		header.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		common header file 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<?php $this->load->view('includes/head'); ?>
<div id="wrapper" <?php echo (is_allowed('edu_admin/home/emulate'))?'class="greybar"':'';?>>
<?php if(is_logged_in()):?>
<?php  $this->load->view('includes/header-loggedin'); ?> 
<?php else: ?>
<?php  $this->load->view('includes/header-public'); ?> 
<?php endif; ?>
<?php if(is_allowed('edu_admin/home/emulate')):?>
<div class="pageTitle"><!--page title div start-->
   
	<div class="maincontent">
	 <?php /* if(isset($this->page_title) && '' != $this->page_title): ?>
      <div class="col span_4_of_4">
        <div class="title"><?php  echo $this->page_title;?></div>
	</div>
	<?php endif; */ ?>
	
	<?php if(is_allowed('edu_admin/home/emulate') && !isset($this->show_district_filter)): ?>
		<div class="findCources">
			User Emulator
			<form name="search-form" action="<?php echo base_url().'edu_admin/home/emulate'; ?>">
			<input type="text" name="name" placeholder="search user" value=""/><input type="submit" class="search" value=""/>
			</form>
		</div>
	<?php endif; ?>	
	<?php 
		if("" !=  $this->session->userdata('emulate')): ?>
		<div class="findCources">	<?php echo anchor('user/switch_admin','Switch to admin'); ?></div>
		<?php endif; ?>
	</div>
	</div>
<?php endif; ?>	

<div class="maincontent <?php echo (isset($this->page_class))?$this->page_class:'' ?>"><!--main container start-->

