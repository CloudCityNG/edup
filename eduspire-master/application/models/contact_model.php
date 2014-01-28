<?php
/**
@Page/Module Name/Class: 		contact_model.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 03  2013
@Purpose:		        		Contain all data management for faq's
@Table referred:				faq
@Table updated:					faq
@Most Important Related Files	NIL
*/
//Chronological development
//***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
/***********************************************************************************

//***********************************************************************************/  

class Contact_model extends CI_Model {
	
	
	public $table_name='contactus';
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	ben binesh
		@Date:			Sept, 03 2013
		@name     | String | title of course
		@email     | String | title of course
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record ,give -1 to remove limit
		@return  array 
		@Purpose:		get  multiple records 
	
	*/
		
	function get_records($name = '',$email = '',$start = 0 , $limit = 10,$select='contID,contFirstName,contLastName,contEmail,contDate'){
		$this->db->select($select);
		if($name)
			$this->db->where("(
				contFirstName LIKE '%$name%'
				OR
				contLastName LIKE '%$name%'
			)");
		if($email)
			$this->db->where('contEmail',$email);	
		$this->db->order_by('contID','ASC');	
		
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
		@Date:			Sept, 03 2013
		@name   | String | title of course
		@email  | numeric| status of recored 
		@return  integer
		@Purpose:		count  multiple records 
	
	*/
	
	function count_records($name = '',$email=''){
		if($name)
			$this->db->where("(
				contFirstName LIKE '%$name%'
				OR
				contLastName LIKE '%$name%'
			)");
		if($email)
			$this->db->where('contEmail',$email);	
		return $this->db->count_all_results($this->table_name);
		
	}
	
	/**
		@Function Name:	get_single_record
		@Author Name:	ben binesh
		@Date:			Sept, 03 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_record($id=0){
		$this->db->where('contID',$id);
		$query = $this->db->get($this->table_name);
		return $query->row();
	}
	
	
	
	/**
		@Function Name:	insert
		@Author Name:	ben binesh
		@Date:			Sept, 03 2013
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
		@Date:			Sept, 03 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		update data 
	
	*/
	function update($id,$data=array()){
		$this->db->where('contID',$id);
		$this->db->update($this->table_name,$data);
		return true;
	}
	
	/**
		@Function Name:	delete
		@Author Name:	ben binesh
		@Date:			Sept, 03 2013
		@id  | numeric| primary key of record 
		@return  boolean
		@Purpose:		delete data 
	
	*/
	function delete($id){
		$this->db->delete($this->table_name, array('contID' => $id)); 
		return true;
	}
	
	
	
	
}//end of class
//end of file 
	
	