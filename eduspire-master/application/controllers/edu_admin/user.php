<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
@Page/Module Name/Class: 		user.php
@Author Name:			 		binesh
@Date:					 		Aug, 30 2013
@Purpose:		        		Contain all controllers functions for the user 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
Chronological development
***********************************************************************************
Ref No.   |  Author name     | Date   | Modification description
***********************************************************************************
RF1	  |   Janet Rajani   | Dec 10  | will redirect to dashboards after emulation
*********************************************************************************/

class User extends CI_Controller {
	
	public $js;
	protected $_id;
	
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->_id = 0;
		$this->load->model('user_model');
		$this->load->helper('common');
		$this->load->helper('form');
		
		if(!is_logged_in()) {
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}else{
			$this->_current_request = 'edu_admin/'.$this->router->class.'/index';
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
		@Author Name:	binesh
		@Date:			Aug, 30 2013
		@Purpose:		show the multiple records and filter and handle mass action request
	
	*/
	public function index()
	{
		
		$data = array();
		if($this->input->post('mass_action')){
			$this->_mass_action();
			return ;
		}
		
		if($this->input->get('export'))
		{
			$this->_export();
			exit();
			
		}
		$this->page_title  ="Manage User";
		$data['meta_title'] ="Manage User";
		$this->js[] = 'js/admin.js';
		$data['layout']       = '';
		$data['name']        = $this->input->get('name'); 
		$data['email']        = $this->input->get('email'); 
		$data['access_level']       = ($this->input->get('access_level')  != '' )?$this->input->get('access_level'):''; 
		$data['status']       = ($this->input->get('status')  != '' )?$this->input->get('status'):''; 
		$num_records          = $this->user_model->count_records( $data['name'], $data['email'] ,$data['access_level'],$data['status'] );
		$base_url             = base_url().'edu_admin/user/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = '30'; 
		$data['results']     = $this->user_model->get_records( $data['name'], $data['email'] ,$data['access_level'], $start , $per_page,$data['status'] );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/user/index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	create 
		@Author Name:	binesh
		@Date:			Aug, 30 2013
		@Purpose:		insert the new record ,validate record
	
	*/
	function create(){
		
		$error = false;
		$errors = array();
		
		$this->page_title ='Create User';
		$data['meta_title'] ='Create User';
		$this->js[] = 'js/tinymce/tinymce.min.js';
		$image = $this->input->post('old_image');
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('userName', 'Username', 'trim|required|min_length[5]|max_length[15]');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|matches[c_password]|min_length[5]|max_length[15]');
			$this->form_validation->set_rules('c_password', 'Password Confirmation', 'trim|required');
			$this->form_validation->set_rules('firstName', 'FirstName', 'trim|required');
			$this->form_validation->set_rules('lastName', 'Last Name', 'trim|required');
			$this->form_validation->set_rules('gender', 'Gender', 'trim|required');
			$this->form_validation->set_rules('accessLevel', 'Access Level', 'trim|required');
			$this->form_validation->set_rules('address', 'Address', 'trim|required');
			$this->form_validation->set_rules('city', 'City', 'trim|required');
			$this->form_validation->set_rules('state', 'State', 'trim|required');
			$this->form_validation->set_rules('zip', 'zip', 'trim|required');
			$this->form_validation->set_rules('phone', 'Phone', 'trim|required');
			$this->form_validation->set_rules('school_district', 'District', 'trim|required');
			$this->form_validation->set_rules('dis_iu_unit', 'IU', 'trim|required');
			if( '' != $this->input->post('userName') ){
				$this->form_validation->set_rules('userName', 'email', 'trim|required|min_length[5]|max_length[15]|callback_duplicate_name_check');
			}else{
				$this->form_validation->set_rules('userName', 'Username', 'trim|required|min_length[5]|max_length[15]');
			}
			
			if(  '' != $this->input->post('email') ){
				$this->form_validation->set_rules('email', 'email', 'callback_duplicate_email_check|valid_email');
			}
			$this->form_validation->set_rules('birthDate', 'birth Date', 'trim|callback_birth_date_check');
			
			$this->form_validation->set_message('required', '%s must not be blank');
			$this->form_validation->set_message('valid_email', 'Email Address must be a valid e-mail address.');
			if($error==false){
				//upload the image 
				
				if($_FILES['profileImage']['name'] != ''){
					$path = UPLOADS.'/users';
					$res_response = upload_file('profileImage',$path);
					if(is_array($res_response) && isset( $res_response['file_name'] ) ){
						// delete old file
						if( $image != '' ){
							//unlink the previouly uploed image 
							unlink( $path.'/'.$image );
						}
						$image  = $res_response['file_name'];
					}
					else{
						$error = true;
						$errors[] = $res_response;
					}
				}	
			}
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$signup_date=date('Y-m-d H:i:s');
				$data_array = array(
					'userName' => $this->input->post('userName'),
					'firstName' => $this->input->post('firstName'),
					'lastName' => $this->input->post('lastName'),
					'profileImage' =>$image,
					'email' => $this->input->post('email'),
					'accessLevel' => $this->input->post('accessLevel'),
					'receiveSystemEmails' => $this->input->post('receiveSystemEmails'),
					'signupDate' => $signup_date,
					'activationFlag' => $this->input->post('activationFlag'),
					'password' => encrypt_password($this->input->post('password')),
					'isAboutUs' => $this->input->post('isAboutUs'),
					
				);
				
				$id = $this->user_model->insert($data_array);
				$this->_set_profile_data($id,$signup_date,FALSE);
				set_flash_message('user details has been inserted successfully','success');
				redirect('edu_admin/user/index');
			}
		}
		$data['image']=$image;
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/user/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	update 
		@Author Name:	binesh
		@Date:			Aug, 30 2013
		@Purpose:		validate and update the record
	
	*/
	function update($id=0){
		$error = false;
		$errors = array();
		
		$data['result']=$this->_load_data($id);
        $this->page_title ='Update '. $data['result']->lastName.' '.$data['result']->firstName. '('. $data['result']->email.')';
		$data['meta_title'] ='Update User';
		$image = $data['result']->profileImage;
		$this->_id = $id;
		
		$this->js[]='js/tinymce/tinymce.min.js';
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('firstName', 'FirstName', 'trim|required');
			$this->form_validation->set_rules('lastName', 'Last Name', 'trim|required');
			$this->form_validation->set_rules('gender', 'Gender', 'trim|required');
			$this->form_validation->set_rules('accessLevel', 'Access Level', 'trim|required');
			$this->form_validation->set_rules('address', 'Address', 'trim|required');
			$this->form_validation->set_rules('city', 'City', 'trim|required');
			$this->form_validation->set_rules('state', 'State', 'trim|required');
			$this->form_validation->set_rules('zip', 'zip', 'trim|required');
			$this->form_validation->set_rules('phone', 'Phone', 'trim|required');
			$this->form_validation->set_rules('school_district', 'District', 'trim|required');
			$this->form_validation->set_rules('dis_iu_unit', 'IU', 'trim|required');
						
			if( ('' != $this->input->post('userName')) &&   '' != $this->input->post('email') ){
				$this->form_validation->set_rules('userName', 'Username', 'callback_duplicate_name_check');
				$this->form_validation->set_rules('email', 'email', 'callback_duplicate_email_check');
			}
			$this->form_validation->set_rules('birthDate', 'birth Date', 'trim|callback_birth_date_check');
			$this->form_validation->set_message('required', '%s must not be blank');
			$this->form_validation->set_message('valid_email', 'Email Address must be a valid e-mail address.');
			if($error==false){
				//upload the image 
				
				if($_FILES['profileImage']['name'] != ''){
					$path = UPLOADS.'/users';
					$res_response = upload_file('profileImage',$path);
					if(is_array($res_response) && isset( $res_response['file_name'] ) ){
						// delete old file
						if( $image != '' ){
							//unlink the previously uploaded image 
							unlink( $path.'/'.$image );
						}
						$image  = $res_response['file_name'];
					}
					else{
						$error = true;
						$errors[] = $res_response;
					}
				}	
			}
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$signup_date=$data['result']->signupDate;
				$data_array = array(
					'userName' => $this->input->post('userName'),
					'firstName' => $this->input->post('firstName'),
					'lastName' => $this->input->post('lastName'),
					'profileImage' =>$image,
					'email' => $this->input->post('email'),
					'accessLevel' => $this->input->post('accessLevel'),
					'receiveSystemEmails' => $this->input->post('receiveSystemEmails'),
					'signupDate' => $signup_date,
					'activationFlag' => $this->input->post('activationFlag'),
					'isAboutUs' => $this->input->post('isAboutUs'),
					
				);
				$this->user_model->update($id,$data_array);
				$this->_set_profile_data($id,$signup_date,FALSE);
				set_flash_message('User details has been updated successfully','success');
				if($redirect=$this->input->get('redirect'))
				{
					redirect($redirect);
				}
				redirect('edu_admin/user/index');
			}
		}
		$data['image']=$image;
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/user/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	_set_profile_data 
		@Author Name:	binesh
		@Date:			Sept, 05 2013
		@Purpose:		insert update user profile 
	
