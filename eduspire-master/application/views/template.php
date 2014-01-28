<?php $this->load->view('includes/header'); ?>
<div id="content" class="section group">
	<?php if(!(isset($layout)) || $layout==''){ $layout='one-column';} ?>
	<?php $this->load->view('template/'.$layout); ?>
</div><!--#content-->
<?php $this->load->view('includes/footer'); ?>

