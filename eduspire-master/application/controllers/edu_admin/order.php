<?php 
/**
@Page/Module Name/Class: 		order.php
@Author Name:			 		ben binesh
@Date:					 		Sept,16 2013
@Purpose:		        		contain all controller functions for the order
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

class Order extends CI_Controller
{
	
	public $js;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->load->model('order_model');
		$this->load->model('inventory_model');
		$this->load->helper('common');
		$this->load->helper('form');
		
		if(!is_logged_in()) {
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}else{
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
		$data['meta_title']='Order: Ipads';
		$this->page_title='Order: Ipads';
		$data['layout']       = '';
		$data['name']         = $this->input->get('name'); 
		$data['email']        = $this->input->get('email'); 
		$data['order_date']   = $this->input->get('order_date'); 
		$data['status']       = ($this->input->get('status')  != '' )?$this->input->get('status'):'';
		$data['course_id']    = $this->input->get('course_id');		
		if($data['course_id'])
		{
			$this->load->model('course_schedule_model');
			$data['course']=$this->course_schedule_model->get_course_detail($data['course_id']);
		}
		
		$num_records          = $this->order_model->count_records( $data['name'], $data['email'],$data['status'],$data['order_date'],PRODUCT_TYPE_IPAD,$data['course_id']);
		$base_url             = base_url().'edu_admin/order/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) )
		{
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->order_model->get_records($data['name'], $data['email'],$data['status'],$data['order_date'],  $start , $per_page,PRODUCT_TYPE_IPAD,$data['course_id'] );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		
		
		$data['main'] = 'edu_admin/order/index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	view
		@Author Name:	ben binesh
		@Date:			Sept, 17 2013
		@Purpose:		load record for view 
	
	*/
	function  view($id=0)
	{
		
		$data['result'] = $this->_load_data($id);
		$data['main']  = 'edu_admin/order/view';
		$this->load->vars($data);
		$this->load->view('template');
		
	}
	
	/**
		@Function Name:	_load_data
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@Purpose:		load the single record  
	
	*/
	function _load_data($id=0)
	{
		if(!$id){
			redirect('home/error_404');
		}
		$data = $this->order_model->get_single_record($id);
		if(empty($data)){
			redirect('home/error_404');
		}else{
			return $data;
		}
		
		
	}
	
	/**
		@Function Name:	delete 
		@Author Name:	ben binesh
		@Date:			Sept, 17 2013
		@Purpose:		validate and delete the record 
	
	*/
	function  delete($id=0)
	{
		$is_delete = true;
		//additional consistency check will come here 
		$data = $this->_load_data($id);
		$order_number=$data->orderNumber;
		if($is_delete)
		{
			$ledger_where=array();
			$ledger_where['alUserID']=$data->orderCustID;
			$ledger_where['alAssignType']=ASGN_IPAD_CONFIGURATION;
			
			
			if($data->orderCourseID)
				$ledger_where['alCnfID']=$data->orderCourseID;
			
			$ledger_id = get_single_value('assignment_ledger','alID',$ledger_where);
			
			$this->order_model->delete($id);
			$this->order_model->delete_item_by_ordernumber($order_number);
			
			//remove the ledger entry 
			if($ledger_id)
			{
				$this->load->model('assignment_model');
				$this->assignment_model->delete_ledger($ledger_id);
				
			}
			//update the transaction data 
			if($data->upgrade_id) //check if member selected the upgrade 
			{
			
				$transaction_where=array();
				$transaction_where['payer_email']    = $data->orderEmail;
				$transaction_where['product_type']   = PRODUCT_TYPE_IPAD;
				$transaction_where['payment_status'] = PAYMENT_COMPLETED;
				$transaction_where['item_number1'] = $data->upgrade_id;
			
			
				$transaction_id = get_single_value('pp_transactions','ppID',$transaction_where);
				if($transaction_id){
					$this->load->model('checkout_model');
					//update the transaction 
					$this->checkout_model->update_transaction($transaction_id,array(
						'payment_status'=>PAYMENT_REFUNDED
					));
				}	
			}
			
			//set the transaction as refunded
			
			
			set_flash_message('Order details has been successfully deleted','success');
		}
		redirect('edu_admin/order/index');
		
	}
	
	
	/**
		@Function Name:	_export 
		@Author Name:	ben binesh
		@Date:			Sept, 17 2013
		@Purpose:		export order data to csv/pdf 
	
	*/
	
	function _export()
	{
		$name         = $this->input->get('name'); 
		$email        = $this->input->get('email'); 
		$order_date = $this->input->get('payment_date'); 
		$status       = ($this->input->get('status')  != '' )?$this->input->get('status'):''; 
		$results          = $this->order_model->get_records( $name, $email,$status, $order_date,0, -1);
		if(empty($results))
		{
			set_flash_message('No records to export ','error');
			redirect('edu_admin/order/index');
		}
				
		$export_array = array();
		//build columns 
		$export_array[] = array(
			'S.No',
			'Date/Time',
			'Order Number',
			'Name',
			'Product Name',
			'Product Price',
			'Status',
			
		);
		$i=1;
		$course_id='';
		
		foreach($results as $result)
		{
			$product_name = '';
			$product_name .=$result->oiProdName.'('.$result->oiProdVariantValue1.')';
			if($result->upgrade_info){
				$product_name .='+'.$result->upgrade_info;
			}	
			$product_price=($result->upgrade_price)?CURRENCY.$result->upgrade_price:'';	
			
			$export_array[]=array(
				$i,
				$result->orderDate."-".$result->orderTime,
				$result->orderNumber,
				str_replace(',','',$result->orderName)."(".$result->orderEmail.')',
				$product_name,
				$product_price,
				$result->orderStatus,
				
			);
			$i++;
		}
		
		
		$file_name = 'order-details-'.time();
		$file_name=url_title($file_name,'-',TRUE);
		//check the report type choice ,default is csv 
		if( RPT_PDF == $this->input->get('export_type'))
		{
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
	
}//End of file 

/* End of file */
