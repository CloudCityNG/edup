<?php
/**
@Page/Module Name/Class: 		news.php
@Author Name:			 		ben binesh
@Date:					 		Aug, 30 2013
@Purpose:		        		Contain all controllers functions for the news 
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

class News extends CI_Controller
{
	
	public $js;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->load->model('news_model');
		$this->load->helper('common');
		$this->load->helper('form');
		
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
		$this->page_title="Manage News";
		$data['meta_title']='Manage News';
		$data['layout']       = '';
		$data['title']        = $this->input->get('title'); 
		$data['status']       = ($this->input->get('status')  != '' )?$this->input->get('status'):''; 
		$num_records          = $this->news_model->count_records( $data['title'], $data['status'] );
		$base_url             = base_url().'edu_admin/news/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) )
		{
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->news_model->get_records( $data['title'], $data['status'], $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/news/index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	create 
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		insert the new record ,validate recored
	
	*/
	function create()
	{
		
		$error = false;
		$errors = array();
		$data=array();
		$this->load->helper('form');
		$this->js[]='js/tinymce/tinymce.min.js';
		$this->page_title   = 'Create News';
		$data['meta_title'] = 'Create News';
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('nwTitle', 'Title', 'trim|required');
			$this->form_validation->set_rules('nwDescription', 'Description', 'trim|required');
			$this->form_validation->set_message('required', '%s must not be blank');
			if( '' != $this->input->post('nwTitle') )
				$this->form_validation->set_rules('nwTitle', 'Title', 'callback_duplicate_check');
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'nwTitle' => $this->input->post('nwTitle'),
								'nwDescription' => $this->input->post('nwDescription'),
								'nwPublish' => $this->input->post('nwPublish'),
								'nwDate' =>date('Y-m-d H:i:s'),
								);
					
				
				$this->news_model->insert($data_array);
				set_flash_message('News details has been inserted successfully','success');
				redirect('edu_admin/news/index');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/news/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	update 
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		validate and update the record
	
	*/
	function update($id=0)
	{
		$error=false;
		$errors=array();
		
		$data['result']=$this->_load_data($id);
		$this->page_title ='Update '.$data['result']->nwTitle;
		$data['meta_title'] = 'Update News';
		$this->js[]='js/tinymce/tinymce.min.js';
		
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('nwDescription', 'Description', 'trim|required');
			$this->form_validation->set_message('required', '%s must not be blank');
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'nwTitle' => $this->input->post('nwTitle'),
								'nwDescription' => $this->input->post('nwDescription'),
								'nwPublish' => $this->input->post('nwPublish'),
								'nwDate' =>date('Y-m-d H:i:s'),
								);
				
				$this->news_model->update($id,$data_array);
				set_flash_message('News details has been updated successfully','success');
				redirect('edu_admin/news/index');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/news/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	delete 
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		validate and delete the record 
	
	*/
	function  delete($id=0)
	{
		$this->_load_data($id);
		$this->news_model->delete($id);	
		set_flash_message('News details has been successfully deleted ','success');
		redirect('edu_admin/news/index');
	}
	/**
		@Function Name:	_load_data
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		load the single record  
	
	*/
	function _load_data($id=0)
	{
		if(!$id)
		{
			redirect('home/error_404');
		}
		$data = $this->news_model->get_single_record($id);
		if(empty($data))
		{
			redirect('home/error_404');
		}
		else
		{
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
		$id=0;
		if ($this->news_model->check_duplicate($id,$title))
		{	
			$this->form_validation->set_message('duplicate_check', 'The  "'.$title.'"  is already created');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */