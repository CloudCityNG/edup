<?php /**@Page/Module Name/Class: 		pay_for_course.php@Author Name:			 		Janet Rajani@Date:					 		Sept, 26 2013@Purpose:		        		display payment form@Table referred:				NIL@Table updated:					NIL@Most Important Related Files	NIL */?><div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>          <div class="checkout">            <div class="left">               <form name="payform" action="" method="post" onsubmit="return validate_card(); ">            <h2>Personal Details</h2>            <ul class="billingInformation">            	<li>                	<label>First Name <input type="text" name="urFirstName" value="<?php  echo isset($urFirstName)?$urFirstName:$this->input->post('urFirstName');?>" maxlength="255" />                        <input type="hidden" name="check_pay_form_validation" value="1" />			</label>                    <label>Last Name<input type="text" name="urLastName" value="<?php  echo isset($urLastName)?$urLastName:$this->input->post('urLastName');?>" maxlength="255" />			</label>                </li>                <li>                	Email Address<input type="text" name="userEmail" value="<?php  echo $this->input->post('userEmail')? $this->input->post('userEmail') :$urEmail;?>" maxlength="255"  readonly/>                </li>            </ul>             <h2>Payments Details</h2>             <ul class="paymentDetails">             	<li>                	<label><span class="required">*</span>Card Type:</label>                        <?php 			$card_type=array('visa'=>'Visa','MasterCard'=>'Master Card');                        $selected_card_type = array('visa'=>'Visa');			echo form_dropdown('cardtype',$card_type,$selected_card_type,'id="card_type"');			?>			 <div class="error"><p><?php echo form_error('cardtype','',''); ?></p></div>                 </li>                <li>                	<label><span class="required">*</span>Cardholder Name:</label>                    <input name="cardholder" id="cardholderName" type="text" value="<?php  echo $this->input->post('cardholder')? $this->input->post('cardholder') :'';?>" class="pmt_required" autocomplete="off">					    <div class="error"><p><?php echo form_error('cardholder','',''); ?></p></div>                </li>                <li>                	<label class="cardNumer"><span class="required">*</span> Card Number:</label>                     <input name="cardnumber" type="text" id="card_num" class="pmt_required" value="<?php  echo $this->input->post('cardnumber')? $this->input->post('cardnumber') :'';?>" autocomplete="off">			<div class="error"><p><?php echo form_error('cardnumber','',''); ?></p></div>                </li>                <li>                	<ul class="chckexpiration">                    	<li class="first">                        	<label><span class="required">*</span>Expiration Date: </label>                             <?php                                  $month_list = get_months_array();                                foreach($month_list as $month_list_key=>$month_list_value)                                {                                    $month_list_final[$month_list_key] = $month_list_value.' ( ' .$month_list_key.' )';                                }                                $yearList = get_years_array(date('Y'),date('Y')+10);                                 echo form_dropdown('cardmonth',$month_list_final). ' '. form_dropdown('cardyear',$yearList);                     ?>                        </li>                        <li class="second">                        	<label><span class="cvc"><span class="required">*</span>CVC</span></label>                        	<input name="cardcvv" id="card_cvv" type="password" value="<?php  echo $this->input->post('cardcvv')? $this->input->post('cardcvv') :'';?>" class="pmt_required" autocomplete="off">                                <div class="error"><p><?php echo $error; ?></p></div>				<div class="error"><p><?php echo form_error('cardmonth','',''); ?></p></div>	                                  <div class="error"><p><?php echo form_error('cardcvv','',''); ?></p></div>                        </li>                    </ul>                </li>                <li class="clear"><input type="submit" name="submit_pay" id="submit_pay" value="Make Payment" class="button"></li>             </ul>             </form>          </div>          </div>       