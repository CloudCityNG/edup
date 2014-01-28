<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Instructor extends CI_Controller {
	
	public $js;
	protected $_id;
	public function __construct() 
	{
		parent::__construct(); 
		$js=array();
		$this->load->helper('common');
		$this->load->helper('form');
		$this->load->model('user_model');
		
		if(!is_logged_in()) {
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}else{
			//check the sufficient access level 
			if(INSTRUCTOR != $this->session->userdata('access_level')){
				set_flash_message('You don\'t have sufficitent permission to access this page  ','warning');
				redirect('home/error');
			}
		}
		$this->_id=$this->session->userdata('user_id');
		
	}
	
	
	public function index()
	{
		$data = array();
		$data['main'] = 'instructor/dashboard';
		$data['layout']='two-column-right';
		/**
			meta information
		*/
		$data['sidebar'] = 'user';
		$data['meta_title']='Instructor Dashboard'	;	
		$data['meta_descrption']='Instructor Dashboard'	;
		$userName  = $this->session->userdata('user_name');  
		$displayName = $this->session->userdata('display_name');
		$user_id   = $this->session->userdata('user_id');
		$data['user'] = $this->user_model->get_single_record($this->_id,'id,userName,firstName,lastName,email,accessLevel,lastLogin,activationFlag,profileImage',false);
		$data['userName']  = $userName;
		$data['displayName'] = $displayName; 
		$data['userId']   = $user_id;
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	public function assignments()
	{
		$this->load->model('assignment_model');
		$data['main']        = 'instructor/assignments'; 
		$userId              = $this->session->userdata('user_id');
		$getLstMember        = $this->assignment_model->getUserCourse($userId);
		$num_records         = $this->assignment_model->countNumRecords($userId, $getLstMember);
		$base_url            = base_url().'instructor/assignments';
		$start               = $this->uri->segment($this->uri->total_segments());
		$per_page            = '10'; 
		$data['results']         = $this->assignment_model->geUserAllAssignments( $userId, $getLstMember , $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());
		 
		$this->load->vars($data);
		$this->load->view('template');
		 
		
	}
	
	public function addassignments()
	{
		$error = false;
		$errors = array();
		$this->load->helper('form');
		$this->js[]='js/jquery-ui.js';
		$this->css[]='css/jquery-ui.css';
		
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('assignmentTitle', 'Assignment Title', 'trim|required');
			$this->form_validation->set_rules('pointValue', 'Point Value ', 'trim|required');  
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            { 
				$this->load->model('assignment_model');
				$userId         = $this->session->userdata('user_id');
				$getLstMember   = $this->assignment_model->getUserCourse($userId); 
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
				$data_array = array(
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
				redirect('instructor/assignments'); 
			} 
		}
		$data['errors'] = $errors;
		$data['main'] = 'instructor/addassignments';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	public function edit($getAssignId)
	{
		$error = false;
		$errors = array();
		$this->load->helper('form');
		$this->js[]='js/jquery-ui.js';
		$this->css[]='css/jquery-ui.css';
		$this->load->model('assignment_model');
		$data['main']        = 'instructor/addassignments'; 
		$userId              = $this->session->userdata('user_id');
		$data['assignData']  = $this->assignment_model->getAssignDetails($getAssignId);
	 
	 	if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('assignmentTitle', 'Assignment Title', 'trim|required');
			$this->form_validation->set_rules('pointValue', 'Point Value ', 'trim|required');  
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            { 
				$this->load->model('assignment_model');
				$userId         = $this->session->userdata('user_id');
				$getLstMember   = $this->assignment_model->getUserCourse($userId); 
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
				$data_array = array(  
								'assignTitle' => $this->input->post('assignmentTitle'),
								'assignActiveDate' => $finalActDate,
								'assignActiveTime' => $finalAppTime, 
								'assignDueDate' => $finalDueDate, 
								'assignDueTime' => $finalDueTime,
								'assignPoints' => $this->input->post('pointValue') 
								);   			 
				$this->assignment_model->updateAssignments($assignId, $data_array); 
				set_flash_message('Assignment is Updated successfully','success');
				redirect('instructor/assignments'); 
			} 
		}
	 
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	public function delete($id=0)
	{ 
		$this->load->model('assignment_model'); 
		$this->assignment_model->deleteAssignments($id); 
		set_flash_message('Assignment Deleted successfully','success');
		redirect('instructor/assignments'); 
	}
	
	public function assignmentsManualGrades($id=0)
	{ 
		$error = false;
		$errors = array();
		$this->load->helper('form');
		$this->load->model('assignment_model'); 
		
		$data['result'] = $this->assignment_model->manual_grade_assignments($id); 
		 
		/*$userId              = $this->session->userdata('user_id');
		$getLstMember        = $this->assignment_model->getUserCourse($userId);
		$num_records         = $this->assignment_model->countNumRecords($userId, $getLstMember);
		$base_url            = base_url().'instructor/assignmentsManualGrades';
		$start               = $this->uri->segment($this->uri->total_segments());
		$per_page            = '10'; 
		$data['results']         = $this->assignment_model->geUserAllAssignments( $userId, $getLstMember , $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());*/
		
		
		
		
		$data['errors'] = $errors;
		$data['main'] = 'instructor/assignmentsManualGrades';
		$this->load->vars($data);
		$this->load->view('template');
	} 
	
	public function gradentry()
	{
		$this->load->model('assignment_model');
		$data['main']        = 'instructor/finalgradeentry'; 
		$userId              = $this->session->userdata('user_id');
		$getLstMember        = $this->assignment_model->getUserCourse($userId);
		$data['results']      = $this->assignment_model->get_user_details_assign($getLstMember);
		$this->load->vars($data);
		$this->load->view('template');
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */