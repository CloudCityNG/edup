<?php 
/**
@Page/Module Name/Class: 		inventory.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 13 2013
@Purpose:		        		contain all controller functions for ipads inventory
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

class Inventory extends CI_Controller {
	
	public $js;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->load->model('inventory_model');
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
		@Date:			Sept, 13 2013
		@Purpose:		show the multiple records and filter 
	
	*/
	public function index()
	{
		if($this->input->post('mass_action'))
		{
			$this->_mass_action();
			return ;
		}
		$this->js[]='js/admin.js';
		$data['layout']       = '';
        $this->page_title     = "Inventory";
		$data['meta_title']   = "Inventory";
		$data['category']     = IPAD_CAT; 
		$data['subcategory']  = $this->input->get('subcategory'); 
		$data['status']       = ($this->input->get('status')  != '' )?$this->input->get('status'):''; 
		$num_records          = $this->inventory_model->count_records( $data['category'],$data['subcategory'], $data['status'] );
		$base_url             = base_url().'edu_admin/inventory/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) )
		{
			$start = 0;
		}
		$per_page            = PER_PAGE; 
		$data['results']     = $this->inventory_model->get_records( $data['category'],$data['subcategory'], $data['status'], $start , $per_page,'invID,invPublish,invCatID,invName, invQuantity, invPrice1, invVariant01, invImage, invSortOrder, invSubcatID');
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$data['main']       = 'edu_admin/inventory/index';
		$this->load->vars($data);
		$this->load->view('template');
	}
	/**
		@Function Name:	create 
		@Author Name:	ben binesh
		@Date:			Sept, 13 2013
		@Purpose:		insert the new record ,validate recored
	
	*/
	function create()
	{
		
		$error = false;
		$errors = array();
		$this->load->helper('form');
		$this->js[]='js/tinymce/tinymce.min.js';
		$image = $this->input->post('old_image');
		$this->page_title     = "Add Product";
		$data['meta_title']   = "Add Product";
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('invCatID', 'Category', 'trim|required');
			$this->form_validation->set_rules('invSubcatID', 'Subcategory', 'trim|required');
			$this->form_validation->set_rules('invName', 'Product Name', 'trim|required');
			$this->form_validation->set_rules('invDescription', 'Product Description', 'trim|required');
			$this->form_validation->set_rules('invPrice1', 'Product Price', 'trim|required|numeric');
			$this->form_validation->set_rules('invQuantity', 'Quantity', 'trim|numeric');
			$this->form_validation->set_rules('invShip', 'Shipping', 'trim|numeric');
			$this->form_validation->set_rules('invSortOrder', 'Sort Order', 'trim|numeric');
			$this->form_validation->set_message('required', '%s must not be blank');
			
			if($error==false)
			{
				//upload the image 
				
				if($_FILES['invImage']['name'] != '')
				{
					$path = UPLOADS.'/ipads';
					$res_response = upload_file('invImage',$path);
					if(is_array($res_response) && isset( $res_response['file_name'] ) )
					{
						// delete old file
						if( '' != $image )
						{
							//unlink the previously uploaded image
							unlink( $path.'/'.$image );
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
								'invCatID' => $this->input->post('invCatID'),
								'invSubcatID' => $this->input->post('invSubcatID'),
								'invName' => $this->input->post('invName'),
								'invDescription' => $this->input->post('invDescription'),
								'invQuantity' => $this->input->post('invQuantity'),
								'invPrice1' => $this->input->post('invPrice1'),
								'invShip' => $this->input->post('invShip'),
								'invVariant01' => $this->input->post('invVariant01'),
								'invImage' => $image,
								'invPublish' => $this->input->post('invPublish'),
								'invSortOrder' => $this->input->post('invSortOrder'),
								);
					
				
				$this->inventory_model->insert($data_array);
				set_flash_message('inventory details has been inserted successfully','success');
				redirect('edu_admin/inventory/index');
			}
		}
		$data['image']=$image;
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/inventory/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	update 
		@Author Name:	ben binesh
		@Date:			Sept, 13 2013
		@Purpose:		validate and update the record
	
	*/
	function update($id=0)
	{
		$error=false;
		$errors=array();
		$data['result']=$this->_load_data($id);
		$image = $data['result']->invImage;
		$this->js[]='js/tinymce/tinymce.min.js';
		$this->page_title     = "Update Product";
		$data['meta_title']   = "Update Product";
		if(count($_POST)>0)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('invCatID', 'Category', 'trim|required');
			$this->form_validation->set_rules('invSubcatID', 'Subcategory', 'trim|required');
			$this->form_validation->set_rules('invName', 'Product Name', 'trim|required');
			$this->form_validation->set_rules('invDescription', 'Product Description', 'trim|required');
			$this->form_validation->set_rules('invPrice1', 'Product Price', 'trim|required|numeric');
			$this->form_validation->set_rules('invQuantity', 'Quantity', 'trim|numeric');
			$this->form_validation->set_rules('invShip', 'Shipping', 'trim|numeric');
			$this->form_validation->set_message('required', '%s must not be blank');
			
			if($error==false)
			{
				//upload the image 
				
				if($_FILES['invImage']['name'] != '')
				{
					$path = UPLOADS.'/ipads';
					$res_response = upload_file('invImage',$path);
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
								'invCatID' => $this->input->post('invCatID'),
								'invSubcatID' => $this->input->post('invSubcatID'),
								'invName' => $this->input->post('invName'),
								'invDescription' => $this->input->post('invDescription'),
								'invQuantity' => $this->input->post('invQuantity'),
								'invPrice1' => $this->input->post('invPrice1'),
								'invShip' => $this->input->post('invShip'),
								'invVariant01' => $this->input->post('invVariant01'),
								'invImage' => $image,
								'invPublish' => $this->input->post('invPublish'),
								'invSortOrder' => $this->input->post('invSortOrder'),
							);
				$this->inventory_model->update($id,$data_array);
				set_flash_message('inventory details has been updated successfully','success');
				redirect('edu_admin/inventory/index');
			}
		}
		$data['image']=$image;
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/inventory/form';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	/**
		@Function Name:	copy
		@Author Name:	copy
		@Date:			Sept, 13 2013
		@Purpose:		copy existing recored and create new one 
	
	*/
	
	function copy($id)
	{
		$result = $this->_load_data($id);
		$data_array=array(
				'invCatID' => $result->invCatID,
				'invSubcatID' => $result->invSubcatID,
				'invName' => $result->invName,
				'invDescription' => $result->invDescription,
				'invQuantity' => $result->invQuantity,
				'invPrice1' => $result->invPrice1,
				'invShip' => $result->invShip,
				'invVariant01' => $result->invVariant01,
				'invImage' => '',
				'invPublish' =>$result->invPublish,
		);
		$new_id=$this->inventory_model->insert($data_array);
		set_flash_message('Ipads has been successfully copied ,edit ipad details ','success');
		redirect('edu_admin/inventory/update/'.$new_id);	
	}
	
	/**
		@Function Name:	delete 
		@Author Name:	ben binesh
		@Date:			Sept, 13 2013
		@Purpose:		validate and delete the record 
	
	*/
	function  delete($id=0)
	{
		$is_delete=true;
		$this->_load_data($id);
		if($this->db->where('oiProdID',$id)->where('oiProdType',PRODUCT_TYPE_IPAD)->count_all_results('orders_items'))
		{
			$is_delete = false;
			set_flash_message('Inventory details cannot be deleted as there are  associated ordes with it ','error');
		}
		if($is_delete)
		{
			$this->inventory_model->delete($id);	
			set_flash_message('Ipads details has been successfully deleted ','success');
		}
		redirect('edu_admin/inventory/index');
	}
	/**
		@Function Name:	_load_date
		@Author Name:	ben binesh
		@Date:			Sept, 13 2013
		@Purpose:		load the single record  
	
	*/
	function _load_data($id=0)
	{
		if(!$id)
		{
			redirect('home/error_404');
		}
		$data = $this->inventory_model->get_single_record($id);
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
		@Function Name:	_mass_action
		@Author Name:	ben binesh
		@Date:			Sept, 13 2013
		@Purpose:		handle the mass action request 
	
	*/
	
	public function _mass_action(){
		$chk_ids=$this->input->post('chk_ids');
		if(!empty($chk_ids) && count($chk_ids > 0))
		{
			$ids =$chk_ids;
			if($this->input->post('delete'))
			{
				
			}
			elseif($this->input->post('activate'))
			{
				$this->inventory_model->update($ids,array(
					'invPublish'=>STATUS_PUBLISH,
				));	
				set_flash_message('Ipads has been successfully activated','success');	
				
			}
			elseif($this->input->post('deactivate'))
			{
				$this->inventory_model->update($ids,array(
					'invPublish'=>STATUS_UNPUBLISH,
				));	
				set_flash_message('Ipads has been successfully deactivated','success');	
			}
			
		}
		redirect('edu_admin/inventory/index');
		
	}
	
	
}//End of file 

/* End of file  */