	*/
	
	function _set_profile_data($id,$signup_date='',$update = FALSE){
			$level=($this->input->post('level'))?implode(',',$this->input->post('level')):'';
			$data_array = array(
					'user_id' => $id,
					'usrBio' => $this->input->post('usrBio'),
					'gender' => $this->input->post('gender'),
					'twitter' => $this->input->post('twitter'),
					'facebook' => $this->input->post('facebook'),
					'aim' => $this->input->post('aim'),
					'msn' => $this->input->post('msn'),
					'signupDate' => $signup_date,
					'siteURL' => $this->input->post('siteURL'),
					'address' => $this->input->post('address'),
					'city' => $this->input->post('city'),
					'state' => $this->input->post('state'),
					'zip' => $this->input->post('zip'),
					'phone' => $this->input->post('phone'),
					'mobileCarrier' => $this->input->post('mobileCarrier'),
					'birthDate' =>  $this->input->post('birth_year').'-'.$this->input->post('birth_month').'-'.$this->input->post('birth_day'),
					'districtAffiliation' =>  $this->input->post('school_district'),
					'buildingAssigned' =>  $this->input->post('buildingAssigned'),
					'buildingAddress' =>  $this->input->post('buildingAddress'),
					'buildingCity' =>  $this->input->post('buildingCity'),
					'buildingState' =>  $this->input->post('buildingState'),
					'buildingZip' =>  $this->input->post('buildingZip'),
					'email2' =>  $this->input->post('email2'),
					'gradCoursesTaking' =>  $this->input->post('gradCoursesTaking'),
					'gradeSubject' =>  $this->input->post('gradeSubject'),
					'yearsActive' =>  $this->input->post('yearsActive'),
					'role' =>  $this->input->post('role'),
					'level' => $level,
					'districtID'=>$this->input->post('school_district'),
					'iuID'=>$this->input->post('dis_iu_unit'),
					
			);
		
		$this->user_model->set_user_profile($id,$data_array);
				
	}
	
	/**
		@Function Name:	delete 
		@Author Name:	binesh
		@Date:			Sept, 06 2013
		@Purpose:		change password of user 
	
	*/
	
	function change_password($id){
		
		$error = false;
		$errors = array();
		$data['result']=$this->_load_data($id,false);
		$data['meta_title'] = 'Change Password';
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|matches[c_password]|min_length[5]|max_length[15]');
			$this->form_validation->set_rules('c_password', 'Password Confirmation', 'trim|required');
			$this->form_validation->set_message('required', '%s must not be blank');
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$update_data = array(
						'password'=>encrypt_password($this->input->post('password')),
				);
				$this->user_model->update($id,$update_data);
				set_flash_message('User password has been successfully changed ','success');
				redirect('edu_admin/user/index');
			}
		}
		
		
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/user/change_password';
		$this->load->vars($data);
		$this->load->view('template');
		
	}
	
	
	/**
		@Function Name:	delete 
		@Author Name:	binesh
		@Date:			Aug, 30 2013
		@Purpose:		validate and delete the record 
	
	*/
	function  delete($id=0){
		$is_delete = true;
		
		//additional consistency check will come here 
		$data = $this->_load_data($id);
		if($is_delete){
			@unlink( UPLOADS.'/users/'.$data->profileImage );
			$this->user_model->delete($id);
			set_flash_message('User details has been successfully deleted','success');
		}
		
		redirect('edu_admin/user/index');
	}
	/**
		@Function Name:	_load_date
		@Author Name:	binesh
		@Date:			Aug, 30 2013
		@Purpose:		load the single record  
	
	*/
	function _load_data($id=0,$profile=true){
		if(!$id){
			show_404('page');
		}
		$data = $this->user_model->get_single_record($id,$select='*',$profile);
		if(empty($data)){
			show_404('page');
		}else{
			return $data;
		}
		
		
	}
	
	/**
		@Function Name:	birth_date_check 
		@Author Name:	ben binesh
		@Date:			18 Dec, 2013
		@Purpose:		validate birth date  
	
	*/
	
	function birth_date_check($title=''){
		if(''==$this->input->post('birth_year') || ''==$this->input->post('birth_month') || ''==$this->input->post('birth_day'))
		{
			$this->form_validation->set_message('birth_date_check', 'Birth date must not be blank');
			return FALSE;
		}else
		{
			return TRUE;
		}
			
		
		
	}
	
	
	/**
		@Function Name:	duplicate_name_check
		@Author Name:	binesh
		@Date:			Aug, 30 2013
		@Purpose:		check the duplicate record in data base with same name
	
	*/
	
	public function duplicate_name_check($title='')
	{	$id = $this->_id;
		if ($this->user_model->check_duplicate($id,'',$title))
		{	
			$this->form_validation->set_message('duplicate_name_check', 'The user with username  "'.$title.'"  is already exists');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	
	/**
		@Function Name:	duplicate_email_check
		@Author Name:	binesh
		@Date:			Aug, 30 2013
		@Purpose:		check the duplicate record in data base with same title  
	
	*/
	
	public function duplicate_email_check($title='')
	{	$id = $this->_id;
		if ($this->user_model->check_duplicate($id,$title))
		{	
			$this->form_validation->set_message('duplicate_email_check', 'The user with email "'.$title.'"  is already exists');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	/**
		@Function Name:	_mass_action
		@Author Name:	binesh
		@Date:			Sept, 02 2013
		@Purpose:		handle the mass action request 
	
	*/
	
	public function _mass_action(){
		$chk_ids=$this->input->post('chk_ids');
		if(!empty($chk_ids) && count($chk_ids > 0)){
			$ids = implode(",", $chk_ids);
			if($this->input->post('activate')){
					$data = array(
								'activationFlag' => ACCOUNT_ACTIVE,
					);
				set_flash_message('Users account has been successfully activated','success');	
						
			}elseif($this->input->post('deactivate')){
					$data = array(
								'activationFlag' => ACCOUNT_INACTIVE,
								
					);
				set_flash_message('Users account has been successfully deactivated','success');	
			}
			$this->user_model->update_multiple($ids,$data);
		}
		redirect('edu_admin/user/index');
	}
	
	
	/**
		@Function Name:	view 
		@Author Name:	binesh
		@Date:			Sept, 09 2013
		@Purpose:		load  user profile 
	
	*/
	function view($id=0){
		$data['meta_title']='View User';
		$data['user']=$this->_load_data($id,true);
		$data['main'] = 'edu_admin/user/view';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	send_activation 
		@Author Name:	binesh
		@Date:			Sept, 10 2013
		@Purpose:		send account activation link to user 
	
	*/
	function send_activation($id=0){
		$user = $this->_load_data($id,false);
		
		if(ACCOUNT_ACTIVE == $user->activationFlag && '' != $user->userName)
		{
			set_flash_message('User account already activated','notice');	
		}
		//send activation email 
		$this->user_model->send_activation_email($user);
		set_flash_message('Account activation mail has been successfully sent','success');
		if('enrollees' == $this->input->get('ref') && ($course_id=$this->input->get('course_id')) ){
			redirect('edu_admin/course_schedule/enrollees?course_id='.$course_id.'&ref=courses');
		}
		redirect('edu_admin/user/index');
	}
	
	/**
		@Function Name:	emulate 
		@Author Name:	ben binesh
		@id:         	|integer|
		@Date:			Sept, 25 2013
		@Purpose:		emulate user 
	
	*/
	function emulate($id=0){
		$user = $this->_load_data($id,false);
		$this->load->model('permission_model');
		//update session values 
		$session_data = array(
							   'user_id'  => $user->id,
							   'email'     => $user->email,
							   'user_name' =>$user->userName, 
							   'access_level' =>$user->accessLevel, 
							   'display_name' =>$user->firstName.' '.$user->lastName ,
							   'logged_in' => TRUE,
							   //insert the current admin id to session  
								// so switching is possible	
								'emulate'   => $this->session->userdata('user_id'),
							    'permission' =>$this->permission_model->get_user_permission_array($user->accessLevel),
							   
						);
						$this->session->set_userdata($session_data);
                //RF1
                $access_level = $this->session->userdata('access_level');
				switch($access_level)
                {
                        case INSTRUCTOR:
                                redirect('instructor');
                        break;

                        case MEMBER:
                                redirect('member');
                        break;

                        default:
                                //redirect to home page 
                                redirect('/');
                        break;
                }
		//End RF1
	}
	
	/**
		@Function Name:	instructor 
		@Author Name:	ben binesh
		@Date:			Sept, 25 2013
		@Purpose:		add instructor and also associate it with the course 
	
	*/
	
	function instructor(){
		$data=array();
		$error = false;
		$errors = array();
        $this->page_title = 'Add Instructor';
		$this->load->model('course_schedule_model');
		$data['courses'] = $this->course_schedule_model->get_courses(0,'',0,0,-1,NULL,STATUS_PUBLISH,false);
		if(count($_POST)>0){
			
			$this->load->library('form_validation');
			$this->form_validation->set_rules('firstName', 'FirstName', 'trim|required');
			$this->form_validation->set_rules('lastName', 'Last Name', 'trim|required');
			$this->form_validation->set_rules('accessLevel', 'Access Level', 'trim|required');
			$this->form_validation->set_message('required',' %s must not be blank');
			$this->form_validation->set_message('valid_email', 'Email Address must be a valid e-mail address.');
			if('' != $this->input->post('email') ){
				$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_duplicate_email_check');
			}else{
				$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			}
						
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$email      = $this->input->post('email');
				$first_name = $this->input->post('firstName');
				$last_name = $this->input->post('lastName');
				$signup_date=date('Y-m-d H:i:s');
				$activation_code = random_string('alnum', 12);
				$data_array = array(
					'firstName' => $first_name,
					'lastName' => $last_name,
					'email' => $email,
					'accessLevel' => $this->input->post('accessLevel'),
					'signupDate' => $signup_date,
					'activationCode'=>$activation_code,
					'activationFlag'=>time(),
				);
				
				$id = $this->user_model->insert($data_array);
				$this->_set_profile_data($id,$signup_date,FALSE);
				
				if($courses = $this->input->post('course')){
					$course_id=$courses[0];
					//update user members ship 
					$this->user_model->update_membership($id,$course_id,true);
					//update the course_instructor table 
					foreach($courses as $course_id){
						$this->course_schedule_model->insert_instructor(array(
							'ciCsID'=>$course_id,
							'ciUID'=>$id,
						));
					}
					
				}
								
				
				$user_activation_link=base_url().'user/activate?email='.$email.'&activation_code='.$activation_code.'&user='.base64_encode($id);
				$email_template = get_content('email_templates','*','etID = 3');
				if(!empty($email_template)){
					$email_template=$email_template[0];
					$searchReplaceArray = array(
						 '[AccountActivationUrl]'   =>anchor($user_activation_link,$user_activation_link), 
						 '[UserName]'   =>$first_name.''.$last_name,
						 '[maximumResponse]'   =>FWPWD_EXPIRE_TIME,
						);
					$email_message =str_replace(
					  array_keys($searchReplaceArray), 
					  array_values($searchReplaceArray),$email_template->etCopy); 
					//get admin emails 
					$emails = get_admin_emails();  
					send_mail($email,SITE_NAME,SENDER_EMAIL,$email_template->etSubject,$email_message,$emails);
				}
				
				set_flash_message('Instructor account has been successfully created','success');
				redirect('edu_admin/user/index');
			}
		}
		
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/user/instructor';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	member
		@Author Name:	ben binesh
		@Date:			Oct, 01 2013
		@user_id        user id 
		@cid			course_id for which the transaction details need to be edited
		@Purpose:		add member also associate it with the course 
	
	*/
	
	
	function member($user_id=0,$cid=0)
	{
		$data=array();
		$error = false;
		$errors = array();
		$this->new_record=true;
		$this->load->model('course_schedule_model');
		$this->load->model('checkout_model');
		
		if($course_id = $this->input->get('course_id')){
			//if request for add new member for the course enrolles page 
			$data['course']=$this->course_schedule_model->get_course_detail($course_id);
			if(empty($data['course']))
				redirect('home/error_404');
		}else{
			//if request for adding new member from the user index page 
			$expired=FALSE;
			if($user_id)
				$expired=TRUE;
			$data['courses'] = $this->course_schedule_model->get_courses(0,'',$user_id,0,-1,NULL,STATUS_PUBLISH,$expired);
			
		}
		
		
		if($user_id){
			//if request for editing membership from course enrollees page 
			$this->new_record    = false;
			$this->_id=$user_id;
			$data['result']=$this->user_model->get_single_record($user_id,'*',true);
			//get the transaction data 
			$transaction_where['payer_email']    =$data['result']->email;
			$transaction_where['product_type']   = PRODUCT_TYPE_COURSE;
			$transaction_where['payment_status'] = PAYMENT_ENROLLED;
			$transaction_where['item_number1'] = $cid;
			$transaction_id = get_single_value('pp_transactions','ppID',$transaction_where);
			if($transaction_id)
			{
				$data['transaction']=	$this->checkout_model->get_data_from_transaction($transaction_id);	
			}
			$data['current_course']=$this->course_schedule_model->get_course_detail($cid);
			if(empty($data['current_course']))
				redirect('home/error_404');
		}
		
		
		$this->page_title ='Member Editor';
		
		
		if(count($_POST)>0){
			
			$this->load->library('form_validation');
			$this->form_validation->set_rules('firstName', 'FirstName', 'trim|required');
			$this->form_validation->set_rules('lastName', 'Last Name', 'trim|required');
			$this->form_validation->set_rules('school_district', 'District', 'trim|required');
			$this->form_validation->set_rules('dis_iu_unit', 'IU', 'trim|required');
			$this->form_validation->set_rules('phone', 'Telephone', 'trim|required');
			$this->form_validation->set_rules('address', 'Address', 'trim|required');
			$this->form_validation->set_rules('city', 'City', 'trim|required');
			$this->form_validation->set_rules('state', 'State', 'trim|required');
			$this->form_validation->set_rules('zip', 'zip', 'trim|required');
			$this->form_validation->set_message('required',' %s must not be blank');
			$this->form_validation->set_message('valid_email', 'Email Address must be a valid e-mail address.');
			
			if('' != $this->input->post('email') ){
				$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_duplicate_email_check');
			}else{
				$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			}
						
			
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				if($this->new_record)
				{	
					
					$email      = $this->input->post('email');
					$first_name = $this->input->post('firstName');
					$last_name = $this->input->post('lastName');
					$signup_date=date('Y-m-d H:i:s');
					$activation_code = random_string('alnum', 12);
					$course_id=$this->input->post('course');
					$act48 = '';
					if($this->input->post('act48')){
						$act48 = $course_id;
					}
					
					$data_array = array(
						'firstName' => $first_name,
						'lastName' => $last_name,
						'email' => $email,
						'act48' => $act48,
						'accessLevel' => MEMBER,
						'signupDate' => $signup_date,
						'memberships' => $course_id,
						'membershipLastUsed	' => $course_id,
						'activationCode'=>$activation_code,
						'activationFlag'=>time(),
					);
					
					$id = $this->user_model->insert($data_array);
					$this->user_model->set_user_profile(
					$id,
					array(
						'user_id'=>$id,
						'phone' => $this->input->post('phone'),
						'districtAffiliation' =>  $this->input->post('districtAffiliation'),
						'districtID'=>$this->input->post('school_district'),
						'iuID'=>$this->input->post('dis_iu_unit'),
						'address'=>$this->input->post('address'),
						'city'=>$this->input->post('city'),
						'state'=>$this->input->post('state'),
						'zip'=>$this->input->post('zip'),
						'signupDate' => $signup_date,
						)
					);
													
					//send account email
					$user_activation_link=base_url().'user/activate?email='.$email.'&activation_code='.$activation_code.'&user='.base64_encode($id);
					$email_template = get_content('email_templates','*','etID = 1');
					if(!empty($email_template)){
						$email_template=$email_template[0];
						$searchReplaceArray = array(
							 '[AccountActivationUrl]'   =>anchor($user_activation_link,$user_activation_link), 
							 '[firstName]'   =>$first_name,
							 '[lastName]'   =>$last_name,
							 '[maximumResponse]'   =>FWPWD_EXPIRE_TIME,
							);
						$email_message = str_replace(
						  array_keys($searchReplaceArray), 
						  array_values($searchReplaceArray),$email_template->etCopy); 
					
						//get admin emails 
						$emails = get_admin_emails();
						send_mail($email,SITE_NAME,SENDER_EMAIL,$email_template->etSubject,$email_message,$emails);
					}
					
					//if course membership is selected 
					if($course_id)
					{
						$course = $this->course_schedule_model->get_course_detail($course_id);
						
						$item_name='';
						$item_name .= $course->cdCourseID.':'.$course->cdCourseTitle; 
						$item_name .='(';
						$item_name .= format_date($course->csStartDate,DATE_FORMAT);
						
						$course_location = $course->csCity.', '.$course->csState; 
						if(COURSE_ONLINE==$course->csCourseType)
							$course_location='Online';
						$item_name  .= '-'.$course_location;
						
						$item_name .=')';
						
						$price = $course->csPrice;
						//if non credit is checked add the non credit price 
						if($this->input->post('act48')){
							$price = $course->csNonCreditPrice;
						}
						
						$show_receipt = 1;
						if($this->input->post('show_receipt')){
							$show_receipt=0;
						}
						
						//add manual transaction details 
						$transaction_data=array(
							'payer_email'=>$email,
							'first_name'=>$first_name,
							'last_name'=>$last_name,
							'address_street'=>$this->input->post('address'),
							'address_city'=>$this->input->post('city'),
							'address_state'=>$this->input->post('state'),
							'address_zip'=>$this->input->post('zip'),
							'payment_gross'=>$price,
							'payment_date'=>date('Y-m-d H:i:s'),
							'payment_status'=>PAYMENT_ENROLLED,
							'num_cart_items'=>1,
							'quantity1'=>1,
							'item_number1'=>$course->csID,
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
						
						//send the  email to user about successfull  course enrollment 
						$email_template = get_content('email_templates','*','etID = 14');
						if(!empty($email_template)){
							$email_template=$email_template[0];
							$course_dates='';
							if(count($course->course_dates)>0){
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
									 '[UserName]'   =>$first_name.' '.$last_name, 
									 '[CourseTitle]'   =>$course->cdCourseID.':'.$course->cdCourseTitle,
									 '[CourseLocation]'   =>$course->csLocation,
									 '[CourseAddress]'   =>$course->csAddress,
									'[CourseCity]'   =>$course->csCity,
									 '[CourseState]'   =>$course->csState,
									 '[CourseDates]'   =>$course_dates,
									 
									);
							$email_message =str_replace(
								  array_keys($searchReplaceArray), 
								  array_values($searchReplaceArray),$email_template->etCopy); 
							//get admin emails 
							$emails = get_admin_emails();	  
							send_mail($email,SITE_NAME,SENDER_EMAIL,$email_template->etSubject,$email_message,$emails);		  
						}
					}
					set_flash_message('Member details has been successfully added','success');
				}
				else
				{
					$email      = $this->input->post('email');
					
					//edit members code
					$previous_courses = get_single_value('users','memberships','id = '.$user_id);	
					if( '' == $previous_courses ){
						$previous_courses = array();
					}else{
						$previous_courses = explode(',',$previous_courses);
					}
										
					$courses_selected='';
					$course_id=0;
					$de_selected_courses=array();
					if(isset($_POST['courses']) && count(0 < $_POST['courses'])){
						$course_id=$_POST['courses'][0];
						$courses_selected = implode(',',$this->input->post('courses'));
						$de_selected_courses = array_diff($previous_courses,$_POST['courses']);
					}
					else
					{
						$de_selected_courses=$previous_courses;
					}
								
					if( 0< count($de_selected_courses))
					{
						//make status as unenrolled
						$this->load->model('course_reservation_model');
						$this->course_reservation_model->set_unenrolled($de_selected_courses,$this->input->post('email'));
						//update the transaction data for these courses 
						foreach($de_selected_courses as $de_selected_course)
						{
								
								$transaction_where['payer_email']    = $email;
								$transaction_where['product_type']   = PRODUCT_TYPE_COURSE;
								$transaction_where['item_number1'] = $de_selected_course;
								$transaction_where['payment_status'] = PAYMENT_ENROLLED;
								$transaction_id = get_single_value('pp_transactions','ppID',$transaction_where);
								if($transaction_id){
									//update the transaction 
									$this->checkout_model->update_transaction($transaction_id,array(
										'payment_status'=>PAYMENT_REFUNDED
									));
								}	
							//get the transaction 
						}
						
						
					}
					//update payment details 
					if(isset($data['transaction']->ppID)){
						$show_receipt = 1;
						if($this->input->post('show_receipt')){
							$show_receipt=0;
						}
						//add manual transaction details 
						$transaction_data=array(
							'payment_mode'=>$this->input->post('payment_mode'),
							'manual_comment'=>$this->input->post('manual_comment'),
							'manual_comment_self'=>$this->input->post('manual_comment_self'),
							'check_number'=>$this->input->post('check_number'),
							'show_receipt'=>$show_receipt,
						);
						$this->checkout_model->update_transaction($data['transaction']->ppID,$transaction_data);	
					}
					
					//get the previous non credit courses 
					$previous_act48=$data['result']->act48;
					$act48 = '';
					if($previous_act48)
					{	$act48_array = array();
						$previous_act48_array=explode(',',$previous_act48);
						if($this->input->post('act48')){
							$act48_array=array_merge($previous_act48_array,array($data['current_course']->csID)); 
						}else{
							$act48_array=array_diff($previous_act48_array,array($data['current_course']->csID));
						}
						if(0 < count($act48_array))
						{
							$act48=implode(',',$act48_array);
						}
						else
						{
							$act48='';
						}
							
					}
					else
					{
						$act48 = '';
						if($this->input->post('act48')){
							$act48 = $data['current_course']->csID;
						}	
					}
					
					
					$data_array = array(
						'firstName' => $this->input->post('firstName'),
						'lastName' => $this->input->post('lastName'),
						'email' => $this->input->post('email'),
						'memberships' =>$courses_selected,
						'membershipLastUsed	' => $course_id,
						'act48' => $act48,
						
					);
					$this->user_model->update($user_id,$data_array);
					$this->user_model->set_user_profile(
						$user_id,
						array(
							'user_id'=>$user_id,
							'phone' => $this->input->post('phone'),
							'districtAffiliation' =>  $this->input->post('districtAffiliation'),
							'districtID'=>$this->input->post('school_district'),
							'iuID'=>$this->input->post('dis_iu_unit'),
							'address'=>$this->input->post('address'),
							'city'=>$this->input->post('city'),
							'state'=>$this->input->post('state'),
							'zip'=>$this->input->post('zip'),
						)
					);
					set_flash_message('Member details has been successfully updated','success');		
				}
				if($redirect = $this->input->get('redirect'))
					redirect($redirect);
				
				if($this->input->get('course_id')){
					redirect('edu_admin/course_schedule/enrollees?course_id='.$course_id);
				}
				redirect('edu_admin/user/index');
				
			}
		}
		
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/user/member';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	/**
		@Function Name:	_export
		@Author Name:	ben binesh
		@Date:			Sept, 20 2013
		@Purpose:		export enrollees details
	
	*/
	function _export()
	{
		
		$name         = $this->input->get('name'); 
		$email        = $this->input->get('email'); 
		$access_level = ($this->input->get('access_level')  != '' )?$this->input->get('access_level'):''; 
		$status       = ($this->input->get('status')  != '' )?$this->input->get('status'):''; 
		$results      = $this->user_model->get_records( $name, $email ,$access_level, 0 , -1,$status );
		if(empty($results))
		{
			set_flash_message('No records to export ','error');
			redirect('edu_admin/user/index');
		}
			
		
		$export_array = array();
		//build columns 
		$export_array[] = array(
			'S.No',
			'Name',
			'Birth Date',
			'Email',
			'Phone',
			'Address',
			'City',
			'State',
			'zip',
			'School District',
			'IU',
			'Track',
			'Status',
			'Account Type',
			'Last Login',
		);
		$i=1;
		
		$school_district='';
		
		foreach($results as $result)
		{
			
			//School District 
			if(is_numeric($result->districtAffiliation)){
				$school_district= get_single_value('district','disName','disID = '.$result->districtAffiliation) ;
			}else{
				$school_district= $result->districtAffiliation; 
			}
			
			//iu
			$iu='';	
			if($result->iuID !=NULL)
				$iu = get_single_value('iu_unit','iuName','iuID = '.$result->iuID) ;
			
			//track
			$track='Not Available';
			if($result->gradeSubject)
				$track=get_single_value('tracks','trName','trID = '.$result->gradeSubject) ;
			
			
			$export_array[]=array(
				$i,
				$result->lastName.' '.$result->firstName.'('.$result->userName.')',
				format_date($result->birthDate,DATE_FORMAT),
				$result->email,
				$result->phone,
				$result->address,
				$result->city,
				$result->state,
				$result->zip,
				$school_district,
				$iu,
				$track,
				$this->user_model->show_status($result->activationFlag),
				$this->user_model->show_access_level($result->accessLevel),
				format_date($result->lastLogin,DATE_FORMAT.' '.TIME_FORMAT),
				
			);
			$i++;
		}
		$file_name = 'users-'.time();
		$file_name=url_title($file_name,'-',TRUE);
		//check the report type choice ,default is csv 
		//generate csv file 
		$this->load->helper('csv');
		array_to_csv($export_array, $file_name.'.csv');
		
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */