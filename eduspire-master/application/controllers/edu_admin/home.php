<?php
/**
@Page/Module Name/Class: 		home.php
@Author Name:			  		ben binesh
@Date:					  		Aug, 16 2013
@Purpose:		         		Admin home controller
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
//Chronological development
//***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
/***********************************************************************************

//***********************************************************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
	
	public $js;
	protected $_id;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->load->helper('common');
		$this->load->model('user_model');
		$this->id = 0;
		if(!is_logged_in())
		{
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}
		else
		{
			//check the sufficient access level 
			$this->_current_request = 'edu_admin/'.$this->router->class.'/'.$this->router->method ;
			if(!is_allowed($this->_current_request))
			{		
				set_flash_message('You don\'t have sufficient permission to access this page','warning');
				redirect('home/error');
			}
		}
		$this->_id=$this->session->userdata('user_id');
			
		
	}
	
	/**
		@Function Name:	index
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		show the multiple records and filter 
	
	*/
	public function index()
	{
		$data        = array();
		$this->js[]  = 'js/jquery-ui.js';
		$this->js[]  = 'js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[] = 'js/fancybox/source/jquery.fancybox.css';
		$this->load->model('news_model');
		$data['layout'] = 'two-column-right';
		$data['meta_title']='Admin Dashboard';
		$data['sidebar'] = 'admin';
		$this->emulate_bar=true;
		
		$data['archives'] = $this->news_model->get_records( '',STATUS_PUBLISH, 0 ,2,'nwID,nwTitle,nwDate,nwDescription');
		$data['user']   = $this->user_model->get_single_record($this->_id,'*',true);
		if(empty($data['user'])){
			redirect('home/error_404');
		}
		
		$this->load->model('course_definition_model');
		$data['courses']=$this->course_definition_model->get_records('',0,0,'','',$start = 0 , 15);
		
		$this->load->model('course_reservation_model');
		
		$data['today_registered'] = $this->course_reservation_model->count_reservation_byFilters(0,STATUS_REGISTERED,date('Y-m-d'));
		$data['today_enrolled']   = $this->course_reservation_model->count_reservation_byFilters(0,STATUS_ENROLLED,date('Y-m-d'),TRUE);
		
		
		$data['this_week_registered'] = $this->course_reservation_model->count_reservation_byFilters(0,STATUS_REGISTERED,date('Y-m-d'),FALSE,TRUE);
		$data['this_week_enrolled']   = $this->course_reservation_model->count_reservation_byFilters(0,STATUS_ENROLLED,date('Y-m-d'),TRUE,TRUE);
		
		$lastweek_date = date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d'))));
		
		$data['last_week_registered'] = $this->course_reservation_model->count_reservation_byFilters(0,STATUS_REGISTERED,$lastweek_date,FALSE,TRUE);
		$data['last_week_enrolled']   = $this->course_reservation_model->count_reservation_byFilters(0,STATUS_ENROLLED,$lastweek_date,TRUE,TRUE);
		
		
		$data['this_month_registered'] = $this->course_reservation_model->count_reservation_byFilters(0,STATUS_REGISTERED,date('Y-m-d'),FALSE,FALSE,TRUE);
		$data['this_month_enrolled']   = $this->course_reservation_model->count_reservation_byFilters(0,STATUS_ENROLLED,date('Y-m-d'),TRUE,FALSE,TRUE);
		
		$lastmonth_date = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-d'))));
		$data['lastmonth_date']=$lastmonth_date;
		$data['last_month_registered'] = $this->course_reservation_model->count_reservation_byFilters(0,STATUS_REGISTERED,$lastmonth_date,FALSE,FALSE,TRUE);
		$data['last_month_enrolled']   = $this->course_reservation_model->count_reservation_byFilters(0,STATUS_ENROLLED,$lastmonth_date,TRUE,FALSE,TRUE);
		
		
		
		$data['main'] = 'edu_admin/index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	emulate
		@Author Name:	ben binesh
		@Date:			Sept, 25, 2013
		@Purpose:		show the multiple records and filter 
	
	*/
	
	function emulate()
	{
		$data  = array();
		$error = true;
		$data['main'] = 'edu_admin/emulate';
		$data['meta_title']='User Emulator';
		$this->load->model('user_model');
		$data['name'] = $this->input->get('name'); 
		//get the current user  course  
		
		$num_records          = $this->user_model->count_users( $data['name'],0,'','');
		$base_url             = base_url().'edu_admin/home/emulate';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->user_model->get_users( $data['name'],0 ,'' ,'', $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	/*
		function used for testing purpose 
	*/
	public function test()
	{
		$this->load->model('data_model');
		//$this->data_model->adjust_course();
		$this->data_model->adjust_product_type();
		
		
		
	}
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */