<?php
/**
@Page/Module Name/Class: 		page.php
@Author Name:			 		ben binesh
@Date:					 		Aug,16 2013
@Purpose:		        		contain all controller functions for cms pages 
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

class page extends CI_Controller {
	
	public $js;
	protected $_id;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->_id=0;
		$this->load->model('page_model');
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
                $this->page_title="Manage Content";
		$data['meta_title']='Manage Content';
		$data['title']        = $this->input->get('title'); 
		$data['url_key']        = $this->input->get('url_key'); 
		$data['status']       = ($this->input->get('status')  != '' )?$this->input->get('status'):''; 
		$num_records          = $this->page_model->count_records( $data['title'], $data['url_key'] ,$data['status'] );
		$base_url             = base_url().'edu_admin/page/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) )
		{
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->page_model->get_records( $data['title'], $data['url_key'] ,$data['status'], $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/page/index';
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
		$image = $this->input->post('old_image');
		$this->page_title="Create Page";
		$data['meta_title']='Create Pages';
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('cpTitle', 'Title', 'trim|required');
			$this->form_validation->set_rules('cpName', 'Page Name', 'trim|required');
			if( '' != $this->input->post('cpUrlKey') )
				$this->form_validation->set_rules('cpUrlKey', 'Url Key', 'callback_duplicate_check');
			$this->form_validation->set_message('required', '%s must not be blank');
			
			if($error==false)
			{
				//upload the image 
				if($_FILES['cpImage']['name'] != '')
				{
					$path = UPLOADS.'/pages';
					$res_response = upload_file('cpImage',$path);
					if(is_array($res_response) && isset( $res_response['file_name'] ) )
					{
						// delete old file
						if( $image != '' )
						{
							//unlink the previosuly uploaded image 
							@unlink( $path.'/'.$image );
						}
						$image  = $res_response['file_name'];
					}
					else{
						$error = true;
						$errors[] = $res_response;
					}
				}	
			}
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'cpTitle' => $this->input->post('cpTitle'),
								'cpName' => $this->input->post('cpName'),
								'cpDescription' => $this->input->post('cpDescription'),
								'cpMetaTitle' => $this->input->post('cpMetaTitle'),
								'cpMetaDescription' => $this->input->post('cpMetaDescription'),
								'cpUrlKey' => url_title($this->input->post('cpUrlKey'),'-',true),
								'cpPublish' => $this->input->post('cpPublish'),
								'cpImage' => $image,
								);
					
				
				$this->page_model->insert($data_array);
				set_flash_message('Page details has been inserted successfully','success');
				redirect('edu_admin/page/index');
			}
		}
		$data['image']=$image;
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/page/form';
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
		$data['meta_title']='Update Pages';
		$data['result']=$this->_load_data($id);
        $this->page_title="Update ".$data['result']->cpTitle;
		$this->_id = $id;
		$image = $data['result']->cpImage;
		$this->js[]='js/tinymce/tinymce.min.js';
		
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('cpName', 'Page Name', 'trim|required');
			$this->form_validation->set_rules('cpTitle', 'Title', 'trim|required');
			if( '' != $this->input->post('cpUrlKey') )
				$this->form_validation->set_rules('cpUrlKey', 'Url Key', 'callback_duplicate_check');
			$this->form_validation->set_message('required', '%s must not be blank');
			if($error==false)
			{
				//upload the image 
				if($_FILES['cpImage']['name'] != '')
				{
					$path = UPLOADS.'/pages';
					$res_response = upload_file('cpImage',$path);
					if(is_array($res_response) && isset( $res_response['file_name'] ) )
					{
						// delete old file
						if( $image != '' )
						{
							//unlink the previously uploaded image 
							@unlink( $path.'/'.$image );
						}
						$image  = $res_response['file_name'];
					}
					else
					{
						$error = true;
						$errors[] = $res_response;
					}
				}	
			}
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'cpTitle' => $this->input->post('cpTitle'),
								'cpName' => $this->input->post('cpName'),
								'cpDescription' => $this->input->post('cpDescription'),
								'cpMetaTitle' => $this->input->post('cpMetaTitle'),
								'cpMetaDescription' => $this->input->post('cpMetaDescription'),
								'cpUrlKey' =>url_title($this->input->post('cpUrlKey'),'-',true),
								'cpPublish' => $this->input->post('cpPublish'),
								'cpImage' => $image,
								);
					
					
					
				
				$this->page_model->update($id,$data_array);
				set_flash_message('Page details has been updated successfully','success');
				redirect('edu_admin/page/index');
			}
		}
		$data['image']=$image;
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/page/form';
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
		set_flash_message('Course type cannot be deleted ','notice');
		redirect('edu_admin/page/index');
	}
	/**
		@Function Name:	_load_date
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
		$data = $this->page_model->get_single_record($id);
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
		
		if ($this->page_model->check_duplicate($id,$title))
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