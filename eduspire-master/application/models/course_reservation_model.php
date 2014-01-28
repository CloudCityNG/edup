<?php
/**
@Page/Module Name/Class: 		page_model.php
@Author Name:			 		ben binesh
@Date:					 		Aug, 28 2013
@Purpose:		        		Contain all data management for static pages 
@Table referred:				courser_reseravations
@Table updated:					cms_page
@Most Important Related Files	NIL
 */
//Chronological development
//***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
/***********************************************************************************

//***********************************************************************************/ 

class Course_reservation_model extends CI_Model {
	
	
	
	public $table_name='course_reservations';
	
	public function __construct()
	{
		parent::__construct();
		
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	ben binesh
		@Date:			Aug, 28 2013
		@name   | String | name of user 
		@email   | String | email
		@access_level  | numeric| access level identifier
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record ,give -1 to remove limit
		@rid  | numeric| primary key of single record 
		@return  array 
		@Purpose:		get  multiple records 
              
		*/
	function get_records($name = '', $email = '', $course_id = 0,$start = 0 , $limit = 10,$status='',$credit=false,$reimbursed=false,$rid=0){
		$this->db->select('
			ur.*,
			cs.csCourseDefinitionId,cs.csMaximumEnrollees,cs.csPaymentStartDate,cs.csStartDate,cs.csEndDate,cs.csState,cs.csCity,cs.csCourseType,
			cd.cdCourseID,cd.cdCourseTitle,
			@a:=@a+1 serial_number,
			
		',false);
		$this->db->join('course_schedule cs','cs.csID = ur.urCourse','INNER');
		$this->db->join('course_definitions cd','cs.csCourseDefinitionId = cd.cdID','LEFT');
		if($name)
			$this->db->where("(
				ur.urFirstName LIKE '%$name%'
				OR
				ur.urLastName LIKE '%$name%'
			)");
		if($email)
			$this->db->where('ur.urEmail',$email);	
			
		if($course_id)
			$this->db->where('ur.urCourse',$course_id);
		
		if($status !== '')
			$this->db->where('ur.urStatus',$status);
		
		if($credit)
			$this->db->where('ur.urCredits',1);
		if($reimbursed)
			$this->db->where('ur.urDistrictReimburse',1);	 
		
		if($rid)
			$this->db->where('ur.uID',$rid);	 
		$this->db->order_by('ur.uID','ASC');		
		if($limit > 0){
			$query = $this->db->get($this->table_name.' ur ,(SELECT @a:= '.$start.') AS a', $limit , $start );
		}else{
			$query = $this->db->get($this->table_name.' ur, (SELECT @a:= '.$start.') AS a');
		}
		
		return $query->result();
	}
	
	/**
		@Function Name:	count_records
		@Author Name:	ben binesh
		@Date:			Aug, 28 2013
		@name   | String | name of user 
		@email   | String | email
		@access_level  | numeric| access level identifier
		@return  integer
		@Purpose:		count  multiple records 
	
	*/
	
	function count_records($name = '', $email = '', $course_id = 0,$status=''){
		
		$this->db->join('course_schedule cs','cs.csID = ur.urCourse','INNER');
		$this->db->join('course_definitions cd','cs.csCourseDefinitionId = cd.cdID','LEFT');
		if($name)
			$this->db->where("(
				ur.urFirstName LIKE '%$name%'
				OR
				ur.urLastName LIKE '%$name%'
			)");
		if($email)
			$this->db->where('ur.urEmail',$email);	
		if($status !== '')
            $this->db->where('ur.urStatus',$status);		
		if($course_id)
			$this->db->where('ur.urCourse',$course_id);
		$count=$this->db->count_all_results($this->table_name.' ur');
		return $count;
		
	}
	
	/**
		@Function Name:	get_single_record
		@Author Name:	ben binesh
		@Date:			Aug, 28 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_record($id=0){
		$this->db->where('uID',$id);
		$query = $this->db->get($this->table_name);
		return $query->row();
	}
	
	/**
		@Function Name:	check_duplicate
		@Author Name:	ben binesh
		@Date:			Aug, 28 2013
		@email   | String | email 
		@course  | String | course id
		@return  integer
		@Purpose:		count duplicate records with matching filter
	
	*/
	
	function check_duplicate($email='',$course=''){
		if($email)
			$this->db->where('urEmail',$email);	
		if($course)
			$this->db->where('urCourse',$course);
					
		$count =  $this->db->count_all_results($this->table_name);
		return $count;
	}
	
	/**
		@Function Name:	insert
		@Author Name:	ben binesh
		@Date:			Aug, 28 2013
		@data   | array | array of single record 
		@return  integer
		@Purpose:		insert data 
	
	*/
	
	function insert($data=array()){
		$this->db->insert($this->table_name,$data);
		return $this->db->insert_id(); 
	}
	
	
	/**
		@Function Name:	update
		@Author Name:	ben binesh
		@Date:			Aug, 28 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		udate data 
	
	*/
	function update($id,$data=array()){
		$this->db->where('uID',$id);
		$this->db->update($this->table_name,$data);
		return true;
	}
	
	/**
		@Function Name:	delete
		@Author Name:	ben binesh
		@Date:			Aug, 28 2013
		@id  | numeric| primary key of record 
		@return  boolean
		@Purpose:		delete data 
         * ********************************** 
        Chronological Development
        Janet Rajani | Nov 26, 2013 | Marked deleted field rather than removing from db
	
	*/
	function delete($id=0){
		$data = array(
			'urStatus'=>STATUS_UNREGISTERED,
			'urTimestamp'=>date('Y-m-d H:i:s'),
		);	
		$this->db->where('uID',$id);
		$this->db->update($this->table_name,$data);
		return true;
	}
	
	/**
		@Function Name :	check_registration
		@Author Name   :	ben binesh
		@Date:			Oct, 16 2013
		@course_id:  | numeric| course id 
		@email:  | string | email
		@return  array of record
		@Purpose:	check the user registration and return registration details
	
	*/
	
	function check_registration($course_id=0,$email=''){
		if($email)
			$this->db->where('urEmail',$email);	
		$this->db->where('urStatus',STATUS_REGISTERED);		
		if($course_id)
			$this->db->where('urCourse',$course_id);
		$query = $this->db->get($this->table_name);
		return $query->row();
	}
	
	/**
		@Function Name :	is_waiting
		@Author Name   :	ben binesh
		@Date:			Oct, 16 2013
		@course_reservation:  | object| course reservation single object
		@return  array of record
		@Purpose:	check weather the user status is pending or waiting
	
	*/

	function is_waiting($course_reservation=object)
	{	
		$course = $this->course_schedule_model->get_course_detail($course_reservation->urCourse);
		$result = self::get_records('','',$course_reservation->urCourse,0, -1,STATUS_REGISTERED);
		foreach($result as $result){
			if($result->uID == $course_reservation->uID){
				if($result->csMaximumEnrollees < $result->serial_number+$course->enrolees_count){
					return true;
				}else{
					return false;
				}
				break;
			}
		}
	
	}
	
	/**
		@Function Name :	is_allowed_to_pay
		@Author Name   :	ben binesh
		@Date:			Oct 17, 2013
		@payment_start_date |string| non-guranteed payment start date 
		@return  boolean
		@Purpose:	check whether the waiting user are allowed to pay or note 
	
	*/
	
	function is_allowed_to_pay($payment_start_date=''){
		$difference = date_difference_days($payment_start_date,date('Y-m-d'));
		if( 1 <= $difference){
			return true;
		}else{
			 return false; 
		}	
	}
	
	/**
		@Function Name :	get_reminder_email_list
		@Author Name   :	ben binesh
		@Date:			Oct 17, 2013
		@waiting       |boolean| waiting flag if true give waiting email list if false giver guaranteed email list 
		@credit       |boolean| get the email list have credit checked 
		@reimbursed       |boolean| get the email list have reimbursed checked  
		@return  array of emails
		@Purpose:	get the email list 
	
	*/
	
	function get_reminder_email_list($waiting = 'waiting')
	{
		$guarnteed_array  = array();
		$waiting_array    =  array();
		$all_array        =  array(); 
		$return           = array();
		$this->load->model('course_schedule_model');
		$this->db->select('DISTINCT(urCourse)',FALSE);
		$query = $this->db->get($this->table_name);
		if( $query->num_rows() >0)
		{
			$results = $query->result();
			
			foreach($results as $result)
			{
				$this->db->select('
					ur.*,
					cs.csCourseDefinitionId,cs.csMaximumEnrollees,cs.csPaymentStartDate,
					@a:=@a+1 serial_number,
					
				',FALSE);
				$this->db->join('course_schedule cs','cs.csID = ur.urCourse','INNER');
				$this->db->where('ur.urCourse',$result->urCourse);
				$this->db->where('ur.urStatus',STATUS_REGISTERED);
				$this->db->where('cs.csStartDate > ',date('Y-m-d'));
				$this->db->order_by('ur.uID','ASC');		
				$query = $this->db->get($this->table_name.' ur, (SELECT @a:= 0) AS a');
				$reservations = $query->result();
				$enrollees_count=$this->course_schedule_model->get_enrollee_count($result->urCourse);
				if($reservations){
					foreach($reservations as $reservation){
						$course=$result->urCourse;
						$email=$reservation->urEmail;
						//for all users(guranteed, waiting).
						$all_array[]=array(
									'email'=>$email,
									'course'=>$course,
								);
						
						//guranteed list 
						$waiting_count = $reservation->csMaximumEnrollees-($reservation->serial_number+$enrollees_count); 
						if( 0 <= $waiting_count ){
							$guarnteed_array[] = array(
								'email'=>$email,
								'course'=>$course,
							);
							
						}else{
							//check payment start date for the waiting list 
							//if(self::is_allowed_to_pay($reservation->csPaymentStartDate)){
								$waiting_array[]=array(
									'email'=>$email,
									'course'=>$course,
								);
													
								
							//}
						}
					}
				}
				
			}
			if($waiting == 'waiting')
				return $waiting_array;
			
			else if($waiting == 'all')
				return $all_array;
		
			else 
				return $guarnteed_array;
			
			
			
		}
			
	}
	
	
	/**
		@Function Name:	get_status_array
		@Author Name:	ben binesh
		@Date:		Nov, 15 2013
		@empty_array    |array| empty value 
 		@empty |boolean| empty flag
		
		@return  array
		@Purpose:		get array of status
		
	*/
	function get_status_array($empty=false,$empty_array=array(''=>'')){
		$status_array=array();
		if($empty){
			$status_array = array_merge($status_array,$empty_array);
		}
		$status_array[STATUS_ENROLLED]   = 'Enrolled';
		$status_array[STATUS_REGISTERED] = 'Registrant';
		$status_array[STATUS_UNREGISTERED] = 'Unregistrant';
		$status_array[STATUS_UNENROLLED] = 'Unenrolled';
		
		return $status_array;
	}
	/**
		@Function Name:	show_status
		@Author Name:	ben binesh
		@Date:		Nov, 15 2013
		@status  | numeric| status of record 
		@return  string
		@Purpose:		return status string 
	
	*/
	function show_status($status = 0){
		$status_array =self::get_status_array();
		return (isset($status_array[$status]))?$status_array[$status]:'Registrant';
	}
	
	/**
		@Function Name:	get_reservation_byFilters
		@Author Name:	ben binesh
		@Date:			Nov, 13 2013
		@course_id  | numeric | course_id
		@status     | boolean | registered or enolled   		
		@registered_date     | string| registration date 
		@return  array 
		@Purpose:		get reservation date statistics 
	
	*/

	function get_reservation_byFilters($course_id = 0,$status='',$registered_date=NULL,$enrollees=false)
	{
		$this->db->select('
			ur.*,
			cs.csCourseDefinitionId,cs.csMaximumEnrollees,cs.csPaymentStartDate,cs.csLocation,cs.csCity,cs.csState,cs.csStartDate,cs.csEndDate,cs.csCourseType,
			cd.cdCourseID,cd.cdCourseTitle
			
		',false);
		$this->db->join('course_schedule cs','cs.csID = ur.urCourse','INNER');
		$this->db->join('course_definitions cd','cs.csCourseDefinitionId = cd.cdID','LEFT');
		$this->db->join('district dis','ur.urDistrictID = dis.disID','LEFT');
		
		if($course_id)
			$this->db->where('ur.urCourse',$course_id);
		
		if($status !== ''){
			if(is_array($status))
				$this->db->where_in('ur.urStatus',$status);
			else
				$this->db->where('ur.urStatus',$status);
		}	
		
		if($registered_date)
		{
			if($enrollees)
				$this->db->where('DATE(urEnrolledTime)',$registered_date);
			else
				$this->db->where('DATE(urTimestamp)',$registered_date);
		}	
			
		$this->db->order_by('cs.csStartDate ASC,dis.disName ASC');		
		$query = $this->db->get($this->table_name.' ur');
		return $query->result();	
	}
	
	/**
		@Function Name:	count_reservation_byFilters
		@Author Name:	ben binesh
		@Date:			Nov, 13 2013
		@course_id     | numeric | course_id
		@status        | boolean | registered or enolled   		
		@registered_date     | string| registration date 
		@return  numeric
		@Purpose:	count records base on various filters 
	
	*/
	
	function count_reservation_byFilters(
										$course_id = 0,
										$status='',
										$current_date=NULL,
										$enrollees=FALSE,
										$current_week=FALSE,
										$current_month=FALSE
	)
	{
	
	
		if($course_id)
			$this->db->where('ur.urCourse',$course_id);
		
		if($status !== '')
			$this->db->where('ur.urStatus',$status);
		

		if($current_date)
		{
			if($current_week)
			{	if($enrollees)
				{
				$this->db->where("SUBDATE('$current_date', WEEKDAY('$current_date')) <= DATE(urEnrolledTime)
				AND DATE(urEnrolledTime) < ADDDATE('$current_date', 7 - WEEKDAY('$current_date'))",'',FALSE);
				}
				else
				{
					$this->db->where("SUBDATE('$current_date', WEEKDAY('$current_date')) <= DATE(urTimestamp)
				AND DATE(urTimestamp) < ADDDATE('$current_date', 7 - WEEKDAY('$current_date'))",'',FALSE);
				}
			}
			elseif($current_month)
			{
				if($enrollees)
					$this->db->where("MONTH(urEnrolledTime) = '".format_date($current_date,'m')."' AND YEAR(urEnrolledTime) = '".format_date($current_date,'Y')."'",'',FALSE);
				else
					$this->db->where("MONTH(urTimestamp) ='".format_date($current_date,'m')."' AND YEAR(urTimestamp) = '".format_date($current_date,'Y')."'",'',FALSE);
			}
			else
			{
				if($enrollees)
					$this->db->where('DATE(urEnrolledTime)',$current_date);
				else
					$this->db->where('DATE(urTimestamp)',$current_date);
			}	
				
		}	
		$count = $this->db->count_all_results($this->table_name.' ur');	
		return $count;
	}	
	
	/**
		@Function Name:	send_daily_registrants_email
		@Author Name:	ben binesh
		@Date:			Nov, 15 2013
		@return  void
		@Purpose:	send daily registrants/enrollees details to admin
	
	*/
	
	
	function send_daily_registrants_email(){
		//get the registration and un-registration details 
		$data['register'] = self::get_reservation_byFilters(0,array(STATUS_REGISTERED,STATUS_UNREGISTERED),date('Y-m-d'));
		
		//get the enrollees and un-enrollees details 
		$data['enrolled'] = self::get_reservation_byFilters(0,array(STATUS_ENROLLED,STATUS_UNENROLLED),date('Y-m-d'),TRUE);
		
		$data['results']=array_merge($data['register'],$data['enrolled']);
		if(empty($data['results'])){
			return;
		}
		$file_name=UPLOADS.'/course-member-'.time().'.xls';
		//load the content to view and get outut as html string 
		$content = $this->load->view('edu_admin/course_reservation/course_member',$data,true);
		file_put_contents($file_name,$content);
		$email_template = get_content('email_templates','*','etID = 22');
		if(!empty($email_template)){
			$email_template=$email_template[0];
			$searchReplaceArray = array(
				'[CurrentDate]'   =>date(DATE_FORMAT),
				'[CourseRegistrant]'   =>$content ,
				);
			$subject=$email_template->etSubject.'-'.date(DATE_FORMAT);
			$email_message = str_replace(
				  array_keys($searchReplaceArray), 
				  array_values($searchReplaceArray),$email_template->etCopy); 
			//get admin emails 
			$emails = get_admin_emails();  
			$emails = array_merge($emails,array('alan.anil@ithands.net'));
			send_mail(ADMIN_EMAIL,SITE_NAME,SENDER_EMAIL,$subject,$email_message,$emails,$file_name);
			@unlink($file_name);
		}	
	}
	
	/**
		@Function Name:	set_unenrolled
		@Author Name:	ben binesh
		@Date:			Dec, 18 2013
		@return  void
		@Purpose:	set  status as un-enrolled
	
	*/
	function set_unenrolled($course_ids=array(),$email)
	{
		$this->db->where_in('urCourse',$course_ids);
		$this->db->where('urEmail',$email);
		$this->db->where('urStatus',STATUS_ENROLLED);
		$this->db->update($this->table_name,array(
			'urStatus'=>STATUS_UNENROLLED,
			'urEnrolledTime'=>date('Y-m-d H:i:s'),
		));
		return ;
	}
	
	
	
}//end of class
//end of file 
	
	