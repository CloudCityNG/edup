<?php
/**
@Page/Module Name/Class: 		page_model.php
@Author Name:			 		ben binesh
@Date:					 		Aug, 19 2013
@Purpose:		        		Contain all data management for static pages 
@Table referred:				cms_page
@Table updated:					cms_page
@Most Important Related Files	NIL
*/
//Chronological development

/***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
//***********************************************************************************

//***********************************************************************************/ 

class Page_model extends CI_Model {
	
	
	
	public $table_name='cms_page';
	
	public function __construct()
	{
		parent::__construct();
		
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@title   | String | title of course
		@status  | numeric| status of record 
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@return  array 
		@Purpose:		get  multiple records 
	
	*/
		
	function get_records($title = '',$url_key='',$status = '',$start = 0 , $limit = 10){
		$this->db->select('cpID,cpName,cpTitle,cpUrlKey,cpPublish');
		
		if($title)
			$this->db->like('cpTitle',$title);
		if($url_key)
			$this->db->where('cpUrlKey',$url_key);	
			
		if($status != '')
			$this->db->where('cpPublish',$status);
		$query = $this->db->get($this->table_name, $limit , $start );
		
		return $query->result();
	}
	
	/**
		@Function Name:	count_records
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@title   | String | title of course
		@status  | numeric| status of record 
		@return  integer
		@Purpose:		count  multiple records 
	
	*/
	
	function count_records($title = '',$url_key='',$status = ''){
		if($title)
			$this->db->like('cpTitle',$title);
		if($url_key)
			$this->db->where('cpUrlKey',$url_key);	
			
		if($status != '')
			$this->db->where('cpPublish',$status);
		return $this->db->count_all_results($this->table_name);
		
	}
	
	/**
		@Function Name:	get_page_by_name
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@name   | String | page name 
		@return  object array
		@Purpose:		get the page details matching with the name 
	
	*/
	
	function get_page_by_name($name=''){
		if($name == ''){
			return ;
		}
		$this->db->where('cpName',$status);
		$query = $this->db->get($this->table_name);
		return $query->row();
		
	}
	
	
	/**
		@Function Name:	get_single_record
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_record($id=0){
		$this->db->where('cpID',$id);
		$query = $this->db->get($this->table_name);
		return $query->row();
	}
	
	/**
		@Function Name:	check_duplicate
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@id  | numeric| primary key of record 
		@course_id   | String | course id
		@return  integer
		@Purpose:		count duplicate records with matching filter
	
	*/
	
	function check_duplicate($id=0,$url_key=''){
		if($url_key)
			$this->db->where('cpUrlKey',$url_key);
		if($id)
			$this->db->where('cpID !=',$id);
		$count =  $this->db->count_all_results($this->table_name);
		return $count;
	}
	
	/**
		@Function Name:	insert
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
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
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		update data 
	
	*/
	function update($id,$data=array()){
		$this->db->where('cpID',$id);
		$this->db->update($this->table_name,$data);
		return true;
	}
	
	/**
		@Function Name:	delete
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@id  | numeric| primary key of record 
		@return  boolean
		@Purpose:		delete data 
	
	*/
	function delete($id){
		$this->db->delete($this->table_name, array('cpID' => $id)); 
		return true;
	}
	
	/**
		@Function Name:	get_status_array
		@Author Name:	ben binesh
		@empty |boolean| empty flag
		@Date:			Aug, 19 2013
		@return  integer
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
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@status  | numeric| status of record 
		@data   | array | array of single record 
		@return  string
		@Purpose:		return status string 
	
	*/
	function show_status($status = 0){
		$status_array =self::get_status_array();
		return (isset($status_array[$status]))?$status_array[$status]:'Unpublished';
	}
	
}//end of class
//end of file 
	
	