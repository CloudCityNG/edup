<?php 
/**
@Page/Module Name/Class: 		slide.php
@Author Name:			 		ben binesh
@Date:					 		Aug,16 2013
@Purpose:		        		contain all controller functions for slides management 
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

class slide extends CI_Controller
{
	
	public $js;
	protected  $_id;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->_id = 0;
		$this->load->model('slide_model');
		$this->load->helper('common');
		$this->load->helper('form');
		
		if(!is_logged_in()) 
		{
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}
		else
		{
			$this->_current_request = 'edu_admin/'.$this->router->class.'/index';
			//check the sufficient access level 
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
        $this->page_title="Manage Slides";
		$data['meta_title']='Manage Slides';
		$data['title']        = $this->input->get('title'); 
		$data['status']       = ($this->input->get('status') != '')?$this->input->get('status'):''; 
		$num_records          = $this->slide_model->count_records( $data['title'],$data['status'] );
		$base_url             = base_url().'edu_admin/slide/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) )
		{
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->slide_model->get_records( $data['title'],$data['status'], $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/slide/index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	create 
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		insert the new record ,validate record
	
	*/
	function create(){
		
		$error = false;
		$errors = array();
		$image = $this->input->post('old_image');
		$this->load->helper('form');
		$this->page_title="Create Slide";
		$data['meta_title']='Create Slide';
		
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('csTitle', 'Title', 'trim|required');
			$this->form_validation->set_rules('csOrder', 'Order', 'trim|numeric');
			if( '' != $this->input->post('csTitle') )
				$this->form_validation->set_rules('csTitle', 'Title', 'callback_duplicate_check');
			
			if(('' == $image) && $_FILES['csImage']['name'] == '' )
			{
				$error = true;
				$errors[]='Please Upload the image ';
			}
			$this->form_validation->set_message('required', '%s must not be blank');
			if($error==false)
			{
				//upload the image 
				
				if($_FILES['csImage']['name'] != '')
				{
					
					$path = UPLOADS.'/slide';
					$res_response = upload_file('csImage',$path);
					if(is_array($res_response) && isset( $res_response['file_name'] ) )
					{
						// delete old file
						if( $image != '' )
						{
							//unlink the previously uploaded image  
							unlink( $path.'/'.$image );
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
								'csTitle'   => $this->input->post('csTitle'),
								'csOrder'   => $this->input->post('csOrder'),
								'csUrl'     => $this->input->post('csUrl'),
								'csImage'   => $image,
								'csPublish' => $this->input->post('csPublish'),
								);
					
				
				$this->slide_model->insert($data_array);
				set_flash_message('Slide details has been inserted successfully','success');
				redirect('edu_admin/slide/index');
			}
		}
		$data['image']=$image;
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/slide/form';
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
		
		$data['result']     = $this->_load_data($id);
		$this->_id          = $data['result']->csID;
		$image              = $data['result']->csImage;
		$this->page_title  = "Update ".$data['result']->csTitle;
		$data['meta_title'] = 'Update Slide';
		$data['image']      = $image;
		$this->js[]         = 'js/tinymce/tinymce.min.js';
		
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('csTitle', 'Title', 'trim|required');
			$this->form_validation->set_rules('csOrder', 'Order', 'trim|numeric');
			if( '' != $this->input->post('csTitle') )
				$this->form_validation->set_rules('csTitle', 'Title', 'callback_duplicate_check');
			if(('' == $image) && $_FILES['csImage']['name'] == '' ){
				$error = true;
				$errors[]='Please Upload the image ';
			}
			$this->form_validation->set_message('required', '%s must not be blank');
			if($error==false){
				if($_FILES['csImage']['name'] != '')
				{
					//upload the image 
					$path = UPLOADS.'/slide';
					$res_response = upload_file('csImage',$path);
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
								'csTitle'   => $this->input->post('csTitle'),
								'csOrder'   => $this->input->post('csOrder'),
								'csUrl'     => $this->input->post('csUrl'),
								'csImage'   => $image,
								'csPublish' => $this->input->post('csPublish'),
								);
					
					
					
				
				$this->slide_model->update($id,$data_array);
				set_flash_message('slide details has been updated successfully','success');
				redirect('edu_admin/slide/index');
			}
		}
		$data['image'] = $image;
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/slide/form';
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
		$data = $this->_load_data($id);
		@unlink( UPLOADS.'/slide/'.$data->csImage );
		$this->slide_model->delete($id);
		set_flash_message('Slide Details has been  successfully deleted  ','success');
		redirect('edu_admin/slide/index');
	}
	/**
		@Function Name:	_load_date
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		load the single record  
	
	*/
	function _load_data($id=0){
		if(!$id)
		{
			redirect('home/error404');
		}
		$data = $this->slide_model->get_single_record($id);
		if(empty($data))
		{
			redirect('home/error404');
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
		$id=$this->_id;
		if ($this->slide_model->check_duplicate($id,$title))
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