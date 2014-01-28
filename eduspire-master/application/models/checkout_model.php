<?php 
/**

@Page/Module Name/Class: 		checkout_model.php
@Author Name:			 		Janet Rajani
@Date:					 		Sep 16, 2013
@Purpose:		        		Model file for checkout process 
@Table referred:				course_reservations,email_templates
@Table updated:					pp_transactions
@Most Important Related Files	NIL
 */
class Checkout_model extends CI_Model
{  
	public $table_name='pp_transactions';
	public function __construct()
	{
                session_start();
		// load database
		parent::__construct();
		$this->load->library('email');
                
	}
        /**
		@Function Name:	checkout_logged_in
		@Author Name:	Janet Rajani
		@Date:			Sep, 16 2013 
		@Purpose:		check if the current user is logged in or not with his email 
	
	*/
	public function checkout_logged_in()
        {
			
            if($this->session->userdata('email') == '')
            {
                return false;
            }
            else 
            {
                return true;
            }
        } 
      
         /**
		@Function Name:	checkout_logged_in
		@Author Name:	Janet Rajani
		@Date:          Sep, 16 2013 
		@Purpose:	After payment is successful, update the db and send 
                                confirmation email to user
	
	*/
	public function save_order_details($payPalProResult,$ppID, $order_id,$course_id, $course_detail, $course_res_id=0)
	{ 
            $TRANSACTIONID          = $payPalProResult['TRANSACTIONID'];
            $ACK                    = $payPalProResult['ACK'];
            $AMT                    = number_format(urldecode($payPalProResult['AMT']),2);
            $TIMESTAMP              = $payPalProResult['TIMESTAMP'];
            //Value of Primary key ppID from the table
            $get_course_session_array    = $this->session->userdata('payment');
            //take all values from session
            $email          = $get_course_session_array['email'];
			
			$course_res_result_user = $this->course_reservation_model->get_single_record($course_res_id);

            $userEmail        =  $course_res_result_user->urEmail;
            $urFirstName    =  $course_res_result_user->urFirstName;
            $urLastName     =  $course_res_result_user->urLastName;
            //array to update transaction data
                $data_to_be_update   = array(
                                    'payer_email'=>$email,
                                    'txn_id'=>$TRANSACTIONID,
                                    'payment_status'=>PAYMENT_ENROLLED,
                                    'payment_gross'=>$AMT,
                                    'payment_mode'=>PAYMENT_MODE_PAYPAL,
                                    'txn_type'=>TXN_TYPE_CART,
                                    'mc_currency'=>'USD'
                                    );
                $data_update_where = array('ppID'=>$ppID);
                $this->update($data_update_where,$data_to_be_update,'pp_transactions');
		
                //if pp_transactions table is updated by transactin id then update orders table
                $update_data_orders = array(
                'orderStatus'=>ORDER_COMPLETED );
                $this->order_model->update($order_id,$update_data_orders);
               
                $payment_email_template_data = get_content('email_templates','*','etID = 14');
                $payment_email_template_data = $payment_email_template_data[0];
				$user_message                = $payment_email_template_data->etCopy;
                $subject                     = $payment_email_template_data->etSubject;
                
                $course_schedule='';
                if(count($course_detail->course_dates)>0)
                {
                    foreach($course_detail->course_dates as $course_date)
                    { 
                        if(COURSE_OFFLINE == $course_detail->csCourseType)
                        { 
                            $course_schedule .= format_date($course_date->csdStartDate,DATE_FORMAT).' (';
                            $course_schedule .= format_date($course_date->csdStartTime,TIME_FORMAT).'-';
                            $course_schedule .= format_date($course_date->csdEndTime,TIME_FORMAT).')';
                            $course_schedule .= "\n";
                        }
                        else
                        {
                            $course_schedule .= format_date($course_date->csdStartDate,DATE_FORMAT).'-';
                            $course_schedule .=format_date($course_date->csdEndDate,DATE_FORMAT);
                        }
                    }
                }

                $course_title='';
                $course_title .=$course_detail->cdCourseID.':'.$course_detail->cdCourseTitle; 
               $user_full_name = $urFirstName.' '.$urLastName;
                /*Replace all constants of email by the dynamic values*/
                 $email_message_replacement = array(
                    "[UserName]"=>$user_full_name,
                    "[UserEmail]"=>$userEmail,
                    "[CourseTitle]"=>$course_title,
                    "[CourseLocation]"=>$course_detail->csLocation,
                    "[CourseAddress]"=>$course_detail->csAddress,
                    "[CourseCity]"=>$course_detail->csCity,
                    "[CourseState]"=>$course_detail->csState,"[CourseZIP]"=>$course_detail->csZIP,
                    "[CourseDates]"=>$course_schedule
                     );
                           /*End payment email*/
                      $user_confirmation_message =    str_replace(array_keys($email_message_replacement),  array_values($email_message_replacement),$user_message);
                          
                    /*End email constant replacement*/
                    //confirmation email to user
                    //get admin emails 
                    $emails = get_admin_emails();
                    send_mail($userEmail,SITE_NAME,SENDER_EMAIL,$subject,$user_confirmation_message,$emails);
                  
                    return true;
                
	}	 
        
         /**
		@Function Name:         userEnrollment
		@Author Name:           Janet Rajani
		@Date:			Sep, 19 2013
		@id  			numeric| primary key of record 
		@data   		array | array of single record 
		@return  		integer
		@Purpose:		update users and user_profile table data 
	*/
        
        function user_enrollment($course_res_id=0)
        {
            //get course reservation data 
            $course_res_result = $this->course_reservation_model->get_single_record($course_res_id);

            $urStatus       =  $course_res_result->urStatus;
            $urEmail        =  $course_res_result->urEmail;
            $urCourse       =  $course_res_result->urCourse;
            $urFirstName    =  $course_res_result->urFirstName;
            $urLastName     =  $course_res_result->urLastName;
            $urDistrict     =  $course_res_result->urDistrict;
            $urDistrictID   =  $course_res_result->urDistrictID;
            $urPhone        =  $course_res_result->urPhone;
			$urIU           =  $course_res_result->urIuID;
			$credit         =  $course_res_result->urCredits;
            if($this->course_schedule_model->check_enrollee($urCourse,$urEmail))
            {
                set_flash_message('Course registrant already enrolled for this course  ','notice');//return ;  
            }
            //update record as enrolled 
            $update_array = array(
				 'urStatus'=>STATUS_ENROLLED,
				 'urEnrolledTime'=>date('Y-m-d H:i:s'),

            );
            $this->course_reservation_model->update($course_res_id,$update_array);
            
            //update user membership 
            $this->load->model('user_model');
            $user = $this->user_model->get_user_byEmail($urEmail);
			$act48 = '';
			if(STATUS_NO == $credit){
				$act48 = $urCourse ;
			}
				
			
			if($user)
            {
                //update user membership 
                $this->user_model->update_membership($user->id,$urCourse,false);
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
				$data_array = array(
						'act48' => $act48,
				);
				
				$this->user_model->update($user->id,$data_array);
            }
            else
            { //add user 
                $signup_date       = date('Y-m-d H:i:s');
                $activation_code   = random_string('alnum', 12);
                //Insert into users table
                $user_array = array(
                    'firstName' => $urFirstName,
                    'lastName' => $urLastName,
                    'email' => $urEmail,
                    'accessLevel' => MEMBER,
                    'memberships'=>$urCourse,
                    'membershipLastUsed'=>$urCourse,
					'act48' =>$act48,
                    'signupDate' => $signup_date,
                    'activationCode'=>$activation_code,
                    'activationFlag'=>time()
             );
                $user_id = $this->user_model->insert($user_array);
                $profile_data = array(
                   'user_id' => $user_id,
                   'signupDate' => $signup_date,
                   'districtAffiliation' =>  $urDistrict,
                   'districtID' =>  $urDistrictID,
                   'iuID' =>  $urIU,
				   'phone' =>  $urPhone,
                );
                $this->user_model->set_user_profile($user_id,$profile_data);

                //send activation account email to user 
                $user_activation_link   = base_url().'user/activate?email='.$urEmail.'&activation_code='.$activation_code.'&user='.base64_encode($user_id);
                $email_template         = get_content('email_templates','*','etID = 1');
                if(!empty($email_template))
                {
                    $email_template=$email_template[0];
                    $searchReplaceArray = array(
                        '[AccountActivationUrl]'   =>anchor($user_activation_link,$user_activation_link), 
                        '[firstName]'   =>$urFirstName,
                        '[lastName]'   =>$urLastName,
                        '[maximumResponse]'   =>FWPWD_EXPIRE_TIME,
				);
                    $email_message = str_replace(
                    array_keys($searchReplaceArray), 
                    array_values($searchReplaceArray),$email_template->etCopy); 
                    //get admin emails 
                    $admin_emails = get_admin_emails();
                    send_mail($urEmail,SITE_NAME,SENDER_EMAIL,$email_template->etSubject,$email_message,$admin_emails);
                }
                
            }

            return true;
  
        }
        
