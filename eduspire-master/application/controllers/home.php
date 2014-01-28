<?php 
/**
@Page/Module Name/Class: 		home.php
@Author Name:			 		ben binesh
@Date:					 		
@Purpose:		        		Home page of site
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL

Chronological Development
**********************************************************************************
| Ref No.  |   Author name	| Date		| Severity 	| Modification description
***********************************************************************************
RF1       | Janet Rajani         | 28 Nov, 2013   | made testimonial dynamic 
***********************************************************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
	
	Public $page_title;
        public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
	}
	
	public function index() 
	{
                //RF1
                $this->load->model('testimonials_model');
                //End RF1
		$data['main'] = 'home';
		//$data['layout']='two-column-right';
		
		
		$this->js[]='js/jquery.bxslider.min.js';
		$this->css[]='css/jquery.bxslider.css';
		$this->load->model('page_model');
		$this->load->model('course_definition_model');
		
		$data['content'] = $this->page_model->get_single_record($page_id=3);
		$data['content_second'] = $this->page_model->get_single_record($page_id=14);
		$data['events'] = get_content('news','nwID,nwTitle,nwDescription,nwDate','nwPublish = '.STATUS_PUBLISH,'nwID','DESC',3);
		$data['featured_courses']=$this->course_definition_model->get_courses('',0,'',0,STATUS_PUBLISH,0,4,TRUE);
		
		if(empty($data['content']))
			show_404();
		
		/**
			meta information
		*/
		$data['meta_title']             = $data['content']->cpMetaTitle;
		$data['meta_descrption']        = $data['content']->cpMetaDescription;
                //RF1
		$data['home_page_testimonial']  = $this->testimonials_model->get_searched_testimonials('','','',0,1,'',2);
                //End RF1
		$data['slides']                 = get_content('cms_slide','*','csPublish = 1','csOrder','ASC');
		$data['sidebar']                = 'home';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	/**
		@Function Name:	page 
		@Author Name:	binesh
		@id:	        | integer | id of page 
 		@Date:			Aug, 23 2013
		@Purpose:		load the static page 
	
	*/
	function page($id=0){
		if(!$id){
			show_404();
		}
		
		if($id==1){
			$this->about_us();
			return ;
		}
		$this->load->model('page_model');
		$data['content'] = $this->page_model->get_single_record($id);
		
		if(empty($data['content']))
			show_404();
		//meta information
		$data['meta_title']=$data['content']->cpMetaTitle;
		$data['meta_descrption']=$data['content']->cpMetaDescription;
		$data['main'] = 'page';
		$this->load->vars($data);
		$this->load->view('template');		
			
		
	}
	
	/**
		@Function Name:	about_us 
		@Author Name:	ben binesh
		@Date:			Oct, 19 2013
		@Purpose:		load the about us page 
	
	*/
	function about_us()
	{
		$this->js[]              ='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]             ='js/fancybox/source/jquery.fancybox.css';
		$this->js[]              ='js/jquery-ui.js';
		$data=array();
                $this->load->model('page_model');
                
		//meta information
		
		$data['content'] 		= $this->page_model->get_single_record($id=1);
		$data['content_second']	= $this->page_model->get_single_record($d=15);
		$data['meta_title']=$data['content']->cpMetaTitle;
		$data['meta_descrption']=$data['content']->cpMetaDescription;
		$this->page_title = $data['content']->cpTitle;
		$data['users']    = get_content('users','id,firstName,lastName,profileImage,accessLevel','activationFlag = \''.ACCOUNT_ACTIVE.'\' AND accessLevel IN ('.INSTRUCTOR.','.MANAGER.' ,'.ADMIN.','.SUPER_ADMIN.') AND isAboutUs =  '.FEATURED,'lastName','ASC');
		$data['main'] = 'about-us';
		$this->load->vars($data);
		$this->load->view('template');		
			
	}
	
	
	
	/**
		@Function Name:	error
		@Author Name:	binesh
		@Date:			Aug, 23 2013
		@Purpose:		load the general error page ,show error from the session message 
	
	*/
	
	function error(){
		$data=array();
		$data['meta_title']='error';
		$data['main'] = 'error';
		$this->load->vars($data);
		$this->load->view('template');	
	}
	
	/**
		@Function Name:	error
		@Author Name:	binesh
		@Date:			Aug, 23 2013
		@Purpose:		load the general message page ,page title is given as query string 
	
	*/
	
	function message(){
		$data=array();
		$data['meta_title'] = $this->input->get('title');
		$data['main'] = 'message';
		$this->load->vars($data);
		$this->load->view('template');	
	}
	
	
	
	/**
		@Function Name:	error
		@Author Name:	binesh
		@Date:			Aug, 23 2013
		@Purpose:		load the general error page ,show error from the session message 
	
	*/
	
	function error_404(){
		$this->load->model('page_model');
		$data['content'] = $this->page_model->get_single_record($id=7);
		
		if(empty($data['content']))
			show_404();
		//meta information
		$data['meta_title']=$data['content']->cpMetaTitle;
		$data['meta_descrption']=$data['content']->cpMetaDescription;
		$data['main'] = '404';
		$this->load->vars($data);
		$this->load->view('template');	
	}
	
	
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */