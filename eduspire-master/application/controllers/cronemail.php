<?php
// ********************************************************************************************************************************
//Page name			:- 			cronemail.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			File using for cronscript for sending emails to users.  
//Date				:- 			11-10-2013
//Table Refered		:-  		N/A
//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  

//---------------------------------------------------------------------------------------- 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cronemail extends CI_Controller {
	
	public $js;
	protected $_id;
	public function __construct() 
	{
		parent::__construct(); 
                use_ssl(FALSE);
		$js=array();
		$this->load->helper('common'); 
		$this->load->helper('url'); 
		$this->load->model('cron_model');
		$this->load->model('assignment_model');	 
	}
	
	/**
		@Function Name:	index
		@Author Name:	Alan anil	
		@Date:			Sep, 03 2013 
		@Purpose:		Index function call when script run.
	
	*/
	public function index()
	{   		
		$data                    = array();	
		$enrolledUser            = array();
		$registeredUser          = array(); 
		$givenDateValue          = '';
		$waitingUsers            = '';
		$targetUserType          = ''; 
		date_default_timezone_set('EST');
		$currentDate             = date("Y-m-d"); 
		//$currentDate             = "2014-02-01";
		$currentTime             = date("H:i").':00';
		//$currentTime             = "17:00:00";
		$this->load->model('course_reservation_model');
		$this->load->model('cron_model');
		$data['emailTemplates']  = $this->cron_model->get_email_templates(); 
		$this->load->model('course_schedule_model');
		// Send emails to user by getting email templates one by one. 
		foreach($data['emailTemplates'] as $emailTemplate) 
		{
			$emailLogStatus = '';// For adding log in auto_email_log table.
			echo "email template id = ".$emailTemplate->aeID; echo "<br>";
			$targetUserType = $emailTemplate->aeTarget;	
			// Check guaranteed / waiting / all
			if($targetUserType == 'guaranteed')  
			{
				$waitingUsers = 'guaranteed';
			}
			else if($targetUserType == 'waiting')
			{
				$waitingUsers = 'waiting';
			}
			else
			{
				$waitingUsers = 'all';
			}
			echo 'Email trigger for user type = '.$waitingUsers; echo "<br>";
			// Fetch user on the basis of user type.
			$getFinalUserList = $this->course_reservation_model->get_reminder_email_list($waitingUsers);
			   
				 echo "<pre>";
				 print_r($getFinalUserList);
				 echo "</pre>";
			   
			// Fetching users details which have to send emails.
			foreach($getFinalUserList as $userDetailList) 
			{
				// Fetch user courses.
				 $paymentStartDate    = '';
				 $course_detail       = $this->course_schedule_model->get_course_detail($userDetailList['course'],true);
				  //echo "<pre>";print_r($course_detail);echo "<pre>";
				$userDetails = $this->cron_model->get_registered_users($userDetailList['email']);
				 echo "<pre>";
				 print_r($userDetails);
				 echo "</pre>";
				 //Section to find actual trigger dates for sending emails to users.
				 $templateTriggerDays = $emailTemplate->aeTriggerDays;
				 $triggerDaysArr      = explode(",",$templateTriggerDays);   
				 // Payment start date.
				 $paymentStartDate      = $course_detail->csPaymentStartDate; 
				 $csRegistrationEndDate = $course_detail->csRegistrationEndDate;
				 $csStartDate           = $course_detail->csStartDate;
				  
				
				 $templateTriggerField = $emailTemplate->aeTriggerField;
				 if($templateTriggerField == 'coRegistrationDateEnd') 
				 {
				 	$givenDateValue = $csRegistrationEndDate;
				 }
				 else if($templateTriggerField == 'coDate1')
				 {
				 	$givenDateValue = $csStartDate;
				 }
				 else
				 {
				 	$givenDateValue = $paymentStartDate;
				 } 
				 
				 foreach($triggerDaysArr as $triggerEmailDate)
				 {
				 	$newTriggerDate = '';
					
					echo "paymentStart/registrationStart/registrationEnd Date = ".$givenDateValue; echo "<br>";
					//echo $triggerEmailDate; echo "<br>"; 
					$givendate = $givenDateValue;
					$day       = $triggerEmailDate;
					$cd        = strtotime($givendate);
					$mth=0;$yr=0;
					$newTriggerDate = date('Y-m-d', mktime(date('h',$cd),
									  date('i',$cd), date('s',$cd), date('m',$cd)+$mth,
								      date('d',$cd)+$day, date('Y',$cd)+$yr));
					
					$templateTriggerTime = $emailTemplate->aeTime;
					
					echo "email trigger date= ". $newTriggerDate; echo "<br>";
					echo "Today date = ".$currentDate ; echo "<br>";	
					echo "currentTime = " .$currentTime;echo "<br>";
					echo "email triggerTime = ".$templateTriggerTime;echo "<br>";
					
					if($newTriggerDate == $currentDate)
					{  echo "Email trigger date and current date are equal";echo "<br>";
						if($currentTime == $templateTriggerTime)
						{ 
							echo "Date and time are checked "."<br>";
							//Check course registration date.
							if($course_detail->csRegistrationEndDate  >= $currentDate):
							 echo "courseRegistrationEndDate is checked ".'<br>';
							//Section Format course schedule date and time
								$course_schedule = ''; 
								if(count($course_detail->course_dates) > 0) { 
									foreach($course_detail->course_dates as $course_dates_detail) { 
										if(COURSE_OFFLINE == $course_detail->csCourseType) { 
											$course_schedule .= format_date($course_dates_detail->csdStartDate,DATE_FORMAT).' ('; 	
											$course_schedule .= format_date($course_dates_detail->csdStartTime,TIME_FORMAT).'-'; 
											$course_schedule .= format_date($course_dates_detail->csdEndTime,TIME_FORMAT).')'; 
											$course_schedule .= "<br>"; 
										} 
										else { 
											$course_schedule .= format_date($course_dates_detail->csdStartDate,DATE_FORMAT).'-'; 	
											$course_schedule .=format_date($course_dates_detail->csdEndDate,DATE_FORMAT); 
											$course_schedule .= "<br>";
										} 
									} 
								}
								//End of section Format course schedule date and time
								
								echo "email send to user = ".$userDetails['urEmail']; echo "<br>"; 
								$user_message        = $emailTemplate->aeCopy;
								$subject             = $emailTemplate->aeSubject;  
								//Replace all constants of email by the dynamic values
								$email_message_replacement = array( 
								"[Field:urName]"=> $userDetails['urFirstName'].' '.$userDetails['urLastName'],
								"[Field:coTitle]"=> $course_detail->cdCourseID.': '.$course_detail->cdCourseTitle,
								"[Field:coLocation]"=> $course_detail->csLocation,
								"[Field:coAddress]"=> $course_detail->csAddress,
								"[Field:coCity]"=> $course_detail->csCity,
								"[Field:coState]"=> $course_detail->csState, 
								"[Field:coZIP]"=> $course_detail->csZIP,
								"[Field:coDates]"=> $course_schedule,
								"[Field:coPaymentDateStart]"=> format_date($course_detail->csPaymentStartDate,DATE_FORMAT),
								"[Field:enrollees]"=> $course_detail->csMaximumEnrollees,
								"[Field:act48]"=> $course_detail->csNonCreditPrice,
								"[Field:coPriceFull]"=> $course_detail->csPrice,
								"[Field:coPriceAct80]"=> $course_detail->csNonCreditPrice ,
								"[Field:coRegistrationDateEnd]" => format_date($course_detail->csRegistrationEndDate,DATE_FORMAT),
								"[Field:coRegistrationDateStart]" => format_date($course_detail->csRegistrationStartDate,DATE_FORMAT),
								"[Field:urEmail]" => $userDetails['urEmail'],
								"[Field:paymentLink]" => "<a href='".base_url()."checkout'>"."Click Here</a>"
								); 
								$user_message =  str_replace(array_keys($email_message_replacement),  
								array_values($email_message_replacement) ,$user_message);
								 $user_confirmation_message    = $user_message;
								
								//End email constant replacement
								//confirmation email to user  
								 // ADMIN_EMAIL for admin email
								 // SENDER_EMAIL for sender email address
								//get admin emails 
								$admin_emails = get_admin_emails();
								$admin_emails=array_merge($admin_emails,array('alan.anil@ithands.net'));
			 			 		send_mail($userDetails['urEmail'], SITE_NAME, SENDER_EMAIL, $subject, 
						 		$user_confirmation_message, $admin_emails); 
							    $emailLogStatus = 'Sent';
								
								//send_mail('alan.anil@ithands.net', SITE_NAME, SENDER_EMAIL, $subject, 
						 		//$user_confirmation_message.$userDetails['urEmail'], ''); 
							     
								
							else:
								 echo "registration date is gone";echo "<br>";	echo "<br>";
							endif; 
							echo "Next Record"; echo "<br>";
						}
					}
			 	   //endif;
				} 
			} 
			 // Adding entry in auto_email_table.
				if($emailLogStatus == 'Sent')
				{
					$data_array = array(
								'aelEventID'=>$emailTemplate->aeID,
				                'aelDate' => date("Y-m-d h:i:s")  
								); 	
					$this->cron_model->add_auto_email_log($data_array); 
				}
		} 
		
		// For sending daily report to admin start.
		$currentTimeToMatch = date("H:i");
		//$currentTimeToMatch = "23:00";
		$timeToMatch        = "23:55";
		if($currentTimeToMatch == $timeToMatch)
		{
			echo "DAILY REPORT HAS BEEN SENT";
			send_mail('alan.anil@ithands.net', SITE_NAME, SENDER_EMAIL, 'Daily summary report', 
						 		 'Daily summary report has been sent', ''); 
			$this->course_reservation_model->send_daily_registrants_email();
		}
		// For sending daily report to admin end.
	}
	/**
		@Function Name:	add_date
		@Author Name:	Alan anil	
		@Date:			Sep, 03 2013 
		@Purpose:		function used to get date from specified days/months of current date.
	
	*/
	function add_date($givendate,$day=0,$mth=0,$yr=0) 
	{
		$cd = strtotime($givendate);
		$newdate = date('Y-m-d', mktime(date('h',$cd),
		date('i',$cd), date('s',$cd), date('m',$cd)+$mth,
		date('d',$cd)+$day, date('Y',$cd)+$yr));
		return $newdate;
    }
}	
 
// 	LOTS OF COMMENTED CODE AND ECHO PRESENT IN FILE THESE LINES FOR TESTING THE SCRIPT BY RUN IT MANUALLY.

 
?>