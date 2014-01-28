<?php
/**
@Page/Module Name/Class: 		course_schedule.php
@Author Name:			  		ben binesh
@Date:					  		Aug, 16 2013
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
//RF1.	  |  ben binesh		 | Sept,19 2013  | major	   | add function to show the course
														enrollees
//REf2    |	 ben binesh      | minor |                     | Add Build your Own Course related functions 
//RF3     |  Alan Anil       |Nov 20 2013    | Normal   | Function for showing and save changes added by user to grades.  
//RF4     |  Alan Anil       |Nov 20 2013    | Minor   | store course id.  
//***********************************************************************************/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Course_schedule extends CI_Controller {
	
	
	public $js;
	protected  $_id;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->_id = 0;
		$this->load->model('course_schedule_model');
		$this->load->helper('common');
		$this->load->helper('form');
		
		if(!is_logged_in())
		{
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}else
		{
			//check the sufficient access level 
			$this->_current_request = 'edu_admin/'.$this->router->class.'/'.$this->router->method ;
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
		
		$data['layout']        = '';
        $this->page_title      = "Course Schedules";
		$data['meta_title']    = 'Course Schedules';
		$data['title']         = $this->input->get('title'); 
		$data['course_id']     = $this->input->get('course_id'); 
		$data['genre']         = $this->input->get('genre'); 
		$data['definition_id'] = $this->input->get('definition_id'); 
		
		if($data['definition_id']){
			$this->load->model('course_definition_model');
			$data['definition']=$this->course_definition_model->get_single_record($data['definition_id']);
		}
		$data['status']       = ($this->input->get('status') != '')?$this->input->get('status'):''; 
		$data['course_type']  = ($this->input->get('course_type') != '')?$this->input->get('course_type'):''; 
		$data['start_date']   = $this->input->get('start_date'); 
		$data['end_date']     = $this->input->get('end_date'); 
		$data['address']      = $this->input->get('address'); 
		$data['course_date']  =	($this->input->get('course_date') != '')?$this->input->get('course_date'):COURSE_CURRENT ; 
		$num_records          = $this->course_schedule_model->count_records($data['title'],$data['course_id'],$data['genre'],$data['start_date'] ,$data['end_date'],$data['status'], $data['course_type'],$data['address'],FALSE,$data['definition_id'],0,$data['course_date']);
		$base_url             = base_url().'edu_admin/course_schedule/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->course_schedule_model->get_records( $data['title'],$data['course_id'],$data['genre'],$data['start_date'] ,$data['end_date'],$data['status'], $data['course_type'],$data['address'], $start , $per_page,FALSE ,$data['definition_id'],0,$data['course_date']);
		
		
		
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/course_schedule/index';
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
		
		$error = false;
		$errors = array();
        $this->page_title="Create Course Schedule";
		$data['meta_title']='Create Course Schedule';                
		$this->load->helper('form');
		$this->js[]='js/jquery-ui.js';
		$this->css[]='css/jquery-ui.css';
		
		if(count($_POST)>0)
		{
			
			$this->load->library('form_validation');
			$this->form_validation->set_rules('csGenreId', 'Course genre', 'trim|required');
			$this->form_validation->set_rules('csCourseDefinitionId', 'Course Definition', 'trim|required');
			$this->form_validation->set_rules('csRegistrationStartDate', 'Registration start date ', 'trim|required');
			$this->form_validation->set_rules('csRegistrationEndDate', 'Registration end date ', 'trim|required');
			$this->form_validation->set_rules('csPaymentStartDate', 'Payment Start Date ', 'trim|required');
			
			if(COURSE_OFFLINE == $this->input->post('csCourseType'))
			{
				//validation for the offline course 
				$this->form_validation->set_rules('csLocation', 'Course location', 'trim|required');
				$this->form_validation->set_rules('csAddress', 'Course address', 'trim|required');
				$this->form_validation->set_rules('csIURegion', 'IU Region', 'trim|required');
				
			}
			else
			{
				//validation for the online course 
				$this->form_validation->set_rules('cs_start_date', 'Couser Start Date', 'trim|required');
				$this->form_validation->set_rules('cs_end_date', 'Course End Date', 'trim|required');
			}
			$this->form_validation->set_rules('csMaximumEnrollees', 'Maximum Enrollees ', 'trim|required|numeric');
			$this->form_validation->set_rules('csPrice', 'Price', 'trim|required|numeric');
			$this->form_validation->set_rules('csNonCreditPrice', 'Non-Credit Price', 'trim|required|numeric');
			$this->form_validation->set_message('required', '%s must not be blank');
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
					
					
					$data_array = array(
								'csPublish' => $this->input->post('csPublish'),
								'csGenreId' => $this->input->post('csGenreId'),
								'csCourseDefinitionId' => $this->input->post('csCourseDefinitionId'),
								'csCourseType' => $this->input->post('csCourseType'),
								'csRegistrationStartDate' => format_date($this->input->post('csRegistrationStartDate'),'Y-m-d'),
								'csRegistrationEndDate' => format_date($this->input->post('csRegistrationEndDate'),'Y-m-d'),
								'csPaymentStartDate' => format_date($this->input->post('csPaymentStartDate'),'Y-m-d'),
								'csLocation' => $this->input->post('csLocation'),
								'csAddress' => $this->input->post('csAddress'),
								'csCity' => $this->input->post('csCity'),
								'csState' => $this->input->post('csState'),
								'csZIP' => $this->input->post('csZIP'),
								'csIURegion' => $this->input->post('csIURegion'),
								'csMaximumEnrollees' => $this->input->post('csMaximumEnrollees'),
								'csPrice' => $this->input->post('csPrice'),
								'csNonCreditPrice' => $this->input->post('csNonCreditPrice'),
								'csNonCreditComment' => $this->input->post('csNonCreditComment'),
								);
					
				
				$course_schedule_id = $this->course_schedule_model->insert($data_array);
				//insert course dates 
				$this->_insert_date($course_schedule_id);
				//insert course instructor
				$this->_add_instructor($course_schedule_id);
				set_flash_message('Course schedule  details has been inserted successfully','success');
				if($redirect=$this->input->get('redirect'))
				{
					redirect($redirect);
				}
				redirect('edu_admin/course_schedule/index');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/course_schedule/form';
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
		$error=false;
		$errors=array();
		
		$data['result']=$this->_load_data($id);
		$this->page_title="Update Course #".$data['result']->csID;
		$data['meta_title']='Update Course Schedule';         
		$this->js[]='js/jquery-ui.js';
		$this->css[]='css/jquery-ui.css';
		
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('csGenreId', 'Course genre', 'trim|required');
			$this->form_validation->set_rules('csCourseDefinitionId', 'Course Definition', 'trim|required');
			$this->form_validation->set_rules('csRegistrationStartDate', 'Registration start date ', 'trim|required');
			$this->form_validation->set_rules('csRegistrationEndDate', 'Registration end date ', 'trim|required');
			$this->form_validation->set_rules('csPaymentStartDate', 'Payment Start Date ', 'trim|required');
			
			if(COURSE_OFFLINE == $this->input->post('csCourseType'))
			{
				//validation for the offline course 
				$this->form_validation->set_rules('csLocation', 'Course location', 'trim|required');
				$this->form_validation->set_rules('csAddress', 'Course address', 'trim|required');
				$this->form_validation->set_rules('csIURegion', 'IU Region', 'trim|required');
				
			}
			else
			{
				//validation for the online course 
				$this->form_validation->set_rules('cs_start_date', 'Course Start Date', 'trim|required');
				$this->form_validation->set_rules('cs_end_date', 'Course End Date', 'trim|required');
			}
			
			$this->form_validation->set_rules('csMaximumEnrollees', 'Maximum Enrollees ', 'trim|required|numeric');
			$this->form_validation->set_rules('csPrice', 'Price', 'trim|required|numeric');
			$this->form_validation->set_rules('csNonCreditPrice', 'Non-Credit Price', 'trim|required|numeric');
			$this->form_validation->set_message('required', '%s must not be blank');
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
					
					
					$data_array = array(
								'csPublish' => $this->input->post('csPublish'),
								'csGenreId' => $this->input->post('csGenreId'),
								'csCourseDefinitionId' => $this->input->post('csCourseDefinitionId'),
								'csCourseType' => $this->input->post('csCourseType'),
								'csRegistrationStartDate' => format_date($this->input->post('csRegistrationStartDate'),'Y-m-d'),
								'csRegistrationEndDate' => format_date($this->input->post('csRegistrationEndDate'),'Y-m-d'),
								'csPaymentStartDate' => format_date($this->input->post('csPaymentStartDate'),'Y-m-d'),
								'csLocation' => $this->input->post('csLocation'),
								'csAddress' => $this->input->post('csAddress'),
								'csCity' => $this->input->post('csCity'),
								'csState' => $this->input->post('csState'),
								'csZIP' => $this->input->post('csZIP'),
								'csIURegion' => $this->input->post('csIURegion'),
								'csMaximumEnrollees' => $this->input->post('csMaximumEnrollees'),
								'csPrice' => $this->input->post('csPrice'),
								'csNonCreditPrice' => $this->input->post('csNonCreditPrice'),
								'csNonCreditComment' => $this->input->post('csNonCreditComment'),
								
								);
					
				
				$this->course_schedule_model->update($id,$data_array);
				//insert course dates 
				$this->_insert_date($id);
				
				//insert course instructor
				$this->_add_instructor($id);
				set_flash_message('Course schedule  details has been updated successfully','success');
				
				if($redirect=$this->input->get('redirect')){
					redirect($redirect);
				}
				redirect('edu_admin/course_schedule/index');
				
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/course_schedule/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	copy
		@Author Name:	ben binesh
		@Date:			Aug, 30 2013
		@id             | numeric | course schedule id 
		@Purpose:		copy existing course schedule and create new one 
	
	*/
	
	function copy($id=0)
	{
		if(!$id)
		{
			show_404('page');
		}
		$course = $this->course_schedule_model->get_course_detail($id);
		if(empty($course))
		{
			show_404('page');
		}
		
		$data_array = array(
						'csPublish' => $course->csPublish,
						'csGenreId' => $course->csGenreId,
						'csCourseDefinitionId' => $course->csCourseDefinitionId,
						'csCourseType' => $course->csCourseType,
						'csStartDate'=>$course->csStartDate,
						'csEndDate'=>$course->csEndDate,
						'csRegistrationStartDate' => $course->csRegistrationStartDate,
						'csRegistrationEndDate' => $course->csRegistrationEndDate,
						'csPaymentStartDate' => $course->csPaymentStartDate,
						'csLocation' => $course->csLocation,
						'csAddress' => $course->csAddress,
						'csCity' => $course->csCity,
						'csState' => $course->csState,
						'csZIP' => $course->csZIP,
						'csIURegion' => $course->csIURegion,
						'csMaximumEnrollees' => $course->csMaximumEnrollees,
						'csPrice' => $course->csPrice,
						'csNonCreditPrice' => $course->csNonCreditPrice,
						'csNonCreditComment' => $course->csNonCreditComment,
						'csCourseSession' => $course->csCourseSession,
					);
		$course_schedule_id = $this->course_schedule_model->insert($data_array);
		
		//copy dates 
		
		$course_dates=$course->course_dates;
		if(!empty($course_dates) && count($course_dates)>0)
		{
			foreach($course_dates as $course_date)
			{
				$schedule_date_array=array(
							'csdCourseScheduleId'=>$course_schedule_id,
							'csdStartDate'=>$course_date->csdStartDate,
							'csdEndDate'=>$course_date->csdEndDate,
							'csdStartTime'=>$course_date->csdStartTime,
							'csdEndTime'=>$course_date->csdEndTime,
				);
				$this->course_schedule_model->insert_date($schedule_date_array);	
			}
			
		}
	
		//copy instructor
		
		$instructors = $this->course_schedule_model->get_instuctors($id);
		if(!empty($instructors))
		{
			foreach($instructors as $instructor)
			{
				$insert_array=array(
					'ciCsID'=>$course_schedule_id,
					'ciUID'=>$instructor->id,
					
				);
			$this->course_schedule_model->insert_instructor($insert_array);
			}
		}
		
		set_flash_message('Course has been successfully copied ,edit course details ','success');
		if('byoc'==$this->input->get_post('ref'))
		{
			redirect('edu_admin/course_schedule/byoc_add/'.$course_schedule_id.'?ref=byoc');	
		}
		redirect('edu_admin/course_schedule/update/'.$course_schedule_id.'?ref=courses');	
	}
	
	
	/**
		@Function Name:	delete 
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		validate and delete the record 
	
	*/
	function  delete($id=0)
	{
		$this->_load_data($id);
		$is_delete = true;
		//check the associated records 
		//get registered user count
		if($this->course_schedule_model->get_registerant_count($id))
		{
			$is_delete = false;
			set_flash_message('Course Schedule cannot  be deleted as there  are registrant associated with it ','error');
		}
		elseif($this->course_schedule_model->get_enrollee_count($id))
		{
			$is_delete = false;
			set_flash_message('Course Schedule cannot  be deleted as there  are enrollee associated with it ','error');
		}
		
		//if delete 
		if($is_delete)
		{
			set_flash_message('Course Schedule details has been successfully deleted  ','success');
			$this->course_schedule_model->delete($id);
			$this->course_schedule_model->delete_date($id);
		}
		if($redirect=$this->input->get_post('redirect'))
		{
			redirect($redirect);
		}
		redirect('edu_admin/course_schedule/index');
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
		$data = $this->course_schedule_model->get_single_record($id);
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
		@Function Name:	_insert_date
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		check the duplicate record in data base with same title  
	
	*/
	
	protected function _insert_date($course_schedule_id=0)
	{	
		
		$start_date ='';
		$end_date  = '';
		if(!$course_schedule_id)
		{
			return ;
		}
		
		// delete all the previous dates first 
		$this->course_schedule_model->delete_date($course_schedule_id);
		
		if(COURSE_ONLINE == $this->input->post('csCourseType'))
		{
			$schedule_date_array=array(
				'csdCourseScheduleId'=>$course_schedule_id,
				'csdStartDate'=>format_date($this->input->post('cs_start_date'),'Y-m-d'),
				'csdEndDate'=>format_date($this->input->post('cs_end_date'),'Y-m-d'),
			);
			$start_date =format_date($this->input->post('cs_start_date'),'Y-m-d');
			$end_date  = format_date($this->input->post('cs_end_date'),'Y-m-d');
			
			$this->course_schedule_model->insert_date($schedule_date_array);
			
		}
		else
		{
			$schedule_dates = $this->input->post('cs_start_date_multiple');
			if(!empty($schedule_dates) && count($schedule_dates)>0)
			{
				for( $i = 0 ; $i < count( $schedule_dates ) ; $i++ )
				{
					if('' != $schedule_dates[$i])
					{
						$start_hour   = '';
						$start_time   = '';
						
						$end_hour   = '';
						$end_time   = '';
						
						if('pm' == $_POST['cs_start_ampm'][$i])
						{
							
							$start_hour = (12 != $_POST['cs_start_hour'][$i])?$_POST['cs_start_hour'][$i]+12:'00';
						}
						else
						{
							$start_hour = $_POST['cs_start_hour'][$i];
						}
						$start_time=$start_hour.':'.$_POST['cs_start_minute'][$i].':00';
							
						
						if('pm' == $_POST['cs_end_ampm'][$i])
						{
							$end_hour = (12 != $_POST['cs_end_hour'][$i])?$_POST['cs_end_hour'][$i]+12:'00';
						}
						else
						{
							$end_hour = $_POST['cs_end_hour'][$i];
						}
						
						$end_time=$end_hour.':'.$_POST['cs_end_minute'][$i].':00';
						
						$schedule_date_array=array(
								'csdCourseScheduleId'=>$course_schedule_id,
								'csdStartDate'=>format_date($schedule_dates[$i],'Y-m-d'),
								'csdStartTime'=>$start_time,
								'csdEndTime'=>$end_time,
						);
						
						$this->course_schedule_model->insert_date($schedule_date_array);
						$end_date=format_date($schedule_dates[$i],'Y-m-d');
					}
					$start_date=format_date($schedule_dates[0],'Y-m-d');
					
				}
			}
		}
		
		//update the course start date and coure end date in course_schedule table 
		//redundent data for performance 
		$this->course_schedule_model->update($course_schedule_id,array(
			'csStartDate'=>$start_date,
			'csEndDate'=>$end_date,
		));
		
	}
	
	/**
		@Function Name:	_add_instructor
		@Author Name:	ben binesh
		@Date:			Aug, 23 2013
		@Purpose:		add instructor to the course 
	
	*/
	function _add_instructor($course_id=0)
	{
		$instructors =$this->input->post('instructor'); 
		//delete old instructors 
		$this->course_schedule_model->delete_instructor($course_id);
		//insert instructor
		if($instructors && count($instructors)>0)
		{
			foreach($instructors as $instructor)
			{
				$insert_array=array(
					'ciCsID'=>$course_id,
					'ciUID'=>$instructor,
					
				);
				
				$this->course_schedule_model->insert_instructor($insert_array);
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
		if(!($course_id = $this->input->get('course_id')))
		{
			redirect('edu_admin/course_schedule/index');
		}
		
		if($this->input->post('mass_action'))
		{
			$this->_send_activation_email();
			
		}
		
		
		if($this->input->get('export'))
		{
			$this->_export_enrollees();
			exit();
		}
		
		if($this->input->get('grade_export'))
		{
			$this->_export_grade();
			exit();
		}
		$this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]='js/fancybox/source/jquery.fancybox.css';
		$this->js[]='js/admin.js';	
		$this->load->model('assignment_model');	
		$data['layout']       = '';
		$data['name']         = $this->input->get('name'); 
		$data['email']        = $this->input->get('course_id'); 
//RF4
		$data['course_id']    = $course_id;
//RF4 end.
		$data['course']       = $this->course_schedule_model->get_course_detail($course_id);
		$data['num_records']  = $this->course_schedule_model->get_enrollee_count($course_id);
		$data['results']      = $this->course_schedule_model->get_enrollees( $data['name'],$course_id, 0 , -1 );
		$data['main'] = 'edu_admin/course_schedule/enrollees';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	
	/**
		@Function Name:	_send_activation_email
		@Author Name:	ben binesh
		@Date:			Jan, 03 2014
		@Purpose:		export enrollees details
	
	*/
	function _send_activation_email()
	{
		$ids=$this->input->post('ids');
		if(!empty($ids) && count($ids > 0))
		{
			$this->load->model('user_model');
			foreach($ids as $id)
			{
				$user = $this->user_model->get_single_record($id);
				if(!empty($user))
				{
					$this->user_model->send_activation_email($user);
				}
			}
			
		}
				
		set_flash_message('Account activation mail has been successfully sent','success');
	}
	
	/**
		@Function Name:	_export_enrollees
		@Author Name:	ben binesh
		@Date:			Sept, 20 2013
		@Purpose:		export enrollees details
	
	*/
	function _export_enrollees()
	{
		if(!($course_id = $this->input->get('course_id')))
		{
			redirect('edu_admin/course_schedule/index');
		}
		
		$results  = $this->course_schedule_model->get_enrollees( '',$course_id, 0 , -1 );
		if(empty($results))
		{
			set_flash_message('No records to export ','error');
			redirect('edu_admin/course_schedule/enrollees?course_id='.$course_id);
		}
		$this->load->model('assignment_model');
		$course       = $this->course_schedule_model->get_course_detail($course_id);
		$course_location=$course->csCity.'-'.$course->csState;
		if(COURSE_ONLINE == $course->csCourseType)
			$course_location='Online';	
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
				$course->cdCourseID.':'.$course->cdCourseTitle,
				$course_location,
				format_date($course->csStartDate,DATE_FORMAT),
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
		@Function Name:	_export_grade
		@Author Name:	ben binesh
		@Date:			Sept, 20 2013
		@Purpose:		export enrollees details
	
	*/
	function _export_grade()
	{
		if(!($course_id = $this->input->get('course_id')))
		{
			redirect('edu_admin/course_schedule/index');
		}
		
		$results  = $this->course_schedule_model->get_enrollees( '',$course_id, 0 , -1);
		if(empty($results))
		{
			set_flash_message('No records to export ','error');
			redirect('edu_admin/course_schedule/enrollees?course_id='.$course_id);
		}
		$this->load->model('assignment_model');
		$course       = $this->course_schedule_model->get_course_detail($course_id);
		$course_location=$course->csCity.'-'.$course->csState;
		if(COURSE_ONLINE == $course->csCourseType)
			$course_location='Online';	
		$export_array = array();
		//build columns 
		$export_array[] = array(
			'S.No',
			'Credits',
			'Tuition',
			'Grade',
			'Course Location',
			'Course Date',
			'Course',
			'Last Name',
			'First Name',
			'Birth Date',
			'Email',
			'Phone',
			'Address',
			'City',
			'State',
			'zip',
		);
		$i=1;
		
		
		foreach($results as $result)
		{
			//remove non-credit enrollees form grade export 
			if(!check_credit($result->act48,$course_id))
				continue;
			
			$ipad_info='';
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
			
			
			$export_array[]=array(
				$i,
				$course->cgCourseCredits,
				'',
				$final_grade,
				$course_location,
				$course->cdCourseID.':'.$course->cdCourseTitle,
				format_date($course->csStartDate,DATE_FORMAT),
				$result->lastName,
				$result->firstName,
				format_date($result->birthDate,DATE_FORMAT),
				$result->email,
				$result->phone,
				$result->address,
				$result->city,
				$result->state,
				$result->zip,
			);
			$i++;
		}
		
		
		$file_name = 'course-grade-export-.'.$course->cdCourseID.':'.$course->cdCourseTitle.'-'.time();
		$file_name=url_title($file_name,'-',TRUE);
		//generate csv file 
		$this->load->helper('csv');
		array_to_csv($export_array, $file_name.'.csv');
		
	}
	
	
/*********************************************************
 * Rf2	
/ Build you own course related functions 
/
**********************************************************/
	
	/**
		@Function Name:	one_credit
		@Author Name:	ben binesh
		@Date:			Oct 10, 2013
		@Purpose:		show the build your own course records with filters
	
	*/
	public function one_credit()
	{
		
		$data=array();
		$this->page_title="Course Schedule(one credit)";
		$data['meta_title']="Course Schedule(one credit)";
		if($this->input->get('export'))
		{
			$this->_export_courses(BYOC_ID);
			exit();
			
		}
		
		if($this->input->post('mass_action'))
		{
			$this->_mass_action_byoc();
			return ;
		}
		$this->js[]='js/admin.js';
		$data['layout']       = '';
		$data['title']        = $this->input->get('title'); 
		$data['course_id']    = $this->input->get('course_id'); 
		$data['genre']        = BYOC_ID;
		$data['status']       = ($this->input->get('status') != '')?$this->input->get('status'):''; 
		$data['course_type']  = COURSE_ONLINE;
		$data['start_date']   = $this->input->get('start_date'); 
		$data['end_date']     = $this->input->get('end_date'); 
		$data['address']      = $this->input->get('address'); 
		$data['course_date']  =	($this->input->get('course_date') != '')?$this->input->get('course_date'):COURSE_CURRENT ; 
		$data['definition_id'] = $this->input->get('definition_id'); 
		
		if($data['definition_id'])
		{
			$this->load->model('course_definition_model');
			$data['definition']=$this->course_definition_model->get_single_record($data['definition_id']);
		}
		
		$num_records          = $this->course_schedule_model->count_records($data['title'],$data['course_id'],$data['genre'],$data['start_date'] ,$data['end_date'],$data['status'], $data['course_type'],$data['address'],TRUE,$data['definition_id'],0,$data['course_date']);
		$base_url             = base_url().'edu_admin/course_schedule/one_credit';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) )
		{
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->course_schedule_model->get_records( $data['title'],$data['course_id'],$data['genre'],$data['start_date'] ,$data['end_date'],$data['status'], $data['course_type'],$data['address'], $start , $per_page ,TRUE,$data['definition_id'],0,$data['course_date']);
		
		
		
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/course_schedule/byoc_index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	/**
		@Function Name:	create 
		@Author Name:	ben binesh
		@Date:			Oct 10, 2013
		@Purpose:		insert/update the new record ,validate recored
	
	*/
	function byoc_add($course_schedule_id=0)
	{
		
		$error = false;
		$errors = array();
		$is_new_record = TRUE;
        $this->page_title = 'Create Course Schedule';
		$this->load->helper('form');
		$this->js[]  = 'js/jquery-ui.js';
		$this->css[] = 'css/jquery-ui.css';
		$this->load->model('course_definition_model');
		if($course_schedule_id)
		{
			$data['result'] = $this->_load_data($course_schedule_id);
			$this->_id      = $data['result']->csID;
			$is_new_record  = false;
            $this->page_title = 'Update Course #'.$data['result']->csID;
			
		}
		$data['session_array']=$this->course_definition_model->get_session_dropdown();
		if( count($_POST) > 0 )
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('csCourseDefinitionId', 'Course Definition', 'trim|required');
			$this->form_validation->set_rules('csCourseSession', 'Course Session', 'trim|required');
			$this->form_validation->set_rules('csRegistrationStartDate', 'Registration start date ', 'trim|required');
			$this->form_validation->set_rules('csRegistrationEndDate', 'Registration end date ', 'trim|required');
			$this->form_validation->set_rules('csPaymentStartDate', 'Payment Start Date ', 'trim|required');
			
			$this->form_validation->set_rules('cs_start_date', 'Course Start Date', 'trim|required');
			$this->form_validation->set_rules('cs_end_date', 'Course End Date', 'trim|required');
			$this->form_validation->set_rules('csMaximumEnrollees', 'Maximum Enrollees ', 'trim|required|numeric');
			$this->form_validation->set_rules('csPrice', 'Price', 'trim|required|numeric');
			$this->form_validation->set_rules('csNonCreditPrice', 'Non-Credit Price', 'trim|required|numeric');
			$this->form_validation->set_message('required', '%s must not be blank');
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
							'csPublish'               => $this->input->post('csPublish'),
							'csGenreId'               => BYOC_ID,
							'csCourseDefinitionId'    => $this->input->post('csCourseDefinitionId'),
							'csCourseType'            => COURSE_ONLINE ,
							'csRegistrationStartDate' => format_date($this->input->post('csRegistrationStartDate'),'Y-m-d'),
							'csRegistrationEndDate'   => format_date($this->input->post('csRegistrationEndDate'),'Y-m-d'),
							'csPaymentStartDate'      => format_date($this->input->post('csPaymentStartDate'),'Y-m-d'),
							'csMaximumEnrollees'      => $this->input->post('csMaximumEnrollees'),
							'csPrice'                 => $this->input->post('csPrice'),
							'csNonCreditPrice'        => $this->input->post('csNonCreditPrice'),
							'csNonCreditComment'      => $this->input->post('csNonCreditComment'),
							'csCourseSession'      => $this->input->post('csCourseSession'),
							);
					
				
				if($is_new_record)
				{
					set_flash_message('Course schedule details has been inserted successfully','success');
					$course_schedule_id = $this->course_schedule_model->insert($data_array);
				}
				else
				{
					$this->course_schedule_model->update($course_schedule_id,$data_array);
					set_flash_message('Course schedule details has been updated successfully','success');
				}
							
				//insert course dates 
				$this->_insert_date($course_schedule_id);
				//insert course instructor
				$this->_add_instructor($course_schedule_id);
				if($redirect=$this->input->get('redirect'))
				{
					redirect($redirect);
				}
				redirect('edu_admin/course_schedule/one_credit');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/course_schedule/byoc_form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	/**
		@Function Name:	_mass_action_byoc
		@Author Name:	ben binesh
		@Date:			Oct 10, 2013
		@Purpose:		handle the mass action request 
	
	*/
	
	public function _mass_action_byoc()
	{
		
		
		$chk_ids=$this->input->post('chk_ids');
		if(!empty($chk_ids) && count($chk_ids > 0))
		{
			$ids = implode(",", $chk_ids);
			if($this->input->post('update_attributes'))
			{
				$this->update_attributes($ids);
				return;
			}
			
		}
		redirect('edu_admin/course_schedule/one_credit');
	}
	
	
	/**
		@Function Name:	update_attributes
		@Author Name:	ben binesh
		@ids            | strings| course ids in csv
		@Date:			Oct 10, 2013
		@Purpose:		handle the mass action request 
	
	*/
	
	function update_attributes($ids='')
	{
		$data = array();
		$error=false;
		$errors=array();
		$data['ids']=$ids;
		$this->js[]  = 'js/jquery-ui.js';
		$this->css[] = 'css/jquery-ui.css';
		$this->load->model('course_definition_model');
		$data['session_array']=$this->course_definition_model->get_session_dropdown();
		if($this->input->post('ids')){
		
			
			$data['ids']=$this->input->post('ids');
			$this->load->library('form_validation');
			$this->form_validation->set_rules('csRegistrationStartDate', 'Registration start date ', 'trim');
			$this->form_validation->set_rules('csRegistrationEndDate', 'Registration end date ', 'trim');
			$this->form_validation->set_rules('csPaymentStartDate', 'Payment Start Date ', 'trim');
			$this->form_validation->set_rules('csCourseSession', 'Course Session ', 'trim');
			
			$this->form_validation->set_rules('cs_start_date', 'Course Start Date', 'trim');
			$this->form_validation->set_rules('cs_end_date', 'Course End Date', 'trim');
			$this->form_validation->set_message('required', '%s must not be blank');
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
					$ids=$data['ids'];
					$data_array=array();
					
					if(''!=$this->input->post('csPublish'))
						$data_array['csPublish']=$this->input->post('csPublish');
					
					if(''!=$this->input->post('csRegistrationStartDate'))
						$data_array['csRegistrationStartDate']=format_date($this->input->post('csRegistrationStartDate'),'Y-m-d');		
					
					if(''!=$this->input->post('csRegistrationEndDate'))
						$data_array['csRegistrationEndDate']=format_date($this->input->post('csRegistrationEndDate'),'Y-m-d');
						
					if(''!=$this->input->post('csPaymentStartDate'))
						$data_array['csPaymentStartDate']=format_date($this->input->post('csPaymentStartDate'),'Y-m-d');
					
					if(''!=$this->input->post('csCourseSession'))
						$data_array['csCourseSession']=$this->input->post('csCourseSession');	
					
					if(''!=$this->input->post('cs_start_date'))
						$data_array['csStartDate']=format_date($this->input->post('cs_start_date'),'Y-m-d');
					
					if(''!=$this->input->post('cs_end_date'))
						$data_array['csEndDate']=format_date($this->input->post('cs_end_date'),'Y-m-d');		
					
					
					if( 0 < count($data_array))
						$this->course_schedule_model->update_multiple($ids,$data_array);
					
					//update courses dates 

					$dates_array=array();
					if(''!=$this->input->post('cs_start_date'))
						$dates_array['csdStartDate']=format_date($this->input->post('cs_start_date'),'Y-m-d');
						
					if(''!=$this->input->post('cs_end_date'))
						$dates_array['csdEndDate']=format_date($this->input->post('cs_end_date'),'Y-m-d');	
					
					if( 0 < count($dates_array))	
						$this->course_schedule_model->update_dates($ids,$dates_array);
					
					
					set_flash_message('Courses attributes  has been updated successfully','success');		
					redirect('edu_admin/course_schedule/one_credit');		
					
			}
			
		}
		if(''==$data['ids']){
			redirect('edu_admin/course_schedule/one_credit');
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/course_schedule/course_attributes_form';
		$this->load->vars($data);
		$this->load->view('template');
		
	}
	
	
	/**
		@Function Name:	_export_enrollees
		@Author Name:	ben binesh
		@Date:			Sept, 20 2013
		@Purpose:		export enrollees details
	
	*/
	function _export_courses($byoc = 0)
	{
		$title        = $this->input->get('title'); 
		$course_id            = $this->input->get('course_id'); 
		//if request for byoc
		if($byoc)
		{
			$genre        = $byoc;
			$course_type  = COURSE_ONLINE; 			
		}
		else
		{
			$genre        = $this->input->get('genre');
			$course_type  = ($this->input->get('course_type') != '')?$this->input->get('course_type'):''; 			
		}
		
		$status       = ($this->input->get('status') != '')?$this->input->get('status'):''; 
		
		$start_date   = $this->input->get('start_date'); 
		$end_date     = $this->input->get('end_date'); 
		$address      = $this->input->get('address'); 
		$results     = $this->course_schedule_model->get_records( $title,$course_id,$genre,$start_date ,$end_date,$status, $course_type,$address, 0 ,-1,TRUE );	
				
		
		if(empty($results))
		{
			set_flash_message('No records to export ','error');
			if($byoc)
			{
				redirect('edu_admin/course_schedule/one-credit');
			}
			
			redirect('edu_admin/course_schedule/');
		}
				
		$export_array = array();
		//build columns 
		$export_array[] = array(
			'S.No',
			'ID',
			'Title',
			'Registration Date',
			'Course Date',
			'Registered',
			'Enrolled',
		);
		$i=1;
		
		foreach($results as $result)
		{
			
			$export_array[]=array(
				$i,
				$result->csID,
				$result->cdCourseID.':'.$result->cdCourseTitle,
				format_date($result->csRegistrationStartDate,DATE_FORMAT).'-'.format_date($result->csRegistrationEndDate,DATE_FORMAT),
				format_date($result->csStartDate,DATE_FORMAT).'-'.format_date( $result->csEndDate,DATE_FORMAT),
				$result->registered_count,
				$result->enrollee_count,
				
			);
			$i++;
		}
		
		
		$file_name = 'course-detials-one-credit-'.time();
		$file_name=url_title($file_name,'-',TRUE);
		//check the report type choice ,default is csv 
		if(RPT_PDF == $this->input->get('export_type')){
			//if pdf 
			
			$this->load->library('pdf');
			$this->pdf->load_view('edu_admin/pdf',array(
					'results'=>$export_array
			));
			$this->pdf->render();
			$this->pdf->stream( $file_name.'.pdf');;
			
			
		}	
		else
		{
			//generate csv file 
			$this->load->helper('csv');
			array_to_csv($export_array, $file_name.'.csv');
		}	
	}
// RF3	
	/**
		@Function Name:	final_grades
		@Author Name:	Alan Anil
		@Date:			Nov, 20 2013
		@Purpose:		show final grade of user and save updates done by user.
	
	*/
	public function final_grades($id)
	{
		if(!$id){
			show_404('page');
	    }
		$this->load->model('assignment_model'); 
		$this->load->model('user_model'); 
		$data['main']   = 'edu_admin/course_schedule/final_grades'; 
		$user_course    = explode("_",$id);
		$user_id        = $user_course[0];
		$user_course    = $user_course[1];
		$data['userId'] = $user_id; 
		$data['userCourse'] = $user_course; 
		$data['getUserDetails']        = $this->assignment_model->show_assign_user_details($user_id); 
		$data['getGradeNum']           = $this->assignment_model->get_user_grade($user_id,$user_course);
		$data['user']                  = $this->user_model->get_single_record($user_id,'*',true);
		$data['getUserDetails']        = $this->assignment_model->show_assign_user_details($user_id);
		$data['getUserPointsDetails']  = $this->assignment_model->get_total_user_assign($user_id); 
		// get course details.
		$data['course']      = $this->course_schedule_model->get_course_detail($user_course,true); 
		$sesssionId          = $this->session->userdata('user_id');
		$getApproved         = '';
		if(count($_POST) > 0 ){ 
			if($this->input->post('save') != '')
			{
				$submitType = "save";
			}
			// check if page post from publish grades.
			if($this->input->post('submitToEduspire') != '')
			{
				$submitType = "submitToEduspire";
			} 
			if($submitType == 'save')  
			{ 
						$gradeArr = array( 'fgComputedGrade'=>$this->input->post('fgCompGrade'),
										   'fgGradeBy' => $sesssionId,
										   'fgGrade' => $this->input->post('fgGrade'),
										   'fgCommentAdmin' => $this->input->post('adminComment'));  
						// update assignmentleadger table.	
						$this->assignment_model->update_grade_entry($user_id, $user_course, $gradeArr); 
			}
			if($submitType == 'submitToEduspire')  
			{ 
						$getApproved = $this->input->post('approved');
						if($getApproved == '') {
							$getApproved = 0;
						}
						
						$gradeArr = array( 'fgComputedGrade'=>$this->input->post('fgCompGrade'),
											'fgCommentAdmin'=>$this->input->post('adminComment'),
										   'fgGrade' => $this->input->post('fgGrade'),
										   'fgGradeBy' => $sesssionId,
										   'fgApproved' => $getApproved,
										   'fgSubmitted' => date("Y-m-d H:i:s")); 
						// update assignmentleadger table.	
						$this->assignment_model->update_grade_entry($user_id, $user_course, $gradeArr);
			} 
			$url = $_SERVER['HTTP_REFERER'];
			redirect($url);
			set_flash_message('Grade Saved successfully','success');
		}
		  
		$this->load->vars($data);
		$this->load->view('template');
	} 
// RF3	End 
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */