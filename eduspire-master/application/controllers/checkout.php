<?php

/**
@Page/Module Name/Class:                        checkout.php
@Author Name:			 		Janet Rajani
@Date:					 	Sept, 23 2013
@Purpose:		        		Contain all controllers function  for ipad selection
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/

//Chronological development
//***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
/***********************************************************************************
//RF1.	  |  ben binesh		 | Oct,16 2013  | major	   | add remove registration functionality 
//RF2.	  |  ben binesh		 | Nov,12 2013  | major	   | add individuals course payment  functionality club
//RF3	  |  Janet Rajani        | Jan,8 2014  | major	   | Update credit at the time of payment
//***********************************************************************************/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Checkout extends CI_Controller 
{
	public $js;

	public function __construct() 
	{

		parent::__construct();
                use_ssl(TRUE);

		$this->page_title="Checkout";

                $js = array();

		// load form, url, general helper

		// load database user model

		// load library form_validation

		$this->load->model('checkout_model'); 

                $this->load->model('course_schedule_model'); 

                $this->load->model('course_reservation_model');

                $this->load->model('order_model');
                $this->load->model('login_model');
                $this->load->model('page_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->library('session');
                $this->load->model('user_model');
                $this->load->model('paypal_model');

	}

     /**
		@Function Name:	pay_for_course
		@Author Name:	Janet Rajani 
		@Date:		Sept, 16 2013
		@Purpose:	Display payPalPro form

	*/

    function pay_for_course()
    {

            $this->js[]='js/jquery-ui.js';
            $this->js[]='js/payment.js';
            $data['form_action']    ='';

            //Page Heading
            $this->page_title="Billing Information";

            $error  = false;

            //Var initialization for checking card exp date
            $error_card_expired = '';

            //If user is not logged in then move to checkout
            if(!is_checkout_logged_in())
            {	
                redirect('checkout');
            }

            //If user submits payPalPro form then validate it and process
            if($this->input->post('check_pay_form_validation'))
            {
               /*validate payment form*/
             
               $this->form_validation->set_rules('cardnumber','Card number','trim|required');
               $this->form_validation->set_rules('cardholder','Cardholder name','trim|required');
               $this->form_validation->set_rules('cardcvv','Card CVC','trim|required');
               $this->form_validation->set_message('required','%s must not be blank');

               //Check if card is expired
               if($this->checkout_model->check_date_validity($this->input->post('cardmonth'),$this->input->post('cardyear')))
               {
                   $error_card_expired    = 'Card expired';
                   $error   = true;
               }

               //If validation is done then process for payment and redirect to login page
               if(($this->form_validation->run()==TRUE) && ($error==false))
               {
                  
                    $this->_pay_for_course_submit();
               }
               
                /*End validation*/

            }//End If 

            else
            {
                    //If user didn't submit the payment form yet(comes first time to the form)
                    //RF3
                    $credit_type    = $this->input->post('credit_type');
                    $price          = $this->input->post('item_price');
                    $course_res_id  = $this->input->post('course_res_id');

                // Set request-specific fields.
                $user_personal_detail = $this->course_reservation_model->get_single_record($course_res_id);

                if(empty($user_personal_detail))
                {
                    redirect('checkout');
                }    

                $course_id              =  $user_personal_detail->urCourse;
                $data['urFirstName']    =  $user_personal_detail->urFirstName;
                $data['urLastName']     =  $user_personal_detail->urLastName;
                $data['urEmail']        =  $user_personal_detail->urEmail;

                  /*set course id and price in session*/  
                 $course_detail_session['payment']  = array(
                    'course_id_session'=>$course_id,
                    'price'=>$price,
                     'credit_type'=> $credit_type,
                    'course_res_id'=>$course_res_id,
                    'email'=>$user_personal_detail->urEmail
                );

                  $this->session->set_userdata( $course_detail_session);
                  //payment form action

                /*End set course id and price in session*/  
                
            }

            $data['error']  = $error_card_expired;
            $data['main'] = 'checkout/pay_for_course';
            $data['layout']='two-column-right-small';
            $data['sidebar']='checkout';
            $this->load->vars($data);
            $this->load->view('template');
    }

    /**
		@Function Name:	_pay_for_course_submit
		@Author Name:	Janet Rajani 
		@Date:		Sept, 16 2013
		@Purpose:	PayPalPro payment process here 
   */

    function _pay_for_course_submit()
    {
            ob_start();
            $get_course_session_array    = $this->session->userdata('payment');

            //take all values from session
            $course_id          = $get_course_session_array['course_id_session'];
            $course_amount      = $get_course_session_array['price'];
            $course_res_id      = $get_course_session_array['course_res_id'];
            //RF3
            $credit_type      = $get_course_session_array['credit_type'];

            //End session fetching
            //Get detail of selected course
            $course             = $this->course_schedule_model->get_course_detail($course_id );
            $course_title ='';

            $course_title .=$course->cdCourseID.':'.$course->cdCourseTitle; 
            $course_title .='(';

            $course_title .=format_date($course->csStartDate,DATE_FORMAT);
			$course_location = $course->csCity.', '.$course->csState; 
			if(COURSE_ONLINE==$course->csCourseType)
				$course_location='Online';
			
            $course_title .= '-'.$course_location;
            
			$course_title .=')';

            //fetch all submitted value of user card
            $paymentType        = 'Sale';
            $urFirstName        = urlencode($this->input->post('urFirstName'));
            $urLastName         = urlencode($this->input->post('urLastName'));
            $creditCardType     = urlencode($this->input->post('cardtype'));
            $credit_card_number   = urlencode($this->input->post('cardnumber'));
            //$cardholder         = $this->input->post('cardholder');

            //Build new string with last 4 digits XXXX-XXXX-XXXX-8678 to save in db
            $new_credit_card_number = "XXXX-XXXX-XXXX-" . substr($credit_card_number,-4,4);
            $expDateMonth       = $this->input->post('cardmonth');

            // Month must be padded with leading zero
            $padDateMonth       = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
            $expDateYear        = urlencode($this->input->post('cardyear'));
            $cvv2Number         = urlencode($this->input->post('cardcvv'));
           
            $country        = 'US';	// US or other valid country code
            $currencyID     = 'USD';// or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
             /*User info save in ppTransaction*/

            $user_detail_data = array(

                                        'payer_email'=>$get_course_session_array['email'],
                                        'first_name'=>$urFirstName,
                                        'last_name'=>$urLastName,
                                        'item_name1'=>$course_title,
                                        'item_number1'=>$course_id,
                                        'quantity1'=>1,
                                        'product_type'=>PRODUCT_TYPE_COURSE,
					'payment_date'=>date('Y-m-d H:i:s'),
                                        'credit_card_number'=>$new_credit_card_number,
                                        //'card_holder_name'=>$cardholder
            
                                     );

            $this->checkout_model->insert('pp_transactions',$user_detail_data);

            $ppID = $this->db->insert_id();

            /*End userinfo save*/

            /*save info in orders and orders_items table*/
            $order_max      = $this->order_model->select_max_id();// last order number in current orders

            $order_number   =   $order_max->orderNumber; // last order number in current orders 

            /*Starting entries in order table*/

            /*/*orders table entry*/

            $order_number_current       = $order_number+1;
            $order_data = array(
                                'orderNumber'=>$order_number_current,
                                'orderCourseID'=>$course_id,
                                'orderStatus'=>ORDER_PENDING,
                                'orderCustID'=>'0',
				'orderProductType'=>PRODUCT_TYPE_COURSE,
                                'orderDate'=>date('Y-m-d'),
                                'orderTime'=>date('H:i:s'),
                                'orderName'=>($urLastName.','.$urFirstName),
                                'orderEmail'=>$get_course_session_array['email'],
                                'orderTotal'=>0
                                );

            //orders_items table entry

            $order_id   = $this->order_model->insert($order_data);

            $order_item_data = array(

                                'oiOrderNumber'=>$order_number_current,
                                'oiQuantity'=>'1',
                                'oiProdType'=>PRODUCT_TYPE_COURSE,
                                'oiProdID'=>$course_id,
                                'oiProdPrice'=>$course_amount
            );

            $order_item_id = $this->order_model->insert_item($order_item_data);

            /*End order_items table entry*/
      

                 //set order id and transaction id in session

                 $order_session['order']  = array(
                    'order_item_id'=>$order_item_id,
                    'order_id'=>$order_id,
                    'ppID'=>$ppID
                 );

                 $this->session->set_userdata( $order_session);

                 //End order session
                  

            /*End info save in orders and order_items table */

            // Add request-specific fields to the request string.

          $nvpStr =	"&PAYMENTACTION=$paymentType&AMT=$course_amount&CREDITCARDTYPE=$creditCardType&ACCT=$credit_card_number"."&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$urFirstName&LASTNAME=$urLastName"."&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";


            // Execute the API operation; 

            $response = $this->paypal_model->pay('DoDirectPayment', $nvpStr); 

            /*if payment successful then save the payment detail in db and mark user as enroll*/

            if(!empty($response) )
            {      
                    //RF3
                    $this->course_reservation_model->update($course_res_id,array('urCredits'=>$credit_type));
                    
                    //if payment done then mark user as entrolled
                    $this->checkout_model->user_enrollment($course_res_id);
                    //sending resonse url, transcation primary key, order id, courseid and course detail object
                    $this->checkout_model->save_order_details($response, $ppID, $order_id, $course_id, $course, $course_res_id);
                    redirect('checkout/thankyou');
            } 

            else  
            {
                    set_flash_message('Your payment was not able to be processed. Please try again.' ,'warning');
                    redirect('checkout/pay_for_course');

            }

        } 


	/**

		@Function Name:	index
		@Author Name:	Janet Rajani 
		@Date:		Sept, 12 2013
		@Purpose:	Check the validity of user email and forward to checkout page
		//RF2
	*/

	public function index()
	{
       
		$this->js[]='js/frontend.js';
                $this->js[]='js/admin.js';
		$this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]='js/fancybox/source/jquery.fancybox.css';
		$this->page_title="Checkout Details";
		//start Rf2

		//check if link is send by admin for single course payment 

		$this->rid = 0;//variable to store the single course reservation id 
		if($rid=$this->input->get('rid'))
                {
                    $this->rid=base64_decode($rid);
		}
		
		$data['layout']='two-column-right-small';
		$data['sidebar']='checkout';
		$this->form_validation->set_rules('userEmail', 'Email', 'required|valid_email');
		$this->form_validation->set_message('required', '%s must not be blank');
		$this->form_validation->set_message('valid_email', 'Email Address must be a valid e-mail address.');
		
                //if validation done and 

		if (($this->form_validation->run() == TRUE) || ($this->session->userdata('email')))
		{
                    $userEmail='9999';
                    if($this->input->post('userEmail'))
                    {
                        $userEmail              = $this->input->post('userEmail');
                        $this->session->set_userdata('email',$userEmail);
                    }
                    elseif($this->session->userdata('email'))
                    {

                        $userEmail              = $this->session->userdata('email');	

                    }

                    $data['userEmail']      = $userEmail; 	

                    $data['results']        = $this->course_reservation_model->get_records('',$userEmail, 0 ,0, -1,'0',false,false,$this->rid);

                    $this->load->model('course_schedule_model');

                    $data['main']           = 'checkout/checkoutdetails';

                    $data['content']= $this->page_model->get_single_record($page_id=12);

                    $this->load->vars($data);

                    $this->load->view('template');

		}

		else

		{

                    $data['main'] = 'checkout/checkout';

                    $this->load->vars($data);

                    $this->load->view('template');

                }

	}

    

	/**

		@Function Name:	checkOutDetails

		@Author Name:	Janet Rajani 

		@Date:		Sept, 12 2013

		@Purpose:	Display the page where user will see all courses with pay button

	

	*/

	public function checkOutDetails()

	{

         if(!is_checkout_logged_in())

         {

                    redirect('checkout');

                }

                $data['main'] = 'checkout/checkoutdetails';

                $this->load->vars($data);

                $this->load->view('template');

	}

	

	/**

		@Function Name:	unregister

		@Author Name:	Janet Rajani 

		@Date:		Sept, 16 2013

		@Purpose:	unregister the user form the course and display the unregister reason form 

	

	*/

	public function unregister($id=0)

	{

                use_ssl(FALSE);

		$error  = false;
		$data  = array();
		$errors=true;

		if(!is_checkout_logged_in())
		{

			show_404();

		}

		$registeration_details = $this->course_reservation_model->get_single_record($id);

		if(!($registeration_details))
                {
			show_404();
		}

		//check result validity

		if($registeration_details->urEmail !=$this->session->userdata('email'))
                {
			show_404();
                }

                $course_id =	$registeration_details->urCourse;

		if(count( $_POST ) > 0)
                {

                    $this->course_reservation_model->delete($id);
                    set_flash_message('Thank you. We have unregistered you from this course.');
                    //get the course details 
                    $course   = $this->course_schedule_model->get_course_detail($course_id );

                    //send email to admin about the unregisteration
                    $email_template = get_content('email_templates','*','etID = 20');

                    if(!empty($email_template))
                    {

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

                        $searchReplaceArray = array(

                                 '[UserName]'   =>$registeration_details->urFirstName.' '.$registeration_details->urFirstName,
                                 '[UserEmail]'   =>$registeration_details->urEmail,
                                 '[CourseTitle]'   =>$course->cdCourseID.':'.$course->cdCourseTitle,
                                 '[CourseLocation]'   =>$course->csLocation,
                                 '[CourseAddress]'   =>$course->csAddress,
                                 '[CourseAddress]'   =>$course->csAddress,
                                 '[CourseCity]'   =>$course->csCity,
                                 '[CourseState]'   =>$course->csState,
                                 '[CourseDates]'   =>$course_dates,
                                 '[Reason]'   =>$this->input->post('reason'),

                                );

                        $email_message = str_replace(

                                        array_keys($searchReplaceArray), 

                                        array_values($searchReplaceArray),$email_template->etCopy); 

                        //get admin emails 

                        $emails = get_admin_emails();

                        send_mail(ADMIN_EMAIL,SITE_NAME,SENDER_EMAIL,$email_template->etSubject,$email_message,$emails);

                    }


			$data['reload']=true;

		}	
	
		$data['main'] = 'checkout/unregister';
		$this->load->vars($data);
		$this->load->view('popup');
		
	}

        /**
		@Function Name:	thankyou
		@Author Name:	Janet Rajani 
		@Date:		Dec, 02 2013
		@Purpose:	Display confirmation message after payment

	*/

	public function thankyou()
	{

		if(!is_checkout_logged_in())
		{
			redirect('checkout');

		}
	
		$data['content']= $this->page_model->get_single_record($page_id=16);

		if(empty($data['content']))
			redirect('home/error_404');

		//meta information
		$data['meta_title']=$data['content']->cpMetaTitle;
		$data['meta_descrption']=$data['content']->cpMetaDescription;

		$this->page_title = $data['content']->cpMetaTitle;
		$data['main'] = 'checkout/thankyou';
		$this->load->vars($data);
		$this->load->view('template');
	}
}