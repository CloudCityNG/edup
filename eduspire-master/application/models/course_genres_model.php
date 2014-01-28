<?php
/**
@Page/Module Name/Class: 		course_genres_model.php
@Author Name:			 		ben binesh
@Date:					 		Aug, 16 2013
@Purpose:		        		Contain all data management functions for the course generes
@Table referred:				course_genres
@Table updated:					course_genres
@Most Important Related Files	NIL
 */

class Course_genres_model extends CI_Model {
	
	
	
	public $table_name='course_genres';
	
	public function __construct()
	{
		parent::__construct();
		
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@title   | String | title of course
		@status  | numeric| status of recored 
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@return  array 
		@Purpose:		get  multiple records 
	
	*/

	
	function get_records($title='', $status='',$start = 0 , $limit = 10){
		$this->db->select('cgID,cgTitle,cgCourseCredits,cgPublish,cgDisplayOrder');
		if($title)
			$this->db->like('cgTitle',$title);
		if($status != '')
			$this->db->where('cgPublish',$status);
		$query = $this->db->get($this->table_name, $limit , $start );
		
		return $query->result();
	}
	
	/**
		@Function Name:	count_records
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@title   | String | title of course
		@status  | numeric| status of recored 
		@return  integer
		@Purpose:		count  multiple records 
	
	*/
	
	function count_records($title='', $status=''){
		if($title)
			$this->db->like('cgTitle',$title);
		if($status != '')
			$this->db->where('cgPublish',$status);
		return $this->db->count_all_results($this->table_name);
		
	}
	
	/**
		@Function Name:	get_single_record
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_record($id=0,$select = '*'){
		$this->db->select($select);
		$this->db->where('cgID',$id);
		$query = $this->db->get($this->table_name);
		return $query->row();
	}
	
	/**
		@Function Name:	check_duplicate
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@id  | numeric| primary key of record 
		@title   | String | title of course
		@return  integer
		@Purpose:		count duplicate records with matching title  
	
	*/
	
	function check_duplicate($id=0,$title=''){
		if($title)
			$this->db->where('cgTitle',$title);
		if($id)
			$this->db->where('cgID !=',$id);
		$count =  $this->db->count_all_results($this->table_name);
		return $count;
	}
	
	/**
		@Function Name:	insert
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
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
		@Date:			Aug, 16 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		udate data 
	
	*/
	function update($id,$data=array()){
		$this->db->where('cgID',$id);
		$this->db->update($this->table_name,$data);
		return true;
	}
	
	/**
		@Function Name:	delete
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
		@id  | numeric| primary key of record 
		@return  boolean
		@Purpose:		delete data 
	
	*/
	function delete($id){
		$this->db->delete($this->table_name, array('cgID' => $id)); 
		return true;
	}
	
	/**
		@Function Name:	get_status_array
		@Author Name:	ben binesh
		@emtpty |boolean| empty flag
		@Date:			Aug, 16 2013
		@return  integer
		@Purpose:		get array of status
		
	*/
	function get_status_array($empty=false){
		$status_array=array();
		if($empty){
			$status_array['']='';
		}
		$status_array[STATUS_PUBLISH]   = 'Published';
		$status_array[STATUS_UNPUBLISH] = 'Unpublished';
		
		return $status_array;
	}
	/**
		@Function Name:	show_status
		@Author Name:	ben binesh
		@Date:			Aug, 16 2013
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
	
	