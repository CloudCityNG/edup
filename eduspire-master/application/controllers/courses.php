<?php
/**
@Page/Module Name/Class: 		courses.php
@Author Name:			 		ben binesh
@Date:					 		Aug, 19 2013
@Purpose:		        		Contain all controller functions for course
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
Chronological Development
***********************************************************************************
Ref No.  |   Author name	| Date		| Severity 	| Modification description
***********************************************************************************
RF1.	  |  ben binesh		 |  Oct,10 2013  | minor	|  add some function related the the build your own course 
RF2       | Janet Rajani         | 28 Nov, 2013   | made testimonial dynamic on course page
RF3       | Janet Rajani         | 10 Dec, 2013   | made testimonial dynamic on course definition page 
***********************************************************************************/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Courses extends CI_Controller {
	
	public $bread_crumbs=array();
	public $byoc = 0;
	public $current_genre=0;
	
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
                //RF2
                $this->load->model('testimonials_model');
                //End RF2
		$this->bread_crumbs[]=anchor(base_url(),SITE_NAME);
	
	}	
	/**
		@Function Name:	index
		@Author Name:	ben binesh
		@Date:			Oct 29, 2013
		@return  none
		@Purpose:	load the all current courses 
	
	*/
	function index(){
		$data=array();
		$this->load->model('course_definition_model');
		$this->load->helper('form');
		$data['main']     = 'courses/index';
		$data['district'] = $this->input->get('district');
		$location='';
		if($data['district']){
			//get the IU  corresponding to the district
			$location =get_single_value('district','disIuUnit','disID = '.$data['district']) ;	
		}
				
		$num_records          = $this->course_definition_model->count_courses('',0,$location,0,STATUS_PUBLISH);
		$base_url             = base_url().'courses/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$this->page_title="Course offerings";
		$data['meta_title']="Course offerings";
		$this->show_district_filter = true;
		$per_page            = PER_PAGE; 
		$data['results'] = $this->course_definition_model->get_courses('',0,$location,0,STATUS_PUBLISH,$start,$per_page);
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());
		
		$this->load->vars($data);
		$this->load->view('template');
	
	}
	
	
	public function single($title=0,$genre_id=0)
	{
		$data=array();
		$errors=array();
		if(!$genre_id){
			show_404();
		}
		$this->js[]='js/jquery.tablesorter.min.js';
		$this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]='js/fancybox/source/jquery.fancybox.css';
		//$this->js[]='js/jquery-ui.js';
		$this->current_genre=$genre_id;
		$data['main']   = 'courses/single';
		$data['layout'] = 'two-column-left';
		$this->load->model('course_genres_model');
		$this->load->model('course_definition_model');
		$this->load->model('course_schedule_model');
                $this->load->model('testimonials_model');
		$data['course_genres'] = $this->course_genres_model->get_single_record($genre_id,'cgID,cgTitle,cgDescription,cgImage');
		if(empty($data['course_genres'])){
			redirect('home/error404');
		}
		//bread crubms 
		$this->bread_crumbs[]= anchor(base_url().'courses','Courses');
		$this->bread_crumbs[] = '<span>'.$data['course_genres']->cgTitle.'</span>';
		
		$this->page_title='Course Details';
		$this->page_class="courceDetail";
		$data['meta_title'] = $data['course_genres']->cgTitle;
		
		if(BYOC_ID == $genre_id){
			//if course is build your own course
			$this->byoc       = $genre_id;
			$data['sessions'] = $this->course_definition_model->get_sessions('',0,-1,TRUE);
			
		}else{
			
			$data['course_definition'] = $this->course_definition_model->get_landing_page($genre_id);
			//get the upcoming courses display in sidebar
			$data['courses'] = $this->course_schedule_model->get_courses($genre_id,'','',0,-1,date('Y-m-d'),STATUS_PUBLISH,TRUE,0,0,0, FALSE,FALSE,'csEndDate','ASC');
		}
		//RF3
                $data['testimonial']  = $this->testimonials_model->get_searched_testimonials('','','',0,1,$genre_id,2);
                //End RF3
		if($this->input->post('one_credit_submit')){
			if($courses_ids=$this->input->post('course')){
				$ids= implode(",", $courses_ids);
				//proceed towards  the registration page 
				$this->register($ids);
				
				return;
			}else{
				$errors[]='Please Select Course';
			}
		}
		
		$data['errors']=$errors; 
		$data['sidebar'] = 'courses';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	/**
		@Function Name:	registration
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@title   | String | title of course 
		@course_id  | numeric|  couser id 
		@return  none
		@Purpose:	load the single course 
	
	*/
	public function registration($title='',$course_id=0){
		$data = array();
		if(!$course_id){
			show_404();
		}
		$this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]='js/fancybox/source/jquery.fancybox.css';
		$data['is_register'] = true;
		$this->load->model('course_schedule_model');
		$data['course'] = $this->course_schedule_model->get_course_detail($course_id);
		
		if(empty($data['course'])){
			show_404();
		}
		$data['instructors']=$this->course_schedule_model->get_instuctors($course_id);
		$data['meta_title'] = $data['course']->cdCourseTitle;
		$data['main']   = 'courses/course';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**********************************************************************
	
	* Ref1 
	
	*** Build your own course related functions 
	******************************************************************/	
	
	
	/**
		@Function Name:	register
		@Author Name:	ben binesh
		@Date:			Oct 10, 2013
		@return  none
		@Purpose:	register user for multiple one credit courses 
	
	*/
	function register($ids='')
	{
		$data   = array();
		$error  = false;
		$errors = array();
		$data['ids'] = $ids;
		$this->load->model('user_model');
		$this->load->model('login_model');
		$this->load->model('course_schedule_model');
		$this->load->model('course_reservation_model');
		$this->load->model('email_template_model');
		$this->load->helper('form');
		$this->js[]='js/frontend.js';	
		
		
		$data['action']=base_url().'courses/register';
		if($this->input->post('ids')){
			$data['ids'] = $this->input->post('ids');
			$this->load->library('form_validation');
			$this->form_validation->set_rules('userEmail', 'User Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('userFirstName', 'User First Name', 'trim|required');
			$this->form_validation->set_rules('userLastName', 'User Last Name', 'trim|required');
			$this->form_validation->set_rules('userPhone', 'User Phone', 'trim|required');
			$this->form_validation->set_rules('dis_iu_unit', 'User Intermediate Unit', 'trim|required');
			$this->form_validation->set_rules('school_district', 'User School District', 'trim|required');
			//if a new district is added by user then validate it
			if(1==$this->input->post('school_district'))
			{
			   $this->form_validation->set_rules('new_district', 'New District', 'trim|required');
			}
					
			 if($email=$this->input->post('userEmail'))
				{

				if($user=$this->user_model->get_user_byEmail($email)){
					if(MEMBER != $user->accessLevel)
					{
						$error=TRUE;
						$errors[]='This email address is invalid for registration. Please use different email address.';
					}
				
				}
			}			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$ids= explode(',',$data['ids']);
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
				//process the multiple course registration
				$remove_ids=array(); //contain course ids for which user already registered/enrolled
				
				foreach( $ids as $course_id )
				{
					
					/*
					Rf1
					//check for user course relation	
					*/
					if( $email = $this->input->post('userEmail') )
					{
						//check if already registered 
						if( $registeration_details = $this->course_reservation_model->check_registration($course_id,$email) )
						{
							$remove_ids[]=$course_id;
						}
						elseif($this->course_schedule_model->check_enrollee($course_id,$email))
						{
							$remove_ids[]=$course_id;
						}
					
					}
				
				}
								
				$ids=array_diff($ids,$remove_ids);
				
				if(!empty($ids)){
					foreach($ids as $course_id)
					{
						$user_district_reimburse  = $this->input->post('userDistrictReimburse');
						$user_credits             = $this->input->post('userCredits');
						//Enter all data in course_reservation table
						$data_array = array(
										'urEmail' => $this->input->post('userEmail'),
										'urFirstName' => $this->input->post('userFirstName'),
										'urLastName' => $this->input->post('userLastName'),
										'urPhone' => $this->input->post('userPhone'),
										'urDistrict' => $school_district_value,
										'urDistrictReimburse' => $this->input->post('userDistrictReimburse'),
										'urCredits' => $this->input->post('userCredits'),
										'urCourse' => $course_id,
										'urIuID'=>$this->input->post('dis_iu_unit'),
										'urDistrictID'=>$school_district_value,
										'urTimestamp'=>date('Y-m-d H:i:s')
									);
						$this->login_model->insert($data_array);
						//End insertion
						/*Sending email*/
						$register                   = $this->input->post('register');
						$registerAndPay             = $this->input->post('registerAndPay');
						$user_full_name             = $this->input->post('userFirstName').' '. $this->input->post('userLastName');
						$course_detail     = $this->course_schedule_model->get_course_detail($course_id);
						$course_total_registrant = $course_detail->registered_count;
						$course_total_enrollee   = $course_detail->enrolees_count;
						$course_schedule='';
						$course_schedule .= format_date($course_detail->csStartDate,DATE_FORMAT).'-';
						$course_schedule .=format_date($course_detail->csEndDate,DATE_FORMAT);
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
			elseif((($course_detail->csMaximumEnrollees)>=($course_total_registrant+$course_total_enrollee)))
                        {
							//guaranteed - Non credit - not reimbursed
                            if(0==$user_district_reimburse && 0==$user_credits)
                            {
                                $email_template_id  = 8;
                            }
                            //guaranteed - Non credit - reimbursed
                            elseif(1==$user_district_reimburse && 0==$user_credits)        
                            {
                                $email_template_id  = 10;
                            }
                            //guaranteed - credit - not reimbursed
                            elseif(0==$user_district_reimburse && 1==$user_credits)
                            {
                                $email_template_id  = 5;
                            }
                            //guaranteed - credit - reimbursed
                            elseif(1==$user_district_reimburse && 1==$user_credits)
                            {
                                $email_template_id  = 9;
                            }
                        }
                        elseif(($course_detail->csMaximumEnrollees) < $course_total_registrant)
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
                        }
						
						//get the email template 
						$email_template_data = $this->email_template_model->get_single_record($email_template_id);
						if($email_template_data){
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
								"[CourseRegistrationDateStart]"=>$course_detail->csRegistrationStartDate,
								"[CourseRegistrationDateEnd]"=>$course_detail->csRegistrationEndDate,
								"[CoursePaymentDateStart]"=>$course_detail->csPaymentStartDate,
								"[CoursePaymentLink]"=>"<a href='".base_url()."checkout'>".base_url()."checkout</a>",
								"[CourseDates]"=>$course_schedule
								 );
							 $user_message =    str_replace(array_keys($email_message_replacement),  array_values($email_message_replacement),$user_message);
							$user_confirmation_message    = $user_message;
						  
						 /*End email constant replacement*/
						 //confirmation email to user
							//get admin emails 
							$emails = get_admin_emails();
							send_mail($this->input->post('userEmail'),SITE_NAME,SENDER_EMAIL,$subject,$user_confirmation_message,$emails);
						}
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

				}
				else{
					$errors[]='You already registered or enrolled for the selected courses ';
				}	
				
			}
			
		}
		else
		{
			if($user_id=$this->session->userdata('user_id')){
				$data['user']=$this->user_model->get_single_record($user_id,'u.id,u.userName,u.firstName,u.lastName,u.email,p.phone',true);
			}
		}
		if(''==$data['ids']){
			redirect();
		}
		$course_ids= explode(',',$data['ids']);
		foreach($course_ids as $course_id)
		{
			$data['courses'][]=$this->course_schedule_model->get_course_detail($course_id);
		
		}
		$data['errors'] = $errors;
		$data['main'] = 'login/login';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	view
		@Author Name:	ben binesh
		@Date:			Oct 14, 2013
		@return  none
		@Purpose:	load the course definition info
	
	*/
	
	function view($title='',$id=''){
		$data = array();
		$this->load->model('course_definition_model');
		$data['course_definition'] = $this->course_definition_model->get_single_record($id);
		$data['main']='courses/view';
		$this->load->vars($data);
		$this->load->view('popup');
	}
	
	/**
		@Function Name:	enrollees
		@Author Name:	ben binesh
		@Date:			Oct 14, 2013
		@return  none
		@Purpose:	load the enrollee list of a course
	
	*/
	
	public function enrollees($title='',$course_id=0)
	{
		$data = array();
		$this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]='js/fancybox/source/jquery.fancybox.css';
		if(!is_logged_in()) {
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}
		$this->load->model('course_schedule_model');
		
		$data['course']       = $this->course_schedule_model->get_course_detail($course_id);
		if(empty($data['course'])){
			redirect('home/error404');
		}
		
		$data['num_records']  = $this->course_schedule_model->get_enrollee_count($course_id);
		$data['results']      = $this->course_schedule_model->get_enrollees('',$course_id, 0 , -1 );
		
		
		$data['meta_title']='Enrollees/'.$data['course']->cdCourseID.':'.$data['course']->cdCourseTitle;	
		$data['meta_descrption']='Enrollees/'.$data['course']->cdCourseID.':'.$data['course']->cdCourseTitle;	
		$data['main'] = 'courses/enrollees';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	/**
		@Function Name:	email_enrollee
		@Author Name:	ben binesh
		@Date:			Nov 07, 2013
		@return         none
		@Purpose: send email to all enrollees
	*/
	
	function email_enrollee($course_id=0){
		if(!$course_id){
			$data['title']='error';
			set_flash_message('Course not Found  ','error');	
			$data['main'] = 'popup_message';
			$this->load->vars($data);
			$this->load->view('popup');
			return ;
		}
			
		
		$data   = array();
		$error  = false;
		$errors = array();
		$this->load->helpers('form');
		$this->load->model('course_schedule_model');
		if(!is_logged_in()) {
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}
		
		if(count($_POST) > 0 ){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('subject', 'Subject', 'trim|required');
			$this->form_validation->set_rules('message', 'Message', 'trim|required');
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
			
				
				//get enrollees emails 
				$enrollee_emails = $this->course_schedule_model->get_enrollees_email($course_id);
				if(!empty($enrollee_emails)){
					$emails=array();
					foreach($enrollee_emails as $enrollee_email)
					{
						$emails[]=$enrollee_email->email;
					}
					
					//get admin emails 
					$admin_emails = get_admin_emails();
					$emails=array_merge($admin_emails,$emails);
					//include the sender email here 
					$emails[]=$this->session->userdata('email');
					foreach($emails as $email){
						send_mail($email,SITE_NAME,SENDER_EMAIL,$this->input->post('subject'),nl2br($this->input->post('message')));
					}
					
					$data['title']   = 'Success';
					set_flash_message('Email has been successfully send to all enrollees ','success');	
				}else{
					//if enrollees not found 
					$data['title']   = 'Error';
					set_flash_message('Enrollee not found  ','Error');	
				
				}
				$data['main'] = 'popup_message';
				$this->load->vars($data);
				$this->load->view('popup');
				return ;
			
			}
			
		}
		$data['main'] = 'courses/email_enrollee';
		$this->load->vars($data);
		$this->load->view('popup');
		
		
	}
	
	/**
		@Function Name:	_export_enrollees
		@Author Name:	ben binesh
		@Date:			Sept, 20 2013
		@Purpose:		export enrollees details
	
	*/
	function export_enrollees()
	{
		if(!($course_id = $this->input->get('course_id'))){
			redirect('home/error_404');
		}
		
		$this->load->model('course_schedule_model');
		$course       = $this->course_schedule_model->get_course_detail($course_id);
		$course_location=$course->csCity.'-'.$course->csState;
		if(COURSE_ONLINE == $course->csCourseType)
			$course_location='Online';	
		$results  = $this->course_schedule_model->get_enrollees( '',$course_id, 0 , -1 );
		if(empty($results))
		{
			set_flash_message('No records to export ','error');
			redirect($this->input->get('redirect'));
		}
		$export_array = array();
		//build columns 
		$export_array[] = array(
			'S.No',
			'Course Name',
			'Course Location',
			'Course Start Date',
			'Last Name',
			'First Name',
			'Email',
			'Phone',
			'District',
			'Teaching',
			'Non-Credit',
			'Ipad',
		);
		$i=1;
		
		$school_district='';
		foreach($results as $result){
		
			$ipad_info='';
			//checking before showing the ipad info 
			//check if this have a ipad assignment ledger entry 
			$ledger_id = get_single_value('assignment_ledger','alID',"alAssignType = '".ASGN_IPAD_CONFIGURATION."' AND alCnfID=".$course_id." AND alUserID= ".$result->id);
			if($ledger_id)
			{
				if($result->oiProdName){
					$ipad_info .= $result->oiProdName;
					$ipad_info .='('.$result->oiProdVariantValue1.' )';
				}
			}
			if($ledger_id)
			{
				if($result->upgrade_id)
				{
					//check the transaction for upgrade 
					$transaction_id = get_single_value('pp_transactions','ppID',"payer_email = '".$result->email."' AND item_number1=".$result->upgrade_id." AND product_type= ".PRODUCT_TYPE_IPAD);
					if($transaction_id)
						$ipad_info .= $result->upgrade_info;
				}
			}
			if(is_numeric($result->districtAffiliation)){
					$school_district= get_single_value('district','disName','disID = '.$result->districtAffiliation) ;
				}else{
					 $school_district= $result->districtAffiliation; 
				}
			
		
			$level=$result->level;
			if($result->gradeSubject):
				$level."\n";
				$level.= get_single_value('tracks','trName','trID = '.$result->gradeSubject) ;
			endif;
			$export_array[]=array(
				$i,
				$course->cdCourseID.':'.$course->cdCourseTitle,
				$course_location,
				format_date($course->csStartDate,DATE_FORMAT),
				$result->lastName,
				$result->firstName,
				$result->email,
				$result->phone,
				$school_district,
				$level,
				show_credit_status($result->act48,$course->csID),
				$ipad_info,
				
							
			);
			$i++;
		}
		
		
		$file_name = 'course-enrollees-'.time();
		$file_name=url_title($file_name,'-',TRUE);
		//generate csv file 
		$this->load->helper('csv');
		array_to_csv($export_array, $file_name.'.csv');
			
	}
	
	
	/**
		@Function Name:	export_grade
		@Author Name:	ben binesh
		@Date:			Sept, 20 2013
		@Purpose:		export enrollees details
	
	*/
	function export_grade()
	{
		if(!($course_id = $this->input->get('course_id')))
		{
			redirect('home/error_404');
		}
		$this->load->model('course_schedule_model');
		$this->load->model('assignment_model');
		$results  = $this->course_schedule_model->get_enrollees( '',$course_id, 0 , -1);
		if(empty($results))
		{
			set_flash_message('No records to export ','error');
			redirect($this->input->get('redirect'));
		}
		
		$course       = $this->course_schedule_model->get_course_detail($course_id);
		$course_location=$course->csCity.'-'.$course->csState;
		if(COURSE_ONLINE == $course->csCourseType)
			$course_location='Online';	
		$export_array = array();
		//build columns 
		$export_array[] = array(
			'S.No',
			'Credits',
			'Tuition',
			'Grade',
			'Course Name',
			'Course Location',
			'Course Start Date',
			'Last Name',
			'First Name',
			'Birth Date',
			'Email',
			'Phone',
			'Address',
			'City',
			'State',
			'zip',
		);
		$i=1;
		
		
		foreach($results as $result)
		{
			//remove non-credit enrollees form grade export 
			if(!check_credit($result->act48,$course_id))
				continue;
			$ipad_info='';
			//grade
			$final_grade='NO GRADE';
			$getGradeNum = $this->assignment_model->get_user_grade($result->id,$course_id);
			 
			if(!isset($getGradeNum['fgComputedGrade']) || !isset($getGradeNum['fgGrade'])) {
						$final_grade= "NO GRADE";
			}
			else {
				if(isset($getGradeNum['fgComputedGrade']) && isset($getGradeNum['fgGrade'])) {
					$totalGrade = ''; 
					$gradeGot   = '';
					$totalGrade = $getGradeNum['fgComputedGrade'];
					$gradeGot   = $getGradeNum['fgGrade']; 
					if($totalGrade == 0) {
						$final_grade = 'A';	 
					}
					else if($gradeGot > $totalGrade) {
						$final_grade = 'A';
					}
					else {
						$final_grade  = $this->assignment_model->percentage($gradeGot, $totalGrade,0);  
					}
					if(isset($getGradeNum['fgApproved']) && $getGradeNum['fgApproved'] == 0) {
					 $final_grade = 'Not Final'; 
					}	
				}
			}  
			

			//track
			$track='Not Available';
			if($result->gradeSubject)
				$track=get_single_value('tracks','trName','trID = '.$result->gradeSubject) ;
			
			
			$export_array[]=array(
				$i,
				$course->cgCourseCredits,
				'',
				$final_grade,
				$course->cdCourseID.':'.$course->cdCourseTitle,
				$course_location,
				format_date($course->csStartDate,DATE_FORMAT),
				$result->firstName,
				$result->lastName,
				format_date($result->birthDate,DATE_FORMAT),
				$result->email,
				$result->phone,
				$result->address,
				$result->city,
				$result->state,
				$result->zip,
			);
			$i++;
		}
		
		$file_name = 'course-grade-export-.'.$course->cdCourseID.':'.$course->cdCourseTitle.'-'.time();
		$file_name=url_title($file_name,'-',TRUE);
		//generate csv file 
		$this->load->helper('csv');
		array_to_csv($export_array, $file_name.'.csv');
		
	}
	
	
	/**
		@Function Name:	members
		@Author Name:	ben binesh
		@Date:			Oct, 20 2013
		@Purpose:		load  member/instructor list 
	
	*/
	
	
	function members(){
		
		$data=array();
		$instructor = FALSE;
		$account    = MEMBER;
		$this->load->model('user_model');
		if(!is_logged_in()) {
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}
		$this->page_title="Members";
		$data['meta_title']='Member List';	
		$data['meta_descrption']='Member List';
		
		$data['main'] = 'user/list';
		$data['name'] = $this->input->get('name'); 
		//get the current user  course  
		if(($course_id=$this->input->get('course_id')) && ($ref=$this->input->get('ref'))){
		
		}else{
			redirect('home/error404');
		}
		
		$data['course_id']=$course_id;
		$this->load->model('course_schedule_model');
		$data['course']=$this->course_schedule_model->get_course_detail($course_id,false);
		if('instructor'==$ref){
			$instructor = TRUE;
			$account    = INSTRUCTOR;
			$this->page_title="Instructors";
			$data['meta_title']='Instructors List';	
			$data['meta_descrption']='Instructors List';
			
		}
		
		
		$data['action']       = base_url().'courses/members?course_id='.$course_id.'&ref='.$ref;
		$num_records          = $this->user_model->count_users( $data['name'],$data['course_id'],$account ,ACCOUNT_ACTIVE,$instructor);
		$base_url             = base_url().'courses/members';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->user_model->get_users( $data['name'], $data['course_id'] ,$account  ,ACCOUNT_ACTIVE, $start , $per_page,$instructor );
		
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		
		
		$this->load->vars($data);
		$this->load->view('template');
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */