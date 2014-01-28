<?php 
/**
@Page/Module Name/Class: 		user.php
@Author Name:			 		binesh
@Date:					 		Sept, 05 2013
@Purpose:		        		Contain all general controller functions for the users 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {
	public $js;
	protected $_id;
	public function __construct()
	{
		parent::__construct();
		use_ssl(FALSE);
		$js=array();
		$this->load->helper('form');
		$this->load->model('user_model');
		$this->load->model('testimonials_model');
		$this->_id=$this->session->userdata('user_id');
	}

	/**

		@Function Name:	index
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		load the list of users 
	*/
	public function index()
	{
		if(!is_logged_in()) {
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}
		$data = array();
		$data['main'] = 'user/list';
		$data['name'] = $this->input->get('name'); 
		$this->page_title="Member Directory";
		$num_records          = $this->user_model->count_users( $data['name'],0,'',ACCOUNT_ACTIVE);
		$base_url             = base_url().'user/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->user_model->get_users( $data['name'],0 ,'' ,ACCOUNT_ACTIVE, $start , $per_page );
		$data['action']=base_url().'user/index';
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['meta_title']='Member List';	
		$data['meta_descrption']='Member List';
		$this->load->vars($data);
		$this->load->view('template');
	}

	/**

		@Function Name:	forgot_credential
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		show the form to get email and verify it ,send password reset instruction  	or username  
	*/

	function forgot_credential(){
		if(is_logged_in()) {
			redirect("/");
		}
		$data=array();
		$error = false;
		$errors = array();
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('email', 'Email Address', 'trim|required|valid_email');	
			$this->form_validation->set_message('required','%s must not be blank. ');
			$this->form_validation->set_message('valid_email','%s be a valid e-mail address. ');
			if ( $this->form_validation->run() == TRUE && $error == false  )
			{
				if( $user = $this->user_model->get_user_byEmail($this->input->post('email')) ){
					if(ACCOUNT_ACTIVE != $user->activationFlag)
					{
						$errors[]='Your Account is Inactive';
					}
					else
					{
						if( 'username' == $this->input->get('ref') )
						{
							//request for username 
							$email_template = get_content('email_templates','*','etID = 17');
							if(!empty($email_template)){
								$email_template=$email_template[0];
								$searchReplaceArray = array(
									'[FirstName]' =>$user->firstName, 
									'[LastName]'  =>$user->lastName,
									'[userName]'  =>$user->userName,
									);
								$email_message = str_replace(
										array_keys($searchReplaceArray), 
										array_values($searchReplaceArray),$email_template->etCopy); 
								//get admin emails 
								$emails = get_admin_emails(); 
								send_mail($user->email,SITE_NAME,SENDER_EMAIL,$email_template->etSubject,$email_message,$emails);	  
							}
							set_flash_message('An email with your eduspire username  has been sent to your email address','success');
						}
						else
						{
							//request for the password reset 
							$this->load->helper('string');
							$verification_code = random_string('alnum', 12);
							$update_array = array(
								'forgotPWCode'=>$verification_code,
								'forgotPWFlag'=>time()
							);
							$this->user_model->update($user->id,$update_array);
							$forgot_password_link=base_url().'user/reset_password?email='.$user->email.'&verification_code='.$verification_code.'&user='.base64_encode($user->id);
							//get the reset password email template 
							$email_template = get_content('email_templates','*','etID = 2');
							if(!empty($email_template)){
								$email_template=$email_template[0];
								$searchReplaceArray = array(
									'[RecoverPasswordURL]'   =>$forgot_password_link, 
									'[userName]'   =>$user->userName,
									'[maximumResponse]'   =>FWPWD_EXPIRE_TIME,
								);
								$email_message =  str_replace(

								  array_keys($searchReplaceArray), 

								  array_values($searchReplaceArray),$email_template->etCopy); 

								//get admin emails 

								$emails = get_admin_emails();

								send_mail($user->email,SITE_NAME,SENDER_EMAIL,$email_template->etSubject,$email_message,$emails);	  

							}

							

							set_flash_message('An email with password reset instruction has been sent to your email address','success');

							redirect('login/signin');

							

						}

					}

										

				}else{

					$errors[]='Email Address does not match any user account email address';	

				}

				

				

			}

		}

		

		$data['errors']=$errors;

		$data['main'] = 'user/forgot_credential';

		$this->load->vars($data);

		$this->load->view('template');

	}

	

	/**

		@Function Name:	change_password

		@Author Name:	ben binesh

		@Date:			Sept, 09 2013

		@Purpose:		show the change password form for logged in  user 

	

	*/

	

	function change_password(){

		if(!is_logged_in()) {

			redirect("login/signin?redirect=".urlencode(get_current_url()));

		}

		$data=array();

		$error = false;

		$errors = array();

		$data['main']   = 'user/change_password';

		$data['user']   = $this->user_model->get_single_record($this->_id,'*',true);

		if( count( $_POST ) > 0 ){

			$this->load->library('form_validation');

			

			$this->form_validation->set_rules('old_password', 'Old Password', 'trim|required');

			if($this->input->post('old_password'))

				$this->form_validation->set_rules('old_password', 'Old Password', 'callback_old_password_check');

			$this->form_validation->set_rules('password', 'Password', 'trim|required|matches[c_password]|min_length[5]|max_length[15]');

			$this->form_validation->set_rules('c_password', 'Password Confirmation', 'trim|required');

			$this->form_validation->set_message('required', '%s must not be blank');

			if ($this->form_validation->run() == TRUE && $error==false  )

            {

				$update_data = array(

						'password'=>encrypt_password($this->input->post('password')),

				);

				$this->user_model->update($this->_id,$update_data);

				set_flash_message('Your password has been successfully changed ','success');

				redirect('user/profile');

			}

			

			

		}

		/**

			meta information

		*/

		

		$data['meta_title']      = 'Change Password';	

		$data['meta_descrption'] = 'Change Password';

		$this->load->vars( $data );

		$this->load->view('template');

		

	}

	

	/**

		@Function Name:	old_password_check

		@Author Name:	ben binesh

		@Date:			Aug, 30 2013

		@Purpose:		check the duplicate record in data base with same name

	

	*/

	

	public function old_password_check($password='')

	{	$id = $this->_id;

		if ($this->user_model->check_password($id,$password))

		{	

			return TRUE;

		}

		else

		{

			$this->form_validation->set_message('old_password_check', 'Invalid old password');

			return FALSE;

		}

		

		return false;

	}

	

	

	/**

		@Function Name:	reset_password

		@Author Name:	binesh

		@Date:			Aug, 16 2013

		@Purpose:		validate the link  show the reset password form  

	

	*/

	

	

	function reset_password(){

		

		if(is_logged_in()) {

			redirect("/");

		}

		$data=array();

		$error = false;

		$errors = array();

		

		$email = $this->input->get('email');

		$verification_code = $this->input->get('verification_code');

		$user_id =$this->input->get('user');

		

		if( (''== $email) || (''== $verification_code) || ('' == $user_id ) ){

			set_flash_message('Invalid password rest link or verification code ','warning');

			redirect('home/error');

		}

		

		if($user = $this->user_model->get_user_data($select='*',base64_decode($user_id),$email,$verification_code))

		{

			

			if($user->forgotPWFlag){

				if((floor(time() - $user->forgotPWFlag)/86400)  > FWPWD_EXPIRE_TIME){

					set_flash_message('You verfication code is expired ','error');

					redirect('home/error');

				}

			}else{

				set_flash_message('You verfication code is expired ','error');

				redirect('home/error');

			}

			//check the code expiration time 

			

		}

		else

		{

			set_flash_message('Invalid password rest link or verification code ','warning');

			redirect('home/error');

			

		}

		//if we reach so far  this means  the link is valid 

		// proceed with  the rest password procedure 

		if(count($_POST)>0){

			$this->load->library('form_validation');

			$this->form_validation->set_rules('username', 'username','trim|required');

			$this->form_validation->set_rules('password', 'New Password', 'trim|required|matches[c_password]|min_length[5]|max_length[15]');

			$this->form_validation->set_rules('c_password', 'Password Confirmation', 'required');

			if ( $this->form_validation->run() == TRUE && $error == false  )

            {

				//verify user name 	

				if($user=$this->user_model->get_user_byUserName($this->input->post('username'))){

				

					$update_data = array(

						'password'=>encrypt_password($this->input->post('password')),

						'forgotPWCode'=>NULL,

						'forgotPWFlag'=>0,

						

					);

					$this->user_model->update($user->id,$update_data);

					set_flash_message('You password has been successfully changed ','success');

					redirect('home/message?title=Success');

				}else{

					$errors[]='Invalid username';	

				}

			}

		}

		$data['errors']=$errors;

		$data['main'] = 'user/reset_password';

		$data['layout']='two-column-right';

		$data['sidebar_data'] = get_content('cms_page','cpDescription','cpID = 2');

		$data['sidebar'] = 'home';

		$this->load->vars($data);

		$this->load->view('template');

		

	}

	

	/**

		@Function Name:	profile

		@Author Name:	binesh

		@Date:			Sept, 09 2013

		@Purpose:		show the user profile page 

	

	*/

	

	

	public function profile(){

		if(!is_logged_in()) {

			redirect("login/signin?redirect=".urlencode(get_current_url()));

		}

		

		

		$data=array();

		

		$data['main'] = 'user/profile';

		$data['layout']='two-column-right';

		$this->page_title="Profile Information";

		$data['user'] = $this->user_model->get_single_record($this->_id,'*',true);

		$this->load->model('news_model');

		$data['archives'] = $this->news_model->get_records( '',STATUS_PUBLISH, 0 ,2,'nwID,nwTitle,nwDate,nwDescription');

		/**

			meta information

		*/

		$data['sidebar']    = 'user';

		$data['meta_title'] = $this->session->userdata('display_name').' Profile';	

		$data['meta_descrption']='Profile Information'		;

		$this->load->vars($data);

		$this->load->view('template');

	}

	

	/**

		@Function Name:	edit

		@Author Name:	ben binesh

		@Date:			Sept, 09 2013

		@Purpose:		show the edit  profile page and update the user profile 

	

	*/

	public function edit(){
		if(!is_logged_in()) {
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}
		$data = array();
		$error = false;
		$errors = array();
		$this->page_title='Profile Information';
		$data['main'] = 'user/edit_profile';
		$data['user'] = $this->user_model->get_single_record($this->_id,'*',true);
		$this->js[]='js/tinymce/tinymce.min.js';
		$image = $data['user']->profileImage;
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('userName', 'Username', 'trim|required|min_length[5]|max_length[15]');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('firstName', 'FirstName', 'trim|required');
			$this->form_validation->set_rules('lastName', 'Last Name', 'trim|required');
			$this->form_validation->set_rules('gender', 'Gender', 'trim|required');
			$this->form_validation->set_rules('address', 'Address', 'trim|required');
			$this->form_validation->set_rules('city', 'City', 'trim|required');
			$this->form_validation->set_rules('state', 'State', 'trim|required');
			$this->form_validation->set_rules('zip', 'zip', 'trim|required');
			$this->form_validation->set_rules('phone', 'Phone', 'trim|required');
			$this->form_validation->set_rules('school_district', 'District', 'trim|required');
			$this->form_validation->set_rules('dis_iu_unit', 'IU', 'trim|required');
			$this->form_validation->set_rules('buildingAssigned', 'School Building Assigned', 'trim|required');
			$this->form_validation->set_rules('buildingAddress', 'School Address', 'trim|required');
			$this->form_validation->set_rules('buildingCity', 'City', 'trim|required');
			$this->form_validation->set_rules('buildingState', 'State', 'trim|required');
			$this->form_validation->set_rules('buildingZip', 'zip', 'trim|required');
			$this->form_validation->set_rules('role', 'Role', 'trim|required');
			$this->form_validation->set_rules('yearsActive', 'Years in this role', 'trim|required');
			$this->form_validation->set_rules('gradCoursesTaking', 'Number of grade courses', 'trim|required');
			$this->form_validation->set_rules('gradeSubject', 'Grade Subject', 'trim|required');
			$this->form_validation->set_rules('level', 'Grade Levels', 'required');
			$this->form_validation->set_rules('birthDate', 'birth Date', 'trim|callback_birth_date_check');
			if( ('' != $this->input->post('userName')) &&   '' != $this->input->post('email') ){
				$this->form_validation->set_rules('userName', 'Username', 'callback_duplicate_name_check|min_length[5]|max_length[15]');
				$this->form_validation->set_rules('email', 'email', 	'callback_duplicate_email_check');
			}

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
				$signup_date=$data['user']->signupDate;
				$data_array = array(
					'userName' => $this->input->post('userName'),
					'firstName' => $this->input->post('firstName'),
					'lastName' => $this->input->post('lastName'),
					'profileImage' =>$image,
					'email' => $this->input->post('email'),
					'signupDate' => $signup_date,
				);
				$this->user_model->update($this->_id,$data_array);
				$this->_set_profile_data($this->_id,$signup_date,FALSE);
				set_flash_message('You profile has been successfully updated','success');
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
				redirect('user/profile');
			}
		}
		/**
			meta information
		*/
		$data['errors']=$errors;
		$data['image']=$image;
		$data['meta_title']=$this->session->userdata('display_name').' Profile';	
		$data['meta_descrption']='Member Dashboard'		;
		$this->load->vars($data);
		$this->load->view('template');

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

		@Function Name:	_set_profile_data 

		@Author Name:	binesh

		@Date:			Sept, 09 2013

		@Purpose:		insert update user profile 

	

	*/

	

	function _set_profile_data($id,$singup_date='',$update = FALSE){

			$level=($this->input->post('level'))?implode(',',$this->input->post('level')):'';

			$data_array = array(

					'user_id' => $id,

					'usrBio' => $this->input->post('usrBio'),

					'gender' => $this->input->post('gender'),

					'twitter' => $this->input->post('twitter'),

					'facebook' => $this->input->post('facebook'),

					'aim' => $this->input->post('aim'),

					'msn' => $this->input->post('msn'),

					'signupDate' => $singup_date,

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

		@Function Name:	duplicate_name_check

		@Author Name:	binesh

		@Date:			Sept, 09 2013

		@Purpose:		check the duplicate record in data base with same name

	

	*/

	

	public function duplicate_name_check($title='')

	{	

		$id = $this->_id;

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

		@Date:			Sept, 09 2013

		@Purpose:		check the duplicate record in data base with same email

	

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

		@Function Name:	view

		@Author Name:	binesh

		@Date:			Sept, 09 2013

		@Purpose:		load the user public profile page 

                Chronological Development

                *****************************************************************

                RF  Developer Name      Date            Description

                **************************************************************************************** 

                RF1 Janet Rajani        18-Nov 2013     Testimonials approved by admin and instructor 

                                                        will be display on instructor profile

	*/

	

	function view($title='',$id=0)

	{

		if(!is_logged_in()) {

			redirect("login/signin?redirect=".urlencode(get_current_url()));

		}

		

		$data=array();

		

		$data['main']           = 'user/view';

                

		$data['user']           = $this->user_model->get_single_record($id,'*',true);

        //RF1

        $data['testimonials']   = $this->testimonials_model->get_instructor_profile_testimonials($id);

       //End RF1

		if(empty($data['user']))

		{

			show_404();

		}

		/**

			meta information

		*/

		$data['meta_title'] =$data['user']->firstName.' '.$data['user']->lastName.' Profile';	

		$data['meta_descrption']=$data['user']->firstName.' '.$data['user']->lastName.' Profile';			

		$this->load->vars($data);

		$this->load->view('template');

	}

	

	/**

		@Function Name:	activation

		@Author Name:	binesh

		@Date:			Sept, 10 2013

		@Purpose:		load the user activation view ,activate the user account 

	

	*/

	function activate(){

		

		if(is_logged_in())

		{

			redirect("/");

		}

		$data=array();

		$error = false;

		$errors = array();

		$this->page_title='Account Setup';

		$email = $this->input->get('email');

		$activation_code = $this->input->get('activation_code');

		$user_id =$this->input->get('user');

		if( (''== $email) || (''== $activation_code) || ('' == $user_id ) )

		{

			set_flash_message('Invalid account activation link or activation code ','warning');

			redirect('home/error');

		}

		if($user = $this->user_model->get_user_data($select='*', base64_decode($user_id), $email, '' ,$activation_code))

		{

			

			if($user->activationFlag)

			{

				//check the code expiration time 

				if((floor(time() - $user->activationFlag)/86400)  > FWPWD_EXPIRE_TIME)

				{

					set_flash_message('You account activation code is expired ','error');

					redirect('home/error');

				}

			}

			else

			{

				set_flash_message('You account activation code is expired ','error');

				redirect('home/error');

			}

					

		}

		else

		{

			set_flash_message('Invalid account activation link or activation code ','warning');

			redirect('home/error');

			

		}

		//if we reach so far  this means  the link is valid 

		// proceed with  the user activation procedure

		if(count($_POST)>0)

		{

			$this->load->library('form_validation');

			$this->form_validation->set_rules('username', 'Username','trim|required|min_length[5]|max_length[15]');

			//check user name duplicate name 

			if('' != $this->input->post('username')){

				$this->form_validation->set_rules('username', 'Username', 'callback_duplicate_name_check|min_length[5]|max_length[15]');

			}

			$this->form_validation->set_rules('password', 'New Password', 'trim|required|matches[c_password]|min_length[5]|max_length[15]');

			$this->form_validation->set_rules('c_password', 'Password Confirmation', 'required');

			$this->form_validation->set_message('required', '%s must not be blank');

			

			if ( $this->form_validation->run() == TRUE && $error == false  )

            {

				//update user details

				$update_data = array(	

						'userName'=>$this->input->post('username'),

						'password'=>encrypt_password($this->input->post('password')),

						'activationCode'=>-1,

						'activationFlag'=>ACCOUNT_ACTIVE,

						

				);

				

				$this->user_model->update($user->id,$update_data);

				set_flash_message('Your account has been successfully created. Please login here to complete your profile','success');

				//redirect to login page 

				$edit_profile_url=base_url().'user/edit?ref=new';

				redirect("login/signin?redirect=".urlencode($edit_profile_url));

				

			}

		}

		$data['errors']=$errors;

		$data['main'] = 'user/activate';

		$this->load->vars($data);

		$this->load->view('template');

	}

	

	/**

		@Function Name:	check_username

		@Author Name:	ben binesh

		@Date:			Sept, 10 2013

		@Purpose:		check the existing username in the database 

	

	*/

	

	function check_username(){

		if($username=$this->input->post('username')){

			if($user=$this->user_model->get_user_byUserName($username,'id')){

				set_response(

				true,

				'Username is not available',

				NULL

				);

			}else{

				set_response(

				NULL,

				NULL,

				'Available'

				);

			}

		}else{

			set_response(

				true,

				'Please enter valid username',

				NULL

				);

		}

		

	}

	

	/**

		@Function Name:	receipts

		@Author Name:	ben binesh

		@Date:			Sept, 10 2013

		@Purpose:		check the existing username in the database 

	

	*/

	

	function receipt(){

		$email=$this->session->userdata('email');

		$data = array();

		$this->load->model('checkout_model');

		$num_records          = $this->checkout_model->count_records( '', $email,'','','',1);

		$base_url             = base_url().'user/receipt';

		$start                = $this->uri->segment($this->uri->total_segments());

		if( !is_numeric( $start ) ){

			$start = 0;

		}

		$per_page            = 20; 

		$data['results']     = $this->checkout_model->get_records('', $email, '', '', '' , $start , $per_page,1 );

		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  

		

		$data['main']             = 'user/receipts';

		$data['meta_title']       = 'My Receipts';	

		$data['meta_descrption']  = 'My Receipts';

		

		$this->load->vars($data);

		$this->load->view('template');

		

	}

	

	

	/**

		@Function Name:	view_receipt

		@Author Name:	ben binesh

		@Date:			Sept, 10 2013

		@Purpose:		check the existing username in the database 

	

	*/

	

	function view_receipt($id=0){

		$data=array();

		$this->load->model('checkout_model');

		$this->load->model('course_schedule_model');

		$data['result']=$this->checkout_model->get_single_record($id);

		if(empty($data['result']))

			show_404('page');

		if($data['result']->item_number1){

			$data['course'] = $this->course_schedule_model->get_course_detail($data['result']->item_number1);

		}	

		$data['main']             = 'user/receipt';

		$data['meta_title']       = 'My Receipt';	

		$data['meta_descrption']  = 'My Receipt';

		$data['userId']           = $id;

		$this->load->vars($data);

		$this->load->view('letterhead');

	

	}	

	/**

		@Function Name:	print_receipt

		@Author Name:	Alan Anil

		@Date:			Dec, 19 2013

		@Purpose:		For viewing and printing receipt on eduspire letterhead. 

	

	*/

	

	function print_receipt($id=0)

	{

		$data = array();

		$this->load->model('checkout_model');

		$this->load->model('course_schedule_model');

		$data['result']=$this->checkout_model->get_single_record($id);

		if(empty($data['result']))

			show_404('page');

			

		if($data['result']->item_number1){

			$data['course'] = $this->course_schedule_model->get_course_detail($data['result']->item_number1);

		}	

		$data['main']             = 'user/printReceipt';

		$data['meta_title']       = 'My Receipt';	

		$data['meta_descrption']  = 'My Receipt';

		

		$this->load->vars($data);

		$this->load->view('letterhead');

	

	}	

	

	

	/**

		@Function Name:	switch_admin

		@Author Name:	ben binesh

		@Date:			Sept, 25 2013

		@Purpose:		switch user back to admin session 

	

	*/

	

	function switch_admin(){

		if(!is_logged_in()) {

			redirect("/");

		}

		

		$this->load->model('user_model');

		$this->load->model('permission_model');	

		$id=$this->session->userdata('emulate');

		$user = $this->user_model->get_single_record($id,'*',false);

		if(empty($user)){

			redirect('home/error404');

		}

		

		//update session values 

		$session_data = array(

							   'user_id'  => $user->id,

							   'email'     => $user->email,

							   'user_name' =>$user->userName, 

							   'access_level' =>$user->accessLevel, 

							   'display_name' =>$user->firstName.' '.$user->lastName ,

							   'logged_in' => TRUE,

								'emulate'   => '',

							    'permission' =>$this->permission_model->get_user_permission_array($user->accessLevel),

						);

		$this->session->set_userdata($session_data);

		if(is_allowed('edu_admin/home/index')){

			redirect('edu_admin');

		}else

			redirect('/');

		

		

	

	}



	/**

		@Function Name:	switch_course

		@Author Name:	ben binesh

		@Date:			Oct 04 2013

		@Purpose:		switch admin course

	

	*/

	

	function switch_course($id=0){

		

		$data=array();

		$this->load->model('course_schedule_model');

		$data['courses'] = $this->course_schedule_model->get_courses('','',$this->_id,0,10,'');

		if($id){

			//proceed with the switching course

			$this->load->model('user_model');

			$this->user_model->update_membership($this->_id,$id,true);

			$data['reload']=true;

		}

		$data['main']             = 'user/switch_course';

		$data['meta_title']       = 'Switch Course';	

		$this->load->vars($data);

		$this->load->view('popup');

			

			

	}	

	

	

	/**

		@Function Name:	about

		@Author Name:	ben binesh

		@Date:			Oct 17, 2013

		@Purpose:		load the user edit form for about content 

	

	*/

	

	function about(){

		$data = array();

		$error = false;

		$errors = array();

		

		if(!is_logged_in()) {

			redirect("login/signin");

		}

		$data['user'] = $this->user_model->get_single_record($this->_id,'*',true);

		if(empty($data['user'])){

			show_404();

		}

		if(count($_POST)>0){

			$this->load->library('form_validation');

			$this->form_validation->set_rules('usrBio', 'About', 'trim|required');

			if ($this->form_validation->run() == TRUE && $error==false  )

            {

				$this->user_model->set_user_profile(

					$this->_id,

					array(

						'usrBio' => $this->input->post('usrBio'),

					)

				);

				$data['reload']=true;

				set_flash_message('Your about details has been successfully updated','success');

			}

		}

		$data['errors']=$errors;

		$data['main']             = 'user/about';

		$this->load->vars($data);

		$this->load->view('popup');

		

	}

	

	/**

		@Function Name:	school

		@Author Name:	ben binesh

		@Date:			Oct 17, 2013

		@Purpose:		load the user edit form for school info

	

	*/

	

	function school(){
		$data = array();
		$error = false;
		$errors = array();
		if(!is_logged_in()) {
			redirect("login/signin");
		}
		$data['user'] = $this->user_model->get_single_record($this->_id,'*',true);
		if(empty($data['user'])){
			show_404();
		}

		if(count($_POST)>0){

			$this->load->library('form_validation');

			//$this->form_validation->set_rules('districtAffiliation', 'School District', 'trim|required');

			$this->form_validation->set_rules('buildingAssigned', 'School Building Assigned', 'trim|required');

			$this->form_validation->set_rules('buildingAddress', 'School Address', 'trim|required');
			$this->form_validation->set_rules('school_district', 'District', 'trim|required');
			$this->form_validation->set_rules('dis_iu_unit', 'IU', 'trim|required');
			$this->form_validation->set_rules('buildingCity', 'City', 'trim|required');
			$this->form_validation->set_rules('buildingState', 'State', 'trim|required');
			$this->form_validation->set_rules('buildingZip', 'zip', 'trim|required');
			$this->form_validation->set_rules('role', 'Role', 'trim|required');
			$this->form_validation->set_rules('yearsActive', 'Years in this role', 'trim|required');
			$this->form_validation->set_rules('gradCoursesTaking', 'Number of grad courses', 'trim|required');
			$this->form_validation->set_rules('gradeSubject', 'Grade Subject', 'trim|required');
			$this->form_validation->set_rules('level', 'Grade Levels', 'required');
			$this->form_validation->set_message('required', '%s must not be blank');
			if ($this->form_validation->run() == TRUE && $error==false  )
			{
				$level=($this->input->post('level'))?implode(',',$this->input->post('level')):'';
				$this->user_model->set_user_profile(
					$this->_id,
					array(
						'user_id'=>$this->_id,
						'districtAffiliation' =>  $this->input->post('school_district'),
						'districtID'=>$this->input->post('school_district'),
						'iuID'=>$this->input->post('dis_iu_unit'),
						'buildingAssigned' =>  $this->input->post('buildingAssigned'),
						'buildingAddress' =>  $this->input->post('buildingAddress'),
						'buildingCity' =>  $this->input->post('buildingCity'),
						'buildingState' =>  $this->input->post('buildingState'),
						'buildingZip' =>  $this->input->post('buildingZip'),
						'gradCoursesTaking' =>  $this->input->post('gradCoursesTaking'),
						'gradeSubject' =>  $this->input->post('gradeSubject'),

						'yearsActive' =>  $this->input->post('yearsActive'),

						'role' =>  $this->input->post('role'),

						'level' => $level,

					)

				);

				$data['reload']=true;

				set_flash_message('Your school details has been successfully updated','success');

			}

		}

		$data['errors']=$errors;

		$data['main']             = 'user/school';

		$this->load->vars($data);

		$this->load->view('popup');

		

	}



	

	/**

		@Function Name:	contact

		@Author Name:	ben binesh

		@Date:			Oct 18, 2013

		@Purpose:		load the user edit form for contact info

	

	*/

	

	function contact(){

		$data = array();

		$error = false;

		$errors = array();

		if(!is_logged_in()) {

			redirect("login/signin");

		}

		$data['user'] = $this->user_model->get_single_record($this->_id,'*',TRUE);

		if(empty($data['user'])){

			show_404();

		}

		if(count($_POST)>0){

			$this->load->library('form_validation');

			$this->form_validation->set_rules('userName', 'Username', 'trim|required|min_length[5]|max_length[15]');

			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

			$this->form_validation->set_rules('email2', 'Secondry Email', 'trim|valid_email');

			$this->form_validation->set_rules('firstName', 'FirstName', 'trim|required');

			$this->form_validation->set_rules('lastName', 'Last Name', 'trim|required');

			$this->form_validation->set_rules('gender', 'Gender', 'trim|required');

			$this->form_validation->set_rules('address', 'Address', 'trim|required');

			$this->form_validation->set_rules('city', 'City', 'trim|required');

			$this->form_validation->set_rules('state', 'State', 'trim|required');

			$this->form_validation->set_rules('zip', 'zip', 'trim|required');

			$this->form_validation->set_rules('phone', 'Phone', 'trim|required');

			$this->form_validation->set_rules('birthDate', 'birth Date', 'trim|callback_birth_date_check');

			if('' != $this->input->post('userName')){

				$this->form_validation->set_rules('userName', 'Username', 'required|min_length[5]|max_length[15]|callback_duplicate_name_check');

			}

			

			if(  '' != $this->input->post('email') ){

				$this->form_validation->set_rules('email', 'email', 'callback_duplicate_email_check');

			}
			$this->form_validation->set_message('required', '%s must not be blank');
			

			if ($this->form_validation->run() == TRUE && $error==false  )

            {

				$this->user_model->update(

					$this->_id,

					array(

						'userName' => $this->input->post('userName'),

						'firstName' => $this->input->post('firstName'),

						'lastName' => $this->input->post('lastName'),

						'email' => $this->input->post('email'),

						

					)

					

				);

				

				

				$this->user_model->set_user_profile(

					$this->_id,

					array(
						'user_id'=>$this->_id,
						'gender' => $this->input->post('gender'),

						'twitter' => $this->input->post('twitter'),

						'facebook' => $this->input->post('facebook'),

						'aim' => $this->input->post('aim'),

						'msn' => $this->input->post('msn'),

						'siteURL' => $this->input->post('siteURL'),

						'address' => $this->input->post('address'),

						'city' => $this->input->post('city'),

						'state' => $this->input->post('state'),

						'zip' => $this->input->post('zip'),

						'phone' => $this->input->post('phone'),

						'mobileCarrier' => $this->input->post('mobileCarrier'),

						'birthDate' =>  $this->input->post('birth_year').'-'.$this->input->post('birth_month').'-'.$this->input->post('birth_day'),

						'email2' =>  $this->input->post('email2'),

					)

				);

				$data['reload']=TRUE;

				set_flash_message('Your contact details has been successfully updated','success');

			}	

		}

		$data['errors']=$errors;

		$data['main']             = 'user/contact';

		$this->load->vars($data);

		$this->load->view('popup');

		

	}

	

	

	function image(){
		$data = array();
		$error = FALSE;
		$errors = array();
		if(!is_logged_in()) {
			redirect("login/signin");
		}
		$data['user'] = $this->user_model->get_single_record($this->_id,'*',true);
		if(empty($data['user'])){
			show_404();
		}
		$image = $data['user']->profileImage;
		if(count($_POST)>0){
			if($_FILES['profileImage']['name'] != ''){
				$path = UPLOADS.'/users';
				$res_response = upload_file('profileImage',$path);
				if(is_array($res_response) && isset( $res_response['file_name'] ) ){
					// delete old file
					if( $image != '' ){
						//unlink the previously uploaded image 
						@unlink( $path.'/'.$image );
					}
					$image  = $res_response['file_name'];
				}
				else{
					$error = TRUE;
					$errors[] = $res_response;
				}
			}
			if(FALSE==$error){
				$this->user_model->update(
					$this->_id,
					array(
						'profileImage' =>$image,
					)
				);
				$data['reload']=TRUE;
				set_flash_message('Your image  has been successfully updated','success');
			}	
			}
		$data['image']  = $image;
		$data['errors'] = $errors;
		$data['main']   = 'user/image';
		$this->load->vars($data);
		$this->load->view('popup');
	}

}	
/* End of file*/

