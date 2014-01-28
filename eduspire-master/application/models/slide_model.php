<?php
/**
@Page/Module Name/Class: 		slide_model.php
@Author Name:			 		ben binesh
@Date:					 		Aug, 20 2013
@Purpose:		        		Contain all data management function for home page slides 
@Table referred:				cms_slide
@Table updated:					cms_slide
@Most Important Related Files	NIL
*/
//Chronological development

/***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
//***********************************************************************************


//***********************************************************************************/

class Slide_model extends CI_Model {
	
	
	
	public $table_name='cms_slide';
	
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
		
	function get_records($title = '',$status = '',$start = 0 , $limit = 10){
		$this->db->select('csID,csTitle,csUrl,csOrder,csPublish');
		
		if($title)
			$this->db->like('csTitle',$title);
		
		if($status != '')
			$this->db->where('csPublish',$status);
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
	
	function count_records($title = '',$status = ''){
		if($title)
			$this->db->like('csTitle',$title);
		if($status != '')
			$this->db->where('csPublish',$status);
		return $this->db->count_all_results($this->table_name);
		
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
		$this->db->where('csID',$id);
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
	
	function check_duplicate($id=0,$title=''){
		if($title)
			$this->db->where('csTitle',$title);
		if($id)
			$this->db->where('csID !=',$id);
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
		@Purpose:		udate data 
	
	*/
	function update($id,$data=array()){
		$this->db->where('csID',$id);
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
		$this->db->delete($this->table_name, array('csID' => $id)); 
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
	
	