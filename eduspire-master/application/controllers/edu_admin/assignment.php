<?php 
/**
@Page/Module Name/Class: 		assignment.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		contain all controller functions assignment
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 //Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  
RF1        Alan Anil		Nov 20 2013        Normal			add fuction for showing users assignment grades.
//---------------------------------------------------------------------------------------- 
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Assignment extends CI_Controller {
	
	public $js;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->load->model('assignment_model');
		$this->load->helper('common');
		$this->load->helper('form');
		
		if(!is_logged_in()) 
		{
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}
		else
		{
			$this->_current_request = 'edu_admin/'.$this->router->class.'/'.$this->router->method ;
			//check the sufficient access level 
			if(!is_allowed($this->_current_request))
			{	
				set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
				redirect('home/error');
			}
		}
		
	}
	
	/**
		@Function Name:	index
		@Author Name:	binesh
		@Date:			Aug, 16 2013
		@Purpose:		show the multiple records and filter 
	
	*/
	public function index()
	{
		$data=array();
		$this->page_title =  "Assignments";
		$data['meta_title'] ='Assignments';
		$data['layout']       = '';
		$data['title']        = $this->input->get('title'); 
		$data['type']         = ($this->input->get('type')  != '' )?$this->input->get('type'):''; 
		$data['course_id']    = $this->input->get('course_id');
		if($data['course_id']){
			$this->load->model('course_schedule_model');
			$data['course'] = $this->course_schedule_model->get_course_detail($data['course_id'],false);
		}
			
		$data['author_id']    = $this->input->get('author_id'); 
		$num_records          = $this->assignment_model->count_records( $data['course_id'],$data['author_id'], $data['type'], $data['title'] );
		$base_url             = base_url().'edu_admin/assignment/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = 30; 
		$data['results']     = $this->assignment_model->get_records($data['course_id'],$data['author_id'], $data['type'], $data['title'], $start , $per_page,0,'assignDueDate ASC, assignDueTime ASC' );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/assignment/index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	create 
		@Author Name:	binesh
		@Date:			Aug, 16 2013
		@Purpose:		insert the new record ,validate recored
	
	*/
	function create(){
		
		$error = false;
		$errors = array();
        $this->page_title='Create Assignment';
		$data['meta_title'] ='Create Assignment' ;
		$this->load->helper('form');
		$this->js[] = 'js/tinymce/tinymce.min.js';
		$this->js[]='js/jquery-ui.js';
		$this->css[]='css/jquery-ui.css';
		$this->load->model('course_schedule_model');
		$data['courses'] = $this->course_schedule_model->get_courses(0,'',0,0,-1,NULL,STATUS_PUBLISH,TRUE);
		
		if(count($_POST)>0){
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('assignType', 'Assignment Type', 'trim|required');
			$this->form_validation->set_rules('assignCnfID', 'Assignment Course', 'trim|required');
			$this->form_validation->set_rules('assignTitle', 'Assignment Title', 'trim|required');
			$this->form_validation->set_rules('assignPoints', 'Point Value', 'trim|required|numeric');
			$this->form_validation->set_rules('assignDeductLatePoints', 'Deducted Point', 'trim|numeric');
			if(ASGN_QUESTIONNAIRE == $this->input->post('assignType')){
				$this->form_validation->set_rules('assignQuestionnaire', 'Questionnaire', 'trim|required');
			}
			$this->form_validation->set_message('required', '%s must not be blank');
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				
				$start_hour   = '';
				$start_time   = '';
				
				$end_hour   = '';
				$end_time   = '';
				
				if('pm' == $this->input->post('activationAP')){
					
					$start_hour = (12 != $this->input->post('activationTime'))?$this->input->post('activationTime')+12:'00';
				}else{
					$start_hour = $this->input->post('activationTime');
				}
				$start_time=$start_hour.':'.$this->input->post('activationMin').':00';
					
				
				if('pm' == $this->input->post('dueAP')){
					$end_hour = (12 != $this->input->post('dueTime'))?$this->input->post('dueTime')+12:'00';
				}else{
					$end_hour = $this->input->post('dueTime');
				}
				$end_time=$end_hour.':'.$this->input->post('dueMin').':00';
				
				$data_array = array(
								'assignType'=>$this->input->post('assignType'),
				                'assignAuthor' => $this->session->userdata('user_id'),
								'assignCnfID' =>$this->input->post('assignCnfID'),
								'assignQuestionnaire' =>$this->input->post('assignQuestionnaire'),
								'creationDate' =>date("Y-m-d H:i:s"), 
								'assignTitle' => $this->input->post('assignTitle'),
								'assignTopic' => $this->input->post('assignTopic'),
								'assignActiveDate' =>format_date($this->input->post('assignActiveDate'),'Y-m-d'),
								'assignActiveTime' => $start_time, 
								'assignDueDate' => format_date($this->input->post('assignDueDate'),'Y-m-d'), 
								'assignDueTime' => $end_time,
								'assignPoints' => $this->input->post('assignPoints') ,
								'assignDeductLatePoints' => $this->input->post('assignDeductLatePoints'), 
								'assignModifiedDate' => date('Y-m-d'), 
								'assignLinkName' => $this->input->post('assignLinkName'), 
								'assignLinkUrl' => $this->input->post('assignLinkUrl') ,
								
								);  

				$this->assignment_model->insertAssignments($data_array); 
				set_flash_message('Assignment is added successfully','success');
				if($this->input->get('assignCnfID')){
				redirect('edu_admin/assignment/index?course_id='.$this->input->get('assignCnfID'));
				}
				redirect('edu_admin/assignment/index');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/assignment/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	update 
		@Author Name:	binesh
		@Date:			Aug, 16 2013
		@Purpose:		validate and update the record
	
	*/
	function update($id=0){
		$data=array();
		$error=false;
		$errors=array();
		
		$data['result']=$this->_load_data($id);
        $this->page_title = 'Update '. $data['result']->assignTitle;
		$data['meta_title'] ='Update Assignment';
		$this->load->model('course_schedule_model');
		$data['courses'] = $this->course_schedule_model->get_courses(0,'',0,0,-1,NULL,STATUS_PUBLISH,true);
		$this->js[] = 'js/tinymce/tinymce.min.js';
		$this->js[]='js/jquery-ui.js';
		$this->css[]='css/jquery-ui.css';
		
		if(count($_POST)>0){
			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('assignType', 'Assignment Type', 'trim|required');
			$this->form_validation->set_rules('assignCnfID', 'Assignment Course', 'trim|required');
			$this->form_validation->set_rules('assignTitle', 'Assignment Title', 'trim|required');
			if(ASGN_QUESTIONNAIRE == $this->input->post('assignType')){
				$this->form_validation->set_rules('assignQuestionnaire', 'Questionnaire', 'trim|required');
			}
			$this->form_validation->set_message('required', '%s must not be blank');
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				
				$start_hour   = '';
				$start_time   = '';
				
				$end_hour   = '';
				$end_time   = '';
				
				if('pm' == $this->input->post('activationAP')){
					
					$start_hour = (12 != $this->input->post('activationTime'))?$this->input->post('activationTime')+12:'00';
				}else{
					$start_hour = $this->input->post('activationTime');
				}
				$start_time=$start_hour.':'.$this->input->post('activationMin').':00';
					
				
				if('pm' == $this->input->post('dueAP')){
					$end_hour = (12 != $this->input->post('dueTime'))?$this->input->post('dueTime')+12:'00';
				}else{
					$end_hour = $this->input->post('dueTime');
				}
				$end_time=$end_hour.':'.$this->input->post('dueMin').':00';
				
				$data_array = array(
								'assignType'=>$this->input->post('assignType'),
				                'assignAuthor' => $this->session->userdata('user_id'),
								'assignCnfID' =>$this->input->post('assignCnfID'),
								'assignQuestionnaire' =>$this->input->post('assignQuestionnaire'),
								'assignTitle' => $this->input->post('assignTitle'),
								'assignTopic' => $this->input->post('assignTopic'),
								'assignActiveDate' =>format_date($this->input->post('assignActiveDate'),'Y-m-d'),
								'assignActiveTime' => $start_time, 
								'assignDueDate' => format_date($this->input->post('assignDueDate'),'Y-m-d'), 
								'assignDueTime' => $end_time,
								'assignPoints' => $this->input->post('assignPoints') ,
								'assignDeductLatePoints' => $this->input->post('assignDeductLatePoints'), 
								'assignModifiedDate' => date('Y-m-d'), 
								'assignLinkName' => $this->input->post('assignLinkName'), 
								'assignLinkUrl' => $this->input->post('assignLinkUrl') ,
								);  

				$this->assignment_model->update_assignment($id,$data_array); 
				set_flash_message('Assignment is added successfully','success');
				if($this->input->get('assignCnfID')){
				redirect('edu_admin/assignment/index?course_id='.$this->input->get('assignCnfID'));
				}
				redirect('edu_admin/assignment/index');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/assignment/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	delete 
		@Author Name:	binesh
		@Date:			Aug, 16 2013
		@Purpose:		validate and delete the record 
	
	*/
	function  delete($id=0){
		$this->_load_data($id);
		$is_delete = true;
		
		if($count = $this->db->where('alAssignID',$id)->count_all_results('assignment_ledger')){
			$is_delete = false;
			set_flash_message('Assignment details cannot be deleted as their are user associated with it   ','error');
		}
		if($is_delete){
			$this->assignment_model->deleteAssignments($id);	
			set_flash_message('Assignment details has been successfully deleted ','success');
		}
		if($this->input->get('assignCnfID')){
		redirect('edu_admin/assignment/index?course_id='.$this->input->get('assignCnfID'));
		}
		redirect('edu_admin/assignment/index');
	}
	/**
		@Function Name:	_load_date
		@Author Name:	binesh
		@Date:			Aug, 16 2013
		@Purpose:		load the single record  
	
	*/
	function _load_data($id=0){
		if(!$id){
			show_404('page');
		}
		$data = $this->assignment_model->get_single_assignment($id);
		if(empty($data)){
			show_404('page');
		}else{
			return $data;
		}
		
		
	}
	
	/**
		@Function Name:	duplicate_check
		@Author Name:	binesh
		@Date:			Aug, 16 2013
		@Purpose:		check the duplicate record in data base with same title  
	
	*/
	
	public function duplicate_check($title='')
	{	$id=0;
		if ($this->news_model->check_duplicate($id,$title))
		{	$this->form_validation->set_message('duplicate_check', 'The  "'.$title.'"  is already created');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
//RF1	
	/**
		@Function Name:	grades
		@Author Name:	Alan Anil
		@Date:			Nov, 20 2013
		@Purpose:		show user grades.
	
	*/
  public function grades($assignId = 0)
  {
		if(!$assignId){
			show_404('page');
		}
		$this->load->model('user_model');
		$data['title']='Assignment Grade';
		// get user list for current active course.
		$data['assignId']       = $assignId;
		$data['userList']       = $this->assignment_model->get_assign_users($assignId);  
		$data['main'] = 'edu_admin/assignment/grades';
		 
	 if(count($_POST)>0)
	 { 
		$this->load->model('course_schedule_model'); 
		$getAll = $this->input->post();  
		$checkCounter = 0; 
		$submitType = '';
		$userId              = $this->session->userdata('user_id'); 
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
						$this->assignment_model->update_assignment_grade($assignUserId, $assignIdVal, $gradeArr);
					 
					 
				}
				if($assignFieldName == 'studentComment') { 
					 
							$commentArr = array( 'alCommentStudent'=>"$postVal");
							// update assignmentleadger table.  
							$this->assignment_model->update_assignment_grade($assignUserId, $assignIdVal, $commentArr);
					 
				}
			}
			if($submitType == 'submitToEduspire')  
			{ 
				if($assignFieldName == 'pointGot') { 
					 
						$gradeArr = array( 'alGrade'=>$postVal,
										   'alGradeBy' => $userId,
										   'alDateSubmitted' => date("Y-m-d H:i:s")); 
						// update assignmentleadger table also add submit date.				 
						$this->assignment_model->update_assignment_grade($assignUserId, $assignIdVal, $gradeArr);
					 
				}
				if($assignFieldName == 'studentComment') { 
					 
							$commentArr = array('alCommentStudent'=>"$postVal",
										        'alDateSubmitted' => date("Y-m-d H:i:s"));  
							// update assignmentleadger table also add submit date.					
							$this->assignment_model->update_assignment_grade($assignUserId, $assignIdVal, $commentArr);
					 
				} 
			} 	
			$checkCounter++;
		}
		if($submitType == 'submitToEduspire') 
			set_flash_message('Grades have been saved and published.','success');
		else 
			set_flash_message('Grade Saved successfully','success');  
      } 
		$this->load->vars($data);
		$this->load->view('template');  
  }
// RF1 End	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */