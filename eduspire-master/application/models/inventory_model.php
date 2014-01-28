<?php
/**
@Page/Module Name/Class: 		news_model.php
@Author Name:			 		Ben binesh
@Date:					 		Sept, 13 2013
@Purpose:		        		Contain all data management for news section
@Table referred:				news
@Table updated:					news
@Most Important Related Files	NIL
 */
//Chronological development
//***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
/*********************************************************************************** 

//***********************************************************************************/ 

class Inventory_model extends CI_Model {
	
	
	
	public $table_name='inventory';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	Ben binesh
		@Date:			Sept, 13 2013
		@category   | String | category
		@subcategory   | String | sub category
		@status  | numeric| status of record 
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@select  |string| column to be select 
		@return  array 
		@Purpose:		get  multiple records 
	
	*/
		
	function get_records($category = IPAD_CAT ,$subcategory = 0 ,$status = '',$start = 0 , $limit = 10,$select='*'){
		
		$this->db->select($select);
		if($category)
			$this->db->where('invCatID',$category);
		if($subcategory)
			$this->db->where('invSubcatID',$subcategory);
		if($status != '')
			$this->db->where('invPublish',$status);
		$this->db->order_by('invSortOrder','ASC');
		if($limit > 0){
			$query = $this->db->get($this->table_name, $limit , $start );
		}else{
			$query = $this->db->get($this->table_name);
		}
		return $query->result();
	}
	
	/**
		@Function Name:	count_records
		@Author Name:	Ben binesh
		@Date:			Sept, 13 2013
		@category   | String | category
		@subcategory   | String | sub category
		@status  | numeric| status of record 
		@return  integer
		@Purpose:		count  multiple records 
	
	*/
	
	function count_records($category = 0 ,$subcategory = 0 ,$status = ''){
		if($category)
			$this->db->where('invCatID',$category);
		if($subcategory)
			$this->db->where('invSubcatID',$subcategory);
		if($status != '')
			$this->db->where('invPublish',$status);
		
		return $this->db->count_all_results($this->table_name);
		
	}
	
	/**
		@Function Name:	get_single_record
		@Author Name:	Ben binesh
		@Date:			Sept, 13 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_record($id=0){
		$this->db->where('invID',$id);
		$query = $this->db->get($this->table_name);
		return $query->row();
	}
	
		
	/**
		@Function Name:	insert
		@Author Name:	Ben binesh
		@Date:			Sept, 13 2013
		@data   | array | array of single record 
		@return  integer
		@Purpose:		insert data 
	
	*/
	
	function insert($data=array()){
		$this->db->insert($this->table_name,$data);
		return $this->db->insert_id(); 
	}
	
	
	/**
		@Function Name:	update
		@Author Name:	Ben binesh
		@Date:			Sept, 13 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  boolean
		@Purpose:		udate data 
	
	*/
	function update( $id, $data = array() ){
		if(is_array($id)){
			$this->db->where_in('invID', $id);
		}else{
			$this->db->where('invID', $id);
		}
		
		$this->db->update($this->table_name, $data);
		return true;
	}
	
	/**
		@Function Name:	delete
		@Author Name:	Ben binesh
		@Date:			Sept, 19 2013
		@id  | numeric| primary key of record 
		@return  boolean
		@Purpose:		delete data 
	
	*/
	
	function delete($id=0){
		if(is_array($id)){
			$this->db->where_in('invID',$id);
		}else{
			$this->db->where('invID',$id);
		}
		$this->db->delete($this->table_name); 
		return true;
	}
	
	/**
		@Function Name:	get_status_array
		@Author Name:	Ben binesh
		@emtpty |boolean| empty flag
		@emtpty_array |array| empty array
		@Date:			Sept, 13 2013
		@return  array
		@Purpose:		get array of status
		
	*/
	function get_status_array($empty=false,$empty_array=array(''=>'')){
		$status_array=array();
		if($empty){
			$status_array = array_merge($status_array,$empty_array);
		}
		$status_array[STATUS_PUBLISH]   = 'Published';
		$status_array[STATUS_UNPUBLISH] = 'Unpublished';
		
		return $status_array;
	}
	/**
		@Function Name:	show_status
		@Author Name:	Ben binesh
		@Date:			Sept, 13 2013
		@status  | numeric| status of recored 
		@return  string
		@Purpose:		return status string 
	
	*/
	function show_status($status = 0){
		$status_array =self::get_status_array();
		return (isset($status_array[$status]))?$status_array[$status]:'Unpublished';
	}
	
	/**
		@Function Name:	get_category_array
		@Author Name:	Ben binesh
		@emtpty |boolean| empty flag
		@emtpty_array |array| empty array
		@Date:			Sept, 13 2013
		@return  array
		@Purpose:		get array of category
		
	*/
	function get_category_array($empty=false, $empty_array = array(''=>'')){
		$category_array = array();
		
		$categories=array(
			IPAD_CAT => 'iPad',
		);
		if($empty){
			$category_array = array_merge( $category_array, $empty_array );
		}
		foreach( $categories as $key => $value ){
			$category_array[$key] = $value;
		}
		
		return $category_array;
	}
	/**
		@Function Name:	show_category
		@Author Name:	Ben binesh
		@Date:			Sept, 13 2013
		@category  | numeric| category id
		@return  string
		@Purpose:		return category value 
	
	*/
	function show_category($category = 0){
		$category_array =self::get_category_array();
		return (isset($category_array[$category]))?$category_array[$category]:'';
	}
	
	
	/**
		@Function Name:	get_subcategory_array
		@Author Name:	Ben binesh
		@emtpty |boolean| empty flag
		@emtpty_array |array| empty array
		@Date:			Sept, 13 2013
		@return  array
		@Purpose:		get array of subcategory
		
	*/
	function get_subcategory_array($empty=false, $empty_array = array(''=>'')){
		$subcategory_array = array();
		
		$subcategories=array(
			1 => 'iPads',
			2 => 'iPad Options',
			3 => 'iPad Mini Options',
		);
		if($empty){
			$subcategory_array = array_merge( $subcategory_array, $empty_array );
		}
		foreach( $subcategories as $key => $value ){
			$subcategory_array[$key] = $value;
		}
		
		return $subcategory_array;
	}
	/**
		@Function Name:	show_subcategory
		@Author Name:	Ben binesh
		@Date:			Sept, 13 2013
		@subcategory  | numeric| subcategory id
		@return  string
		@Purpose:		return subcategory value 
	
	*/
	function show_subcategory($subcategory = 0){
		$subcategory_array =self::get_subcategory_array();
		return (isset($subcategory_array[$subcategory]))?$subcategory_array[$subcategory]:'';
	}
	
	
	
}//end of class
//end of file 
	
	