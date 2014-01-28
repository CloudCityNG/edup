<?php
/**
@Page/Module Name/Class: 		news_model.php
@Author Name:			 		binesh
@Date:					 		Aug, 30 2013
@Purpose:		        		Contain all data management for news section
@Table referred:				news
@Table updated:					news
@Most Important Related Files	NIL
 */

class News_model extends CI_Model {
	
	
	
	public $table_name='news';
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	binesh
		@Date:			Aug, 19 2013
		@title   | String | title of course
		@status  | numeric| status of recored 
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@return  array 
		@Purpose:		get  multiple records 
	
	*/
		
	function get_records($title = '',$status = '',$start = 0 , $limit = 10,$select='*'){
		
		$this->db->select($select);
		if($title)
			$this->db->like('nwTitle',$title);
		
		if($status != '')
			$this->db->where('nwPublish',$status);
		$this->db->order_by('nwID','DESC');
		$query = $this->db->get($this->table_name, $limit , $start );
		
		return $query->result();
	}
	
	/**
		@Function Name:	count_records
		@Author Name:	binesh
		@Date:			Aug, 19 2013
		@title   | String | title of course
		@status  | numeric| status of recored 
		@return  integer
		@Purpose:		count  multiple records 
	
	*/
	
	function count_records($title = '',$status = ''){
		if($title)
			$this->db->like('nwTitle',$title);
		
		if($status != '')
			$this->db->where('nwPublish',$status);
			
		
		return $this->db->count_all_results($this->table_name);
		
	}
	
	/**
		@Function Name:	get_single_record
		@Author Name:	binesh
		@Date:			Aug, 19 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_record($id=0){
		$this->db->where('nwID',$id);
		$query = $this->db->get($this->table_name);
		return $query->row();
	}
	
	/**
		@Function Name:	check_duplicate
		@Author Name:	binesh
		@Date:			Aug, 19 2013
		@id  | numeric| primary key of record 
		@titte   | String | new title 
		@return  integer
		@Purpose:		count duplicate records with matching filter
	
	*/
	
	function check_duplicate($id=0,$title = ''){
		if($title)
			$this->db->where('nwTitle',$title);
		if($id)
			$this->db->where('nwID != ',$id);
		$count =  $this->db->count_all_results($this->table_name);
		return $count;
	}
	
	/**
		@Function Name:	insert
		@Author Name:	binesh
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
		@Author Name:	binesh
		@Date:			Aug, 19 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  boolean
		@Purpose:		udate data 
	
	*/
	function update( $id, $data = array() ){
		$this->db->where('nwID', $id);
		$this->db->update($this->table_name, $data);
		return true;
	}
	
	/**
		@Function Name:	delete
		@Author Name:	binesh
		@Date:			Aug, 19 2013
		@id  | numeric| primary key of record 
		@return  boolean
		@Purpose:		delete data 
	
	*/
	function delete($id){
		$this->db->delete($this->table_name, array('nwID' => $id)); 
		return true;
	}
	
	/**
		@Function Name:	get_status_array
		@Author Name:	binesh
		@emtpty |boolean| empty flag
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
		@Author Name:	binesh
		@Date:			Aug, 19 2013
		@status  | numeric| status of recored 
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
	
	