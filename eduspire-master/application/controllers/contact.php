<?php

/**

@Page/Module Name/Class: 		contact.php

@Author Name:			 		ben binesh

@Date:					 		Sept, 03 2013

@Purpose:		        		Contain all controllers functions for the contact  

@Table referred:				NIL

@Table updated:					NIL

@Most Important Related Files	NIL



Chronological development

***********************************************************************************

Ref No.  |   Author name	| Date		| Severity 	| Modification description

***********************************************************************************

RF1       |  Janet Rajani  |  Dec,24 2013  | minor  |  Added captcha 

*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Contact extends CI_Controller {

	

	/**

		@Function Name:	index

		@Author Name:	binesh

		@Date:			Sept, 03 2013

		@Purpose:		load the contact form and handle post request 

	

	*/

	public function index()

	{
		use_ssl(FALSE);
		
		$data=array();

		$error=false;

		$errors=array();

                

		$data['main'] = 'contact';

		$this->load->model('contact_model');

		$this->page_title='Contact Us';

		$this->load->helper(array('form','cap'));

		if(count($_POST)>0){

			$this->load->library('form_validation');

			$this->form_validation->set_rules('contFirstName', 'First Name', 'trim|required');

			$this->form_validation->set_rules('contLastName', 'Last Name', 'trim|required');

			$this->form_validation->set_rules('contEmail', 'Email', 'trim|required|valid_email');

			$this->form_validation->set_message('required', '%s must not be blank');

			$this->form_validation->set_message('valid_email', 'Email Address must be a valid e-mail address.');

                        //RF1

                        $this->form_validation->set_rules('captcha', 'Captcha', 'trim|required|callback_captcha');

                        //End RF1

			if ($this->form_validation->run() == TRUE && $error==false  )

            {
					
					$data_array = array(

							'contFirstName' => $this->input->post('contFirstName'),

							'contLastName' => $this->input->post('contLastName'),

							'contEmail' => $this->input->post('contEmail'),

							'contMessage' => $this->input->post('contMessage'),

							'contDate' => date('Y:m:d H:i:s'),

								);

					
					
					$this->contact_model->insert($data_array);
					
					$email=$this->input->post('contEmail');
					$firstName=$this->input->post('contFirstName');
					$lastName=$this->input->post('contLastName');
					$message=$this->input->post('contMessage');
					
					/*send email to  admins */
					$email_template = get_content('email_templates','*','etID = 28');
					if(!empty($email_template)){
						
						$email_template=$email_template[0];
						$searchReplaceArray = array(
							 '[UserName]'   =>$firstName.' '.$lastName, 
							 '[UserEmail]'   =>$email,
							 '[ContactMessage]'   =>$message,
							
							);
						 $email_message = str_replace(
						  array_keys($searchReplaceArray), 
						  array_values($searchReplaceArray),$email_template->etCopy); 
						
						//get admin emails 
						$emails = get_admin_emails(); 
						send_mail(ADMIN_EMAIL,$firstName.' '.$lastName,$email,$email_template->etSubject,$email_message,$emails);	  
					}
				
				//redirect to the thanks page   

				redirect('thanks-contact-us');

				

			}

		}

		

		/**

			meta information

		*/

		$data['errors'] = $errors;

		$data['meta_title']='Contact Us';

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

            use_ssl(FALSE);

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

