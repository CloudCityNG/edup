<?php

/**
@Page/Module Name/Class: 		login.php
@Author Name:			 	janet rajani
@Date:					Aug,19 2013
@Purpose:		        	Contain all data management functions for the course schedules
@Table referred:			course_schedule, district, iu_unit, email_templates
@Table updated:				course_schedule
@Most Important Related Files	NIL

*/
/*Chronological development
***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
//***********************************************************************************
//Ref1.	  |  ben binesh		 |  Oct,09 2013  | minor	|  add some consistency checking code 
//Ref2.	  |  ben binesh		 |  Dec,10 2013  | minor	|  add dynamic content to the courser registration thanks page 
//RF3     | Janet Rajani         | Dec 13, 2013  | Major        | Categorized email template into 8 parts 
//***********************************************************************************/


if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Login extends CI_Controller 
{
	public $js;
	public function __construct()
	{
           
		parent::__construct();
                $js=array();
                
		// load form, url, general helper
		// load database user model
		// load library form_validation
		$this->load->model('login_model');
		$this->load->model('permission_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->helper('url');
                $this->load->library('session');
                $this->load->helper('cap');
		 		
	}
	/**
		@Function Name:	index 
		@Author Name:	Janet
 		@Date:		Sep, 4 2013
		@Purpose:	Login form where user can register for a course

	Chronological development
	***********************************************************************************
	Ref No     | Name	        | Date		| Purpose 
	RF1	    | ben binesh	|  Oct 09, 2013	| add some consistency checking code 
	***********************************************************************************/
	public function index($course_id=1)
	{   
                use_ssl(FALSE);
		$this->js[]='js/jquery-ui.js';
		$this->js[]='js/frontend.js';
		
		$course_schedule ='';
   
		$this->load->model('course_schedule_model');
		$this->load->model('course_reservation_model');
		$this->load->model('user_model');
		$this->load->model('order_model');
		$this->load->model('location_model');
		$this->load->model('email_template_model');
		$districts = $this->location_model->get_districts();
		$course_detail = $this->course_schedule_model->get_course_detail($course_id);
		$data['courses'][]=$course_detail;
                if(empty($course_detail))
                {
                    redirect('/');
                }    
	
                /*End course detail*/
		$error = false;
		$errors = array();
		$registration_closed=false;
		$error_message='Registration closed';
		$data['show_pay'] = true;
	
		/*
		Rf1
		*/
		//check for the registration deadline  
		if(0 > date_difference_days(date('Y-m-d'),$course_detail->csStartDate))
        {
			$registration_closed=true;	
			$error_message="The deadline for payment has passed. If you would still like to enroll in the course, please use the 'Contact' menu bar option to email us to see if there is still opportunity for you to enroll";
		}
		
		//check for the registration deadline  
		if(0 > date_difference_days(date('Y-m-d'),$course_detail->csRegistrationEndDate))
        {
			$registration_closed=true;
			$error_message="The deadline for payment has passed. If you would still like to enroll in the course, please use the 'Contact' menu bar option to email us to see if there is still opportunity for you to enroll";
			
		}
		// Course deadline checking 
		$course_total_registrant = $course_detail->registered_count;
		$course_total_enrollee   = $course_detail->enrolees_count;
		
		//check  maximum enrollees limit reached
		if($this->course_schedule_model->check_enrollee_limit($course_detail))
                {
			$error_message='Unfortunately this course is full. Please see our course schedule to find another course that you can take';
			$registration_closed=true;
		}
		
		if($registration_closed)
        {
			set_flash_message($error_message,'error');
			redirect(get_seo_url('course-registration',$course_detail->csID,$course_detail->cdCourseTitle));
		}
			
		if($course_detail->csMaximumEnrollees < $course_total_registrant+$course_total_enrollee)
                {
			$data['show_pay'] = false;
		}
		
		/*
		end Rf1
		*/
		
		//end consistency checking 
		//if form submitted
		if(count($_POST)>0)
		{
                   
                /* Get the actual captcha value that we stored in the session (see below) */
                    //Validate fields
                    $this->form_validation->set_rules('userEmail', 'User Email', 'trim|required|valid_email');
                    $this->form_validation->set_rules('userFirstName', 'User First Name', 'trim|required');
                    $this->form_validation->set_rules('userLastName', 'User Last Name', 'trim|required');
                    $this->form_validation->set_rules('userPhone', 'User Phone', 'trim|required');
                    $this->form_validation->set_rules('dis_iu_unit', 'User Intermediate Unit', 'trim|required');
                    $this->form_validation->set_rules('school_district', 'User School District', 'trim|required');
                 
                    $this->form_validation->set_rules('userDistrictReimburse', 'Reimbursement', 'trim|required');
                    $this->form_validation->set_rules('userCredits', 'Credits', 'trim|required');
                    
                    //RF4
                    $this->form_validation->set_rules('captcha', 'Captcha', 'trim|required|callback_captcha');
                   
                    //End RF4
                    
                    //if a new district is added by user then validate it
                    $this->form_validation->set_message('required', '%s must not be blank');
                    if('0'==$this->input->post('school_district'))
                    {
                       $this->form_validation->set_rules('new_district', 'New District', 'trim|required');
                    }
				
                    /*
                            Rf1
                            //check for user course relation	
                    */
                    if($email=$this->input->post('userEmail'))
                    {
                        if($user=$this->user_model->get_user_byEmail($email))
                        {
                            if(MEMBER != $user->accessLevel)
                            {
                                $error=TRUE;
                                $errors[]='This email address is invalid for registration. Please use different email address.';
                            }
						
                        }
						//check if already registered 

                        if($registeration_details=$this->course_reservation_model->check_registration($course_id,$email))
                        {
                                $error = TRUE;
                                if($this->course_reservation_model->is_waiting($registeration_details))
                                {
                                        //if waiting status 
                                        //now check if the user is eligible to pay for course 
                                        if($this->course_reservation_model->is_allowed_to_pay($course_detail->csPaymentStartDate))
                                        {
                                                $errors[]='Please note that you have already registered for this course with this email address. Go to <a href="'.base_url().'checkout"/>'.base_url().'checkout</a> to make a payment for the course.';
                                        }
                                        else
                                        {
                                                $errors[]="Please note that you have already registered for this course with this email address and you are on the wait list. You will be eligible to enroll in the course after ".format_date($course_detail->csPaymentStartDate,DATE_FORMAT).", along with the other waitlist people on a first-come, first-served basis.";
                                        }

                                }
                                else
                                {
                                        //if guaranteed status
                                        $errors[]='Please note that you have already registered for this course with this email address. Go to <a href="'.base_url().'checkout"/>'.base_url().'checkout</a> to make a payment for the course.';
                                }

                        }
                        elseif($this->course_schedule_model->check_enrollee($course_id,$email))
                        {
                            //check if user is already enrolled
                            $error = TRUE;
                            $errors[]="Please note that you have already paid and are enrolled in this course with this email address";
                            //end checking
                        }
                    }
				
                    /*
                            end Rf1
                    */
		
                    if ($this->form_validation->run() == TRUE && $error==false  )
                    {

                        //format course schedule date and time
                        $course_schedule='';
                        if(count($course_detail->course_dates)>0)
                        {
                            foreach($course_detail->course_dates as $course_dates_detail)
                            { 
                                if(COURSE_OFFLINE == $course_detail->csCourseType)
                                { 
                                    $course_schedule .= format_date($course_dates_detail->csdStartDate,DATE_FORMAT).' (';
                                    $course_schedule .= format_date($course_dates_detail->csdStartTime,TIME_FORMAT).'-';
                                    $course_schedule .= format_date($course_dates_detail->csdEndTime,TIME_FORMAT).')';
                                    $course_schedule .= "\n";
                                }
                                else
                                {
                                    $course_schedule .= format_date($course_dates_detail->csdStartDate,DATE_FORMAT).'-';
                                    $course_schedule .=format_date($course_dates_detail->csdEndDate,DATE_FORMAT);
                                }
                            }
                        }   
					
                        //if district is added by user then make entry in district table
                        if($this->input->post('school_district')==1)
                        {
                            $district_data = array(
                                'disName'=>$this->input->post('new_district'),
                                'disPublish'=>0,
                                'disUserAdded'=>1,
                                'disIuUnit'=>$this->input->post('dis_iu_unit')
                            );
                            $this->location_model->insert_district($district_data);
                            $school_district_value    = $this->db->insert_id();
                        }
                        else 
                        {
                            $school_district_value  = $this->input->post('school_district');
                        }
                        //Enter all data in course_reservation table
                        $waiting = 0;	
                        $user_district_reimburse  = $this->input->post('userDistrictReimburse');
                        $user_credits             = $this->input->post('userCredits');
                        $data_array = array(
                                            'urEmail' => $this->input->post('userEmail'),
                                            'urFirstName' => $this->input->post('userFirstName'),
                                            'urLastName' => $this->input->post('userLastName'),
                                            'urPhone' => $this->input->post('userPhone'),
                                            'urDistrict' => $school_district_value,
                                            'urDistrictReimburse' => $user_district_reimburse,
                                            'urCredits' => $user_credits,
                                            'urCourse' => $course_id,
                                            'urIuID'=>$this->input->post('dis_iu_unit'),
                                            'urDistrictID'=>$school_district_value,
                                            'urTimestamp'=>date('Y-m-d H:i:s'),
                                            );
                        $this->login_model->insert($data_array);
                       //End insertion
                        /*Sending email*/
                        $register                   = $this->input->post('register');
                        $registerAndPay             = $this->input->post('registerAndPay');
                        $user_full_name             = $this->input->post('userFirstName').' '. $this->input->post('userLastName');
                         $current_date = strtotime(date('Y-m-d'));
                         $csPaymentStartDate    = strtotime($course_detail->csPaymentStartDate);
                         $csRegistrationEndDate = strtotime($course_detail->csRegistrationEndDate);
                       
                        //If registered After Guaranteed Deadline(payment start date) and Before Final Deadline(reg end date)
                         if($current_date>$csPaymentStartDate && $current_date==$csRegistrationEndDate)
                        {
                            //RF3
                            //guarantee - Non credit - not reimbursed
                            if(0==$user_district_reimburse && 0==$user_credits)
                            {
                                $email_template_id  = 26;
                            }
                            //guarantee - Non credit - reimbursed

                            elseif(1==$user_district_reimburse && 0==$user_credits)        
                            {
                                $email_template_id  = 27;
                            }
                            //guarantee - credit - not reimbursed
                            elseif(0==$user_district_reimburse && 1==$user_credits)
                            {
                                $email_template_id  = 24;
                            }

                            //guarantee - credit - reimbursed
                            elseif(1==$user_district_reimburse && 1==$user_credits)
                            {
                                $email_template_id  = 25;
                            }
                        }

                        //Guaranteed user (if registered user didn't reach the max enrollee limit)
                        elseif((($course_detail->csMaximumEnrollees)>=($course_total_registrant+$course_total_enrollee)))
                        {
                            //RF3

                            //guarantee - Non credit - not reimbursed

                            if(0==$user_district_reimburse && 0==$user_credits)
                            {
                                $email_template_id  = 8;
                            }

                            //guarantee - Non credit - reimbursed
                            elseif(1==$user_district_reimburse && 0==$user_credits)        
                            {
                                $email_template_id  = 10;
                            }

                            //guarantee - credit - not reimbursed
                            elseif(0==$user_district_reimburse && 1==$user_credits)
                            {
                                $email_template_id  = 5;
                            }

                            //guarantee - credit - reimbursed
                            elseif(1==$user_district_reimburse && 1==$user_credits)
                            {
                                $email_template_id  = 9;
                            }
                        }

                        elseif(($course_detail->csMaximumEnrollees)< $course_total_registrant+$course_total_enrollee)
                        {
                            //waiting - Non credit - not reimbursed
                            if(0==$user_district_reimburse && 0==$user_credits)
                            {
                                $email_template_id  = 12;
                            }

                            //waiting - Non credit - reimbursed
                            elseif(1==$user_district_reimburse && 0==$user_credits)        
                            {
                                $email_template_id  = 15;
                            }

                            //waiting - credit - not reimbursed
                            elseif(0==$user_district_reimburse && 1==$user_credits)
                            {
                                $email_template_id  = 11;
                            }

                            //waiting - credit - reimbursed
                            elseif(1==$user_district_reimburse && 1==$user_credits)
                            {
                                $email_template_id  = 13;
                            }

                            //End RF3
                        }

                       $email_template_data = $this->email_template_model->get_single_record($email_template_id);
                       $user_message        = $email_template_data->etCopy;
                       $subject             = $email_template_data->etSubject;
                        
                        /*Replace all constants of email by the dynamic values*/
                         $email_message_replacement = array(
                            "[UserName]"=>$user_full_name,
                            "[UserEmail]"=>$this->input->post('userEmail'),
                            "[CourseTitle]"=>$course_detail->cdCourseID.': '.$course_detail->cdCourseTitle,
                            "[CourseLocation]"=>$course_detail->csLocation,
                            "[CourseAddress]"=>$course_detail->csAddress,
                            "[CourseCity]"=>$course_detail->csCity,
                            "[CourseState]"=>$course_detail->csState,"[CourseZIP]"=>$course_detail->csZIP,
                            "[CourseEndGuaranteedDate]"=>format_date($course_detail->csPaymentStartDate,'M d, Y'),
                            "[CourseRegistrationDateStart]"=>format_date($course_detail->csRegistrationStartDate,'M d, Y'),
                            "[CourseRegistrationDateEnd]"=>format_date($course_detail->csRegistrationEndDate,'M d, Y'),
                            "[CoursePaymentDateStart]"=>format_date($course_detail->csPaymentStartDate,'M d, Y'),
                            "[CoursePaymentLink]"=>"<a href='".base_url()."checkout'>".base_url()."checkout</a>",
                            "[CourseDates]"=>$course_schedule
                             );

                        $user_confirmation_message =    str_replace(array_keys($email_message_replacement),  array_values($email_message_replacement),$user_message);

                        /*End email constant replacement*/
                        //confirmation email to user
                        if($email_template_data)
                        {
                           //get admin emails 
                            $emails = get_admin_emails();
                            send_mail($this->input->post('userEmail'),SITE_NAME,SENDER_EMAIL,$subject,$user_confirmation_message,$emails);
                        }

                        //If mail send to user, then send an email to admin also
                        if($registerAndPay)
                        {
                            $user_detail  = array(

                                    'email'=>$this->input->post('userEmail')
                            );
                            $this->session->set_userdata( $user_detail);
                            redirect('checkout');
                        }
                        else
                        {
                                redirect('login/thankYou');
                        }

                        /*End email sent*/
                    }
                }
                else
                {
                    if($user_id=$this->session->userdata('user_id'))
                    {
                        $data['user']=$this->user_model->get_single_record($user_id,'u.id,u.userName,u.firstName,u.lastName,u.email,p.phone,p.districtID,p.iuID',true);
                    }
                }
        $data['errors'] = $errors;
        $data['main'] = 'login/login';
        $data['layout']='';
        $data['sidebar']='';
        $this->load->vars($data);
        $this->load->view('template');

        /*End Janet*/
	}
	
        /**
		@Function Name:	captcha
		@Author Name:	Janet Rajani
		@Date:		Dec, 24 2013
		@Purpose:	Captcha verification
	
	*/
	
	function captcha($captcha='')
	{	
                use_ssl(FALSE);
                /* Get the actual captcha value that we stored in the session (see below) */
                $word = $this->session->userdata('captcha_word');
                /* Get the user's entered captcha value from the form */

               //they are equal then it will return 0
                if(strcmp(strtoupper($captcha),strtoupper($word)) == 0)
                {
                    /* Clear the session variable */

                    $this->session->unset_userdata('captcha_word');
                    return TRUE;
		}
		else
		{
                        $this->form_validation->set_message('captcha', 'Invalid captcha');
                        return FALSE;
		}
	}
        
        /**
		@Function Name:	logout 
		@Author Name:	ben binesh
 		@Date:		Sep, 11 2013
		@Purpose:	logout session
	
	*/
	public function signin()
        {
            use_ssl(FALSE);
            $data=array();

            $error = false;
            $errors = array();
            //check of already logged in

            if($this->login_model->is_logged_in())
            {
                    redirect('/');
            }

            if(count($_POST)>0)
            {
                    $this->form_validation->set_rules('username', 'Username', 'trim|required');
                    $this->form_validation->set_rules('password', 'Password', 'trim|required');
                    if ($this->form_validation->run() == TRUE && $error==false  )
                    {	
                            if($user = $this->login_model->check(
                                                            $this->input->post('username'),
                                                            $this->input->post('password')
                                                    ))
                            {
		
                                    //check account activation flag

                                    if(ACCOUNT_ACTIVE == $user->activationFlag)
                                    { 
                                        //if account is active 
                                        //set user data in session 
                                        $session_data = array(
                                        'user_id'  => $user->id,
                                        'email'     => $user->email,
                                        'user_name' =>$user->userName, 
                                        'access_level' =>$user->accessLevel, 
                                        'display_name' =>$user->firstName.' '.$user->lastName ,
                                        'logged_in' => TRUE,
                                        'permission' =>$this->permission_model->get_user_permission_array($user->accessLevel),

                                        );
                                        $this->session->set_userdata($session_data);
                                        //update the last login and login count 
                                        $this->login_model->update_last_login($user->id);

                                        if($redirect  = $this->input->get('redirect'))
                                        {
                                                redirect($redirect);
                                        }
                                        else
                                        {
                                                //redirect specific to the access level 

                                                $access_level = $this->session->userdata('access_level');

                                                switch($access_level)
                                                {
                                                        case INSTRUCTOR:
                                                                redirect('instructor');
                                                        break;

                                                        case MEMBER:
                                                                redirect('member');
                                                        break;

                                                        case ADMIN:
                                                        case MANAGER:

                                                        case SUPER_ADMIN:
                                                                redirect('edu_admin');
                                                        break;

                                                        default:
                                                                //redirect to home page 
                                                                redirect('/');
                                                        break;
                                                }
                                            }
					}
					else
					{
						$errors[]='Your account is not active ';	
					}		
				}
				else
				{
					$errors[]='Invalid Username or Password';
				}
                    }
		}
        $data['meta_title']='Log In';
		$this->page_title='Log In';
		$data['errors']=$errors;
		$data['main'] = 'login';
		$this->load->vars($data);
		$this->load->view('template');
		
	}

        /**
		@Function Name:	logout 
		@Author Name:	ben binesh
 		@Date:		Sep, 11 2013
		@Purpose:	logout session

	*/

	public function logout()
        {
                use_ssl(FALSE);
		$session_data = array(
						'user_id'  => '',
						'email'     => '',
						'access_level' =>'', 
						'display_name' =>'',
						'user_name' =>'',
						'emulate' =>'',
						'logged_in' => FALSE,
						'permission'=>'',
                     );
                //unset session on logout
		$this->session->unset_userdata($session_data);
		redirect('/');		
	}
	/**
		@Function Name:	thankYou 
		@Author Name:	ben binesh
 		@Date:		Sep, 11 2013
		@Purpose:	thanks message

	*/

	/*
		Ref2
	*/
	public function thankYou()
	{
                use_ssl(FALSE);
		$data = array();
		$this->load->model('page_model');
		$data['content'] = $this->page_model->get_single_record(17);
		if(empty($data['content']))
			redirect('home/error_404');
		//meta information
		$data['meta_title']=$data['content']->cpMetaTitle;
		$data['meta_descrption']=$data['content']->cpMetaDescription;
		$this->page_title = $data['content']->cpMetaTitle;
		$data['main']='login/thankyou';
		$this->load->vars($data);
		$this->load->view('template');	
	}

         /**
		@Function Name:	iu_districts 
		@Author Name:	Janet
		@iu_ID:	        | integer | id of Intermediate Unit 
 		@Date:		Sep, 11 2013
		@Purpose:	Load all district of selected IU

	*/

        function iu_districts($iu_ID =0)
        {
            use_ssl(FALSE);
           
$school_district_array=get_dropdown_array('district',$where_condition=array('disIuUnit'=>$iu_ID,'disID != '=>'1','disPublish'=>STATUS_PUBLISH),$order_by='disName',$order='ASC','disID','disName','',true,array(1=>'Other','0'=>'Select'));
           
            echo form_dropdown('school_district',$school_district_array,'');
        }
}
/* End of file welcome.php */