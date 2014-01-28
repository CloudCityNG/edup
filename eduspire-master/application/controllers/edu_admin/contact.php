<?php
/**
@Page/Module Name/Class: 		inventory.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 03 2013
@Purpose:		        		contain all controller functions contact forms 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact extends CI_Controller
{
	
	public $js;
	protected $_id;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->_id=0;
		$this->load->model('contact_model');
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
				set_flash_message('You don\'t have sufficient permission to access this page','warning');
				redirect('home/error');
			}
		}
		
	}
	
	/**
		@Function Name:	index
		@Author Name:	ben binesh
		@Date:			Sept, 03 2013
		@Purpose:		show the multiple records and filter 
	
	*/
	public function index()
	{
		
		if($this->input->get('export'))
		{
			$this->_export();
			return;
		}
		$data['meta_title']   = 'Contacts';
		$this->page_title     = 'Contacts';
		$data['layout']       = '';
		$data['name']         = $this->input->get('name'); 
		$data['email']        = $this->input->get('email'); 
		$num_records          = $this->contact_model->count_records( $data['name'], $data['email']);
		$base_url             = base_url().'edu_admin/contact/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) )
		{
			$start = 0;
		}
		$per_page            = PER_PAGE;
		$data['results']     = $this->contact_model->get_records( $data['name'], $data['email'] , $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/contact/index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	/**
		@Function Name:	view
		@Author Name:	ben binesh
		@Date:			Sept, 03 2013
		@Purpose:		view the record 
	
	*/
	function view($id=0)
	{
		$data['result']=$this->_load_data($id);
		$data['main'] = 'edu_admin/contact/view';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	delete 
		@Author Name:	ben binesh
		@Date:			Sept, 03 2013
		@Purpose:		validate and delete the record 
	
	*/
	function  delete($id=0)
	{
		$this->_load_data($id);
		$this->contact_model->delete($id);
		set_flash_message('Contact details has been successfully deleted ','success');
		redirect('edu_admin/contact/index');
	}
	
	/**
		@Function Name:	_load_data
		@Author Name:	ben binesh
		@Date:			Sept, 03 2013
		@Purpose:		load the single record  
	
	*/
	function _load_data($id=0)
	{
		if(!$id)
		{
			show_404('page');
		}
		$data = $this->contact_model->get_single_record($id);
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
		@Function Name:	_export
		@Author Name:	ben binesh
		@Date:			Sept, 03 2013
		@Purpose:		load the single record  
	
	*/
	
	function _export()
	{
		$name        = $this->input->get('name'); 
		$email        = $this->input->get('email'); 
		$results     = $this->contact_model->get_records( $name,$email, 0, -1,"*");
		if(empty($results))
		{
			set_flash_message('No records to export ','error');
			redirect('edu_admin/contact/index');
		}
				
		$export_array = array();
		//build columns 
		$export_array[] = array(
			'S.No',
			'Last Name',
			'First Name',
			'Email',
			'Contact Date',
		);
		$i=1;
		$course_id='';
		
		foreach($results as $result){
			
			$export_array[]=array(
				$i,
				$result->contLastName,
				$result->contFirstName,
				$result->contEmail,
				format_date($result->contDate,DATE_FORMAT.' '.TIME_FORMAT),
				
			);
			$i++;
		}
		
		
		$file_name = 'contact-details-'.time();
		$file_name=url_title($file_name,'-',TRUE);
		//check the report type choice ,default is csv 
		if(RPT_PDF == $this->input->get('export_type')){
			//if pdf 
			
			$this->load->library('pdf');
			$this->pdf->load_view('edu_admin/pdf',array(
					'results'=>$export_array
			));
			$this->pdf->render();
			$this->pdf->stream( $file_name.'.pdf');;
						
		}	
		else{
		
			$this->load->helper('csv');
			array_to_csv($export_array, $file_name.'.csv');
		}	
		
	}
	
}//End of class
//End of file 
