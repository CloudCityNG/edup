<?php
/**
@Page/Module Name/Class: 		order_model.php
@Author Name:			 	Janet Rajani
@Date:					Aug, 29 2013
@Purpose:		        	Contain all data management functions for the login/registration order
@Table referred:			orders, orders_items
@Table updated:				orders,orders_items
@Most Important Related Files	NIL
*/
//Chronological development

//***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
//***********************************************************************************
//RF1.	  |  ben binesh		 | Sept,16 2013  | major	   | add function to show the ipads orders in admin 	

//***********************************************************************************



class Order_model extends CI_Model {
	
	public $table_orders='orders';
	public $table_order_items='orders_items';
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	ben binesh
		@Date:			Sept , 17 2013
		@name          | String | name of user 
		@email   	   | String | email
		@status        | String | order status 
		@order_date  | String | order date 
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@product_type   numeric | order product type 
		@course_id   numeric | course id associated with the order 
		@return  array 
		@Purpose:		get  multiple records with product upgrade info
	
	*/
	/*
		Ref1
	*/	
	
	function get_records($name = '', $email = '',$status = '',$order_date='', $start = 0 ,  $limit = 10,$product_type=PRODUCT_TYPE_IPAD,$course_id=0)
	{
		$this->db->select('
						o.orderID, o.orderNumber, o.orderStatus, o.orderDate, o.orderTime, o.orderCustID, o.orderName,o.orderEmail,
						oi.oiID, oi.oiProdName,oi.oiProdID , oi.oiProdPrice, oi.oiProdVariantValue1,
						
						(
							SELECT
								oiProdName
							FROM 
								'.$this->table_order_items.'
							WHERE
								oiOrderNumber = o.orderNumber
								AND 
								oiID != oi.oiID 
								LIMIT 1
								
						) AS upgrade_info
						,
						(
							SELECT
								oiProdPrice
							FROM 
								'.$this->table_order_items.'
							WHERE
								oiOrderNumber = o.orderNumber
								AND 
								oiID != oi.oiID 
								LIMIT 1
								
						) AS upgrade_price
						
						
						
						
		');
		$this->db->join($this->table_order_items.' oi','o.orderNumber = oi.oiorderNumber');
		if($product_type !==''){
			$this->db->where('o.orderProductType',$product_type);	
			
		}
		
		if($name)
			$this->db->like("o.orderName",$name);	
		
		if($email)
			$this->db->where('o.orderEmail',$email);	
			
		if($order_date)
			$this->db->where('o.orderDate',$order_date);	
		
		if($status != '')
			$this->db->where('o.orderStatus',$status);
		
		if($course_id)
			$this->db->where('o.orderCourseID',$course_id);
		
		
		$this->db->order_by('o.orderID','DESC');
		$this->db->group_by('o.orderID');
		if($limit > 0){
			$query = $this->db->get($this->table_orders.' o', $limit , $start );
		}else{
			$query = $this->db->get($this->table_orders.' o');
		}
		
		return $query->result();
	}
	
	/**
		@Function Name:	count_records
		@Author Name:	ben binesh
		@Date:			Sept , 16 2013
		@name          | String | name of user 
		@email   	   | String | email
		@status        | String | order status 
		@order_date  | String | order date 
		@product_type   numeric | order product type 
		@course_id   numeric | course id associated with the order 		
		@return         Integer  
		@Purpose:		count records
	
	*/
	
	function count_records($name = '', $email = '',$status = '',$order_date='',$product_type=PRODUCT_TYPE_IPAD,$course_id=0)
	{
		$this->db->select('COUNT(distinct(orderID)) AS numrows',TRUE);
		$this->db->join($this->table_order_items.' oi','o.orderNumber = oi.oiorderNumber');
		
		if($product_type !==''){
			$this->db->where('o.orderProductType',$product_type);	
		}
		
		if($name)
			$this->db->like("o.orderName",$name);	
		
		if($email)
			$this->db->where('o.orderEmail',$email);	
			
		if($order_date)
			$this->db->where('o.orderDate',$order_date);	
		
		if($course_id)
			$this->db->where('o.orderCourseID',$course_id);
		
		if($status != '')
			$this->db->where('o.orderStatus',$status);
		
		$count = $this->db->get($this->table_orders.' o');
		$count=$count->result();
		if(isset($count[0]->numrows)){
			$count = $count[0]->numrows;
		}else{
			$count=0;
		}
		return $count;
		
	}
	
	
	
	/**
		@Function Name:	get_single_record
		@Author Name:	Ben binesh
		@Date:			Sept, 17 2013
		@id  | numeric| primary key of record 
		@user_id  | numeric| user id associated with the order 
		@course_id  | numeric| courser id associated with the course
		@return  array
		@Purpose:	get the single record 
	
	*/
	function get_single_record($id=0,$user_id=0,$course_id=0){
		$this->db->select('
			o.*,
			oi.*,
			i.invSubcatID,
			(
							SELECT
								oiProdName
							FROM 
								'.$this->table_order_items.'
							WHERE
								oiOrderNumber = o.orderNumber
								AND 
								oiID != oi.oiID 
								LIMIT 1
								
			) AS upgrade_info
			,
			(
				SELECT
					oiProdPrice
				FROM 
					'.$this->table_order_items.'
				WHERE
					oiOrderNumber = o.orderNumber
					AND 
					oiID != oi.oiID 
					LIMIT 1
					
			) AS upgrade_price
			,
			(
				SELECT
					oiProdID
				FROM 
					orders_items
				WHERE
					oiOrderNumber = o.orderNumber
					AND 
					oiID != oi.oiID 
					LIMIT 1
				
			) AS upgrade_id
		');
		$this->db->join($this->table_order_items.' oi','o.orderNumber = oi.oiorderNumber');
		$this->db->join('inventory i', 'oi.oiProdID=i.invID','LEFT');
		if($id)
			$this->db->where('o.orderID',$id);
		if($user_id)
			$this->db->where('o.orderCustID',$user_id);
		if($course_id)
			$this->db->where('o.orderCourseID',$course_id);	
		$this->db->order_by('o.orderID','DESC');
		$this->db->group_by('o.orderID');	
		$query = $this->db->get($this->table_orders.' o');
		return $query->row();
	}
	
	/**
		@Function Name:	get_order_byOrderNumber
		@Author Name:	Ben binesh
		@Date:			Sept, 17 2013
		@order_number  | numeric| order number 
		@return  array of object 
		@Purpose:	get the order matching with the order number 
	
	*/
	
	function get_order_byOrderNumber($order_number=0){
		$this->db->where('orderNumber',$order_number);
		$query = $this->db->get($this->table_orders);
		return $query->row();
	}
		
	/**
		@Function Name:	insert
		@Author Name:	Janet Rajani
		@Date:			Aug, 21 2013
		@data   		array | array of single record 
		@return  		integer
		@Purpose:		insert data 
	
	*/
	
	function insert($data=array()){
		$this->db->insert($this->table_orders,$data);
		return $this->db->insert_id(); 
	}
	
	
	/**
		@Function Name:	update
		@Author Name:	Janet Rajani
		@Date:			Aug, 29 2013
		@id  			numeric| primary key of record 
		@data   		array | array of single record 
		@return  		integer
		@Purpose:		udate data 
	
	*/
	function update($id,$data=array()){
		$this->db->where('orderID',$id);
		$this->db->update($this->table_orders,$data);
		return true;
	}
	
	/**
		@Function Name:	delete
		@Author Name:	Janet Rajani
		@Date:			Aug, 29 2013
		@id  			numeric| primary key of record 
		@return  		boolean
		@Purpose:		delete data 
	
	*/
	function delete($id=0){
		$this->db->delete($this->table_orders, array('orderID' => $id));
		return true;
	}
	
	/**
	  @Function Name: select_max_id
	  @Author Name: Janet Rajani
	  @Date:   Sep, 17 2013
	  @return        | array 
	  @Purpose:    get  max number of a field
	 
	*/
	function select_max_id(){
		$this->db->select_max('orderNumber');
		$query = $this->db->get($this->table_orders,1);
		return $query->row();
	}
	
	
	/**
		@Function Name:	get_status_array
		@Author Name:	ben binesh
		@Date:			Sept , 17 2013
		@emtpty |boolean| empty flag
		@return  integer
		@Purpose:		get array of status
		
	*/
	function get_status_array($empty=false,$empty_array=array(''=>'')){
		$status_array=array();
		if($empty){
			$status_array = array_merge($status_array,$empty_array);
		}
		$status_array[ORDER_PENDING]   = ORDER_PENDING;
		$status_array[ORDER_PAYPAL_PENDING] = ORDER_PAYPAL_PENDING;
		$status_array[ORDER_COMPLETED] = PAYMENT_COMPLETED;
		
		return $status_array;
	}
/**********************************************************************
*** ORDER ITMEMS RELATED FUNCTIONS 
******************************************************************/	
	/**
		@Function Name:	delete_item
		@Author Name:	ben binesh
		@Date:			Sept, 17 2013
		@id  			numeric| primary key of record 
		@return  		boolean
		@Purpose:		delete data 
	
	*/
	function delete_item($id=0){
		$this->db->delete($this->table_order_items, array('oiID' => $id));
		return true;
	}
	
	
	/**
		@Function Name:	delete_item
		@Author Name:	ben binesh
		@Date:			Sept, 17 2013
		@id  			numeric| primary key of record 
		@return  		boolean
		@Purpose:		delete data 
	
	*/
	function delete_item_by_ordernumber($order_number){
		$this->db->delete($this->table_order_items, array('oiOrderNumber' => $order_number));
		return true;
	}
	
	/**
		@Function Name:	insert_item
		@Author Name:	ben binesh
		@Date:			Sept, 23 2013
		@data           |array| record array
		@return  		boolean
		@Purpose:		delete data 
	
	*/
	function insert_item($data=array()){
		$this->db->insert($this->table_order_items,$data);
		return $this->db->insert_id(); 
	}
	
	/**
		@Function Name:	update_item
		@Author Name:	ben binesh
		@Date:			Sept, 23 2013
		@data           |array| record array
		@id             |integer| primary key 
		@return  		boolean
		@Purpose:		delete data 
	
	*/
	function update_item($id,$data=array()){
		$this->db->where('oiID',$id);
		$this->db->update($this->table_order_items,$data);
		return true;
	}
	
	
	
}//end of class
//end of file 
	
	