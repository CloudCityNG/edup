<div class="block clearfix">	
<h3 class="block-title">Status</h3>
<div class="block-content">
<div class="left">
<div class="name"><?php echo $this->session->userdata('display_name'); ?></div>
<div class="username">(<?php echo $user->userName;  ?>)</div>
</div>
<div class="right">
<div class="profile-image">
	<?php 
		$profile_image=( '' != $user->profileImage)?$user->profileImage:'default.jpg';
		$profile_image=base_url().'uploads/users/'.$profile_image;
	?>
	<img src="<?php echo crop_image($profile_image); ?>" title="<?php echo $this->session->userdata('display_name'); ?>" alt="<?php echo $this->session->userdata('display_name'); ?>"/>
</div>	
</div>
</div><!--.block-content-->
</div><!--.block-->


<div class="block clearfix">	
<h3 class="block-title">NAVIGATION</h3>
<div class="block-content">

<h4>Member Tools</h4>
<ul>
	<li><?php  echo anchor('user/profile','My Profile'); ?></li>
	<li><?php  echo anchor('member/receipt','My Receipts'); ?></li>
	<li><?php  echo anchor('#','Final Grades'); ?></li>
</ul>

<h4>Other Tools</h4>
<ul>
	<li><?php  echo anchor('faq','Frequently Asked Questions'); ?></li>
	<li><?php  echo anchor('user','Members Directory'); ?></li>
	<li><?php  echo anchor('events','News'); ?></li>
</ul>
</div><!--.block-content-->
</div><!--.block-->

