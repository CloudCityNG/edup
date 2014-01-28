<?php
/**
@Page/Module Name/Class: 		text.php
@Author Name:			 		ben binesh
@Date:					 		Dec,18 2013
@Purpose:		        		contain all controller functions for cms text 
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

class Text extends CI_Controller {
	
	public $js;
	protected $_id;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->_id=0;
		$this->load->model('text_model');
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
		$data['layout']       = '';
                $this->page_title="Manage Texts";
		$data['meta_title']='Manage Texts';
		$data['name']        = $this->input->get('name'); 
		$data['status']       = ($this->input->get('status')  != '' )?$this->input->get('status'):''; 
		$num_records          = $this->text_model->count_records( $data['name'],$data['status'] );
		$base_url             = base_url().'edu_admin/text/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) )
		{
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->text_model->get_records( $data['name'],$data['status'], $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/text/index';
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
		$this->page_title="Create Text";
		$data['meta_title']='Create Text';
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('ctName', 'Text Name', 'trim|required');
			$this->form_validation->set_rules('ctText', 'Text', 'trim|required');
			if( '' != $this->input->post('ctName') )
				$this->form_validation->set_rules('ctName', 'Text Name', 'callback_duplicate_check');
			$this->form_validation->set_message('required', '%s must not be blank');
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								
								'ctName' => $this->input->post('ctName'),
								'ctText' => $this->input->post('ctText'),
								'ctPublish' => $this->input->post('ctPublish'),
								);
					
				
				$this->text_model->insert($data_array);
				set_flash_message('Text details has been inserted successfully','success');
				if($redirect=$this->input->get('redirect'))
					redirect($redirect);	
				redirect('edu_admin/text/index');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/text/form';
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
		$data=array();
		$error=false;
		$errors=array();
		$data['meta_title']='Update Text';
		$data['result']=$this->_load_data($id);
        $this->page_title="Update ".$data['result']->ctName;
		$this->_id = $id;
		$this->js[]='js/tinymce/tinymce.min.js';
		
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('ctName', 'Text Name', 'trim|required');
			$this->form_validation->set_rules('ctText', 'Text', 'trim|required');
			if( '' != $this->input->post('ctName') )
				$this->form_validation->set_rules('ctName', 'Text Name', 'callback_duplicate_check');
			$this->form_validation->set_message('required', '%s must not be blank');
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
					$data_array = array(
								'ctName' => $this->input->post('ctName'),
								'ctText' => $this->input->post('ctText'),
								'ctPublish' => $this->input->post('ctPublish'),
						);
					
					
					
				
				$this->text_model->update($id,$data_array);
				set_flash_message('Text details has been updated successfully','success');
				if($redirect=$this->input->get('redirect'))
					redirect($redirect);	
				redirect('edu_admin/text/index');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/text/form';
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
		set_flash_message('Text  cannot be deleted ','notice');
		redirect('edu_admin/text/index');
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
			show_404('page');
		}
		$data = $this->text_model->get_single_record($id);
		if(empty($data))
		{
			show_404('page');
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
	{	$id=$this->_id;
		
		if ($this->text_model->check_duplicate($id,$title))
		{	
			$this->form_validation->set_message('duplicate_check', 'The  "'.$title.'"  is already created');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	
	
}/*End of Class*/
/* End of file */
