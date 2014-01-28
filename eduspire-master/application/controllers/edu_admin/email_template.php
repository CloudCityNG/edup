<?php
/**
@Page/Module Name/Class: 		email_template.php
@Author Name:			 		Janet Rajani
@Date:					        Sep,2 2013
@Purpose:		        	    Managing all emails
@Table referred:				email_templates
@Table updated:					email_templates
@Most Important Related Files	NIL
Chronological development
//***********************************************************************************
//| Ref No.|  Author name	 | Date	             | Severity | Modification description
//***********************************************************************************
//RF1	  |   Alan Anil	  | Oct 11  | Function for updating cron email templates.
//*********************************************************************************** 

 */
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class email_template extends CI_Controller
{
    public function __construct()
	{
        parent::__construct();
        use_ssl(FALSE);
		$js=array();
		$this->load->model('email_template_model');
		$this->load->helper('common');
		$this->load->helper('form');
        if(!is_logged_in())
		{
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}
		else
		{ 
			//check the sufficient access level 
			$this->_current_request = 'edu_admin/'.$this->router->class.'/index';
			if(!is_allowed($this->_current_request))
			{		
				set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
				redirect('home/error');
			}
        } 	
     }
        /**
		@Function Name:         index
		@Author Name:           Janet rajani
		@Date:			        Sep,2 2013
		@Purpose:		        show the multiple records and filter 
	
	*/
	public function index()
	{
		$data=array();
		$data['layout']       = '';
        $data['etID']         = $this->input->get('etID'); 
		$data['etTitle']      = $this->input->get('etTitle'); 
        $data['etSubject']    = $this->input->get('etSubject'); 
		$num_records          = $this->email_template_model->count_records( $data['etTitle'],$data['etSubject'] );
		$base_url             = base_url().'edu_admin/email_template/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = PER_PAGE;
		$this->page_title='Manage Email Templates';
		$data['meta_title']="Email Templates";
		$data['results']     = $this->email_template_model->get_records( $data['etTitle'],$data['etSubject'], $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/email_template/index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:         create 
		@Author Name:           Janet rajani
		@Date:			Sep,2 2013
		@Purpose:		insert the new record ,validate recored
	
	*/
	function create(){
		
		$error = false;
		$errors = array();
		$data=array();
		$this->load->helper('form');
		$this->js[]='js/tinymce/tinymce.min.js';
		$this->page_title ='Create Email Template';
		$data['meta_title']='Create Email Template ';
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('etTitle', 'Title', 'trim|required');
			$this->form_validation->set_rules('etSubject', 'Subject', 'trim|required');
                        $this->form_validation->set_rules('etCopy', 'Description', 'trim|required');
			if( '' != $this->input->post('etSubject') )
				$this->form_validation->set_rules('etSubject', 'Subject', 'callback_duplicate_check');
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'etTitle' => $this->input->post('etTitle'),
								'etCopy' => $this->input->post('etCopy'),
								'etSubject' => $this->input->post('etSubject')
								);
					
				
				$this->email_template_model->insert($data_array);
				set_flash_message('Email template created successfully','success');
				redirect('edu_admin/email_template/index');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/email_template/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:         update 
		@Author Name:           Janet rajani
		@Date:			Sep,2 2013
		@Purpose:		validate and update the record
	
	*/
	function update($id=0){
		$error=false;
		$errors=array();
		
		$data['result']=$this->_load_data($id);
		$this->page_title ='Update '.$data['result']->etTitle;
		$data['meta_title']='Update Email Template';
		$this->js[]='js/tinymce/tinymce.min.js';
		
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('etTitle', 'Title', 'trim|required');
			$this->form_validation->set_rules('etSubject', 'Subject', 'trim|required');
                        $this->form_validation->set_rules('etCopy', 'Description', 'trim|required');
			if ($this->form_validation->run() == TRUE && $error==false  )
                        {
				$data_array = array(
								'etTitle' => $this->input->post('etTitle'),
								'etSubject' => $this->input->post('etSubject'),
								'etCopy' => $this->input->post('etCopy')
								);
					
					
				
				$this->email_template_model->update($id,$data_array);
				set_flash_message('Email template has been updated successfully','success');
				redirect('edu_admin/email_template/index');
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/email_template/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
        /**
		@Function Name:	_load_date
		@Author Name:	Janet Rajani
		@Date:		Sep 2, 2013
		@Purpose:       load the single record  
	
	*/
	function _load_data($id=0){
		if(!$id){
			show_404('page');
		}
		$data = $this->email_template_model->get_single_record($id);
		if(empty($data)){
			show_404('page');
		}else{
			return $data;
		}

	}
	// RF1 Start
	/**
		@Function Name:         update_cron_email 
		@Author Name:           Alan Anil
		@Date:			        Sep,7 2013
		@Purpose:		        validate and update the record
	
	*/
	function update_cron_email($id=0){
		$this->_current_request = 'edu_admin/'.$this->router->class.'/index' ; 
		if(!is_allowed($this->_current_request))
		{		
			set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
			redirect('home/error');
		}
		else
		{
			$error=false;
			$errors=array();
			$data=array();
			$data['meta_title']='Update Email Template';
			$activeVal = '';
			if(!$id){
				show_404('page');
			}
			$data['result'] = $this->email_template_model->get_single_cron_record($id);
			$this->js[]='js/tinymce/tinymce.min.js'; 
			if(count($_POST)>0){
				$this->load->library('form_validation'); 
				$this->form_validation->set_rules('aeSubject', 'Subject', 'trim|required');
				$this->form_validation->set_rules('aeTriggerDateField', 'Trigger Date', 'trim|required');
				$this->form_validation->set_rules('aeTriggerDateOffset', 'Trigger Date Offset', 'trim|required');
				$this->form_validation->set_rules('aeTarget', 'E-mail target', 'trim|required');
				$this->form_validation->set_rules('aeCopyMessage', 'Message', 'trim|required'); 
				if ($this->form_validation->run() == TRUE && $error==false  )
							{
							$userId = $this->session->userdata('user_id');
							$actMin = $this->input->post('aeEmailMin');
							if($actMin == '0') {
							$actMin = '00';  ;
							}
							$activationT    = $this->input->post('aeEmailTime').':'.$actMin.' '.$this->input->post('aeEmailAP'); 
							if($this->input->post('activationDate') == ' ') {  
							$finalAppTime   =  '00:00:00'; 
							}
							else {
								if($this->input->post('aeEmailAP') == '0' || $this->input->post('aeEmailTime') == '0') { 
								$finalAppTime   =  'NULL'; 
								}
								else {
								$appliedDate    = date("Y-m-d").' '.$activationT; 
								$finalAppTime   = date("H:i:s", strtotime($appliedDate));
								} 
							}
							if($this->input->post('active') == '' || $this->input->post('active') == 0) { 
								$activeVal = 0;
							}
							else {
								$activeVal = 1;
							}
					$data_array = array(
									'aeActive' => $activeVal,
									'aeSubject' => $this->input->post('aeSubject'),
									'aeTriggerField' => $this->input->post('aeTriggerDateField'),
									'aeTriggerDays' => $this->input->post('aeTriggerDateOffset'),
									'aeTime' => $finalAppTime, 
									'aeTarget' => $this->input->post('aeTarget'),
									'aeCopy' => $this->input->post('aeCopyMessage'),
									'aeEdited' =>date('Y-m-d H:i:s'),
									'aeEditedBy' => $userId 
									);
					 
					$this->email_template_model->update_cron_email($id,$data_array);
					set_flash_message('Email template has been updated successfully','success');
					redirect('edu_admin/email_template/index');
				}
			}
			$data['errors'] = $errors;
			$data['main'] = 'edu_admin/email_template/cronform';
			$this->load->vars($data);
			$this->load->view('template');
		}
	}
	// RF1 End.
	
}
?>
