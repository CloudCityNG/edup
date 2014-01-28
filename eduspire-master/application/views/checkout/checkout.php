<?php 
// ********************************************************************************************************************************
//Page name			:- 			checkout.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			View file for checkout process  
//Date				:- 			26-08-2013
//Table Referred		:-  		N/A
//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  

//---------------------------------------------------------------------------------------- 
?>
<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<p>
Here you can enter your email address for checkout.
</p>
<div id="form">  
    <div class="error_msg error">
		<?php echo validation_errors('<p>', '</p>');?>
         					
    </div> 
   <form action=""" method="post" accept-charset="utf-8">  
    <label for="userEmailId" >* E-mail</label>
    <?php $userEmail = array('name' => 'userEmail', 'id' => 'userEmailId');
    echo form_input($userEmail);?>   
    <?php $submitCheckout = array('name' => 'submitCheckout', 'id' => 'submitCheckoutId','class' => 'submit');
    echo form_submit($submitCheckout, 'Checkout'); ?>    
    <?=form_close();?>                   
</div>
 