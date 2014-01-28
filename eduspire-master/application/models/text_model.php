<?php
/**
@Page/Module Name/Class: 		text_model.php
@Author Name:			 		ben binesh
@Date:					 		Dec, 18 2013
@Purpose:		        		Contain all data management for static texts 
@Table referred:				cms_text
@Table updated:					cms_text
@Most Important Related Files	NIL
*/
//Chronological development

/***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
//***********************************************************************************

//***********************************************************************************/ 

class Text_model extends CI_Model {
	
	
	
	public $table_name='cms_text';
	
	public function __construct()
	{
		parent::__construct();
		
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	ben binesh
		@Date:			Dec, 18 2013
		@name   | String | text name 
		@status  | numeric| status of record 
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@return  array 
		@Purpose:		get  multiple records 
	
	*/
		
	function get_records($name = '',$status = '',$start = 0 , $limit = 10){
		$this->db->select('ctID,ctName,ctPublish');
		
		if($name)
			$this->db->like('ctName',$name);
		
		if($status != '')
			$this->db->where('ctPublish',$status);
		$query = $this->db->get($this->table_name, $limit , $start );
		
		return $query->result();
	}
	
	/**
		@Function Name:	count_records
		@Author Name:	ben binesh
		@Date:			Dec, 18 2013
		@name   | String | text name 
		@status  | numeric| status of record 
		@return  integer
		@Purpose:		count  multiple records 
	
	*/
	
	function count_records($name = '',$status = ''){
		if($name)
			$this->db->like('ctName',$name);
		if($status != '')
			$this->db->where('ctPublish',$status);
		return $this->db->count_all_results($this->table_name);
		
	}
	
	/**
		@Function Name:	get_text_by_name
		@Author Name:	ben binesh
		@Date:			Dec, 18 2013
		@name   | String | text name 
		@return  object array
		@Purpose:		get the text details matching with the name 
	
	*/
	
	function get_text_by_name($name=''){
		if($name == ''){
			return ;
		}
		$this->db->where('ctName',$status);
		$query = $this->db->get($this->table_name);
		return $query->row();
		
	}
	
	
	/**
		@Function Name:	get_single_record
		@Author Name:	ben binesh
		@Date:			Dec, 18 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_record($id=0,$status=''){
		$this->db->where('ctID',$id);
		if($status != '')
			$this->db->where('ctPublish',$status);
		$query = $this->db->get($this->table_name);
		return $query->row();
	}
	
	/**
		@Function Name:	check_duplicate
		@Author Name:	ben binesh
		@Date:			Dec, 18 2013
		@id  | numeric| primary key of record 
		@name   | String | text name
		@return  integer
		@Purpose:		count duplicate records with matching filter
	
	*/
	
	function check_duplicate($id=0,$name=''){
		if($name)
			$this->db->where('ctName',$name);
		if($id)
			$this->db->where('ctID !=',$id);
		$count =  $this->db->count_all_results($this->table_name);
		return $count;
	}
	
	/**
		@Function Name:	insert
		@Author Name:	ben binesh
		@Date:			Dec, 18 2013
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
		@Date:			Dec 18 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		update data 
	
	*/
	function update($id,$data=array()){
		$this->db->where('ctID',$id);
		$this->db->update($this->table_name,$data);
		return true;
	}
	
	/**
		@Function Name:	delete
		@Author Name:	ben binesh
		@Date:			Dec 18 2013
		@id  | numeric| primary key of record 
		@return  boolean
		@Purpose:		delete data 
	
	*/
	function delete($id){
		$this->db->delete($this->table_name, array('ctID' => $id)); 
		return true;
	}
	
	/**
		@Function Name:	get_status_array
		@Author Name:	ben binesh
		@empty |boolean| empty flag
		@empty_array |array| empty value
		@Date:			Dec 18 2013
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
		@Date:			Dec, 18 2013
		@status  | numeric| status of record 
		@return  string
		@Purpose:		return status string 
	
	*/
	function show_status($status = 0){
		$status_array =self::get_status_array();
		return (isset($status_array[$status]))?$status_array[$status]:'Unpublished';
	}
	
}//end of class
//end of file 
	
	