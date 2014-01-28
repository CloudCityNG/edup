<?php 
/**
@Page/Module Name/Class: 		course_reservation.php
@Author Name:			 		ben binesh
@Date:					 		Aug,16  2013
@Purpose:		        		controller logic for course reservation
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  
//Ref1  ben binesh 			Sept 18 2013    normal 		add function to enroll user to course 	
//---------------------------------------------------------------------------------------- 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_reservation extends CI_Controller {
	
	public $js;
	public function __construct()
	{	
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->load->model('course_reservation_model');
		$this->load->helper('common');
		$this->load->helper('form');
		if(!is_logged_in()) 
		{
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}
		else
		{
			$this->_current_request = 'edu_admin/'.$this->router->class.'/'.$this->router->method ;
			//check the sufficient access level 
			if(!is_allowed($this->_current_request))
			{	
				set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
				redirect('home/error');
			}
		}
		
	}
	
	/**
		@Function Name:	index
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		show the multiple records and filter 
	
	*/
	public function index()
	{
		if(!($course_id = $this->input->get('course_id')))
		{
			redirect('edu_admin/course_schedule/index');
		}
		
		if($this->input->get('export'))
		{
			$this->_export(STATUS_REGISTERED);
			exit();
			
		}
		$data['meta_title']='Course Reservation';
		$this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]='js/fancybox/source/jquery.fancybox.css';	
		$this->js[]='js/admin.js';
		$this->load->model('course_schedule_model');
		$data['course'] = $this->course_schedule_model->get_course_detail($course_id,false);
		$data['content'] = get_content('cms_page','cpDescription','cpID = 4');
		$data['layout']       = '';
		$data['name']        = $this->input->get('name'); 
		$data['email']        = $this->input->get('email'); 
		$data['course_id']    = $this->input->get('course_id'); 
		
		$num_records          = $this->course_reservation_model->count_records($data['name'],$data['email'] ,$data['course_id'],STATUS_REGISTERED);
		$base_url             = base_url().'edu_admin/course_reservation/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) )
		{
			$start = 0;
		}
		$per_page            = 200;
		$data['results']     = $this->course_reservation_model->get_records( $data['name'],$data['email'] ,$data['course_id'], $start , $per_page,STATUS_REGISTERED);
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		
		
		
		$data['main'] = 'edu_admin/course_reservation/index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	create 
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		insert the new record ,validate recored
	
	*/
	function create()
	{
		
		if(! $this->input->get('course_id'))
		{
			redirect('edu_admin/course_schedule/index');
		}
                $this->js[]='js/jquery-ui.js';
		$this->js[]='js/frontend.js';
        $this->page_title   = "Add Course Registrant";
		$data['meta_title'] = 'Add Course Registrant';
		
		$error = false;
		$errors = array();
		
		
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('urEmail', 'Email', 'trim|required!valid_email');
			$this->form_validation->set_rules('urCourse', 'Course', 'trim|required');
			$this->form_validation->set_rules('urFirstName', 'First name', 'trim|required');
			$this->form_validation->set_rules('urLastName', 'Last name', 'trim|required');
			$this->form_validation->set_rules('urPhone', 'Phone', 'trim|required');
			$this->form_validation->set_rules('dis_iu_unit', 'IU', 'trim|required');
			$this->form_validation->set_rules('school_district', 'School District', 'trim|required');
			$this->form_validation->set_message('required', '%s must not be blank');
			$this->form_validation->set_message('valid_email', 'Email Address must be a valid e-mail address.');
			if('0'==$this->input->post('school_district'))
                        {
                           $this->form_validation->set_rules('new_district', 'New District', 'trim|required');
                        }
			if($email=$this->input->post('urEmail'))
			{
				if($this->course_reservation_model->check_duplicate($email,$this->input->post('urCourse')))
				{
					//check if email already subscribe for this course
					$errors[] = "This email address is already registered/enrolled for current course";
					$error=true;
				}
			}
					
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'urEmail' => $this->input->post('urEmail'),
								'urFirstName' => $this->input->post('urFirstName'),
								'urLastName' => $this->input->post('urLastName'),
								'urDistrict' => $this->input->post('urDistrict'),
								'urCredits' => $this->input->post('urCredits'),
								'urDistrictReimburse' => $this->input->post('urDistrictReimburse'),
								'urPhone' => $this->input->post('urPhone'),
								'urCourse' => $this->input->post('urCourse'),
								'urDistrictID' => $this->input->post('school_district'),
								'urIuID' => $this->input->post('dis_iu_unit'),
								'urTimestamp' => date('Y-m-d H:i:s'),
								);
					
				
				$this->course_reservation_model->insert($data_array);
				set_flash_message('Course registrant details has been inserted successfully','success');
				redirect('edu_admin/course_reservation/index?course_id='.$this->input->get_post('course_id'));
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/course_reservation/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	update 
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		validate and update the record
	
	*/
	function update($id=0)
	{
		
		if(! $this->input->get('course_id'))
		{
			redirect('edu_admin/course_schedule/index');
		}
		$error=false;
		$errors=array();
		$data['result']=$this->_load_data($id);
		$this->page_title="Update # ". $data['result']->uID;
		$data['meta_title']="Update Registrant";
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('urEmail', 'Email', 'trim|required!valid_email');
			$this->form_validation->set_rules('urCourse', 'Course', 'trim|required');
			$this->form_validation->set_rules('urFirstName', 'First name', 'trim|required');
			$this->form_validation->set_rules('urLastName', 'Last name', 'trim|required');
			$this->form_validation->set_rules('urPhone', 'Phone', 'trim|required');
			$this->form_validation->set_rules('dis_iu_unit', 'IU', 'trim|required');
			$this->form_validation->set_rules('school_district', 'School District', 'trim|required');
			
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'urEmail' => $this->input->post('urEmail'),
								'urFirstName' => $this->input->post('urFirstName'),
								'urLastName' => $this->input->post('urLastName'),
								'urDistrict' => $this->input->post('urDistrict'),
								'urCredits' => $this->input->post('urCredits'),
								'urDistrictReimburse' => $this->input->post('urDistrictReimburse'),
								'urPhone' => $this->input->post('urPhone'),
								'urCourse' => $this->input->post('urCourse'),
								'urDistrictID' => $this->input->post('school_district'),
								'urIuID' => $this->input->post('dis_iu_unit'),
								'urTimestamp' => date('Y-m-d H:i:s'),
								);
					
					
				
				$this->course_reservation_model->update($id,$data_array);
				set_flash_message('Course registrant details has been updated successfully','success');
				redirect('edu_admin/course_reservation/index?course_id='.$this->input->get_post('course_id'));
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/course_reservation/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	delete 
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		validate and delete the record 
	
	*/
	function  delete($id=0)
	{
		if(!$id){
			show_404('page');
		}
		$data = $this->_load_data($id);
		$this->course_reservation_model->delete($id);
		set_flash_message('Course registrant has been successfully deleted  ','success');
		redirect('edu_admin/course_reservation/index?course_id='.$data->urCourse);
	}
	/**
		@Function Name:	_load_date
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		load the single record  
	
	*/
	function _load_data($id=0)
	{
		if(!$id)
		{
			show_404('page');
		}
		$data = $this->course_reservation_model->get_single_record($id);
		if(empty($data))
		{
			show_404('page');
		}
		else
		{
			return $data;
		}
		
		
	}
	
	/**
		@Function Name:	duplicate_check
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		check the duplicate record in data base with same title  
	
	*/
	
	public function duplicate_check($title='')
	{	
		$id=0;
		if ($this->course_reservation_model->check_duplicate($id,$title))
		{	
			$this->form_validation->set_message('duplicate_check', 'The  "'.$title.'"  is already created');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	
	/**
		@Function Name:	_export
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		generate the csv report of records
	
	*/
	function _export($status=STATUS_REGISTERED)
	{
		$name        = $this->input->get('name'); 
		$email        = $this->input->get('email'); 
		$course_id   = $this->input->get('course_id'); 
		$results     = $this->course_reservation_model->get_records( $name,$email,$course_id, 0, -1, $status);
		
		$csv_array = array();
		//build columns 
		$csv_array[] = array(
			'S.No',
			'Course Name',
			'Course Location',
			'Course Start Date',
			'Last Name',
			'First Name',
			'Email',
			'Phone',
			'IU',
			'District',
			'Non-Credit',
			'Reimburse',
			'Registered Date',
			
		);
		$i=1;
		$course_id='';
		if(!empty($results))
		{
			foreach($results as $result)
			{
				$course_id=$result->cdCourseID;
				//school district 
				$school_district = $result->urDistrict;
				if(is_numeric($result->urDistrict))
				{
					$school_district =get_single_value('district','disName','disID = '.$result->urDistrict) ;
				}
				
				
				$course_location= $result->csCity.','.$result->csState;  
				if(COURSE_ONLINE==$result->csCourseType)
					$course_location='Online';
				
				
				//iu
				$iu=get_single_value('iu_unit','iuName','iuID = '.$result->urIuID) ;
				$csv_array[]=array(
					$i,
					$result->cdCourseID.' : '.$result->	cdCourseTitle,
					$course_location,
					format_date($result->csStartDate,DATE_FORMAT),
					$result->urLastName,
					$result->urFirstName,
					$result->urEmail,
					format_phone_number($result->urPhone),
					$iu,
					$school_district,
					show_yesNo_text($result->urCredits,true),
					show_yesNo_text($result->urDistrictReimburse),
					format_date($result->urTimestamp,DATE_FORMAT.' '.TIME_FORMAT),
					
				);
				$i++;
			}
		}	
			
		
		$this->load->helper('csv');
		if(STATUS_UNENROLLED==$status)
			$file_name='course un-enrolled-'.$course_id.' '.time();
		elseif(STATUS_REGISTERED==$status)
			$file_name='course registrant-'.$course_id.' '.time();
		else
			$file_name='course unregistrant-'.time();
		$file_name=url_title($file_name,'-',TRUE);
		array_to_csv($csv_array, $file_name.'.csv');
	}
	
	/**
		@Function Name:	enroll
		@Author Name:	ben binesh
		@id             | integer | course reservation id 
		@Date:			Sept, 18 2013
		@Purpose:		enroll user for the course ,add/update user to the users table and
						also send activation account email to user 
	
	*/
	
	function enroll($id=0)
	{
		$content = array();
		$error   = false;
		$errors  = array();
		
		//get course reservation data 
		$data = $this->_load_data($id);
		$content['data']=$data;
		$this->load->model('course_schedule_model');
		$this->load->model('checkout_model');
		$course = $this->course_schedule_model->get_course_detail($data->urCourse);
		$content['course']= $course;
		if( count($_POST) > 0 )
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('manual_price', 'Price', 'trim|required|numeric');
			$this->form_validation->set_rules('payment_mode', 'Payment method', 'trim|required|');
			$this->form_validation->set_message('required','%s must not be blank');
			if($this->form_validation->run() == TRUE && $error==false  )
            {			
				
				//update user membership 
				$this->load->model('user_model');
				$user = $this->user_model->get_user_byEmail($data->urEmail);
				$credit=$data->urCredits;
				$urCourse=$data->urCourse;
				$act48 = '';
				if(STATUS_NO == $credit){
					$act48 = $urCourse ;
				}
				if($user)
				{
					//update user members ship 
					$this->user_model->update_membership($user->id,$data->urCourse,false);
					
					if(STATUS_NO == $credit){
						if($user->act48)
						{
							$act48=$user->act48.','.$urCourse;
						}
						else
						{
							$act48=$urCourse;
						}
					}
					//update credit value 
					$data_array = array(
						'act48' => $act48,
					);
					$this->user_model->update($user->id,$data_array);
					
					
				}
				else
				{	
					//add user 
					
					$signup_date=date('Y-m-d H:i:s');
					$activation_code = random_string('alnum', 12);
					$user_array = array(
							'referralCode'=>$data->urReferralCode,
							'firstName' => $data->urFirstName,
							'lastName' => $data->urLastName,
							'email' => $data->urEmail,
							'accessLevel' => MEMBER,
							'act48' => $act48,
							'memberships'=>$data->urCourse,
							'membershipLastUsed'=>$data->urCourse,
							'signupDate' => $signup_date,
							'activationCode'=>$activation_code,
							'activationFlag'=>time(),
					);
					
					$user_id = $this->user_model->insert($user_array);
					$profile_data = array(
							'user_id' => $user_id,
							'signupDate' => $signup_date,
							'districtAffiliation' =>  $data->urDistrict,
							'districtID' =>  $data->urDistrictID,
							'iuID' =>  $data->urIuID,
							'phone' =>  $data->urPhone,
					);
					$this->user_model->set_user_profile($user_id,$profile_data);
					
					//send activation account email to user 
					$user_activation_link=base_url().'user/activate?email='.$data->urEmail.'&activation_code='.$activation_code.'&user='.base64_encode($user_id);
					$email_template = get_content('email_templates','*','etID = 1');
					if(!empty($email_template)){
						$email_template=$email_template[0];
						$searchReplaceArray = array(
							 '[AccountActivationUrl]'   =>anchor($user_activation_link,$user_activation_link) ,
							 '[firstName]'   =>$data->urFirstName,
							 '[lastName]'   =>$data->urLastName,
							 '[maximumResponse]'   =>FWPWD_EXPIRE_TIME,
							
							);
					$email_message = str_replace(
							array_keys($searchReplaceArray), 
							array_values($searchReplaceArray),$email_template->etCopy); 
						
					//get admin emails 
					$emails = get_admin_emails();
					send_mail($data->urEmail,SITE_NAME,SENDER_EMAIL,$email_template->etSubject,$email_message,$emails);	  
					}
						
						
				}
				
				$item_name  = '';
				$item_name .= $course->cdCourseID.':'.$course->cdCourseTitle; 
				$item_name .= '(';
				
				$item_name .= format_date($course->csStartDate,DATE_FORMAT);
				$course_location = $course->csCity.', '.$course->csState; 
				if(COURSE_ONLINE==$course->csCourseType)
				$course_location='Online';
				
				$item_name .= '-'.$course_location;
				$item_name .= ')';
				
				$show_receipt=1;
				if($this->input->post('show_receipt'))
				{
					$show_receipt=0;
				}
				//add manual transaction details 
				$transaction_data=array(
					'payer_email'=>$data->urEmail,
					'first_name'=>$data->urFirstName,
					'last_name'=>$data->urFirstName,
					'payment_gross'=>$this->input->post('manual_price'),
					'payment_date'=>date('Y-m-d H:i:s'),
					'payment_status'=>PAYMENT_ENROLLED,
					'num_cart_items'=>1,
					'quantity1'=>1,
					'item_number1'=>$data->urCourse,
					'item_name1'=>$item_name,
					'txn_type'=>TXN_TYPE_CART,
					'mc_currency'=>'USD',
					'product_type'=>PRODUCT_TYPE_COURSE,
					'payment_mode'=>$this->input->post('payment_mode'),
					'manual_comment'=>$this->input->post('manual_comment'),
					'manual_comment_self'=>$this->input->post('manual_comment_self'),
					'check_number'=>$this->input->post('check_number'),
					'show_receipt'=>$show_receipt,
					
				);
				
				$this->checkout_model->insert('pp_transactions',$transaction_data);
				
				//send the  email to user about course enrollment 
				$email_template = get_content('email_templates','*','etID = 14');
				if(!empty($email_template))
				{
					$email_template=$email_template[0];
					$course_dates='';
					if(count($course->course_dates)>0)
					{
						foreach($course->course_dates as $course_date){ 
							if(COURSE_OFFLINE == $course->csCourseType){ 
								$course_dates .= format_date($course_date->csdStartDate,DATE_FORMAT).' (';
								$course_dates .= format_date($course_date->csdStartTime,TIME_FORMAT).'-';
								$course_dates .= format_date($course_date->csdEndTime,TIME_FORMAT).')';
								$course_dates .= "\n";
							}else{
							
								$course_dates .= format_date($course_date->csdStartDate,DATE_FORMAT).'-';
								$course_dates .=format_date($course_date->csdEndDate,DATE_FORMAT);
							}
						}
					}
					$searchReplaceArray = array(
						 '[UserName]'   =>$data->urFirstName.' '.$data->urLastName, 
						 '[CourseTitle]'   =>$course->cdCourseID.':'.$course->cdCourseTitle,
						 '[CourseLocation]'   =>$course->csLocation,
						 '[CourseAddress]'   =>$course->csAddress,
						 '[CourseCity]'   =>$course->csCity,
						 '[CourseState]'   =>$course->csState,
						 '[CourseDates]'   =>$course_dates,
						 
						);
					$email_message = str_replace(
						  array_keys($searchReplaceArray), 
						  array_values($searchReplaceArray),$email_template->etCopy); 
					//get admin emails 
					$emails = get_admin_emails(); 
					send_mail($data->urEmail,SITE_NAME,SENDER_EMAIL,$email_template->etSubject,$email_message,$emails);		  
				}
				
				//update record as enrolled 
				$update_array = array(
					'urStatus'=>STATUS_ENROLLED,
					'urEnrolledTime'=>date('Y-m-d H:i:s'),
				);
				$this->course_reservation_model->update($id,$update_array);
				set_flash_message('User has been successfully enrolled  ','success');
				$content['reload']=true;
			
			}
			
		}
		$content['errors'] = $errors;
		$content['main'] = 'edu_admin/course_reservation/enroll';
		$this->load->vars($content);
		$this->load->view('popup');
				
	}
	/**
		@Function Name:	pay
		@Author Name:	ben binesh
		@id             | integer | course reservation id 
		@Date:			Nov, 12 2013
		@Purpose:		send payment link to  user 
	
	*/
	function pay($id=0)
	{
		$reservation_detail = $this->_load_data($id);
		$this->load->model('course_schedule_model');
		$course = $this->course_schedule_model->get_course_detail($reservation_detail->urCourse);
		//send the  email to user with the payment link 
		$email_template = get_content('email_templates','*','etID = 21');
		if(!empty($email_template)){
			$email_template=$email_template[0];
			$course_dates='';
			if(count($course->course_dates)>0)
			{
				foreach($course->course_dates as $course_date)
				{ 
					if(COURSE_OFFLINE == $course->csCourseType)
					{ 
						$course_dates .= format_date($course_date->csdStartDate,DATE_FORMAT).' (';
						$course_dates .= format_date($course_date->csdStartTime,TIME_FORMAT).'-';
						$course_dates .= format_date($course_date->csdEndTime,TIME_FORMAT).')';
						$course_dates .= "\n";
					}
					else
					{
					
						$course_dates .= format_date($course_date->csdStartDate,DATE_FORMAT).'-';
						$course_dates .=format_date($course_date->csdEndDate,DATE_FORMAT);
					}
				}
			}
				//update record 
				$this->course_reservation_model->update($id,array(
					'urPaymentLink'=>time()
				));
			
			$course_payment_link=base_url().'checkout?rid='.base64_encode($id);
			$searchReplaceArray = array(
				 '[UserName]'   =>$reservation_detail->urFirstName.' '.$reservation_detail->urLastName, 
				 '[CourseTitle]'   =>$course->cdCourseID.':'.$course->cdCourseTitle,
				 '[CourseLocation]'   =>$course->csLocation,
				 '[CourseDates]'   =>$course_dates,
				 '[CoursePaymentLink]'   =>$course_payment_link,
				 
				);
				$email_message = str_replace(
				  array_keys($searchReplaceArray), 
				  array_values($searchReplaceArray),$email_template->etCopy); 
			//get admin emails 
			$emails = get_admin_emails();	  
			send_mail($reservation_detail->urEmail,SITE_NAME,SENDER_EMAIL,$email_template->etSubject,$email_message,$emails );
					
			
		}
		
		set_flash_message('Payment link is successfully sent to the user ','success');
		if($redirect = $this->input->get('redirect'))
			redirect($redirect);	
				
		redirect('edu_admin/course_reservation/index?course_id='.$course->cdID.'&ref=courses');
		
	}
	
	/**
		@Function Name:	course_unregistrant
		@Author Name:	ben binesh
		@Date:			Dec 06, 2013
		@Purpose:		load the course unregistrant list 
	
	*/
	
	function course_unregistrant()
	{
		
		if($this->input->get('export'))
		{
			$this->_export(STATUS_UNREGISTERED);
			exit();
		}
		
		$this->load->model('course_schedule_model');
		$data['name']        = $this->input->get('name'); 
		$data['email']        = $this->input->get('email'); 
		$num_records          = $this->course_reservation_model->count_records($data['name'],$data['email'] ,0,STATUS_UNREGISTERED);
		$base_url             = base_url().'edu_admin/course_reservation/course_unregistrant';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = PER_PAGE;
		$data['results']     = $this->course_reservation_model->get_records( $data['name'],$data['email'] ,0, $start , $per_page,STATUS_UNREGISTERED);
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		
		$data['main'] = 'edu_admin/course_reservation/course_unregistrant';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	/**
		@Function Name:	course_unenrollees
		@Author Name:	ben binesh
		@Date:			Dec 06, 2013
		@Purpose:		load the course unenrollees  list 
	
	*/
	function course_unenrollees()
	{
		
		if($this->input->get('export'))
		{
			$this->_export(STATUS_UNENROLLED);
			exit();
		}
		
		$this->load->model('course_schedule_model');
		$data['name']        = $this->input->get('name'); 
		$data['email']        = $this->input->get('email'); 
		$num_records          = $this->course_reservation_model->count_records($data['name'],$data['email'] ,0,STATUS_UNENROLLED);
		$base_url             = base_url().'edu_admin/course_reservation/course_unenrollees';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = PER_PAGE;
		$data['results']     = $this->course_reservation_model->get_records( $data['name'],$data['email'] ,0, $start , $per_page,STATUS_UNENROLLED);
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		
		$data['main'] = 'edu_admin/course_reservation/course_unenrollees';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */