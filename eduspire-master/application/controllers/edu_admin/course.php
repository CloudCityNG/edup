<?php
/**
@Page/Module Name/Class: 		course.php
@Author Name:			  		ben binesh
@Date:					  		Jan, 20 2013
@Purpose:		         		Contain all controller functions for the course schedule
								and dates 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
//Chronological development
//***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
/***********************************************************************************
 
//***********************************************************************************/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Course extends CI_Controller {
	
	
	public $js;
	protected  $_id;
	public function __construct()
	{
		parent::__construct();
         use_ssl(FALSE);
		$js=array();
		$this->_id = 0;
		$this->load->helper('common');
		$this->load->helper('form');
		
		if(!is_logged_in())
		{
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}else
		{
			//check the sufficient access level 
			$this->_current_request = 'edu_admin/course_schedule/enrollees';
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
	public function enrollees()
	{
		
		$data=array();
		$this->load->model('course_model');
		$this->load->model('assignment_model');
		if($this->input->get('export'))
		{
			$this->_export_enrollees();
			exit();
			
		}
		
		$data['layout']        = '';
        $this->page_title      = "Course Enrollees";
		$data['meta_title']    = 'Course Enrollee';
		$data['name']          = $this->input->get('name'); 
		$data['course_id']     = $this->input->get('course_id'); 
		$num_records           = $this->course_model->count_enrollees(
		$data['name'],$data['course_id']);
		$base_url             = base_url().'edu_admin/course/enrollees';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->course_model->get_enrollees( $data['name'],$data['course_id'],$start , $per_page);
		
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/course/enrollees';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	public function registrants()
	{
		$data=array();
		$this->load->model('course_reservation_model');
		if($this->input->get('export'))
		{
			$this->_export_registrants(STATUS_REGISTERED);
			exit();
			
		}
		$data['meta_title']='Course Registrants';
		
		
		$data['name']        = $this->input->get('name'); 
		$data['email']        = $this->input->get('email'); 
		$data['course_id']    = $this->input->get('course_id'); 
		$num_records          = $this->course_reservation_model->count_records($data['name'],$data['email'] ,$data['course_id'],STATUS_REGISTERED);
		$base_url             = base_url().'edu_admin/course/registrants';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) )
		{
			$start = 0;
		}
		$per_page            = PER_PAGE;
		$data['results']     = $this->course_reservation_model->get_records( $data['name'],$data['email'] ,$data['course_id'], $start , $per_page,STATUS_REGISTERED);
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		
		
		
		$data['main'] = 'edu_admin/course/registrants';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	
	function _export_enrollees(){
		$name          = $this->input->get('name'); 
		$course_id     = $this->input->get('course_id'); 
		$results     = $this->course_model->get_enrollees( $name,$course_id,0 , -1);
		$export_array = array();
		//build columns 
		$export_array[] = array(
			'S.No',
			'Course Name',
			'Course Location',
			'Course Start Date',
			'Last Name',
			'First Name',
			'Birth Date',
			'Username',
			'E-mail',
			'Phone',
			'Address',
			'City',
			'State',
			'zip',
			'School District',
			'IU',
			'Track',
			'Non-Credit',
			'Ipad',
			'Ipad Options',
			'Grade',
			
		);
		$i=1;
		
		$school_district='';
		
		foreach($results as $result)
		{
			$course_id=0;
			if($result->csID)
				$course_id=$result->csID;
			$course_location= $result->csCity.','.$result->csState;  
			if(COURSE_ONLINE==$result->csCourseType)
				$course_location='Online';
			$ipad_info='';
			$upgrade_info='';
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
						$upgrade_info = $result->upgrade_info;
				}
			}
			
			
			//School District 
			if(is_numeric($result->districtAffiliation)){
				$school_district= get_single_value('district','disName','disID = '.$result->districtAffiliation) ;
			}else{
				$school_district= $result->districtAffiliation; 
			}
			
			
			//iu 
			$iu='';
			if($result->iuID)
				$iu = get_single_value('iu_unit','iuName','iuID = '.$result->iuID) ;
			
			//grade
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
			
			$credit = show_credit_status($result->act48,$course_id);
			$export_array[]=array(
				$i,
				$result->cdCourseID.':'.$result->cdCourseTitle,
				$course_location,
				format_date($result->csStartDate,DATE_FORMAT),
				$result->lastName,
				$result->firstName,
				format_date($result->birthDate,DATE_FORMAT),
				$result->userName,
				$result->email,
				$result->phone,
				$result->address,
				$result->city,
				$result->state,
				$result->zip,
				$school_district,
				$iu,
				$track,
				$credit,
				$ipad_info,
				$upgrade_info,
				$final_grade,
				
				
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
		@Function Name:	_export
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		generate the csv report of records
	
	*/
	function _export_registrants($status=STATUS_REGISTERED)
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

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */