<?php
/**
@Page/Module Name/Class:                        checkout.php
@Author Name:			 		Janet Rajani
@Date:					 	Nov, 29 2013
@Purpose:		        		Right side menu of checkout process
*/?>
<div class="checkout">    
<div class="right">
        <ul>
            <li <?php echo (strpos($main, 'checkoutdetails'))?'class="active"':''?> >1. Checkout Details</li>
            <li <?php echo (strpos($main, 'pay_for_course'))?'class="active"':''?> >2. Billing Information</li>
            <li <?php echo (strpos($main, 'thankyou'))?'class="active"':''?>>3. Confirmation</li>
        </ul>
    </div>
    </div>