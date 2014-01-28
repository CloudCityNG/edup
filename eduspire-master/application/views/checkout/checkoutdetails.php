<?php 
/*******************************************************************************************************
Page name				checkoutdetails.php
Author Name				alan anil
Purpose 		 		File used for showing no of courses for which user has registered.  
Date			 		26-08-2013
Table Referred		 		N/A
*******************************************************************************************************/
?>
<script>
jQuery(document).ready(function($) {
	$(".fancybox").click(function() {
		$.fancybox.open({
			href : jQuery(this).attr('href'),
			type : 'iframe',
			padding : 5
		});
		return false;
	});
});
</script>
<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="flash_message">
    <?php get_flash_message(); ?>
</div>
<div class="checkout">
<div class="left">
<?php if(isset($content) && !empty($content) ): ?>
<p>
    <?php echo $content->cpDescription;?>
</p>
<?php endif; ?>

<?php $checkCounter = 1; if(isset($results) && (!empty($results)) ): 

		foreach($results as $result): 
		//get course details 
                $course_id  = $result->urCourse;
		 
		$course = $this->course_schedule_model->get_course_detail($course_id);
		if(!empty($course)):
		?>
                <form action="<?php echo base_url().'checkout/pay_for_course' ?>" method="post">
                <div class="discription">
                        <ul>
                            <li>

                              <?php echo $course->cdCourseID ?>: <?php echo $course->cdCourseTitle  ?><br>
                                    <?php  echo format_date($course->csStartDate,DATE_FORMAT);?>

                              <?php if(COURSE_OFFLINE == $course->csCourseType): ?>
                               <br>
                              <?php echo $course->csAddress;?>, <?php echo $course->csCity;?> , <?php echo $course->csState;?>
                              <?php endif; ?>
                            </li>
                                <li> 
                                    <span>
                                      <input type="radio" checked name="item_price" 
                  value="<?php echo $course->csPrice; ?>" onclick='changeValue(<?php echo $course->csPrice; ?>,<?php echo $course_id;?>, <?php echo $course->cgCourseCredits;?>);' />
                                      <?php echo $course->cgCourseCredits;?> Credits (<?php echo CURRENCY; ?><?php echo $course->csPrice; ?>)</span><span>

                  <input type="radio" name="item_price" value="<?php echo $course->csNonCreditPrice; ?>" onclick= 'changeValue(<?php echo $course->csNonCreditPrice; ?>,<?php echo $course_id;?>, "0");' />
                                      0 Credits (<?php echo CURRENCY; ?><?php echo $course->csNonCreditPrice; ?>)</span>
                                </li>
                            </ul>
                            <div class="row">
                                    <?php $is_pay = true; 	
									$payment_error_message='';
                                    //if links comming as individual payment links 
                                    if(isset($this->rid) && $this->rid)
                                    {
                                            $is_pay=true;
                                    }
                                    else
                                    {
                                        //check the maximum enrollees 
										
										if($this->course_schedule_model->check_enrollee_limit($course))
										{
											$payment_error_message='Unfortunately this course is full. Please see our course schedule to find another course that you can take';
											$is_pay=false;
										}else{
											
											//check for the registration deadline  
											if(0 > date_difference_days(date('Y-m-d'),$course->csRegistrationEndDate))
											{
												$is_pay=false;
											  $payment_error_message="The deadline for payment has passed. If you would still like to enroll in the course, please use the 'Contact' menu bar option to email us to see if there is still opportunity for you to enroll. ";
											}
											//check for the waiting list 
											elseif($this->course_reservation_model->is_waiting($result))
											{
												//check for payment start date 
												if(!($this->course_reservation_model->is_allowed_to_pay($result->csPaymentStartDate)))
												{
													$payment_error_message= 'You are currently on the wait list. You will be notified on or after '.format_date($result->csPaymentStartDate,DATE_FORMAT).' that enrollment is open for the wait list if spots are still available at that time';
												   $is_pay=false;
												}
											}
										} ?>
										<span><?php echo $payment_error_message; ?></span>	
									<?php } ?>
                                    
								
                                <span>
                                 <?php 
                                 echo anchor('checkout/unregister/'.$result->uID,'Unregister','class="submit fancybox"');?>
                                </span>
                                    <?php if($is_pay): ?>
                                <span>
                                    
                                        <input type="hidden" name="price" id="price_<?php echo $course_id;?>" value="<?php echo $course->csPrice; ?>" />
                                        <input type="hidden" name="credit_type" id="credit_type_<?php echo $course_id;?>" value="<?php echo $course->cgCourseCredits;?>"/>
                                        <input type="hidden" name="course_res_id"  value="<?php echo $result->uID; ?>" />
                                        <input type="submit" name="pay_button" value="Pay Now" class="submit"/>

                                    
                                </span>
                            <?php endif; ?>
                                </div>
                    </div>
                    </form> 
                    <?php endif; $checkCounter++;?>
                
        <?php endforeach; ?>
        
        <?php else: ?>
        <p class="no-record">No record found </p>
        <?php endif; ?>
        
    </div> 
</div> 