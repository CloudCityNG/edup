<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
@Page/Module Name/Class: 		subscribe.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 12 2013
@Purpose:		        		Contain all controllers functions for the subscription
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 Chronological development
***********************************************************************************
Ref No.  |   Author name	| Date		| Severity 	| Modification description
***********************************************************************************
RF1       |  Janet Rajani  |  Dec,24 2013  | minor  |  Added captcha 
*/
class Subscribe extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
	}
	
	
	/**
		@Function Name:	index
		@Author Name:	binesh
		@Date:			Sept, 12 2013
		@Purpose:		load the subscription form and handle post request 
	
	*/
	public function index()
	{
        $js = array();
        $this->js[] ='js/frontend.js';
		$data=array();
		$error=false;
		$errors=array();
                $this->page_title="Stay Informed!";
		$data['main'] = 'subscribe';
		$this->load->model('newsletter_model');
		
		$this->load->helper('form');
                $this->load->helper('cap');
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('newsFirstName', 'First Name', 'trim|required|alpha');
			$this->form_validation->set_rules('newsLastName', 'Last Name', 'trim|required|alpha');
			$this->form_validation->set_rules('newsEmail', 'Email', 'trim|required|valid_email|is_unique[newsletter.newsEmail]');
			$this->form_validation->set_message('required', '%s must not be blank');
			$this->form_validation->set_message('valid_email', 'Email Address must be a valid e-mail address.');
			$this->form_validation->set_message('is_unique', 'Your email address is already subscribe ');
                        //RF1
                        $this->form_validation->set_rules('captcha', 'Captcha', 'trim|required|callback_captcha');
                        //End RF1
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'newsFirstName' => $this->input->post('newsFirstName'),
								'newsFirstName' => $this->input->post('newsFirstName'),
								'newsEmail' => $this->input->post('newsEmail'),
								'newsGradeLevel' => $this->input->post('newsGradeLevel'),
								'newsSchoolDistrict' => $this->input->post('school_district'),
								'newsTeachesSubject' => $this->input->post('newsTeachesSubject'),
								'newsReferralMethod' => $this->input->post('newsReferralMethod'),
								'newsReferralMethodOther' => $this->input->post('newsReferralMethodOther'),
								'newsIU' => $this->input->post('newsIU'),
								'newsSignupDate' => date('Y:m:d H:i:s'),
								);
				$this->newsletter_model->insert($data_array);
				//redirect to the thanks page   
				redirect('thanks-subscription-us');
				
			}
		}
		
		/**
			meta information
		*/
		$data['errors'] = $errors;
		$data['meta_title']='Subscribe';
		$this->load->vars($data);
		$this->load->view('template');
	}
		/*
                //RF4
		@Function Name:	captcha
		@Author Name:	Janet Rajani
		@Date:		Dec, 24 2013
		@Purpose:	validate captcha
	
	*/
	function captcha($captcha='')
	{	
                /* Get the actual captcha value that we stored in the session (see below) */
                $word = $this->session->userdata('captcha_word');
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
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */