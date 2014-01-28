<?php
/**
@faq/Module Name/Class: 		faq.php
@Author Name:			 		ben binesh
@Date:					 		Aug, 27 2013
@Purpose:		        		Contain all controller functions for the faqs
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Faq extends CI_Controller {
	
	public $js;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->load->model('faq_model');
		$this->load->helper('common');
		$this->load->helper('form');
		
		if(!is_logged_in())
		{
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}
		else
		{
			
			//check the sufficient access level 
			$this->_current_request = 'edu_admin/'.$this->router->class.'/index' ;
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
		$data=array();
		$this->page_title="Manage FAQs";
		$data['meta_title']='Manage FAQs';
		$data['layout']       = '';
		$data['title']        = $this->input->get('title'); 
		$data['audience']       = ($this->input->get('audience')  != '' )?$this->input->get('audience'):''; 
		$data['status']       = ($this->input->get('status')  != '' )?$this->input->get('status'):''; 
		$num_records          = $this->faq_model->count_records( $data['title'], $data['audience'] ,$data['status'] );
		$base_url             = base_url().'edu_admin/faq/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = '30'; 
		$data['results']     = $this->faq_model->get_records( $data['title'], $data['audience'] ,$data['status'], $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/faq/index';
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
		$this->load->helper('form');
        $this->page_title = 'Create FAQ';
		$data['meta_title']='Create FAQ';
		$this->js[]='js/tinymce/tinymce.min.js';
		
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('question', 'Question', 'trim|required');
			$this->form_validation->set_rules('nestedMenuOrder', 'Display Order', 'trim|required|numeric');
			$this->form_validation->set_message('required', '%s must not be blank');
		
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'faqPublish	' => $this->input->post('faqPublish'),
								'nestedMenuOrder' => $this->input->post('nestedMenuOrder'),
								'intendedAudience' => $this->input->post('intendedAudience'),
								'question' => $this->input->post('question'),
								'answer' => $this->input->post('answer'),
								'answer' => $this->input->post('answer'),
								);
					
				
				$this->faq_model->insert($data_array);
				set_flash_message('Faq details has been inserted successfully','success');
				redirect('edu_admin/faq/index');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/faq/form';
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
		
		$data['result']=$this->_load_data($id);
		$this->page_title = 'Update FAQ';
		$data['meta_title']='Update FAQ';
		$this->js[]='js/tinymce/tinymce.min.js';
		
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('question', 'Question', 'trim|required');
			$this->form_validation->set_rules('nestedMenuOrder', 'Display Order', 'trim|required|numeric');
			$this->form_validation->set_message('required', '%s must not be blank');
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'faqPublish	' => $this->input->post('faqPublish'),
								'nestedMenuOrder' => $this->input->post('nestedMenuOrder'),
								'intendedAudience' => $this->input->post('intendedAudience'),
								'question' => $this->input->post('question'),
								'answer' => $this->input->post('answer'),
								'answer' => $this->input->post('answer'),
								);
					
					
					
				
				$this->faq_model->update($id,$data_array);
				set_flash_message('faq details has been updated successfully','success');
				redirect('edu_admin/faq/index');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/faq/form';
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
		$this->_load_data($id);
		$this->faq_model->delete($id);	
		set_flash_message('Faq details has been successfully deleted ','success');
		redirect('edu_admin/faq/index');
	}
	/**
		@Function Name:	_load_date
		@Author Name:	binesh
		@Date:			Aug, 16 2013
		@Purpose:		load the single record  
	
	*/
	function _load_data($id=0){
		if(!$id){
			show_404('faq');
		}
		$data = $this->faq_model->get_single_record($id);
		if(empty($data)){
			show_404('faq');
		}else{
			return $data;
		}
		
		
	}
	
	
	
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */