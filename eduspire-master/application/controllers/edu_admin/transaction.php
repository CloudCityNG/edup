<?php 
/**
@Page/Module Name/Class: 		transaction.php
@Author Name:			 		ben binesh
@Date:					 		Sept,16 2013
@Purpose:		        		contain all controller functions for the transaction
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

class transaction extends CI_Controller 
{
	
	public $js;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->load->model('checkout_model');
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
		@Author Name:	ben binesh
		@Date:			Sept  16 2013
		@Purpose:		show the multiple records and filter 
	
	*/
	public function index()
	{
		
		if($this->input->get('export'))
		{
			$this->_export();
			return;
		}
		$data['meta_title']   = 'Transactions';
		$this->page_title     = "Transactions";
		$data['layout']       = '';
		$data['name']         = $this->input->get('name'); 
		$data['email']        = $this->input->get('email'); 
		$data['txn_id']       = $this->input->get('txn_id'); 
		$data['payment_date'] = $this->input->get('payment_date'); 
		$data['status']       = ($this->input->get('status')  != '' )?$this->input->get('status'):''; 
		$num_records          = $this->checkout_model->count_records( $data['name'], $data['email'], $data['txn_id'],$data['payment_date'],   $data['status'] );
		$base_url             = base_url().'edu_admin/transaction/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) )
		{
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->checkout_model->get_records($data['name'], $data['email'], $data['txn_id'],$data['payment_date'],   $data['status'],  $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main'] = 'edu_admin/transaction/index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	/**
		@Function Name:	view
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		load the single transaction details 
	
	*/
	function view($id=0)
	{
		
		
		$data=array();
		$data['result']=$this->_load_data($id);
		$data['main'] = 'edu_admin/transaction/view';
		$data['meta_title']   = 'View transactions';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	/**
		@Function Name:	_load_data
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		load the single record  
	
	*/
	function _load_data($id=0){
		if(!$id)
		{
			redirect('home/error_404');
		}
		$data = $this->checkout_model->get_data_from_transaction($id);
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
		@Function Name:	_export 
		@Author Name:	ben binesh
		@Date:			Sept, 16 2013
		@Purpose:		export transaction data to csv/pdf 
	
	*/
	
	function _export()
	{
		$name         = $this->input->get('name'); 
		$email        = $this->input->get('email'); 
		$txn_id       = $this->input->get('txn_id'); 
		$payment_date = $this->input->get('payment_date'); 
		$status       = ($this->input->get('status')  != '' )?$this->input->get('status'):''; 
		$results          = $this->checkout_model->get_records( $name, $email, $txn_id,$payment_date,   $status,0, -1);
		
		if(empty($results))
		{
			set_flash_message('No records to export ','error');
			redirect('edu_admin/transaction/index');
		}
				
		$export_array = array();
		//build columns 
		$export_array[] = array(
			'S.No',
			'Transaction ID / Invoice',
			'Name Address',
			'Email',
			'Item',
			'Amount',
			'Status',
			'Payment Date',
			
		);
		$i=1;
		$course_id='';
		
		foreach($results as $result){
			
			$export_array[]=array(
				$i,
				$result->txn_id.'/'.$result->ppID,
				$result->first_name.' '.$result->first_name.'\n'.$result->address_street.'\n'.
				$result->address_city.','.$result->address_state.' '.$result->address_zip, 
				$result->payer_email,
				$result->item_name1,
				$result->payment_gross,
				$result->payment_status,
				$result->payment_date,
				
			);
			$i++;
		}
		
		$file_name = 'transaction-details-'.time();
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
		else
		{
			//generate csv file 
			$this->load->helper('csv');
			array_to_csv($export_array, $file_name.'.csv');
		}	
		
	}
	
}

/* End of file transaction.php */
/* Location: ./application/controllers/edu_admin/transaction.php */