<?php /**@Page/Module Name/Class:                        Testimonials.php@Author Name:			 		janet rajani@Date:					 	Nov, 13 2013@Purpose:		        		testimonials related functions@Most Important Related Files	                login_model.php,testimonials_model.php*/if ( ! defined('BASEPATH')) exit('No direct script access allowed');class Testimonials extends CI_Controller {	public $js;	public function __construct() 	{		parent::__construct();                use_ssl(FALSE);                $js = array();		// load form, url, general helper		// load database user model		// load library form_validation                $this->load->model('login_model');                $this->load->model('testimonials_model');		$this->load->helper('form');		$this->load->library('form_validation');		$this->load->helper('url');		$this->load->library('session');	}     /*		@Function Name:	index		@Author Name:	Janet Rajani 		@Date:		Nov, 13 2013		@Purpose:	Display all testimonials on front-end    */    function index()    {			$data=array();			$data['meta_title']='Testimonials';            $data['layout']         = '';            $data['level']          = $this->input->get('level');            $data['gradeSubject']   = $this->input->get('gradeSubject');            $data['course_id']      = $this->input->get('course_id');            $num_records            = $this->testimonials_model->count_searched_testimonials( $data['level'], $data['gradeSubject'], $data['course_id']);            $base_url               = base_url().'testimonials/index';            $start                  = $this->uri->segment($this->uri->total_segments());            if( !is_numeric( $start ) )            {               $start = 0;            }            $per_page               = '15';             //Get testimonials filtered and approved by admin            $data['results'] = $this->testimonials_model->get_searched_testimonials($data['level'], $data['gradeSubject'], $data['course_id'],$start,$per_page);            $data['pagination_links']   = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());            $this->page_class="testimonialContent";            $data['main']               = 'testimonials/index';            $this->load->vars($data);            $this->load->view('template');    }    /**		@Function Name:	instructor_approval		@Author Name:	Janet Rajani 		@Date:		Noc, 12 2013		@Purpose:	Display all admin approved and assigned testimonials    */    function instructor_approval()    {        //Check permission        //check the sufficient access level         $this->_current_request = $this->router->class.'/'.$this->router->method ;        if(!is_allowed($this->_current_request))        {		                set_flash_message('You don\'t have sufficient permission to access this page  ','warning');                redirect('home/error');        }		 $instructor_id          = $this->session->userdata('user_id');		//If submitted       if($this->input->post()>0)       {           $data_insert     = array();           //Array of checked checkboxes(status boxes)           foreach($this->input->post('chk_ids') as $tID)           {               //If instructor hit approve button               if($this->input->post('activate'))               {                   $instructor_approved_status = 2;               //If instructor hit disapprove button               }                else                {                    $instructor_approved_status = 1;                }                //Update table with the status                    $data_insert = array('instructor_approved'=>$instructor_approved_status);                $this->testimonials_model->update_testimonial_approval($data_insert, $tID, $instructor_id );           }       }		        //End permission       $this->js[]='js/admin.js';       $data['layout']         = '';       //Get userid(instructor in this case) from session             $data['instructor_approved']   = ($this->input->get('instructor_approved')  != '' )?$this->input->get('instructor_approved'):'';        $num_records            = $this->testimonials_model->count_instructor_testimonials($instructor_id,$data['instructor_approved']);       $base_url               = base_url().'testimonials/instructor_approval';       $start                  = $this->uri->segment($this->uri->total_segments());       if( !is_numeric( $start ) )       {           $start = 0;       }       $per_page               = PER_PAGE;        //It will give all questionnaire title with the count of no of questions in it       $data['results']        = $this->testimonials_model->get_records_instructor_testimonials($instructor_id,$data['instructor_approved'], $start , $per_page );       $data['pagination_links']   = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());                $data['main']               = 'testimonials/instructor_approval';       $this->load->vars($data);       $this->load->view('template');    }}