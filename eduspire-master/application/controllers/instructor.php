<?php
// ********************************************************************************************************************************
//Page name			:- 			instructor.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			File used for instructor profile.  
//Date				:- 			05-09-2013
//Table Refered		:-  		N/A
//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  
	
//---------------------------------------------------------------------------------------- 
 
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Instructor extends CI_Controller {
	
	public $js;
	protected $_id;
	public function __construct() 
	{
		parent::__construct(); 
                use_ssl(FALSE);
		$js=array();
		$this->load->helper('common');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('user_model');
		$this->load->model('assignment_model');  
		if(!is_logged_in()){
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		} 
		$this->_id=$this->session->userdata('user_id');
		
	}
	
	/**
		@Function Name:	index
		@Author Name:	Alan anil	
		@Date:			Oct, 09 2013 
		@Purpose:		Default function call when intructor calls
	
	*/ 
	public function index()
	{ 
		$data = array();
		$data['main'] = 'instructor/dashboard';
		$data['layout']='two-column-right';
		/**
			meta information
		*/ 
		$data['sidebar']         = 'user';
		$data['meta_title']      ='Instructor Dashboard'	;	
		$data['meta_descrption'] ='Instructor Dashboard'	;
		$this->js[]              ='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]             ='js/fancybox/source/jquery.fancybox.css';
		$this->js[]              ='js/jquery-ui.js';
		$this->load->model('news_model');
		$data['archives']        = $this->news_model->get_records( '',STATUS_PUBLISH, 0 ,2,'nwID,nwTitle,nwDate,nwDescription');
		$data['user']            = $this->user_model->get_single_record($this->_id,'*',true);
		if(empty($data['user'])){
			redirect('home/error_404');
		} 
		//get the current course  
		$data['course_id']   = $data['user']->membershipLastUsed;
		$this->load->model('course_schedule_model');
		$data['course']      = $this->course_schedule_model->get_course_detail($data['course_id'],true);
		//get the courses 
		$data['courses']     = $this->course_schedule_model->get_courses(0,'',0,0,-1,NULL,STATUS_PUBLISH,TRUE,$this->_id); 
		$userName            = $this->session->userdata('user_name');  
		$displayName         = $this->session->userdata('display_name');
		$user_id             = $this->session->userdata('user_id'); 
		$data['userName']    = $userName;
		$data['displayName'] = $displayName; 
		$data['userId']      = $user_id;
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	assignments
		@Author Name:	Alan anil	
		@Date:			Oct, 09 2013 
		@Purpose:		assignments function to show all assignments
	
	*/  
	public function assignments($course_id=0)
	{
		//check the sufficient access level 
		$this->_current_request = $this->router->class.'/assignments' ; 
		if(!is_allowed($this->_current_request))
		{		
			set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
			redirect('home/error');
		}
		else
		{ 
			if(!$course_id){
				show_404('page');
		    } 
			$error = false;
			$errors = array();
			$this->load->helper('form');
			$this->js[]='js/jquery-ui.js';
			$this->css[]='css/jquery-ui.css';
			$this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
       		$this->css[]='js/fancybox/source/jquery.fancybox.css'; 
			$this->load->model('assignment_model');
			// To show list of assignments.
			$data['main']        = 'instructor/assignments'; 
			$userId              = $this->session->userdata('user_id'); 
			if($course_id){
					$getLstMember=$course_id;
			}else{
					$getLstMember   = $this->assignment_model->getUserCourse($userId); 
			}
			$data['results']     = $this->assignment_model->geUserAllAssignments( $userId, $getLstMember , $start=0 , $per_page=0 );
			$data['activeCourse']= $getLstMember;
			$this->load->model('course_schedule_model');
			$data['course']      = $this->course_schedule_model->get_course_detail($data['activeCourse'],true);
			 
			// When new assignment is added. 
			if(count($_POST)>0){
				$this->load->library('form_validation');
				$this->form_validation->set_rules('assignmentTitle', 'Assignment Title', 'trim|required');
				$this->form_validation->set_rules('pointValue', 'Point Value ', 'trim|required|is_natural|numeric'); 
				$this->form_validation->set_rules('assignLinkName', 'Name of link ', 'trim'); 
				$this->form_validation->set_rules('assignLinkUrl', 'URL', 'trim');  
				
				if ($this->form_validation->run() == TRUE && $error==false  )
				{ 
					$this->load->model('assignment_model');
					$userId         = $this->session->userdata('user_id');
					//get course id present in the url  
					$getLstMember   = $getLstMember;  
					$actMin = $this->input->post('activationMin');
					if($actMin == '0')
					{
						$actMin = '00';  ;
					} 
					$activationT    = $this->input->post('activationTime').':'.$actMin.' '.$this->input->post('activationAP'); 
					if($this->input->post('activationDate') == ' ')
					{ 
						$finalActDate   = 'NULL' ; 
						$finalAppTime   =  'NULL'; 
					}
					else
					{
						if($this->input->post('activationAP') == '0' || $this->input->post('activationTime') == '0')
						{ 
							$finalAppTime   =  'NULL'; 
						}
						else
						{
							$appliedDate    = $this->input->post('activationDate').' '.$activationT; 
							$finalAppTime   = date("H:i:s", strtotime($appliedDate));
						}
						$activationDate = $this->input->post('activationDate');
						$finalActDate   = date("Y-m-d", strtotime($activationDate)); 
					}
					
					$dueMin = $this->input->post('dueMin');
					if($dueMin == '0')
					{
						$dueMin = '00';  ;
					}
					$dueT           = $this->input->post('dueTime').':'.$dueMin.' '.$this->input->post('dueAP');
					if($this->input->post('dueDate') == ' ')
					{ 
						$finalDueDate   = 'NULL' ; 
						$finalDueTime   =  'NULL'; 
					}
					else
					{
						if($this->input->post('dueAP') == '0' || $this->input->post('dueTime') == '0')
						{ 
							$finalDueTime   =  'NULL'; 
						}
						else
						{
							$dueDate        = $this->input->post('dueDate').' '.$dueT; 
							$finalDueTime   = date("H:i:s", strtotime($dueDate));
						}
						$dueDate        = $this->input->post('dueDate');
						$finalDueDate   = date("Y-m-d", strtotime($dueDate));
					} 
					// make insert data array.
					$data_array = array(
									'assignType'=>ASGN_TYPE_COURSE,
									'assignAuthor' => $userId ,
									'assignCnfID' =>$getLstMember,
									'creationDate' =>date("Y-m-d H:i:s"), 
									'assignTitle' => $this->input->post('assignmentTitle'),
									'assignActiveDate' => $finalActDate,
									'assignActiveTime' => $finalAppTime, 
									'assignDueDate' => $finalDueDate, 
									'assignDueTime' => $finalDueTime,
									'assignPoints' => $this->input->post('pointValue'), 
									'assignTopic' => $this->input->post('assignTopic'),
									'assignModifiedDate' => date("Y-m-d"),
									'assignLinkName' => $this->input->post('assignLinkName'), 
									'assignLinkUrl' => $this->input->post('assignLinkUrl') 
									);   
					$this->assignment_model->insertAssignments($data_array); 
					set_flash_message('Assignment is added successfully','success'); 
					redirect('instructor'); 
				} 
			}
			$data['errors'] = $errors;
			$this->load->vars($data);
			$this->load->view('template'); 
		}
	}
	/**
		@Function Name:	addassignments
		@Author Name:	Alan anil	
		@Date:			Oct, 09 2013 
		@Purpose:		Funtion for adding new assignments
	
	*/ 
	public function addassignments($course_id=0)
	{ 
		if(!$course_id){
				show_404('page');
		}
		$error = false;
		$errors = array();
		$this->load->helper('form');
		$this->js[]='js/jquery-ui.js';
		$this->css[]='css/jquery-ui.css';
		
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('assignmentTitle', 'Assignment Title', 'trim|required');
			$this->form_validation->set_rules('pointValue', 'Point Value ', 'trim|required|is_natural|numeric');  
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            { 
				$this->load->model('assignment_model');
				$userId         = $this->session->userdata('user_id');
				//get course id present in the url 
				if($course_id){
					$getLstMember=$course_id;
				}else{
					$getLstMember   = $this->assignment_model->getUserCourse($userId); 
				}
				
				$actMin = $this->input->post('activationMin');
				if($actMin == '0')
				{
					$actMin = '00';  ;
				}
				
		        $activationT    = $this->input->post('activationTime').':'.$actMin.' '.$this->input->post('activationAP'); 
				if($this->input->post('activationDate') == ' ')
				{ 
					$finalActDate   = 'NULL' ; 
					$finalAppTime   =  'NULL'; 
				}
				else
				{
					if($this->input->post('activationAP') == '0' || $this->input->post('activationTime') == '0')
					{ 
						$finalAppTime   =  'NULL'; 
					}
					else
					{
						$appliedDate    = $this->input->post('activationDate').' '.$activationT; 
						$finalAppTime   = date("H:i:s", strtotime($appliedDate));
					}
					$activationDate = $this->input->post('activationDate');
					$finalActDate   = date("Y-m-d", strtotime($activationDate)); 
				}
				
				$dueMin = $this->input->post('dueMin');
				if($dueMin == '0')
				{
					$dueMin = '00';  ;
				}
				$dueT           = $this->input->post('dueTime').':'.$dueMin.' '.$this->input->post('dueAP');
				if($this->input->post('dueDate') == ' ')
				{ 
					$finalDueDate   = 'NULL' ; 
					$finalDueTime   =  'NULL'; 
				}
				else
				{
					if($this->input->post('dueAP') == '0' || $this->input->post('dueTime') == '0')
					{ 
						$finalDueTime   =  'NULL'; 
					}
					else
					{
						$dueDate        = $this->input->post('dueDate').' '.$dueT; 
						$finalDueTime   = date("H:i:s", strtotime($dueDate));
					}
					$dueDate        = $this->input->post('dueDate');
					$finalDueDate   = date("Y-m-d", strtotime($dueDate));
				} 
				//make data array for insert.
				$data_array = array(
								'assignType'=>ASGN_TYPE_COURSE,
				                'assignAuthor' => $userId ,
								'assignCnfID' =>$getLstMember,
								'creationDate' =>date("Y-m-d H:i:s"), 
								'assignTitle' => $this->input->post('assignmentTitle'),
								'assignActiveDate' => $finalActDate,
								'assignActiveTime' => $finalAppTime, 
								'assignDueDate' => $finalDueDate, 
								'assignDueTime' => $finalDueTime,
								'assignPoints' => $this->input->post('pointValue') 
								);   
				$this->assignment_model->insertAssignments($data_array); 
				set_flash_message('Assignment is added successfully','success');
				if($redirect=$this->input->get_post('redirect')){
					redirect($redirect);
				}
				redirect('instructor'); 
			} 
		}
		$data['errors'] = $errors;
		$data['main'] = 'instructor/assignments';
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	edit
		@Author Name:	Alan anil	
		@Date:			Oct, 09 2013 
		@Purpose:		For edit an assignment
	
	*/  
	public function edit($getAssignId=0)
	{
		$this->_current_request = $this->router->class.'/edit' ; 
		if(!is_allowed($this->_current_request))
		{		
			set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
			redirect('home/error');
		}
		else
		{ 
			if(!$getAssignId){
				show_404('page');
		    } 
			$error               = false;
			$errors              = array();
			$this->load->helper('form'); 
			$this->css[]         ='css/jquery-ui.css';
			$this->js[]          ='js/fancybox/source/jquery.fancybox.pack.js';
			$this->css[]         ='js/fancybox/source/jquery.fancybox.css';
			$this->js[]          ='js/jquery-ui.js';
			$this->load->model('assignment_model');
			$this->load->model('course_schedule_model');
			$data['main']        = 'instructor/assignments'; 
			$userId              = $this->session->userdata('user_id');
			$data['assignData']  = $this->assignment_model->getAssignDetails($getAssignId); 
			$getLstMember        = $this->assignment_model->get_course_from_assignid($getAssignId); 
			
			/*Section for adding users to a new assignment.*/
			$getAllCourseUsers   = $this->assignment_model->get_all_course_users_id($getLstMember); 
			$innerData['alCnfID']      = $data['assignData'][0]->assignCnfID;
			$innerData['alAssignID']   = $data['assignData'][0]->assignID;
			$innerData['alAssignType'] = '10'; 
			$innerData['alUserID']     = '';
			foreach($getAllCourseUsers as $getAllCourseUsersVal) {
				$innerData['alUserID'] = $getAllCourseUsersVal->id;	
				$getUserStatus = $this->assignment_model->find_user_entry_al($getAllCourseUsersVal->id, 
				$data['assignData'][0]->assignCnfID, $data['assignData'][0]->assignID);
				 	
				if(empty($getUserStatus)) {
					$this->assignment_model->insert_assignments_ledger($innerData);
					 
				}
			}  
			/*Section for adding users to a new assignment end .*/
			
			$data['results']     = $this->assignment_model->geUserAllAssignments( $userId, $getLstMember , $start=0 , $per_page=0 );
			// get current active course.
			$data['activeCourse']= $getLstMember;
			// get user course details.
			$data['course']      = $this->course_schedule_model->get_course_detail($data['activeCourse'],true);  
			if(isset($getAssignId) && $getAssignId != '') {
			// get user list for current active course.
			 $data['userList']       = $this->assignment_model->get_assign_users($getAssignId);  
			} 
			if(count($_POST)>0){
				$this->load->library('form_validation');
				$this->form_validation->set_rules('assignmentTitle', 'Assignment Title', 'trim|required');
				$this->form_validation->set_rules('pointValue', 'Point Value ', 'trim|required|is_natural|numeric');  
				$this->form_validation->set_rules('assignTopic', 'Description ', 'trim');  
				
				if ($this->form_validation->run() == TRUE && $error==false  )
				{ 
					$this->load->model('assignment_model');
					$userId         = $this->session->userdata('user_id');
					$actMin = $this->input->post('activationMin');
					if($actMin == '0')
					{
						$actMin = '00';  ;
					}
					
					$activationT    = $this->input->post('activationTime').':'.$actMin.' '.$this->input->post('activationAP'); 
					if($this->input->post('activationDate') == ' ')
					{ 
						$finalActDate   = 'NULL' ; 
						$finalAppTime   =  'NULL'; 
					}
					else
					{
						if($this->input->post('activationAP') == '0' || $this->input->post('activationTime') == '0')
						{ 
							$finalAppTime   =  'NULL'; 
						}
						else
						{
							$appliedDate    = $this->input->post('activationDate').' '.$activationT; 
							$finalAppTime   = date("H:i:s", strtotime($appliedDate));
						}
						$activationDate = $this->input->post('activationDate');
						$finalActDate   = date("Y-m-d", strtotime($activationDate)); 
					}
					
					$dueMin = $this->input->post('dueMin');
					if($dueMin == '0')
					{
						$dueMin = '00';  ;
					}
					$dueT           = $this->input->post('dueTime').':'.$dueMin.' '.$this->input->post('dueAP');
					if($this->input->post('dueDate') == ' ')
					{ 
						$finalDueDate   = 'NULL' ; 
						$finalDueTime   =  'NULL'; 
					}
					else
					{
						if($this->input->post('dueAP') == '0' || $this->input->post('dueTime') == '0')
						{ 
							$finalDueTime   =  'NULL'; 
						}
						else
						{
							$dueDate        = $this->input->post('dueDate').' '.$dueT; 
							$finalDueTime   = date("H:i:s", strtotime($dueDate));
						}
						$dueDate        = $this->input->post('dueDate');
						$finalDueDate   = date("Y-m-d", strtotime($dueDate));
					} 
					$assignId =  $this->input->post('assignId');
					//make data array for updation.
					$data_array = array(  
									'assignType'=>ASGN_TYPE_COURSE,
									'assignTitle' => $this->input->post('assignmentTitle'),
									'assignActiveDate' => $finalActDate,
									'assignActiveTime' => $finalAppTime, 
									'assignDueDate' => $finalDueDate, 
									'assignDueTime' => $finalDueTime,
									'assignPoints' => $this->input->post('pointValue'), 
									'assignTopic' => $this->input->post('assignTopic'),
									'assignModifiedDate' => date("Y-m-d"),
									'assignLinkName' => $this->input->post('assignLinkName'), 
									'assignLinkUrl' => $this->input->post('assignLinkUrl') 
									);   			 
					$this->assignment_model->updateAssignments($assignId, $data_array); 
					set_flash_message('Assignment is Updated successfully','success');
					redirect('instructor'); 
				} 
			}
			 
			$this->load->vars($data);
			$this->load->view('template');
		}
	}
	/**
		@Function Name:	delete
		@Author Name:	Alan anil	
		@Date:			Oct, 09 2013 
		@Purpose:		For deleting an assignment
	
	*/  
	public function delete($id=0)
	{ 
		$this->_current_request = $this->router->class.'/delete' ; 
		if(!is_allowed($this->_current_request))
		{		
			set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
			redirect('home/error');
		}
		else
		{
			if(!$id){
				show_404('page');
		    }
			$this->load->model('assignment_model'); 
			if($id != '' && $id != 0) {
			// get assignment id and delete assignment.
				$this->assignment_model->deleteAssignments($id); 
				set_flash_message('Assignment Deleted successfully','success');
			}
			redirect('instructor'); 
		}	
	}
	/**
		@Function Name:	assignmentsManualGrades
		@Author Name:	Alan anil	
		@Date:			Sep, 03 2013 
		@Purpose:		For addding manual grades for assigments
	
	*/ 
	public function assignmentsManualGrades($id=0)
	{ 
		if(!$id){
				show_404('page');
		}
		$error = false;
		$errors = array();
		$this->load->helper('form');
		$this->load->model('assignment_model'); 
	 	// get no of records from database.
	    $num_records             = $this->assignment_model->show_graded($id);
		$data['courseDetails']   = $this->assignment_model->getAssignDetails($id);
		// Make baseurl for pagination.
		$base_url                = base_url().'instructor/assignmentsManualGrades';
		$start                   = $this->uri->segment($this->uri->total_segments());
		// set page limit.
		$per_page                = '10'; 
		$data['results']         = $this->assignment_model->manual_grade_assignments($id, $start , $per_page );
		$data['pagination_links']= paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());
 		$userId              = $this->session->userdata('user_id');
		$getLstMember        = $this->assignment_model->getUserCourse($userId);
		$data['activeCourse']= $getLstMember;
		$this->load->model('course_schedule_model');
		// get course details.
		$data['course']      = $this->course_schedule_model->get_course_detail($data['activeCourse'],true);  
		// If page post. 
		if(count($_POST)>0){   
			$this->load->model('assignment_model');
			$getAll = $this->input->post(); 
			$checkCounter = 0; 
			foreach($getAll as $postVal)
			{
				$arrIndex     = array_keys($getAll);
				$userIndex    = $arrIndex[$checkCounter];
				$getUserId    =  explode('_',$userIndex);
				$assignUserId = $getUserId[1];  
				if($postVal != '' && $assignUserId != '') {
					$data_array = array( 'alGrade'=>$postVal);  
					$this->assignment_model->update_assignment_grade($assignUserId,$id, $data_array);	
				}		
				$checkCounter++;
			} 
			set_flash_message('Grade Saved successfully','success');
			redirect('instructor/assignments');
		}
		 
		$data['errors'] = $errors;
		$data['main'] = 'instructor/assignmentsManualGrades';
		$this->load->vars($data);
		$this->load->view('template');
	} 
	/**
		@Function Name:	gradeentry
		@Author Name:	Alan anil	
		@Date:			Sep, 03 2013 
		@Purpose:		For entring grades in assignments
	
	*/ 
	public function gradeentry($course_id=0)
	{
		$this->_current_request = $this->router->class.'/gradeentry' ; 
		if(!is_allowed($this->_current_request))
		{		
			set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
			redirect('home/error');
		}
		else
		{
			if(!$course_id){
				show_404('page');
		    }
			$this->load->model('assignment_model');
			$this->load->model('email_template_model');
			$this->load->model('course_schedule_model');
			$this->js[]          ='js/fancybox/source/jquery.fancybox.pack.js';
		    $this->css[]         ='js/fancybox/source/jquery.fancybox.css';
	 	    $this->js[]          ='js/jquery-ui.js';
			$data['main']        = 'instructor/finalgradeentry'; 
			$this->load->model('page_model');
			$data['content']     = $this->page_model->get_single_record($page_id=13);
			/* User grade entry check and do it if not */  
			$innerFinalGradeData = array(); 
			$getAllCourseUsers   = $this->assignment_model->get_all_course_users_id($course_id);
			
			foreach($getAllCourseUsers as $getAllCourseUsersVal) { 
				$getUserStatus = $this->assignment_model->find_user_entry_fg($getAllCourseUsersVal->id, $course_id); 
				if(empty($getUserStatus)) {
					$innerFinalGradeData['fgCnfID']     = $course_id;
					$innerFinalGradeData['fgUserID']    = $getAllCourseUsersVal->id;
					$this->assignment_model->insert_final_grades_entry($innerFinalGradeData);
					
					 
				}
			}  
			  
			/* User grade entry check and do it if not  end */
			/*
				meta info
			*/
			if(!empty($data['content'])){
				$data['meta_title']=$data['content']->cpMetaTitle;
				$data['meta_descrption']=$data['content']->cpMetaDescription;
			}
			
			$userId              = $this->session->userdata('user_id');
			if($course_id){
				$getLstMember = $course_id;
			}else{
			// get user current active course.
				$getLstMember   = $this->assignment_model->getUserCourse($userId); 
			}
			$assignmentList      = $this->assignment_model->geUserAllAssignments($userId, $getLstMember , 0 , 0);
			$data['activeCourse']= $getLstMember;
			$this->load->model('course_schedule_model');
			// get course details.
			$data['course']      = $this->course_schedule_model->get_course_detail($data['activeCourse'],true);
			// get list of assignment for this course.
			foreach($assignmentList as $assignmentLists):   
			  $getAssignId[] = $assignmentLists->assignID; 
			  endforeach; 
			if(isset($getAssignId) && $getAssignId != '') {
				$getAssignString = implode(",", $getAssignId);
				}
			// get no of users for current assignments.
			if(isset($getAssignString) && $getAssignString != '') {
			$data['results']       = $this->assignment_model->get_assign_users($getAssignString);  
			}
			// if page post. 
			if(count($_POST)>0){   
				$this->load->model('assignment_model');
				$getAll = $this->input->post(); 
				$checkCounter = 0; 
				$submitType = '';
				$userId              = $this->session->userdata('user_id');
				$getLstMember        = $getLstMember;
				// check if page post from save button.
				if($this->input->post('save') != '')
				{
					$submitType = "save";
				}
				// check if page post from publish grades.
				if($this->input->post('submitToEduspire') != '')
				{
					$submitType = "submitToEduspire";
				} 
				foreach($getAll as $postVal)
				{ 
					$assignUserId = ''; 
					$assignFieldName      = '';
					$arrIndex     = array_keys($getAll);
					$userIndex    = $arrIndex[$checkCounter];  
					$getUserId    =  explode('_',$userIndex);
					 if(isset($getUserId[1]) && $getUserId[1] != '') {
						$assignUserId    = $getUserId[1]; 
						$assignFieldName = $getUserId[0]; 
					 } 
					$postVal = trim($postVal); 
					if($submitType == 'save')  
					{
						if($assignFieldName == 'fgCompGrade') { 
							 
								$gradeArr = array( 'fgComputedGrade'=>$postVal,
												   'fgGradeBy' => $userId);  
								// update assignmentleadger table.	
								$this->assignment_model->update_grade_entry($assignUserId, $getLstMember, $gradeArr);
							 
						}
						if($assignFieldName == 'adminComment') { 
							 
									$commentArr = array( 'fgCommentAdmin'=>$postVal,
														 'fgGradeBy' => $userId);  
									// update assignmentleadger table.					 
									$this->assignment_model->update_grade_entry($assignUserId, $getLstMember, $commentArr);
							 
						}
						if($assignFieldName == 'fgGrade') { 
							 
									$fgGradeArr = array( 'fgGrade'=>$postVal );  
									// update assignmentleadger table.					 
									$this->assignment_model->update_grade_entry($assignUserId, $getLstMember, $fgGradeArr);
							 
						}
					}
					if($submitType == 'submitToEduspire')  
					{
						if($assignFieldName == 'fgCompGrade') { 
							 
								$gradeArr = array( 'fgComputedGrade'=>$postVal,
												   'fgGradeBy' => $userId,
												   'fgApproved' => 1,
												   'fgSubmitted' => date("Y-m-d H:i:s"));  
								// update assignmentleadger table.	
								$this->assignment_model->update_grade_entry($assignUserId, $getLstMember, $gradeArr);
							 
						}
						if($assignFieldName == 'adminComment') { 
							 
									$commentArr = array( 'fgCommentAdmin'=>$postVal,
														 'fgGradeBy' => $userId,
														 'fgApproved' => 1,
														 'fgSubmitted' => date("Y-m-d H:i:s"));  
									// update assignmentleadger table.					 
									$this->assignment_model->update_grade_entry($assignUserId, $getLstMember, $commentArr);
							 
						}
						if($assignFieldName == 'fgGrade') { 
							 
									$fgGradeArr = array( 'fgGrade'=>$postVal );  
									// update assignmentleadger table.					 
									$this->assignment_model->update_grade_entry($assignUserId, $getLstMember, $fgGradeArr);
							 
						}
					}
							
					$checkCounter++;
				}  
				if($submitType == 'submitToEduspire') 
				{
					$email_template_id  = 18;  //Waiting list 
					$email_template_data = $this->email_template_model->get_single_record($email_template_id);
					$user_message        = $email_template_data->etCopy;
					$subject             = $email_template_data->etSubject;
					$userId              = $this->session->userdata('user_id');
					$getLstMember        = $this->assignment_model->getUserCourse($userId);
					$course_detail       = $this->course_schedule_model->get_course_detail($getLstMember);
					 
					//Replace all constants of email by the dynamic values
					$email_message_replacement = array(
						 
						"[CourseTitle]"=>$course_detail->cdCourseID.': '.$course_detail->cdCourseTitle,
						"[CourseLocation]"=>$course_detail->csLocation,
						"[CourseAddress]"=>$course_detail->csAddress,
						"[CourseCity]"=>$course_detail->csCity,
						"[CourseState]"=>$course_detail->csState,
						"[CourseZIP]"=>$course_detail->csZIP,  
						"[CourseDates]"=>format_date($course_detail->csStartDate,DATE_FORMAT).' - '.
										 format_date($course_detail->csEndDate,DATE_FORMAT),
						"[noOfUsersComplete]"=>$this->input->post('noOfUserSubmitted'),
						"[noOfUsersPending]"=>$this->input->post('totalNoOfUsers')-$this->input->post('noOfUserSubmitted'),  			 
						"[ExportFinalGradesURL]"=>"<a href='".base_url()."edu_admin/course_schedule/enrollees?course_id=$getLstMember&ref=courses'>"."Click Here</a>"				 
					);
					 
					$user_message =    str_replace(array_keys($email_message_replacement),  array_values($email_message_replacement)
										,$user_message);
					$user_confirmation_message    = $user_message;
					// sending email to admins.   
					//send_mail(ADMIN_EMAIL,$subject,$user_confirmation_message); 
					$admin_emails = get_admin_emails();  
					
					send_mail(ADMIN_EMAIL,SITE_NAME,SENDER_EMAIL,$subject,$user_confirmation_message, $admin_emails);
									
					
			   } 
				set_flash_message('Grade Saved successfully','success'); 
			}
			 
			$this->load->vars($data);
			$this->load->view('template');
		}
	}
	/**
		@Function Name:	gradebook
		@Author Name:	Alan anil	
		@Date:			Sep, 09 2013 
		@Purpose:		To show user gradebook
	
	*/  
	public function gradebook()
	{
		$this->js[]          ='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]         ='js/fancybox/source/jquery.fancybox.css';
		$this->js[]          ='js/jquery-ui.js';
		$this->load->model('assignment_model');
		$getAssignId         = ''; 
		$data['main']        = 'instructor/gradebook'; 
		$userId              = $this->session->userdata('user_id');
		$getLstMember        = $this->assignment_model->getUserCourse($userId);
		$assignmentList      = $this->assignment_model->geUserAllAssignments($userId, $getLstMember , 0 , 0);
		$this->load->model('course_schedule_model');
		$data['course']      = $this->course_schedule_model->get_course_detail($getLstMember,true);
		// get assignment list for current course. 
		foreach($assignmentList as $assignmentLists):   
          $getAssignId[] = $assignmentLists->assignID; 
          endforeach; 
        if(isset($getAssignId) && $getAssignId != '') {
        $getAssignString = implode(",", $getAssignId);
		}
		if(isset($getAssignString) && $getAssignString != '') {
        $data['results']       = $this->assignment_model->get_assign_users($getAssignString);  
		} 
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	gradebookdetails
		@Author Name:	Alan anil	
		@Date:			Sep, 09 2013 
		@Purpose:		To show user gradebook details
	
	*/  
	public function gradebookdetails($userId)
	{
		if(!$userId){
				show_404('page');
	    }
		$data = array();
		$this->load->model('assignment_model');
		$this->load->model('course_schedule_model');
		$data['main']    = 'instructor/gradebookdetails';
		$data['layout']  ='two-column-right';
		$this->js[]      ='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]     ='js/fancybox/source/jquery.fancybox.css';
		$this->js[]      ='js/jquery-ui.js';
		/**
			meta information
		*/
		$data['sidebar']              = 'usergradebook';
		$data['meta_title']           = 'User Gradebok'	;	
		$data['meta_descrption']      = 'User Gradebok';
		$data['results']              = $this->assignment_model->get_users_grade_details($userId);  
		$data['assignDetails']        = $this->assignment_model->show_assign_user_details($userId);
		$data['getUserPointsDetails'] = $this->assignment_model->get_total_user_assign($userId);
		$getLstMember                 = $this->assignment_model->getUserCourse($userId);
		$data['course']               = $this->course_schedule_model->get_course_detail($getLstMember,true); 
		
		 
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	gradebookdetails
		@Author Name:	Alan anil	
		@Date:			Sep, 09 2013 
		@Purpose:		Save changes made by instructor in user grades.
	
	*/  
	function saveUsersPoints() 
	{ 
		$this->load->model('assignment_model');
		$this->load->model('email_template_model');
		$this->load->model('course_schedule_model');
		 
		$getAll = $this->input->post(); 
		$checkCounter = 0; 
		$submitType = '';
		$userId              = $this->session->userdata('user_id');
		$getLstMember        = 0;
		$assignIdVal         = $this->input->post('assignIdVal');
		// check if page post from save button.
		if($this->input->post('save') != '')
		{
			$submitType = "save";
		}
		// check if page post from publish grades button.
		if($this->input->post('submitToEduspire') != '')
		{
			$submitType = "submitToEduspire";
		} 
		// check if page post from final grade entry button.
		if($this->input->post('submitAndFinalGradeEntry') != '')
		{
			$submitType = "saveAndFinalGradeEntry";
		}
		// get all posted values. 
		foreach($getAll as $postVal)
		{ 
			$assignUserId = ''; 
			$assignFieldName      = '';
			$arrIndex     = array_keys($getAll);
			$userIndex    = $arrIndex[$checkCounter];  
			$getUserId    =  explode('_',$userIndex);
			 if(isset($getUserId[1]) && $getUserId[1] != '') {
				$assignUserId    = $getUserId[1]; 
				$assignFieldName = $getUserId[0]; 
			 } 
			 $postVal = trim($postVal);
			if($submitType == 'save')  
			{
				if($assignFieldName == 'pointGot') { 
					 
						$gradeArr = array( 'alGrade'=>$postVal,
										   'alGradeBy' => $userId); 
						// update assignmentleadger table.				 
						$this->assignment_model->update_assign_leadger($assignUserId, $assignIdVal, $getLstMember, $gradeArr);
					 
				}
				if($assignFieldName == 'studentComment') { 
					 
							$commentArr = array( 'alCommentStudent'=>"$postVal");
							// update assignmentleadger table.  
							$this->assignment_model->update_assign_leadger($assignUserId, $assignIdVal, $getLstMember, $commentArr);
					 
				}
			}
			if($submitType == 'submitToEduspire')  
			{ 
				if($assignFieldName == 'pointGot') { 
					 
						$gradeArr = array( 'alGrade'=>$postVal,
										   'alGradeBy' => $userId,
										   'alDateSubmitted' => date("Y-m-d H:i:s")); 
						// update assignmentleadger table also add submit date.				 
						$this->assignment_model->update_assign_leadger($assignUserId, $assignIdVal, $getLstMember, $gradeArr);
					 
				}
				if($assignFieldName == 'studentComment') { 
					 
							$commentArr = array('alCommentStudent'=>"$postVal",
										        'alDateSubmitted' => date("Y-m-d H:i:s"));  
							// update assignmentleadger table also add submit date.					
							$this->assignment_model->update_assign_leadger($assignUserId, $assignIdVal, $getLstMember, $commentArr);
					 
				} 
			}
			if($submitType == 'saveAndFinalGradeEntry')  
			{
				if($assignFieldName == 'pointGot') { 
					 
						$gradeArr = array( 'alGrade'=>$postVal,
										   'alGradeBy' => $userId); 
						// update assignmentleadger table.				 
						$this->assignment_model->update_assign_leadger($assignUserId, $assignIdVal, $getLstMember, $gradeArr);
					 
				}
				if($assignFieldName == 'studentComment') { 
					 
							$commentArr = array( 'alCommentStudent'=>"$postVal");  
							// update assignmentleadger table.
							$this->assignment_model->update_assign_leadger($assignUserId, $assignIdVal, $getLstMember, $commentArr);
					 
				}
			}		
			$checkCounter++;
		}
		set_flash_message('Grade Saved successfully','success');
		if($submitType == "saveAndFinalGradeEntry") {
			redirect('instructor');
		}
		else {
			redirect('instructor');
		} 
	} 
}

/* End of file instructor.php */
/* Location: ./application/controllers/instructor.php */