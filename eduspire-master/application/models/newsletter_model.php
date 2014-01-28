<?php
/**
@Page/Module Name/Class: 		newsletter_model.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 12  2013
@Purpose:		        		Contain all data management for newsletters
@Table referred:				newsletter
@Table updated:					newsletter
@Most Important Related Files	NIL
*/

//Chronological development
//***********************************************************************************
//| Ref No.|  Author name	 | Date	             | Severity | Modification description
//***********************************************************************************
//RF1.	  |  ben binesh		 | Sept,  17 2012  | minor	   | add the unsubscribe related functions 

//*****************************************************

class Newsletter_model extends CI_Model {
	
	
	public $table_name='newsletter';
	public $table_unsubscribe='unsubscribe';
 
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	ben binesh
		@Date:			Sept, 03 2013
		@name     | String | name
		@email     | String | email
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record ,give -1 to remove limit
		@select  | string column to be selected
		@return  array 
		@Purpose:		get  multiple records 
	
	*/
		
	function get_records($name = '',$email = '',$start = 0 , $limit = 10,$select='newsID,newsFirstName,newsLastName	,newsEmail,newsSignupDate,newsSchoolDistrict'){
		$this->db->select($select);
		if($name)
			$this->db->where("(
				newsFirstName LIKE '%$name%'
				OR
				newsLastName LIKE '%$name%'
			)");
		if($email)
			$this->db->where('newsEmail',$email);	
		$this->db->order_by('newsID','ASC');	
		
		if($limit > 0){
			$query = $this->db->get($this->table_name, $limit , $start );
		}else{
			$query = $this->db->get($this->table_name);
		}
		
		return $query->result();
	}
	
	/**
		@Function Name:	count_records
		@Author Name:	ben binesh
		@Date:			Sept, 12 2013
		@name   | String | title of course
		@email  | numeric| status of recored 
		@return  integer
		@Purpose:		count  multiple records 
	
	*/
	
	function count_records($name = '',$email=''){
		if($name)
			$this->db->where("(
				newsFirstName LIKE '%$name%'
				OR
				newsLastName LIKE '%$name%'
			)");
		if($email)
			$this->db->where('newsEmail',$email);	
		return $this->db->count_all_results($this->table_name);
		
	}
	
	/**
		@Function Name:	get_single_record
		@Author Name:	ben binesh
		@Date:			Sept, 12 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_record($id=0){
		$this->db->where('newsID',$id);
		$query = $this->db->get($this->table_name);
		return $query->row();
	}
	
	
	
	/**
		@Function Name:	insert
		@Author Name:	ben binesh
		@Date:			Sept, 12 2013
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
		@Date:			Sept, 12 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		udate data 
	
	*/
	function update($id,$data=array()){
		$this->db->where('newsID',$id);
		$this->db->update($this->table_name,$data);
		return true;
	}
	
	/**
		@Function Name:	delete
		@Author Name:	ben binesh
		@Date:			Sept, 12 2013
		@id  | numeric| primary key of record 
		@return  boolean
		@Purpose:		delete data 
	
	*/
	function delete($id=0){
		if(is_array($id)){
			$this->db->where_in('newsID',$id);
		}else{
			$this->db->where('newsID',$id);
		}
		$this->db->delete($this->table_name); 
		return true;
	}
	
/***************************
*** Unsubscribe table related functions 
****************************/	
	/**
	* Rf1 
	*/
	
	/**
		@Function Name:	get_unsubscribe_records
		@Author Name:	ben binesh
		@Date:			Sept, 17 2013
		@email | string| email 
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record ,give -1 to remove limit
		@return  array 
		@Purpose:	get multiple records	
	
	*/
	
	
	function get_unsubscribe_records($email='',$start='',$limit){
		if($email)
			$this->db->where('uEmail',$email);	
		$this->db->order_by('uID','ASC');	
		
		if($limit > 0){
			$query = $this->db->get($this->table_unsubscribe, $limit , $start );
		}else{
			$query = $this->db->get($this->table_unsubscribe);
		}
		return $query->result();
	}
	
	
	
	/**
		@Function Name:	count_unsubscribe_records
		@Author Name:	ben binesh
		@Date:			Sept, 17 2013
		@email | string| email 
		@return  integer
		@Purpose:	count multiple records	
	
	*/
	
	function count_unsubscribe_records($email=''){
		if($email)
			$this->db->where('uEmail',$email);	
		$count = $this->db->count_all_results($this->table_unsubscribe);
		return $count;
	}
	
	/**
		@Function Name:	insert_subscribe
		@Author Name:	ben binesh
		@Date:			Sept, 17 2013
		@data   | array | array of single record 
		@return  integer
		@Purpose:		insert data 
	
	*/
	
	function insert_subscribe($data=array()){
		$this->db->insert($this->table_unsubscribe,$data);
		return $this->db->insert_id(); 
	}
	
	
	/**
		@Function Name:	update_subscribe
		@Author Name:	ben binesh
		@Date:			Sept, 17 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		udate data 
	
	*/
	function update_subscribe($id,$data=array()){
		$this->db->where('uID',$id);
		$this->db->update($this->table_unsubscribe,$data);
		return true;
	}
	
	/**
		@Function Name:	delete_subscribe
		@Author Name:	ben binesh
		@Date:			Sept, 17 2013
		@id  | numeric/array| primary key of record 
		@return  boolean
		@Purpose:		delete data 
	
	*/
	function delete_unsubscribe($id=0){
		if(is_array($id)){
			$this->db->where_in('uID',$id);
		}else{
			$this->db->where('uID',$id);
		}
		$this->db->delete($this->table_unsubscribe); 
		return true;
	}
	/**
		@Function Name:	get_single_unsubscribe
		@Author Name:	ben binesh
		@Date:			Sept, 12 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_unsubscribe($id=0){
		$this->db->where('uID',$id);
		$query = $this->db->get($this->table_unsubscribe);
		return $query->row();
	}
	
	
}//end of class
//end of file 
	
	