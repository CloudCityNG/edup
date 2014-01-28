<?php
/**
@Page/Module Name/Class: 		assignment.php
@Author Name:			 		ben binesh
@Date:					 		Oct 08, 2013
@Purpose:		        		Contain all data controller functions for assignments 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/

//Chronological development
/***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
//***********************************************************************************

//***********************************************************************************/

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Assignment extends CI_Controller {
	
	public $js;
	protected $_id;
	public function __construct() 
	{
		parent::__construct(); 
                use_ssl(FALSE);
		$js=array();
		$this->load->helper('common');
		$this->load->helper('form');
		$this->load->model('assignment_model');
		//$this->_current_request = $this->router->class.'/'.$this->router->method ;
		
		if(!is_logged_in()) {
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}else{
			/*
			//check the sufficient access level 
			if(INSTRUCTOR != $this->session->userdata('access_level')){
				set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
				redirect('home/error');
			}*/
		}
		$this->_id=$this->session->userdata('user_id');
		
	}
	
	
	public function index()
	{
		if(!is_allowed('assignment/index'))
		{
			set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
			redirect('home/error');
		}
		
		if(!($data['for_course']=$this->input->get('for_course'))){
			redirect('home/error404');
		}
		$this->load->model('course_schedule_model');
		$data['course']=$this->course_schedule_model->get_course_detail($data['for_course']);
		if(empty($data['course'])){
			redirect('home/error404');
			
		}
		
		
		if($this->input->post('mass_action')){
			$this->_import();
			return ;
		}
		
		$this->js[]='js/admin.js';
		
		$data['definition_id']    = $this->input->get('definition_id'); 
		$data['course_id']    = $this->input->get('course_id'); 
		$data['author_id']    = $this->input->get('author_id'); 
		$num_records          = $this->assignment_model->count_records( $data['course_id'],$data['author_id'], '', '',$data['definition_id'] );
		$base_url             = base_url().'assignment/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->assignment_model->get_records($data['course_id'],$data['author_id'], '', '', $start , $per_page,$data['definition_id'] );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'assignment/index';
		$this->load->vars($data);
		$this->load->view('template');
		
	}
	
	public function view($title='',$id='')
	{	
		$data=array();
		$data['main']='assignment/view';
		$data['result']=$this->assignment_model->getAssignDetails($id);
		if(empty($data['result'])){
			redirect('home/error404');
		}
		$this->load->vars($data);
		$this->load->view('template');
		 
		
	}
	
	
	function _import(){
		$chk_ids=$this->input->post('chk_ids');
		$for_course=$this->input->get('for_course');
		if(!empty($chk_ids) && count($chk_ids > 0))
		{
			//get  assignments 
			$assignments=$this->assignment_model->get_assignments_Byids($chk_ids);
			if(!empty($assignments))
			{
				foreach($assignments as $assignment)
				{
					$assignments_array=array(
						'assignAuthor' => $this->session->userdata('user_id'),
						'assignCnfID'  => $for_course,
						'creationDate' => date('Y-m-d H:i:s'),
						'assignType'   => $assignment->assignType,
						'assignSession'=>$assignment->assignSession,
						'assignQuestionnaireSession'=>$assignment->assignQuestionnaireSession,
						'assignQuestionnaire'=>$assignment->assignQuestionnaire,
						'assignAppliedDate'=>$assignment->assignAppliedDate,
						'assignTitle'=>$assignment->assignTitle,
						'assignTopic'=>$assignment->assignTopic,
						'assignActiveDate'=>$assignment->assignActiveDate,
						'assignActiveTime'=>$assignment->assignActiveTime,
						'assignDueDate'=>$assignment->assignDueDate,
						'assignDueTime'=>$assignment->assignDueTime,
						'assignPoints'=>$assignment->assignPoints,
						'assignDeductLatePoints'=>$assignment->assignDeductLatePoints,
						'assignGroups'=>$assignment->assignGroups,
						'assignHide'=>$assignment->assignHide,
						'assignVisibility'=>$assignment->assignVisibility,
						'assignDaysToHide'=>$assignment->assignDaysToHide,
						'assignInitialPostLength'=>$assignment->assignInitialPostLength,
						'assignResponsesRequired'=>$assignment->assignResponsesRequired,
						'assignResponsePostLength'=>$assignment->assignResponsePostLength,
						'assignHidePostNames'=>$assignment->assignHidePostNames,
						'assignModifiedDate'=>date('Y-m-d'),
						'assignLinkName'=>$assignment->assignLinkName,
						'assignLinkUrl'=>$assignment->assignLinkUrl,
					);
					//insert assignment 
					
					$this->assignment_model->insertAssignments($assignments_array);
						
				}
				
				set_flash_message('Selected assignments has been successfully imported.please update the assignment dates ','notice');
				redirect('instructor');	
			}
			
		}
		redirect('home/error404');
	}
	
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */