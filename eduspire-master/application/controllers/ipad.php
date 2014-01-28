<?php 
/**
@Page/Module Name/Class: 		ipad.php
@Author Name:			 		Ben binesh
@Date:					 		Sept, 20 2013
@Purpose:		        		Contain all controllers function  for ipad selection
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ipad extends CI_Controller {
	protected $_user_id;
	public function __construct()
	{
		parent::__construct();
                use_ssl(TRUE);
		$this->load->model('course_schedule_model');
		$this->load->model('paypal_model');
		$this->load->model('inventory_model');
		$this->load->model('checkout_model');
		$this->load->model('assignment_model');
		$this->load->model('page_model');
		$this->load->model('order_model');
		$this->load->model('user_model');
		$this->load->model('assignment_model');
		$this->load->helper('common');
		$this->load->helper('form');
		
		if(!is_logged_in()) {
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}else{
			//check the sufficient access level 
			if(MEMBER != $this->session->userdata('access_level')){
				set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
				redirect('home/error');
			}
		}
		$this->_user_id=$this->session->userdata('user_id');
		
	}
	
	/**
		@Function Name:	index
		@Author Name:	ben binesh
		@Date:			Sept, 20 2013
		@return  void
		@Purpose:		load the ipad selection form 
	
	*/
	public function index($assignment_id=0)
	{
		
		$data   = array();
		$error  = false;
		$errors = array();
		$ipad_cart=array();
		
		if(!$assignment_id){
			redirect('home/error404');
		}
		$assignment=$this->assignment_model->get_single_assignment($assignment_id);
		if(empty($assignment)){
			redirect('home/error404');
		}
		
		
		//check if this assignment already completed or not 
		//get data form the assignment table 
		$ledger_details = $this->assignment_model->get_users_grade_details($this->_user_id,$assignment_id);
		
		if(!empty($ledger_details)){
			set_flash_message('You already selected the ipad ','warning');
			redirect('member');
		}
		
		$user = $this->user_model->get_single_record($this->session->userdata('user_id'),'email,act48',true);
		
		if(empty($user)){
			redirect('home/error_404');
		}
		//check for the course membership 
		if(!$this->course_schedule_model->check_enrollee($assignment->assignCnfID,$user->email))
		{
			redirect('home/error404');
		}
		
		// if assignment is ipad type
		//check for credit status 
		$course   = $this->course_schedule_model->get_course_detail($assignment->assignCnfID );
		
		if(empty($course)){
			redirect('home/error_404');
		}
		
		if(!(check_credit($user->act48,$course->csID))){
			redirect('home/error404');
		}
		
		
		
		
		
		//set the assignment id in the session 
		$this->session->set_userdata('assignment',$assignment_id);
		
		
		/**
			meta information
		*/
		
		$data['results']    = $this->inventory_model->get_records(IPAD_CAT ,0 ,STATUS_PUBLISH,0 ,-1);
		$data['content']      = $this->page_model->get_single_record($page_id=9);
		$data['block_first']  = $this->page_model->get_single_record($page_id=10);
		$data['block_second'] = $this->page_model->get_single_record($page_id=11);
		if(count($_POST)>0){
			
			
			$this->load->library('form_validation');
			$this->form_validation->set_rules('product_id', 'Ipad', 'trim|required');
			if($product_id=$this->input->post('product_id')){
				$this->form_validation->set_rules('color_'.$product_id, 'Color', 'trim|required');
				$this->form_validation->set_rules('option_id_'.$product_id, 'Upgrade', 'trim|required');
			}else{
				$this->form_validation->set_rules('color_101', 'Color', 'trim|required');
				$this->form_validation->set_rules('option_id_101', 'Upgrade', 'trim|required');
			}
			
			
			
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				
				//$ipad_cart_details;
				$product_id=$this->input->post('product_id');
				$product=$this->inventory_model->get_single_record($product_id);
				$standard_upgrade='';
				if('standard' == $this->input->post('option_id_'.$product_id)){
					$standard_upgrade=STANDARD_IPAD;
				}
				$ipad_cart[]=array(
					'id'=>$product_id,
					'product_name'=>$product->invName.' '.$standard_upgrade,
					'option'=>$this->input->post('color_'.$product_id),
					'price'=>$product->invPrice1,
				);
				if($option_id = $this->input->post('option_id_'.$product_id)){
					if('standard' !=$option_id){
						$product_option=$this->inventory_model->get_single_record($option_id);
						$ipad_cart[]=array(
							'id'=>$option_id,
							'product_name'=>$product_option->invName,
							'option'=>'',
							'price'=>$product_option->invPrice1,
						);
					}
				}
				$this->session->set_userdata(array(
					'cart'=>$ipad_cart,
				));
				redirect('ipad/checkout');
				
			}
		}
		$data['layout']='two-column-right';
		if(!(empty($data['content']))){
			$data['meta_title']=$data['content']->cpMetaTitle;
			$data['meta_descrption']=$data['content']->cpMetaDescription;
		}
		$data['errors']   = $errors;
		$data['main']     = 'ipad/ipad';
		$data['sidebar']  = 'ipad';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	upgrade
		@Author Name:	ben binesh
		@Date:			Sept, 20 2013
		@Purpose:		load the ipad selection form 
	
	*/
	function checkout(){
		$data=array();
		$error=false;
		$errors=array();
		//get ipad cart 
		$cart = $this->session->userdata('cart');
		if(empty($cart)){
			set_flash_message('Cart is empty ','error');
			redirect('home/error');
		}
		$data['content']= $this->page_model->get_single_record($page_id=12);
		$data['cart']=$cart;
		
		//proceed to the payment process 
		if(count($_POST)>0){
			$upgrade=false;
			if(count($cart)>1){ //user also selected the upgrade 
				$upgrade = true;
				$total_amount = $cart[0]['price']+$cart[1]['price'];
			}
			
				
			//get user details 
			$user=$this->user_model->get_single_record($this->_user_id,'u.id,u.userName,u.firstName,u.lastName,u.email,p.phone,p.address,p.city,p.state,p.zip',true);
			$product_id=$cart[0]['id'];
			$product=$this->inventory_model->get_single_record($product_id);
			//insert ipad order
			$order_number = $this->order_model->select_max_id();
			$order_number = $order_number->orderNumber+1;
			$assignment_id=$this->session->userdata('assignment');
			$assignment=$this->assignment_model->get_single_assignment($assignment_id);
			if(empty($assignment)){
				redirect('home/error404');
			}
			$order_array=array(
				'orderNumber'=>$order_number,
				'orderStatus'=>ORDER_PENDING,
				'orderProductType'=>PRODUCT_TYPE_IPAD,
				'orderDate'=>date('Y-m-d'),
				'orderTime'=>date('H:i:s'),
				'orderCustID'=>$this->_user_id,
				'orderCourseID'=>$assignment->assignCnfID,
				'orderName'=>$user->lastName.' '.$user->firstName,
				'orderEmail'=>$user->email,
				'orderIPAddress'=>$this->input->ip_address(),
				'orderTelephone'=>$user->phone,
				'orderCCStreet'=>$user->address,
				'orderCCCity'=>$user->city,
				'orderCCState'=>$user->state,
				'orderCCZIP'=>$user->zip,
				'orderTotal'=>$product->invPrice1,
			);
			$order_id = $this->order_model->insert($order_array);
			//insert order item for ipad 
			$order_item_data = array(
				'oiOrderNumber'=>$order_number,
				'oiQuantity'=>'1',
				'oiProdID'=>$product_id,
				'oiProdName'=>$product->invName,
				'oiProdType'=>PRODUCT_TYPE_IPAD,
				'oiProdPrice'=>$product->invPrice1,
				'oiProdVariantValue1'=>$cart[0]['option'],
			);
			$this->order_model->insert_item($order_item_data);
			
			//set assignment completed 	and assignment ledger
			
			$assignment=$this->assignment_model->get_single_assignment($assignment_id);
			$assignment_ledger_array=array(
				'alCnfID'=>$assignment->assignCnfID,	
				'alAssignID'=>$assignment->assignID,	
				'alAssignType'=>$assignment->assignType,	
				'alUserID'=>$user->id,
				'alSubmissionID'=>$assignment->assignType,
				'alDateSubmitted'=>date('Y-m-d H:i:s'),
				'alGrade'=>$assignment->assignPoints,
				'alGradeBy'=>'-1',
				'alGradeDateTime'=>date('Y-m-d H:i:s'),
			);
			$ledger_id = $this->assignment_model->insert_ledger($assignment_ledger_array);
				
			//if ipad upgrade is selected 
			if($upgrade){
				$upgrade_product_id = $cart[1]['id'];
				$upgrade_product = $this->inventory_model->get_single_record($upgrade_product_id);
				
				//insert order item for ipad upgrade
				$order_item_data = array(
					'oiOrderNumber'=>$order_number,
					'oiQuantity'=>'1',
					'oiProdID'=>$upgrade_product_id,
					'oiProdType'=>PRODUCT_TYPE_IPAD,
					'oiProdName'=>$upgrade_product->invName,
					'oiProdPrice'=>$upgrade_product->invPrice1,
				);
				$this->order_model->insert_item($order_item_data);
				$this->session->set_userdata(array(
					'payment_ipad'=>array(
						'ledger_id' =>$ledger_id,
						'order_number'=>$order_number,
						'order_id'  =>$order_id,
						'product_id' =>$upgrade_product_id, 
						'product_name'=>$cart[0]['product_name'],
						'upgrade_name'=>$upgrade_product->invName,						
						'price'=>$total_amount,
					),
				));
				//proceed to the payment page 
				redirect('ipad/payment');	
			}
			else
			{
				//set the order completed if no upgrade choosen 
				$this->order_model->update(
					$order_id,
					array(
						'orderStatus'=>ORDER_COMPLETED,
					)
				);
				
			}
			//empty session cart details
			$this->session->unset_userdata('payment_ipad');					
			$this->session->unset_userdata('cart');	
			//unset assignment session
			$this->session->unset_userdata('assignment');				
			set_flash_message('Your iPad selection has been processed','success');
			redirect('member');		
		}
	
		$data['errors']=$errors;
		$data['main'] = 'ipad/cart';
        $this->load->vars($data);
        $this->load->view('template');
			
	}
	/**
		@Function Name:	cancel
		@Author Name:	ben binesh
		@Date:			Sept, 25 2013
		@Purpose:		cancel the ipad cart and redirect to user dashboard
	
	*/
	function  cancel(){
		$session_data = array('cart'  =>array());
		$this->session->unset_userdata($session_data);
		//redirect to  member dashboard 
		redirect('member');
	}
	
	
	/**
		@Function Name:	cancel
		@Author Name:	ben binesh
		@Date:			Sept, 25 2013
		@Purpose:		cancel the ipad cart and redirect to user dashboard
	
	*/
	function  payment(){
		$data=array();
		$error=false;
		$errors=array();
		$error_card_expired='';
		ob_start();
		//get ipad cart 
		$payment_details = $this->session->userdata('payment_ipad');
		if(empty($payment_details)){
			set_flash_message('Cart is empty ','error');
			redirect('home/error');
		}
		
		$user=$this->user_model->get_single_record($this->_user_id,'u.id,u.userName,u.firstName,u.lastName,u.email,p.phone,p.address,p.city,p.state,p.zip',true);
        $data['urFirstName']    =  $user->firstName;
        $data['urLastName']     =  $user->lastName;
        $data['urEmail']        =  $user->email;
		
		if(count($_POST)>0){
		
			$this->load->library('form_validation');
			$this->form_validation->set_rules('cardnumber','Card Number','trim|required');
            $this->form_validation->set_rules('cardcvv','Card CVV','trim|required');
            $this->form_validation->set_rules('cardholder','Cardholder name','trim|required');
			
            $this->form_validation->set_message('required','%s must not be blank');
            //Check if card is expired
            if($this->checkout_model->check_date_validity($this->input->post('cardmonth'),$this->input->post('cardyear')))
            {
                   $errors[]    = 'Card expired';
				   $error_card_expired    = 'Card expired';
                   $error   = true;
            }
            //If validation is done then process for payment and dashboard
            if(($this->form_validation->run()==TRUE) && ($error==false))
            {
				
				//take all values from session
				$order_number   = $payment_details['order_number'];
				$product_id     = $payment_details['product_id'];
				$amount         = $payment_details['price'];
				$order_id      	= $payment_details['order_id'];
				$product_name   = $payment_details['product_name'];
				$upgrade_name   = $payment_details['upgrade_name'];
				$ledger_id      = $payment_details['ledger_id']; 
				//fetch all submitted value of user card
				$paymentType        = 'Sale';
				$urFirstName        = urlencode($this->input->post('urFirstName'));
				$urLastName         = urlencode($this->input->post('urLastName'));
				$creditCardType     = urlencode($this->input->post('cardtype'));
				$creditCardNumber   = urlencode($this->input->post('cardnumber'));
				$expDateMonth       = $this->input->post('cardmonth');
				$credit_card_number   = urlencode($this->input->post('cardnumber'));
				//Build new string with last 4 digits XXXX-XXXX-XXXX-8678 to save in db
				$new_credit_card_number = "XXXX-XXXX-XXXX-" . substr($credit_card_number,-4,4);
				// Month must be padded with leading zero
				$padDateMonth       = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));

				$expDateYear        = urlencode($this->input->post('cardyear'));
				$cvv2Number         = urlencode($this->input->post('cardcvv'));
			   
				$country        = 'US';	// US or other valid country code
				$currencyID     = 'USD';// or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
				 /*User info save in ppTransaction*/
				$transaction_data = array(
										'payer_email'=>$user->email,
										'first_name'=>$urFirstName,
										'last_name'=>$urLastName,
										'item_name1'=>$product_name.'-'.$upgrade_name,
										'item_number1'=>$product_id,
										'quantity1'=>1,
										'product_type'=>PRODUCT_TYPE_IPAD,
										'txn_type'=>TXN_TYPE_CART,
										'payment_mode'=>PAYMENT_MODE_PAYPAL,
										'mc_currency'=>$currencyID,
										'credit_card_number'=>$new_credit_card_number 
									);
				$ppID=$this->checkout_model->insert('pp_transactions',$transaction_data);
					
				/*End info save in orders and order_items table */
				// Add request-specific fields to the request string.
				$nvpStr =	"&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
										"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$urFirstName&LASTNAME=$urLastName"."&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";

				// Execute the API operation; 
				$response = $this->paypal_model->pay('DoDirectPayment', $nvpStr); 
				
				//if transaction is successfull 
				if($response['TRANSACTIONID']) 
				{       
					//set order completed	
					$this->order_model->update(
						$order_id,
						array(
							'orderStatus'=>ORDER_COMPLETED,
						)
					);
					$AMT                    = number_format(urldecode($payPalProResult['AMT']),2);
					//update transaction 
					$update_transaction_data =  array(
									'txn_id'=> $response['TRANSACTIONID'],
                                    'payment_status'=>PAYMENT_COMPLETED,
                                    'payment_date'=>date('Y-m-d H:i:s'),
                                    'payment_gross'=>number_format(urldecode($response['AMT']),2),
                                   );
					$this->checkout_model->update_transaction($ppID,$update_transaction_data);
									
					
					//send successful ipad upgrade email
					$email_template = get_content('email_templates','*','etID = 19');
					if(!empty($email_template)){
						$email_template=$email_template[0];
						$searchReplaceArray = array(
							 '[Ipad]'   =>'ipad', 
							 '[UpgradeInfo]'   =>$product_name.' - '.$upgrade_name,
							 '[UserName]'   =>$urFirstName.' '.$urLastName ,
							 '[Price]'   =>CURRENCY.$amount,
							
							);
					$email_message = str_replace(
						  array_keys($searchReplaceArray), 
						  array_values($searchReplaceArray),$email_template->etCopy);
						//get admin emails 
						$emails = get_admin_emails();		
						send_mail($user->email,SITE_NAME,SENDER_EMAIL,$email_template->etSubject,	$email_message,$emails);	  
					}
					
					
					set_flash_message('Your iPad selection has been processed ','success');
				
					//empty session cart details
					$this->session->unset_userdata('payment_ipad');					
					$this->session->unset_userdata('cart');					
					redirect('home/message?title=Success');
					
				} 
				else  
				{
					set_flash_message('Your payment was not able to be processed. Please try again.' ,'error');
					//empty session payment related session 
					$assignment_id=$this->session->userdata('assignment');
					$this->session->unset_userdata('payment_ipad');					
					$this->session->unset_userdata('cart');		
					$this->session->unset_userdata('assignment');	
					
					//revert back all the process
					//delete ledger 
					$this->order_model->delete($order_id);
					$this->order_model->delete_item_by_ordernumber($order_number);
					$this->assignment_model->delete_ledger($ledger_id);
					$this->checkout_model->delete_transaction($ppID);
					redirect('ipad/index/'.$assignment_id);
					
				}
			}
		}
		
		$data['errors']  = $errors;
		$data['error']=$error_card_expired ;
        $data['main'] = 'checkout/pay_for_course';
        $data['layout']='';
        $data['sidebar']='';
        $this->load->vars($data);
        $this->load->view('template');
		
	}
	
	
	
	
	
}

/* End of file ipad.php */
/* Location: ./application/controllers/ipad.php */