         /**
		@Function Name:         get_data_from_transaction
		@Author Name:           Janet Rajani
		@Date:			Sep, 18 2013
		@id  			numeric| primary key of record 
		@data   		array | array of single record 
		@return  		integer
		@Purpose:		udate data 
	
	*/
	function get_data_from_transaction($ppID)
        {
            $this->db->where('ppID',$ppID);
            $query = $this->db->get('pp_transactions');
            return $query->row();
	}
        
	/**
		@Function Name:	 select
		@Author Name:	 Janet Rajani
		@Date:		 Sep, 17 2013
		@return          | array 
		@Purpose:	 get  multiple records 
	
	*/
	function select($select, $tableName, $whereCondition)
        {
		$this->db->select($select);
		$query = $this->db->get($tableName);
		return $query->result();
	}
        /**
	
		@Function Name:         insert
		@Author Name:           Janet Rajani
		@Date:			Sep, 16 2013
		@data   		array | array of single record 
		@return  		integer
		@Purpose:		insert data 
	
	*/
	
	function insert($table_name,$data=array())
        {
		$this->db->insert($table_name,$data);
		return $this->db->insert_id(); 
	}
        /**
		@Function Name:         update
		@Author Name:           Janet Rajani
		@Date:			Sep, 17 2013
		@id  			numeric| primary key of record 
		@data   		array | array of single record 
		@return  		integer
		@Purpose:		udate data 
	
	*/
	function update($id=array(),$data=array(),$table_name)
        {

		$this->db->update($table_name,$data, $id);
                //Need this comment
               // echo $this->db->last_query();
		return true;
	}
	/**
		@Function Name:	get_records
		@Author Name:	ben binesh
		@Date:			Sept , 16 2013


		@name          | String | name of user 
		@email   	   | String | email
		@txn_id        | String | transaction id 
		@payment_date  | String | payment date 
		@status        | String | payment status 
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@return  array 
		@Purpose:		get  multiple records 
	
	*/

