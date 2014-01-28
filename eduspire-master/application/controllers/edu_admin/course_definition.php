<?php
/**
@Page/Module Name/Class: 	    course_definition.php
@Author Name:			  		ben binesh
@Date:					  		Aug, 16 2013
@Purpose:		         		Contain all conroller functions for the course definition
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
//Chronological development
//***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
/***********************************************************************************
//RF1.	  |  ben binesh		 | Nov ,16 2013  | major	   | add the BYOC session
							                                 related functions 
  
//***********************************************************************************/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_definition extends CI_Controller {
	
	public $js;
	protected  $_id;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->_id = 0;
		$this->load->model('course_definition_model');
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
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		show the multiple records and filter 
	
	*/
	public function index()
	{
		$data                 = array();
		$data['meta_title']	  =	'All Courses';
 		$this->page_title     = "All Courses";
		$data['layout']       = '';
		$data['title']        = $this->input->get('title'); 
		$data['genre']        = $this->input->get('genre'); 
		$data['facilitator']  = $this->input->get('facilitator'); 
		$data['course_id']    = $this->input->get('course_id'); 
		$data['status']       = ($this->input->get('status') != '')?$this->input->get('status'):''; 
		$num_records          = $this->course_definition_model->count_records($data['title'],$data['genre'] ,$data['facilitator'],$data['course_id'],$data['status'] );
		$base_url             = base_url().'edu_admin/course_definition/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->course_definition_model->get_records( $data['title'],$data['genre'] ,$data['facilitator'],$data['course_id'],$data['status'] , $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		
		$data['main'] = 'edu_admin/course_definition/index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	create 
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		insert the new record ,validate recored
	
	*/
	function create(){
		
		$error = false;
		$errors = array();
		$data=array();
        $this->page_title="Create Course";
		$data['meta_title']='Create Course';
		$this->load->helper('form');
		$this->js[]='js/tinymce/tinymce.min.js';
		
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('cdGenre', 'Genre', 'trim|required');
			$this->form_validation->set_rules('cdCourseID', ' Course Id', 'trim|required');
			$this->form_validation->set_rules('cdCourseTitle', 'Title', 'trim|required');
			if( '' != $this->input->post('cdCourseID') )
				$this->form_validation->set_rules('cdCourseID', 'Course Id', 'callback_duplicate_check');
			$this->form_validation->set_message('required', '%s must not be blank');
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'cdGenre' => $this->input->post('cdGenre'),
								'cdCourseID' => $this->input->post('cdCourseID'),
								'cdCourseTitle' => $this->input->post('cdCourseTitle'),
								'cdDescription' => $this->input->post('cdDescription'),
								'cdGoals' => $this->input->post('cdGoals'),
								'cdOutline' => $this->input->post('cdOutline'),
								'cdEvaluationMethod' => $this->input->post('cdEvaluationMethod'),
								'cdPublish' => $this->input->post('cdPublish'),
								'cdFeatured' => $this->input->post('cdFeatured'),
								);
					
				
				$course_id = $this->course_definition_model->insert($data_array);
				set_flash_message('Course definition details has been inserted successfully','success');
				redirect('edu_admin/course_definition/index');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/course_definition/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	update 
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		validate and update the record
	
	*/
	function update($id=0){
		$error=false;
		$errors=array();
		$data=array();
		$data['result']=$this->_load_data($id);
		$this->page_title="Update ".$data['result']->cdCourseTitle;
		$data['meta_title']='Update Course';
		$this->_id = $id;
		$this->js[]='js/tinymce/tinymce.min.js';
		
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('cdGenre', 'Genre', 'trim|required');
			$this->form_validation->set_rules('cdCourseID', ' Course Id', 'trim|required');
			$this->form_validation->set_rules('cdCourseTitle', 'Title', 'trim|required');
			if( '' != $this->input->post('cdCourseID') )
				$this->form_validation->set_rules('cdCourseID', 'Course Id', 'callback_duplicate_check');
			$this->form_validation->set_message('required', '%s must not be blank');
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'cdGenre' => $this->input->post('cdGenre'),
								'cdCourseID' => $this->input->post('cdCourseID'),
								'cdCourseTitle' => $this->input->post('cdCourseTitle'),
								'cdDescription' => $this->input->post('cdDescription'),
								'cdGoals' => $this->input->post('cdGoals'),
								'cdOutline' => $this->input->post('cdOutline'),
								'cdEvaluationMethod' => $this->input->post('cdEvaluationMethod'),
								'cdPublish' => $this->input->post('cdPublish'),
								'cdFeatured' => $this->input->post('cdFeatured'),
								);
					
					
				
				$this->course_definition_model->update($id,$data_array);
				set_flash_message('Course definition details has been updated successfully','success');
				redirect('edu_admin/course_definition/index');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/course_definition/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	
	
	/**
		@Function Name:	delete 
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		validate and delete the record 
	
	*/
	function  delete($id=0){
		$data = $this->_load_data($id);
		$is_delete = true;
		
		if($this->db->where('csCourseDefinitionId',$id)->count_all_results('course_schedule')){
			$is_delete = false;
			set_flash_message('Course definition cannot be deleted as there are courses schedule associated with it ','error');
		}
		
		if($is_delete){
			$this->course_definition_model->delete($id);
			set_flash_message('Course definition has been successfully deleted ','success');
			
		}
		redirect('edu_admin/course_definition/index');
		
	}
	/**
		@Function Name:	_load_date
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		load the single record  
	
	*/
	function _load_data($id=0){
		if(!$id){
			show_404('page');
		}
		$data = $this->course_definition_model->get_single_record($id);
		if(empty($data)){
			show_404('page');
		}else{
			return $data;
		}
		
		
	}
	
	/**
		@Function Name:	duplicate_check
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		check the duplicate record in data base with same title  
	
	*/
	
	public function duplicate_check($title='')
	{	
		$id = $this->_id;
		if ($this->course_definition_model->check_duplicate($id,$title))
		{	$this->form_validation->set_message('duplicate_check', 'The  "'.$title.'"  is already created');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	
/*********************************************************
 * Rf1	
/ Build you own course session related functions 
/
**********************************************************/	
	/**
		@Function Name:	sessions
		@Author Name:	ben binesh
		@Date:			Nov, 06 2013
		@Purpose:		show the multiple records and filter 
	
	*/
	
	function sessions()
	{
		$data=array();
		$data['meta_title']   = 'Course Sessions';
		$this->page_title     = 'Course Sessions';
		$data['layout']       = '';
		$data['year']         = $this->input->get('year'); 
		$num_records          = $this->course_definition_model->count_sessions($data['year']);
		$base_url             = base_url().'edu_admin/course_definition/session';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->course_definition_model->get_sessions( $data['year'],$start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		
		$data['main'] = 'edu_admin/course_definition/sessions';
		$this->load->vars($data);
		$this->load->view('template');
		
	}
	
	
	/**
		@Function Name:	session
		@Author Name:	ben binesh
		@Date:			Nov, 06 2013
		@Purpose:		create and update byoc session
	
	*/
	
	
	function session($id=0)
	{
		$error = false;
		$errors = array();
		$is_new_record = TRUE;
		$data=array();
		
		$this->load->helper('form');
		$this->js[]  = 'js/jquery-ui.js';
		$this->css[] = 'css/jquery-ui.css';
		$data['meta_title']   = 'Create Session';
		$this->page_title     = 'Create Session';
		if($id){
			$data['result'] = $this->_load_session($id);
			$this->_id      = $data['result']->bsID;
			$is_new_record  = false;
			$data['meta_title']   = 'Update Session';
			$this->page_title     = 'Update Session';
			
		}
		
		
		if(count($_POST)>0){
			
			$this->load->library('form_validation');
			$this->form_validation->set_rules('bsStartDate', 'Session Start Date', 'trim|required');
			$this->form_validation->set_rules('bsEndDate', 'Session End Date', 'trim|required');
			$this->form_validation->set_message('required', '%s must not be blank');
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
							'bsStartDate' => format_date($this->input->post('bsStartDate'),'Y-m-d'),
							'bsEndDate' => format_date($this->input->post('bsEndDate'),'Y-m-d'),
							
						);
					
				
				if($is_new_record)
				{
					set_flash_message('Course session details has been inserted successfully','success');
					$course_schedule_id = $this->course_definition_model->insert_session($data_array);
				}
				else
				{
					$this->course_definition_model->update_session($id,$data_array);
					set_flash_message('Course session details has been updated successfully','success');
				}
							
				if($redirect=$this->input->get('redirect')){
					redirect($redirect);
				}
				redirect('edu_admin/course_definition/sessions');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/course_definition/session_form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	/**
		@Function Name:	delete_session
		@Author Name:	ben binesh
		@Date:			Nov, 06 2013
		@Purpose:		delete the course session
	
	*/
	
	function delete_session($id=0)
	{
		$data = $this->_load_session($id);
		$is_delete = true;
		
		if($this->db->where('csCourseSession',$id)->count_all_results('course_schedule'))
		{
			$is_delete = false;
			set_flash_message('Course session cannot be deleted as there are courses schedule associated with it ','error');
		}
		if($is_delete)
		{
			$this->course_definition_model->delete_session($id);
			set_flash_message('Course session has been successfully deleted ','success');
			
		}
		redirect('edu_admin/course_definition/sessions');
		
	}
	
	
	/**
		@Function Name:	_load_session
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		load the single record  
	
	*/
	function _load_session($id=0){
		if(!$id){
			show_404('page');
		}
		$data = $this->course_definition_model->get_single_session($id);
		if(empty($data)){
			show_404('page');
		}else{
			return $data;
		}
		
		
	}
	
	
}//end of class 

/* End of file */
