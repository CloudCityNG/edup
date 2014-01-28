<?php
/**
@Page/Module Name/Class: 		location_model.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 04 2013
@Purpose:		        		Contain all data management for table iu_unit and district
@Table referred:				iu_unit ,district
@Table updated:					iu_unit ,district
@Most Important Related Files	NIL

//Chronological development
//***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
/***********************************************************************************

//***********************************************************************************/



class Location_model extends CI_Model {
	
	
	
	public $table_iuUnit   ='iu_unit';
	public $table_district ='district';
	
	public function __construct()
	{
		parent::__construct();
		
	}
	
	/**
		@Function Name:	get_districts
		@Author Name:	ben binesh
		@Date:			Sept, 04 2013
		@name     | String | district name 
		@user_added  | numeric | is user added flag
		@iu_unit  | numeric | IU unit 
		@status  | numeric| status of record 
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@return  array 
		@Purpose:		get  multiple records 
	
	*/
		
	function get_districts($name = '',$user_added = '' , $status = '',$iu_unit = 0,$start = 0 , $limit = 10,$select='*'){
		$this->db->select($select,', iu.iuName');
		$this->db->join($this->table_iuUnit.' iu','dis.disiuUnit = iu.iuID','LEFT');	
		if($name)
			$this->db->like('dis.disName',$name);
		if($user_added != '')
			$this->db->where('dis.disUserAdded',$user_added);	
		if($status != '')
			$this->db->where('dis.disPublish',$status);
		if($iu_unit)
			$this->db->where('disIuUnit',$iu_unit);	
		$this->db->order_by('dis.disName','ASC');	
		
		if($limit > 0)
			$query = $this->db->get($this->table_district.' dis', $limit , $start );
		else
			$query = $this->db->get($this->table_district.' dis');
		
		return $query->result();
	}
	
	/**
		@Function Name:	count_districts
		@Author Name:	ben binesh
		@Date:			Sept, 04 2013
		@name     | String | district name 
		@user_added  | numeric | is user added flag
		@iu_unit  | numeric | IU unit 
		@status  | numeric| status of record 
		@return  integer
		@Purpose:		count record based on filters 
	
	*/
	
	function count_districts($name = '',$user_added = '' , $status = '',$iu_unit = 0){
		if($name)
			$this->db->like('disName',$name);
		if($user_added != '')
			$this->db->where('disUserAdded',$user_added);	
		if($status != '')
			$this->db->where('disPublish',$status);
		if($iu_unit)
			$this->db->where('disIuUnit',$iu_unit);	
		return $this->db->count_all_results($this->table_district);
		
	}
	
	/**
		@Function Name:	get_single_district
		@Author Name:	ben binesh
		@Date:			Sept, 04 2013
		@id  | numeric| primary key of record 
		@select  | string | column to be  selected 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_district($id=0,$select='*'){
		$this->db->select($select);
		$this->db->join($this->table_iuUnit.' iu',$this->table_district.'.disiuUnit = iu.iuID','LEFT');
		$this->db->where('disID',$id);
		$query = $this->db->get($this->table_district);
		return $query->row();
	}
	
	
	
	/**
		@Function Name:	check_duplicate
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@id           | numeric| primary key of record 
		$name         |string | name of district 
		@return  integer
		@Purpose:		count duplicate records with matching filter
	
	*/
	
	function check_duplicate_district($id=0,$name=''){
		if($name)
			$this->db->where('disName',$name);
		if($id)
			$this->db->where('disID !=',$id);
		$count =  $this->db->count_all_results($this->table_district);
		return $count;
	}
	
	/**
		@Function Name:	insert_district
		@Author Name:	ben binesh
		@Date:			Sept, 04 2013
		@data   | array | array of single record 
		@return  integer
		@Purpose:		insert data 
	
	*/
	
	function insert_district($data=array()){
		$this->db->insert($this->table_district,$data);
		return $this->db->insert_id(); 
	}
	
	
	/**
		@Function Name:	update_district
		@Author Name:	ben binesh
		@Date:			Sept, 04 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		udate data 
	
	*/
	function update_district($id=0,$data=array()){
		$this->db->where('disID',$id);
		$this->db->update($this->table_district,$data);
		return true;
	}
	
	/**
		@Function Name:	delete_dristrict
		@Author Name:	ben binesh
		@Date:			Sept, 04 2013
		@id  | numeric| primary key of record 
		@return  boolean
		@Purpose:		delete data 
	
	*/
	function delete_district($id){
		$this->db->delete($this->table_district, array('disID' => $id)); 
		return true;
	}
	
	/**
		@Function Name:	get_status_array
		@Author Name:	binesh
		@emtpty |boolean| empty flag
		@Date:			Sept, 04 2013
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
		@Date:			Sept, 04 2013
		@status  | numeric| status of recored 
		@data   | array | array of single record 
		@return  string
		@Purpose:		return status string 
	
	*/
	function show_status($status = 0){
		$status_array =self::get_status_array();
		return (isset($status_array[$status]))?$status_array[$status]:'Unpublished';
	}
	
	/**
		@Function Name:	get_user_added_array
		@Author Name:	binesh
		@emtpty |boolean| empty flag
		@Date:			Sept, 04 2013
		@return  integer
		@Purpose:		get array of user added (Yes or No)
		
	*/
	function get_user_added_array($empty=false,$empty_array=array(''=>'')){
		$user_added_array=array();
		if($empty){
			$user_added_array = array_merge($user_added_array,$empty_array);
		}
		$user_added_array[ADDED_BY_USER]   = 'Yes';
		$user_added_array[ADDED_BY_AMDIN]  = 'No';
		
		return $user_added_array;
	}
	/**
		@Function Name:	show_user_added
		@Author Name:	ben binesh
		@Date:			Sept, 04 2013
		@status  | numeric| value of recored 
		@data   | array | array of single record 
		@return  string
		@Purpose:		return user added string (Yes or no)
	
	*/
	function show_user_added($user_added = 0){
		$user_added_array =self::get_user_added_array();
		return (isset($user_added_array[$user_added]))?$user_added_array[$user_added]:'No';
	}
	
	
	
/************************************************
*
******** IU Unit Related functions
*
**************************************************/	
	
	/**
		@Function Name:	get_iuUnits
		@Author Name:	ben binesh
		@Date:			Sept, 04 2013
		@name     | String | name 
		@status  | numeric| status of record 
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@return  array 
		@Purpose:		get  multiple records 
	
	*/
		
	function get_iuUnits($name = '', $status = '',$start = 0 , $limit = 10,$select='*'){
		$this->db->select($select);
		if($name)
			$this->db->like('iuName',$name);
		if($status != '')
			$this->db->where('iuPublish',$status);
		$this->db->order_by('iuID','ASC');	
		if($limit > 0)
			$query = $this->db->get($this->table_iuUnit, $limit , $start );
		else
			$query = $this->db->get($this->table_iuUnit);
		
		return $query->result();
	}
	
	/**
		@Function Name:	count_iuUnits
		@Author Name:	ben binesh
		@Date:			Sept, 04 2013
		@name     | String |  name 
		@status  | numeric| status of record 
		@return  integer
		@Purpose:		count record based on filters 
	
	*/
	
	function count_iuUnits($name = '', $status = ''){
		if($name)
			$this->db->like('iuName',$name);
		if($status != '')
			$this->db->where('iuPublish',$status);
		return $this->db->count_all_results($this->table_iuUnit);
		
	}
	
	/**
		@Function Name:	get_single_iuUnit
		@Author Name:	ben binesh
		@Date:			Sept, 04 2013
		@id  | numeric| primary key of record 
		@select  | string | column to be  selected 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_iuUnit($id=0,$select='*'){
		$this->db->select($select);
		$this->db->where('iuID',$id);
		$query = $this->db->get($this->table_iuUnit);
		return $query->row();
	}
	
	
	/**
		@Function Name:	check_duplicate
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@id           | numeric| primary key of record 
		$name         |string | name of district 
		@return  integer
		@Purpose:		count duplicate records with matching filter
	
	*/
	
	function check_duplicate_iuUnit($id=0,$name=''){
		if($name)
			$this->db->where('iuName',$name);
		if($id)
			$this->db->where('iuID !=',$id);
		$count =  $this->db->count_all_results($this->table_iuUnit);
		return $count;
	}
	
	
	/**
		@Function Name:	insert_iuUnit
		@Author Name:	ben binesh
		@Date:			Sept, 04 2013
		@data   | array | array of single record 
		@return  integer
		@Purpose:		insert data 
	
	*/
	
	function insert_iuUnit($data=array()){
		$this->db->insert($this->table_iuUnit,$data);
		return $this->db->insert_id(); 
	}
	
	
	/**
		@Function Name:	update_iuUnit
		@Author Name:	binesh
		@Date:			Sept, 04 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		udate data 
	
	*/
	function update_iuUnit($id=0,$data=array()){
		$this->db->where('iuID',$id);
		$this->db->update($this->table_iuUnit,$data);
		return true;
	}
	
	/**
		@Function Name:	delete_iuUnit
		@Author Name:	ben binesh
		@Date:			Sept, 04 2013
		@id  | numeric| primary key of record 
		@return  boolean
		@Purpose:		delete data 
	
	*/
	function delete_iuUnit($id){
		$this->db->delete($this->table_iuUnit, array('iuID' => $id)); 
		return true;
	}
	
	
	
	
	
}//end of class
//end of file 
	
	