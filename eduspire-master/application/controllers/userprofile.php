<?php 
/**
@Page/Module Name/Class: 		userprofile.php
@Author Name:			 		Alan Anil
@Date:					 		Dec, 12 2013
@Purpose:		        		Contain all general controller functions for the user profile 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
 
class Userprofile extends CI_Controller {
	
	public $js;
	protected $_id;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->load->helper('form');
		$this->load->model('user_model'); 
		
	}
	
	/**
		@Function Name:	index
		@Author Name:	Alan Anil
		@Date:			Dec, 12 2013
		@Purpose:		load the list of users 
	
	*/
	
	public function instructorbio($id=0)
	{
		 
		$data = array();  
		$data['user']        = $this->user_model->get_single_record($id,'*',true);
		$this->load->model('testimonials_model');  
		$this->load->model('course_schedule_model'); 
		//get the courses 
		$data['courses']     = $this->course_schedule_model->get_courses(0,'',0,0,-1,NULL,STATUS_PUBLISH,true,$id);
		$data['main']        = 'userprofile/instructor';  
		$data['testimonial'] = $this->testimonials_model->get_instructor_profile_testimonials($id,5,0);
		$this->load->vars($data); 
		$this->load->view('popup');
	 }
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */