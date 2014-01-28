<?php
/**
@Page/Module Name/Class: 		course_genres.php
@Author Name:			  		ben binesh
@Date:					  		Aug, 16 2013
@Purpose:		         		Contain all controller functions for the course schedule
								and dates 
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

class Course_genres extends CI_Controller {
	
	public $js;
	protected  $_id;
	protected $_current_request='';
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->_id = 0;
		$this->load->model('course_genres_model');
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
		$data['layout']='';
        $this->page_title="Course Genres";
		$data['meta_title']='Course Genres';
		$data['title']            =  $this->input->get('title'); 
		$data['status']           = ($this->input->get('status') != '')?$this->input->get('status'):''; 
		$num_records              = $this->course_genres_model->count_records($data['title'],$data['status']);
		$base_url                 = base_url().'edu_admin/course_genres/index';
		$start                    = $this->uri->segment($this->uri->total_segments());
		if(!is_numeric($start)){
			$start = 0;
		}
		$per_page                 = PER_PAGE; 
		$data['results']         = $this->course_genres_model->get_records( $data['title'], $data['status'] , $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		
		$data['main'] = 'edu_admin/course_genres/index';
		
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
		
		$error=false;
		$errors=array();
		$this->load->helper('form');
        $this->page_title="Create Course genre";
		$data['meta_title']='Create Course genre';
		$this->js[]='js/tinymce/tinymce.min.js';
		$image = $this->input->post('old_image');
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('cgTitle', 'Genre Title', 'trim|required');
			if('' != $this->input->post('cgTitle') )
				$this->form_validation->set_rules('cgTitle', 'Genre Title', 'callback_duplicate_check');
			
			$this->form_validation->set_rules('cgCourseCredits', 'Course Credits', 'trim|required|numeric');
			$this->form_validation->set_rules('cgDisplayOrder', 'Display Order', 'trim|numeric');
			$this->form_validation->set_message('required', '%s must not be blank');
			if($error==false)
			{
				//upload the image 
				if($_FILES['cgImage']['name'] != '')
				{
					$path = UPLOADS.'/course';
					$res_response = upload_file('cgImage',$path);
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
								'cgTitle' => $this->input->post('cgTitle'),
								'cgCourseCredits' => $this->input->post('cgCourseCredits'),
								'cgCourseNotes' => $this->input->post('cgCourseNotes'),
								'cgDescription' => $this->input->post('cgDescription'),
								'cgDisplayOrder' => $this->input->post('cgDisplayOrder'),
								'cgPublish' => $this->input->post('cgPublish'),
								'cgImage' => $image,
								
								);
					
				
				$this->course_genres_model->insert($data_array);
				set_flash_message('Course type details has been inserted successfully','success');
				redirect('edu_admin/course_genres/index');
			}
		}
		$data['image']=$image;
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/course_genres/form';
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
        $this->page_title="Update ".$data['result']->cgTitle;
		$data['meta_title']='Update Course Genres';                
		$this->_id = $id;
		$image = $data['result']->cgImage;
		$this->js[]='js/tinymce/tinymce.min.js';
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('cgTitle', 'Genre Title', 'trim|required');
			if('' != $this->input->post('cgTitle') )
				$this->form_validation->set_rules('cgTitle', 'Genre Title', 'callback_duplicate_check');
			$this->form_validation->set_rules('cgCourseCredits', 'Course Credits', 'trim|required|numeric');
			$this->form_validation->set_rules('cgDisplayOrder', 'Display Order', 'trim|numeric');
			$this->form_validation->set_message('required', '%s must not be blank');
			if($error==false)
			{
				//upload the image 
				
				if($_FILES['cgImage']['name'] != '')
				{
					$path = UPLOADS.'/course';
					$res_response = upload_file('cgImage',$path);
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
								'cgTitle' => $this->input->post('cgTitle'),
								'cgCourseCredits' => $this->input->post('cgCourseCredits'),
								'cgCourseNotes' => $this->input->post('cgCourseNotes'),
								'cgDescription' => $this->input->post('cgDescription'),
								'cgDisplayOrder' => $this->input->post('cgDisplayOrder'),
								'cgPublish' => $this->input->post('cgPublish'),
								'cgImage' => $image,
								);
					
				
				$this->course_genres_model->update($id,$data_array);
				set_flash_message('Course type details has been updated successfully','success');
				redirect('edu_admin/course_genres/index');
			}
		}
		$data['image']=$image;
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/course_genres/form';
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
		$is_delete = true;
		if($this->db->where('cdGenre',$id)->count_all_results('course_definitions')){
			$is_delete = false;
			set_flash_message('Course type cannot be deleted as there are definitions associated with it ','error');
		}
		
		if($is_delete)
		{
			$this->course_genres_model->delete($id);
			set_flash_message('Course type has been successfully deleted ','success');
			
		}
		redirect('edu_admin/course_genres/index');
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
			redirect('home/error404');
		}
		$data = $this->course_genres_model->get_single_record($id);
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
		if ($this->course_genres_model->check_duplicate($id,$title))
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