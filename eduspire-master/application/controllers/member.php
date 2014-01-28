<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends CI_Controller {
	
	public $js;
	protected $_id;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->load->helper('common');
		$this->load->helper('form');
		$this->load->model('user_model');
		
		if(!is_logged_in()) {
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}else{
			//check the sufficient access level 
			if(MEMBER != $this->session->userdata('access_level')){
				set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
				redirect('home/error');
			}
		}
		$this->_id=$this->session->userdata('user_id');
		
		
	}
	
	
	public function index()
	{	
		$data = array();
		$data['main'] = 'member/dashboard';
		$data['layout']='two-column-right';
		$this->js[]='js/jquery-ui.js';
		$this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]='js/fancybox/source/jquery.fancybox.css';
		
		/**
			meta information
		*/
		$data['sidebar'] = 'user';
		$data['meta_title']='Member Dashboard'	;	
		$data['meta_descrption']='Memeber Dashboard';
		$this->load->model('news_model');
		$data['archives'] = $this->news_model->get_records( '',STATUS_PUBLISH, 0 ,2,'nwID,nwTitle,nwDate,nwDescription');
		$data['user'] = $this->user_model->get_single_record($this->_id,'*',true);
		if(empty($data['user'])){
			redirect('home/error_404');
		}
		
		/*
		//get the current course  
		$data['course_id']=$data['user']->membershipLastUsed;
		$data['course']=$this->course_schedule_model->get_course_detail($data['course_id'],true);
		
		*/
		
		//get courses 
		$this->load->model('course_schedule_model');
		$data['courses'] = $this->course_schedule_model->get_courses('','',$this->_id,0,10,'');
		
		//check pending registered courses
		$this->load->model('course_reservation_model');
		
		if($this->course_reservation_model->count_records('',$data['user']->email,0,STATUS_REGISTERED)){
			$data['pay_for_course']=true;
		}
				
		$this->load->model('assignment_model');
		
		
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	grades
		@Author Name:	Alan Anil
		@Date:			Nov, 12 2013
		@Purpose:		Show user grades 
	
	*/
	public function grades($course_id = 0)
	{
		if(!$course_id){
				show_404('page');
	    }
		$data                    = array();
		$data['main']            = 'member/grades';
		$data['meta_title']      = 'Member Grades';
		$data['sidebar']         = 'user';
		$data['layout']          = 'two-column-right';	
		$data['meta_descrption'] = 'Member Grades'; 
		$this->load->model('assignment_model'); 
		$this->load->model('user_model');
		$this->load->model('news_model');
		$this->load->model('news_model');
		$data['archives'] = $this->news_model->get_records( '',STATUS_PUBLISH, 0 ,2,'nwID,nwTitle,nwDate,nwDescription');
		$data['user'] = $this->user_model->get_single_record($this->_id,'*',true);
		if(empty($data['user'])){
			redirect('home/error_404');
		}
		//get courses 
		$this->load->model('course_schedule_model');
		$data['courses'] = $this->course_schedule_model->get_courses('','',$this->_id,0,10,'');
		//$data['finalGradeCourses'] = $this->user_model->get_user_courses($this->_id); 
		$data['userId']  = $this->_id;
		$data['courseId'] = $course_id;
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	grade_sheet
		@Author Name:	Alan Anil
		@Date:			Nov, 12 2013
		@Purpose:		Show list of all grades of users 
	
	*/
	public function grade_sheet($course_id = 0)
	{
		if(!$course_id){
				show_404('page');
	    }
		$data                    = array();
		$data['main']            = 'member/gradesSheet';
		$data['meta_title']      = 'Member Grades Sheet';
		$data['sidebar']         = 'user';
		$data['layout']          = 'two-column-right';	
		$data['meta_descrption'] = 'Member Grades Sheet'; 
		$this->js[]='js/jquery-ui.js';
		$this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]='js/fancybox/source/jquery.fancybox.css';
		$this->load->model('assignment_model'); 
		$this->load->model('user_model');
		$this->load->model('news_model');
		$this->load->model('news_model');
		$data['archives'] = $this->news_model->get_records( '',STATUS_PUBLISH, 0 ,2,'nwID,nwTitle,nwDate,nwDescription');
		$data['user'] = $this->user_model->get_single_record($this->_id,'*',true);
		if(empty($data['user'])){
			redirect('home/error_404');
		}
		//get courses 
		$this->load->model('course_schedule_model');
		$data['courses'] = $this->course_schedule_model->get_courses('','',$this->_id,0,10,''); 
		$data['userId']  = $this->_id;
		$data['courseId'] = $course_id;
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	comment
		@Author Name:	Alan Anil
		@Date:			Dec, 18 2013
		@Purpose:		Show user comment
	
	*/
	
	public function comment($assignId=0)
	{
		 
		$data = array();  
		$this->load->model('assignment_model');   
		//get the courses 
		$data['main']    = 'comment/comment';
		// fetch user comments.  
		$data['getAssignPointsDetails'] = $this->assignment_model->get_points_earned($this->_id, $assignId);
		$this->load->vars($data); 
		$this->load->view('popup');
	 }
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */