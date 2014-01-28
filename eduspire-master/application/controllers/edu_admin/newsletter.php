<?php 
/**
@Page/Module Name/Class: 		newsletter.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 12  2013
@Purpose:		        		controller logic for newsletter
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Newsletter extends CI_Controller {
	
	public $js;
	protected $_id;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->_id=0;
		$this->load->model('newsletter_model');
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
		@Author Name:	binesh
		@Date:			Sept, 03 2013
		@Purpose:		show the multiple records and filter 
	
	*/
	public function index()
	{
		$data=array();
		if($this->input->get('export')){
			$this->_export();
			return;
		}
		if($this->input->post('mass_action')){
			$this->_mass_action();
			return ;
		}
		$data['meta_title'] = 'Newsletter Subscription';
		$this->page_title   = 'Newsletter Subscription';
		
 		$this->js[] = 'js/admin.js';
		$data['name']         = $this->input->get('name'); 
		$data['email']        = $this->input->get('email'); 
		$num_records          = $this->newsletter_model->count_records( $data['name'], $data['email']);
		$base_url             = base_url().'edu_admin/newsletter/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = '20'; 
		$data['results']     = $this->newsletter_model->get_records( $data['name'], $data['email'] , $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/newsletter/index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	/**
		@Function Name:	view
		@Author Name:	binesh
		@Date:			Sept, 03 2013
		@Purpose:		view the record 
	
	*/
	function view($id=0){
		$data['result']=$this->_load_data($id);
		$data['main'] = 'edu_admin/newsletter/view';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	delete 
		@Author Name:	binesh
		@Date:			Sept, 03 2013
		@Purpose:		validate and delete the record 
	
	*/
	function  delete($id=0){
		$this->_load_data($id);
		$this->newsletter_model->delete($id);
		set_flash_message('User subscripton  details has been successfully deleted ','success');
		redirect('edu_admin/newsletter/index');
	}
	/**
		@Function Name:	_load_date
		@Author Name:	binesh
		@Date:			Sept, 03 2013
		@Purpose:		load the single record  
	
	*/
	function _load_data($id=0){
		if(!$id){
			show_404('page');
		}
		$data = $this->newsletter_model->get_single_record($id);
		if(empty($data)){
			show_404('page');
		}else{
			return $data;
		}
	}
	
	/**
		@Function Name:	_export
		@Author Name:	ben binesh
		@Date:			Sept, 03 2013
		@Purpose:		load the single record  
	
	*/
	
	function _export(){
		$name        = $this->input->get('name'); 
		$email        = $this->input->get('email'); 
		$results     = $this->newsletter_model->get_records( $name,$email, 0, -1,"*");
		if(empty($results))
		{
			set_flash_message('No records to export ','error');
			redirect('edu_admin/newsletter/index');
		}
				
		$export_array = array();
		//build columns 
		$export_array[] = array(
			'S.No',
			'Last Name',
			'First Name',
			'Email',
			'School District',
			'Hear about us',
			'Subject Area',
			'Grade Level',
			'IU',
			'Signup Date',
		);
		$i=1;
		$course_id='';
		
		foreach($results as $result){
			$school_district='';
			if(is_numeric($result->newsSchoolDistrict))
			{
				$school_district = get_single_value('district','disName','disID = '.$result->newsSchoolDistrict) ;
			}
			else
			{
				$school_district = $result->newsSchoolDistrict; 
			}
			$hear_about='';
			if(4 == $result->newsReferralMethod){
				$hear_about = $result->newsReferralMethodOther;
			}else{
				$hear_about = show_hearabout_text($result->newsReferralMethod);
			}
			
			$export_array[]=array(
				$i,
				$result->newsLastName,
				$result->newsFirstName,
				$result->newsEmail,
				$school_district,
				$hear_about,
				$result->newsTeachesSubject,
				$result->newsGradeLevel,
				 $result->newsIU,
				format_date($result->newsSignupDate,DATE_FORMAT.' '.TIME_FORMAT),
				
			);
			$i++;
		}
		
		
		$file_name = 'newsletter-details-'.time();
		$file_name=url_title($file_name,'-',TRUE);
		//generete csv report 
		$this->load->helper('csv');
		array_to_csv($export_array, $file_name.'.csv');
		
		
	}
	
	
	/**
		@Function Name:	_mass_action
		@Author Name:	binesh
		@Date:			Sept, 02 2013
		@Purpose:		handle the mass action request 
	
	*/
	
	public function _mass_action(){
		$chk_ids=$this->input->post('chk_ids');
		if(!empty($chk_ids) && count($chk_ids > 0)){
			$ids =$chk_ids;
			if($this->input->post('delete')){
				$this->newsletter_model->delete($ids);	
				set_flash_message('Users subscription  has been successfully deleted','success');	
			}
			
		}
		redirect('edu_admin/newsletter/index');
		
	}
	
/*********************************
* Email Unsubscribe emails  functions 
	
********************************/
	/**
		@Function Name:	unsubscribe
		@Author Name:	binesh
		@Date:			Sept, 17 2013
		@Purpose:		show the multiple records and filter 
	
	*/
	public function unsubscribe()
	{
		
		if($this->input->post('mass_action')){
			$this->_mass_action_unsubscribe();
			return ;
		}
		if($this->input->get('export'))
		{
			$this->_export_unsubscribe();
			return;
		}
		$this->js[] = 'js/admin.js';
		$data['meta_title'] = 'Newsletter Unubscription';
		$this->page_title   = 'Newsletter Unsubscription';
		$data['email']        = $this->input->get('email'); 
		$num_records          = $this->newsletter_model->count_unsubscribe_records($data['email']);
		$base_url             = base_url().'edu_admin/newsletter/unsubscribe';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = 30; 
		$data['results']     = $this->newsletter_model->get_unsubscribe_records($data['email'] , $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/newsletter/unsubscribe';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	district 
		@Author Name:	binesh
		@Date:			Aug, 16 2013
		@Purpose:		insert/update  record ,validate record
	
	*/
	function unsubscribe_email($id=0){
		
		$error = false;
		$errors = array();
		$is_new_record=true;
		$this->load->helper('form');
		$this->js[]='js/tinymce/tinymce.min.js';
		$data['meta_title'] = 'Add Unsubscription';
		$this->page_title   = 'Add Unsubscription';
		
		if($id){
			$data['result']=$this->_load_unsubcribe_data($id);
			$is_new_record=false;
			$data['meta_title'] = 'Add Unsubscription';
			$this->page_title   = 'Add Unsubscription';
		}
		
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('uEmail', 'Email', 'trim|required!valid_email');
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'uEmail' => $this->input->post('uEmail'),
								'uTimestamp' =>date('Y-m-d H:i:s'), 
								);
					
				if($is_new_record){
					$this->newsletter_model->insert_subscribe($data_array);
					set_flash_message('Unsubcribe email  has been inserted successfully','success');
				}else{
					$this->newsletter_model->update_subscribe($id,$data_array);
					set_flash_message('Unsubcribe email  details has been update successfully','success');
				}
				redirect('edu_admin/newsletter/unsubscribe');
				
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/newsletter/unsubscribe_form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	
	
	/**
		@Function Name:	_mass_action_unsubscribe
		@Author Name:	ben binesh
		@Date:			Sept, 17 2013
		@Purpose:		handle the mass action request 
	
	*/
	
	public function _mass_action_unsubscribe(){
		$chk_ids=$this->input->post('chk_ids');
		if(!empty($chk_ids) && count($chk_ids > 0)){
			$ids =$chk_ids;
			if($this->input->post('delete')){
				$this->newsletter_model->delete_unsubscribe($ids);	
				set_flash_message('Unsubscription emails   has been successfully deleted','success');	
			}
			
		}
		redirect('edu_admin/newsletter/unsubscribe');
		
	}
	
	/**
		@Function Name:	delete_ubsubscribe 
		@Author Name:	binesh
		@Date:			Sept, 17 2013
		@Purpose:		validate and delete the record 
	
	*/
	function  delete_ubsubscribe($id=0){
		$this->_load_unsubcribe_data($id);
		$this->newsletter_model->delete_unsubscribe($id);
		set_flash_message('Unsubscription email has been successfully deleted ','success');
		redirect('edu_admin/newsletter/unsubscribe');
	}
	
	
	/**
		@Function Name:	_load_unsubcribe_data
		@Author Name:	ben binesh
		@Date:			Sept, 17 2013
		@Purpose:		load the single record  
	
	*/
	function _load_unsubcribe_data($id=0){
		if(!$id){
			show_404('page');
		}
		$data = $this->newsletter_model->get_single_unsubscribe($id);
		if(empty($data)){
			show_404('page');
		}else{
			return $data;
		}
	}
	
	
	/**
		@Function Name:	_export_unsubscribe
		@Author Name:	ben binesh
		@Date:			Dec, 17 2013
		@Purpose:		load the single record  
	
	*/
	
	function _export_unsubscribe()
	{
		$email        = $this->input->get('email'); 
		$results     = $this->newsletter_model->get_unsubscribe_records($email, 0,-1);
		if(empty($results))
		{
			set_flash_message('No records to export ','error');
			redirect('edu_admin/newsletter/unsubscribe');
		}
				
		$export_array = array();
		//build columns 
		$export_array[] = array(
			'S.No',
			'Email',
			'Date',
		);
		$i=1;
		
		
		foreach($results as $result){
			
			$export_array[]=array(
				$i,
				$result->uEmail,
				format_date($result->uTimestamp,DATE_FORMAT.''.TIME_FORMAT),
			);
			$i++;
		}
		
		$file_name = 'newsletter-unsubscription-details-'.time();
		$file_name=url_title($file_name,'-',TRUE);
		//generete csv report 
		$this->load->helper('csv');
		array_to_csv($export_array, $file_name.'.csv');
		
		
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */