<div id="headcontainer"><!--header container start-->
	<div class="maincontent ">
	<header>

	<div class="section group"><!--content of home page div-->

	<div class="col span_1_of_4">
		<div class="logo"><a href="<?php echo base_url();?>" title="Eduspire Home"><img src="<?php echo base_url(); ?>images/logo.png" border="0" alt="Eduspire Logo"> </a></div>
	</div>
	<div class="col span_3_of_4">
      <div class=" navigation">
		<nav id="nav" role="navigation"> <a href="#nav" title="Show navigation">Show navigation</a> <a href="#" title="Hide navigation">Hide navigation</a>
	  <?php  $this->load->view('includes/main-menu'); ?>  
	  </nav>
	</div>
    </div>
    </div>
	
	</header>
	</div>
</div><!--header container end-->