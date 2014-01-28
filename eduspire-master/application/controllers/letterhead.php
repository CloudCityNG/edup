<?php
// ********************************************************************************************************************************
//Page name			:- 			letterhead.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			File for showing data on client letterhead.  
//Date				:- 			Dec-18-2013
//Table Refered		:-  		N/A
//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  

//---------------------------------------------------------------------------------------- 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Letterhead extends CI_Controller {
	
	public $js;
	protected $_id;
	public function __construct() 
	{
		parent::__construct(); 
                use_ssl(FALSE);
		$js=array();
		$this->load->helper('common'); 
		$this->load->helper('url');  
		$this->_id=$this->session->userdata('user_id');
		$this->js[]='js/jquery-ui.js';
	}
	
	/**
		@Function Name:	index
		@Author Name:	Alan anil	
		@Date:			Sep, 03 2013 
		@Purpose:		Index function call when script run.
	
	*/
	public function index($course_id = 0)
	{
	    $data = array();
		$data['main'] = 'grades/finalGrade';  
		$this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]='js/fancybox/source/jquery.fancybox.css'; 
		/**
			meta information
		*/ 
		$data['meta_title']='Member Grades'	;	
		$data['meta_descrption']='Memeber Grades';
		$this->load->model('user_model');  
		$this->load->model('assignment_model'); 
		$data['user'] = $this->user_model->get_single_record($this->_id,'*',true);
		if(empty($data['user'])){
			redirect('home/error_404');
		}
		//get courses 
		$this->load->model('course_schedule_model');
		$data['courses'] = $this->course_schedule_model->get_courses('','',$this->_id,0,10,''); 
		$data['course']  = $this->course_schedule_model->get_course_detail($course_id);
		$data['userId']  = $this->_id;
		$data['courseId'] = $course_id;  
		$this->load->vars($data);
		$this->load->view('letterhead');
	}	 
}	
 
// 	LOTS OF COMMENTED CODE AND ECHO PRESENT IN FILE THESE LINES FOR TESTING THE SCRIPT BY RUN IT MANUALLY.

 
?>