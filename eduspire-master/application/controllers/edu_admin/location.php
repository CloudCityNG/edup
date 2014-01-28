<?php 
/**
@Page/Module Name/Class: 		location.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 04 2013
@Purpose:		        		contain all controller functions for iu and districts 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Location extends CI_Controller {
	
	public $js;
	protected $_id;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->_id=0;
		$this->load->model('location_model');
		$this->load->helper('common');
		$this->load->helper('form');
		
		if(!is_logged_in())
		{
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}else
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
		@Author Name:	binesh
		@Date:			Sept, 04 2013
		@Purpose:		show the multiple records and filter 
	
	*/
	public function index()
	{
		/*
		if($this->input->get('export')){
			$this->_export_district();
			return;
		}
		*/
		$this->page_title   = "Manage District";
		$data['meta_title'] = "Manage District";
		
		$this->js[]='js/admin.js'		;
		$data['name']         = $this->input->get('name'); 
		$data['user_added']   = ($this->input->get('user_added') != '')?$this->input->get('user_added'):'';
		$data['status']       = ($this->input->get('status') != '')?$this->input->get('status'):''; 
		$data['iu_unit']      = $this->input->get('iu_unit'); 
		
		$num_records          = $this->location_model->count_districts( $data['name'], $data['user_added'], $data['status'] , $data['iu_unit']);
		$base_url             = base_url().'edu_admin/location/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = 30; 
		$data['results']     = $this->location_model->get_districts( $data['name'], $data['user_added'], $data['status'] , $data['iu_unit'] , $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/location/district_index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	district 
		@Author Name:	binesh
		@Date:			Aug, 16 2013
		@Purpose:		insert/update  record ,validate record
	
	*/
	function district($id=0){
		
		$error = false;
		$errors = array();
		$is_new_record=true;
		$this->load->helper('form');
		$this->js[]='js/tinymce/tinymce.min.js';
		$this->page_title='Create District';
		$data['meta_title']='Create District';
		if($id){
			$data['result']=$this->_load_district($id);
			$this->_id=$data['result']->disID;
            $this->page_title='Update '.$data['result']->disName;
			$data['meta_title']='Update District';
			$is_new_record=false;
			
		}
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('disName', 'District Name', 'trim|required');
			$this->form_validation->set_rules('disIuUnit', 'IU', 'trim|required');
			if( '' != $this->input->post('disName') )
				$this->form_validation->set_rules('disName', 'disName', 'callback_duplicate_distict_check');
			$this->form_validation->set_message('required', '%s must not be blank');
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'disName' => $this->input->post('disName'),
								'disPublish' => $this->input->post('disPublish'),
								'disUserAdded' => ADDED_BY_AMDIN,
								'disIuUnit' => $this->input->post('disIuUnit'),
								);
					
				if($is_new_record){
					$this->location_model->insert_district($data_array);
					set_flash_message('District  details has been inserted successfully','success');
				}else{
					$this->location_model->update_district($id,$data_array);
					set_flash_message('District  details has been update successfully','success');
				}
				redirect('edu_admin/location/index');
				
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/location/district_form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	delete 
		@Author Name:	binesh
		@Date:			Sept, 04 2013
		@Purpose:		validate and delete the record 
	
	*/
	function  delete_district($id=0){
		$this->_load_district($id);
		$is_delete=true;
		if($count=$this->db->where('urDistrictID',$id)->count_all_results('course_reservations')){
			$is_delete=false;
			set_flash_message('District details cannot be deleted','warning');
		}
		if($is_delete){
			$this->location_model->delete_district($id);
			set_flash_message('District details has been successfully deleted ','success');
		}
		redirect('edu_admin/location/index');
	}
	/**
		@Function Name:	_load_district
		@Author Name:	binesh
		@Date:			Sept, 04 2013
		@Purpose:		load the single record  
	
	*/
	function _load_district($id=0){
		if(!$id){
			redirect('home/error404');
		}
		$data = $this->location_model->get_single_district($id);
		if(empty($data)){
			redirect('home/error404');
		}else{
			return $data;
		}
	}
	
	
	
	/**
		@Function Name:	duplicate_distict_check
		@Author Name:	binesh
		@Date:			Aug, 16 2013
		@Purpose:		check the duplicate record in data base with same title  
	
	*/
	
	public function duplicate_distict_check($title='')
	{	$id=$this->_id;
		if ($this->location_model->check_duplicate_district($id,$title))
		{	
			$this->form_validation->set_message('duplicate_distict_check', 'The  "'.$title.'"  is already created');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	/**
		@Function Name:	_export_district
		@Author Name:	binesh
		@Date:			Sept, 04 2013
		@Purpose:		load the single record  
	
	*/
	
	function _export_district(){
		$name        = $this->input->get('name'); 
		$email        = $this->input->get('email'); 
		$results     = $this->location_model->get_records( $name,$email, 0, -1,"*");
		if(empty($results))
		{
			set_flash_message('No records to export ','error');
			redirect('edu_admin/location/index');
		}
				
		$export_array = array();
		//build columns 
		$export_array[] = array(
			'S.No',
			'Last Name',
			'First Name',
			'Email',
			'Message',
			'location Date',
		);
		$i=1;
		$course_id='';
		
		foreach($results as $result){
			
			$export_array[]=array(
				$i,
				$result->contLastName,
				$result->contFirstName,
				$result->contEmail,
				$result->contMessage,
				format_date($result->contDate,DATE_FORMAT.' '.TIME_FORMAT),
				
			);
			$i++;
		}
		
		
		$file_name = 'location-details-'.time();
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
	
/************************************************
*
******** IU Unit Related functions
*
**************************************************/	
	/**
		@Function Name:	iu_unit
		@Author Name:	binesh
		@Date:			Sept, 04 2013
		@Purpose:		show the multiple records and filter 
	
	*/
	public function iu_unit()
	{
		
		if($this->input->get('export')){
			$this->_export_iuUnit();
			return;
		}
		$this->page_title="Manage IUs";
		$data['meta_title']="Manage IUs";
		$data['name']         = $this->input->get('name'); 
		$data['status']       = ($this->input->get('status') != '')?$this->input->get('status'):''; 
		
		
		$num_records          = $this->location_model->count_iuUnits( $data['name'], $data['status']);
		$base_url             = base_url().'edu_admin/location/iu_unit';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = 40; 
		$data['results']     = $this->location_model->get_iuUnits( $data['name'], $data['status'] , $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/location/iuUnit_index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	create_iu_unit 
		@Author Name:	binesh
		@Date:			Aug, 16 2013
		@Purpose:		insert/update  record ,validate record
	
	*/
	function create_iu_unit($id=0){
		
		$error = false;
		$errors = array();
		$is_new_record=true;
		$this->load->helper('form');
		$this->page_title = 'Create IU';
		$data['meta_title']='Create IU ';                        
		if($id){
			$data['result']=$this->_load_iuUnit($id);
			$this->_id=$data['result']->iuID;
            $this->page_title ='Update '.$data['result']->iuName;
			$data['meta_title']='Update IU ';
			$is_new_record=false;
			
		}
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('iuName', 'IU Name', 'trim|required');
			if( '' != $this->input->post('iuName') )
				$this->form_validation->set_rules('iuName', 'IU Name', 'callback_duplicate_iuUnit_check');
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
								'iuName' => $this->input->post('iuName'),
								'iuPublish' => $this->input->post('iuPublish'),
								);
					
				if($is_new_record){
					$this->location_model->insert_iuUnit($data_array);
					set_flash_message('IU details has been inserted successfully','success');
				}else{
					$this->location_model->update_iuUnit($id,$data_array);
					set_flash_message('IU  details has been updated successfully','success');
				}
				redirect('edu_admin/location/iu_unit');
				
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/location/iuUnit_form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	duplicate_iuUnit_check
		@Author Name:	binesh
		@Date:			Aug, 16 2013
		@Purpose:		check the duplicate record in data base with same title  
	
	*/
	
	public function duplicate_iuUnit_check($title='')
	{	$id=$this->_id;
		if ($this->location_model->check_duplicate_iuUnit($id,$title))
		{	
			$this->form_validation->set_message('duplicate_iuUnit_check', 'The  "'.$title.'"  is already created');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	/**
		@Function Name:	delete_iu_unit 
		@Author Name:	binesh
		@Date:			Sept, 04 2013
		@Purpose:		validate and delete the record 
	
	*/
	function  delete_iu_unit($id=0){
		$is_delete=TRUE;
		$this->_load_iuUnit($id);
		
		if($this->db->where('disIuUnit',$id)->count_all_results('district')){
			$is_delete=FALSE;
			set_flash_message('IU  cannot be deleted ','error');
		}
		if($is_delete)
		{
			$this->location_model->delete_iuUnit($id);
			set_flash_message('IU  details has been successfully deleted ','success');
		}
		redirect('edu_admin/location/iu_unit');
	}
	/**
		@Function Name:	_load_district
		@Author Name:	binesh
		@Date:			Sept, 04 2013
		@Purpose:		load the single record  
	
	*/
	function _load_iuUnit($id=0){
		if(!$id){
			redirect('home/errpr404');
		}
		$data = $this->location_model->get_single_iuUnit($id);
		if(empty($data)){
			redirect('home/errpr404');
		}else{
			return $data;
		}
	}
	
	/**
		@Function Name:	_export_district
		@Author Name:	binesh
		@Date:			Sept, 04 2013
		@Purpose:		load the single record  
	
	*/
	
	function _export_iuUnit(){
		$name        = $this->input->get('name'); 
		$email        = $this->input->get('email'); 
		$results     = $this->location_model->get_records( $name,$email, 0, -1,"*");
		if(empty($results))
		{
			set_flash_message('No records to export ','error');
			redirect('edu_admin/location/index');
		}
				
		$export_array = array();
		//build columns 
		$export_array[] = array(
			'S.No',
			'Last Name',
			'First Name',
			'Email',
			'Message',
			'location Date',
		);
		$i=1;
		$course_id='';
		
		foreach($results as $result){
			
			$export_array[]=array(
				$i,
				$result->contLastName,
				$result->contFirstName,
				$result->contEmail,
				$result->contMessage,
				format_date($result->contDate,DATE_FORMAT.' '.TIME_FORMAT),
				
			);
			$i++;
		}
		
		
		$file_name = 'location-details-'.time();
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
			
			/*
			$this->load->view('edu_admin/pdf',array(
					'results'=>$export_array
			));
			*/
		}	
		else{
		
			$this->load->helper('csv');
			array_to_csv($export_array, $file_name.'.csv');
		}	
		
	}	
	
	
	/**
		@Function Name:	district_approve
		@Author Name:	ben binesh
		@Date:			Oct,01  2013
		@Purpose:		apporve the user added district 
	
	*/
	
	function district_approve($id=0){
		$district=$this->_load_district($id);
		$update_array=array(
			'disPublish'=>STATUS_PUBLISH,
			'disUserAdded'=>ADDED_BY_AMDIN,
		);
		$this->location_model->update_district($id,$update_array);
		set_flash_message('District has been successfully approved','success');
		redirect('edu_admin/location/index');
	}
	
	/**
		@Function Name:	district_approve
		@Author Name:	ben binesh
		@Date:			Oct,01  2013
		@Purpose:		apporve the user added district 
	
	*/
	
	function district_merge($id=0){
		$data=array();
		$error=false;	
		$data['district']=$this->_load_district($id);
		
		$data['districts']= $this->location_model->get_districts( '', '', '' , '', 0 , -1 );
		if(count($_POST)){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('district', 'District', 'trim|required');	
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$district_id =$this->input->post('district');
				
				//update the corresponding tables 
				$this->db->query(
					"UPDATE course_reservations
					 SET 
						urDistrictID = $district_id,
						urDistrict   = '$district_id'
					 WHERE urDistrictID = $id
				");
				
				//update the corresponding tables 
				$this->db->query(
					"UPDATE users_profiles
					SET 
						districtID = $district_id,
						districtAffiliation   = '$district_id'
						WHERE districtID = $id
				");
				
				
				//delete district 
				$this->location_model->delete_district($id);
				
				
				set_flash_message('District has been successfully approved','success');
				redirect('edu_admin/location/index');
			}
		}	
		
		$data['main'] = 'edu_admin/location/district_merge';
		$this->load->vars($data);
		$this->load->view('template');
		
	}
	
	
	
	
	
	
}//End of class

/* End of file */
