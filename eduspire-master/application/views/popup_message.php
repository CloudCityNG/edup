<script>
jQuery(document).ready(function($) {
	$('#close_fancy').click(function(){
		parent.$.fancybox.close();
	});
	
});
 <?php 
	if(isset($url)){
		echo('top.location.href ="'.$url.'";');
	}	
	if(isset($reload)){
		echo('parent.location.reload(true);');
	}
	
 ?>
</script>
<h1><?php echo $title ?></h1>
<div class="flash_message">
<?php get_flash_message(); ?>
</div> 
<a href="#" id="close_fancy" class="editButton">OK</a>