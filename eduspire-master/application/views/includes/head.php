<?php 
/**
@Page/Module Name/Class: 		head.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		common head 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<!DOCTYPE html>
<!-- HTML5 Boilerplate -->
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

<head>
	<meta charset="utf-8">
	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width">
	<title><?php echo (isset($meta_title) && $meta_title!='')?$meta_title.'-'.SITE_NAME:SITE_NAME; ?></title>
	<!-- Responsive stuff -->
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<link rel="icon" type="image/png" href="<?php echo base_url() ?>images/favicon.png" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/html5reset.css" media="all">
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/style.css" media="all">
	<?php if(is_logged_in()): ?>
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/admin.css" media="all">
	<?php endif; ?>
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/col.css" media="all">
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/responsivenavigation.css">
	<!-- Responsive Stylesheets -->
<link rel="stylesheet" media="only screen and (max-width: 1024px) and (min-width: 769px)" href="<?php echo base_url(); ?>css/1024.css">
<link rel="stylesheet" media="only screen and (max-width: 768px) and (min-width: 481px)" href="<?php echo base_url(); ?>css/768.css">
<link rel="stylesheet" media="only screen and (max-width: 480px)" href="<?php echo base_url(); ?>css/480.css">
	
	
	<script src="<?php echo base_url(); ?>js/jquery.min.js"></script>
	<script src="<?php echo base_url(); ?>js/navigation.js"></script>
	<!--inlcude page specific js and css here -->
	<?php if( isset( $this->js ) && count( $this->js ) > 0 ): ?>
		<?php foreach($this->js as $js): ?>
			<script type='text/javascript' src="<?php echo base_url().$js; ?>"></script>
		<?php endforeach; ?>
	<?php endif; ?>
	
	<?php if( isset( $this->css ) && count( $this->css ) > 0 ): ?>
		<?php foreach($this->css as $css): ?>
			<link href="<?php echo base_url().$css; ?>" rel="stylesheet" type="text/css" />
		<?php endforeach; ?>
	
	<?php endif; ?>
	<meta name="description" content="<?php echo ( isset( $meta_description ) && $meta_description != '' )?$$meta_description:SITE_DESCRIPTION; ?>" />
    <script type="text/javascript">
	$(document).ready(function() {
		var winheight = $(window).height();
        $('#wrapper').css({'min-height': winheight-150})
		$('.info-text-container').hover(function(){
			$('.info-text-container .edit_link').toggle();
		});
    });
</script>
	</head>
<body>
