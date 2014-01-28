 <?php
// ********************************************************************************************************************************
//Page name			:- 			letterhead.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			File for showing data on client letterhead.  
//Date				:- 			Dec-18-2013
//Table Refered		:-  		N/A
//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  

//---------------------------------------------------------------------------------------- 
?>
<?php $this->load->view('includes/head'); ?>
<div  class="letterHeadOuter">
<div class="letterHead">
      <h1><img src="/images/letterHeadLogo.png" border="0" alt="Eduspire Logo"></h1> 
    </div>
<div id="content" class="clearfix">
	<?php $this->load->view($main); ?>
</div><!--#content-->
</div>
</div><!--#container-->
</body>
</html>