	function get_records($name = '', $email = '', $txn_id = '', $payment_date='', $status = '', $start = 0 ,  $limit = 10,$show_receipt='')
                {
		$this->db->select('ppID, payer_email, first_name, last_name, address_name, address_street, address_city, address_state, address_zip, address_country, address_country_code, txn_id, payment_gross, payment_date	, item_name1, txn_type,payment_status');
		
		if($name)
			$this->db->where("
				MATCH
					(first_name,last_name) 
				AGAINST 
					('$name' IN BOOLEAN MODE)
		",'',false);	
		if($email)
			$this->db->where('payer_email',$email);	
		
		if($txn_id)
			$this->db->where('txn_id',$txn_id);	
		if($payment_date)
			$this->db->where('payment_date',$payment_date);	
		
		if($status != ''){
			$this->db->where('payment_status',$status);
		}
		if($show_receipt !=='')	{
			$this->db->where('show_receipt',$show_receipt);
		}
		$this->db->order_by('ppID','DESC');
		if($limit > 0){
			$query = $this->db->get($this->table_name, $limit , $start );
		}else{
			$query = $this->db->get($this->table_name);
		}
		
		return $query->result();
	}

	
	/*
		@Function Name:	count_records

		@Author Name:	ben binesh
		@Date:			Sept , 16 2013

		@name          | String | name of user 
		@email   	   | String | email
		@txn_id        | String | transaction id 
		@payment_date  | String | payment date 
		@status        | String | payment status 
		@return         Integer  
		@Purpose:		count records
	
	*/

	function count_records($name = '', $email = '', $txn_id = '', $payment_date='', $status = '',$show_receipt='')
        {
		if($name)
			$this->db->where("
				MATCH
					(first_name,last_name) 
				AGAINST 
					('$name' IN BOOLEAN MODE)
		",'',false);	
		if($email)
			$this->db->where('payer_email',$email);	
		
		if($txn_id)
			$this->db->where('txn_id',$txn_id);	
		if($payment_date)
			$this->db->where('payment_date',$payment_date);	
		
		if($show_receipt !=='')	{
			$this->db->where('show_receipt',$show_receipt);
		}
		
		if($status != ''){
			$this->db->where('payment_status',$status);
		}	
		return $this->db->count_all_results($this->table_name);
		
	}


	/*
		@Function Name:	get_single_record
		@Author Name:	ben binesh
		@Date:			Sept, 19 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/

	function get_single_record($id=0)
        {
		$this->db->where('ppID',$id);
		$query = $this->db->get($this->table_name);

		return $query->row();
	}
	
	/**
		@Function Name:	get_status_array
		@Author Name:	ben binesh
		@Date:			Sept , 16 2013
		@emtpty |boolean| empty flag
		@return  integer
		@Purpose:		get array of status
		
	*/
	function get_status_array($empty=false,$empty_array=array(''=>''))
        {
		$status_array=array();
		if($empty){
			$status_array = array_merge($status_array,$empty_array);
		}
		$status_array[PAYMENT_ENROLLED]   = PAYMENT_ENROLLED;
		$status_array[PAYMENT_REFUNDED] = PAYMENT_REFUNDED;
		$status_array[PAYMENT_COMPLETED] = PAYMENT_COMPLETED;
		$status_array[PAYMENT_REVERSED] = PAYMENT_REVERSED;
		
		return $status_array;
	}
    
	/**
		@Function Name:	check_date_validity
		@Author Name:	Janet Rajani
		@Date:			Sept , 24 2013
		@Purpose:		Check if current date is greated than the set date
		
	*/
        function check_date_validity($month,$year)
        {
            $current_month = date('m');
            $current_year = date('Y');
            if($year < $current_year)
            {
                $result = 'Year is not valid'; 
            }
            elseif(($year == $current_year))
            {
                if($month<$current_month)
                    $result = 'Month is not valid';
                else
                    $result = '';
            }
            elseif(($year > $current_year))
            {
                    $result = '';
            }
            return $result;
        }
		
		
        /**
        @Function Name:	get_payment_mode_array
        @Author Name:	ben binesh
        @Date:			Sept , 25 2013
        @emtpty |boolean| empty flag
        @return  integer
        @Purpose:		get array of payment mode 

        */

        function get_payment_mode_array($empty=false,$empty_array=array(''=>''))
        {
                $payment_mode_array=array();
                if($empty){
                        $payment_mode_array = array_merge($$payment_mode_array,$empty_array);
                }
                $payment_mode_array[PAYMENT_MODE_PAYPAL]   			  = 'By Paypal';
                $payment_mode_array[PAYMENT_MODE_PERSONAL_CHECK]      = 'Personal Check';
                $payment_mode_array[PAYMENT_MODE_BRANDNAN_DIRECT_PAY] = 'Brandman Direct Pay';
                $payment_mode_array[PAYMENT_MODE_DISTRICT_CHECK] = 'District Check';
                $payment_mode_array[PAYMENT_MODE_MANUAL]              = 'Other';
                return $payment_mode_array;
        }	
		
		
        /**
        @Function Name:	show_access_level
        @Author Name:	ben binesh
        @Date:			Aug, 30 2013
        @access_level  | numeric| access level of recored 
        @return  string
        @Purpose:		return access level string 

        */
        function show_payment_mode($payment_mode = 0)
        {
                $payment_mode_array =self::get_payment_mode_array();
                return (isset($payment_mode_array[$payment_mode]))?$payment_mode_array[$payment_mode]:'Manual';
        }

        /*
                @Function Name:	update_transaction
                @Author Name:	ben binesh
                @Date:			Sept , 25 2013
                $id            |integer| primary key 
                @return  boolean
                @Purpose:		update transaction  

        */

        function update_transaction($id,$data=array())
        {
			$this->db->where('ppID',$id);
			$this->db->update($this->table_name,$data);
			return true;
        }
		
		/*
			@Function Name:	delete_transaction
			@Author Name:	ben binesh
			@Date:			Dec , 27 2013
			$id            |integer| primary key 
			@return         boolean
			@Purpose:	   delete transaction  

        */

        function delete_transaction($id)
        {
			$this->db->where('ppID',$id);
			$this->db->delete($this->table_name,$data);
			return true;
        }
}
?>