<?php
/**
@Page/Module Name/Class: 		permission_model.php
@Author Name:			 		ben binesh
@Date:					 		Oct 18, 2013
@Purpose:		        		Contain all data management function user permissions 
@Table referred:				permission_groups,permission_map,permissions
@Table updated:					cms_page
@Most Important Related Files	NIL
*/

class Permission_model extends CI_Model {
	
	public $table_permission='permissions';
	public $table_permission_groups='permission_groups';
	public $table_permission_map='permission_map';

	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

/********************************************************
*
* Permission Table Related functions 	
*
**********************************************************/	
	
	/**
		@Function Name:	get_permissions
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@parent_id      | numeric| parent id of permission
		@return  object array 
		@Purpose:		get permissions data 
	
	*/
	
	function get_permissions($parent_id=''){
		$result=array();
		$this->db->select('p.*,parent.permission as parentName');
		if($parent_id !== ''){
			$this->db->where('p.parentID',$parent_id);
		}
		$this->db->join($this->table_permission.' parent' ,'p.parentId = parent.permissionID','left');
		$this->db->order_by('p.parentID','ASC');
		$query = $this->db->get($this->table_permission.' p ');
		$result = $query->result(get_class($this));
		return $result;
	
	}
	
	/**
		@Function Name:	get_single_permission
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@id      | numeric| primary key
		@return  object array 
		@Purpose:		get single permissions data 
	
	*/
	
	function get_single_permission($id)
	{
		$result = array();
		$this->db->select('p.*,parent.permission as parentName');
		$this->db->where('p.permissionID',$id);
		$this->db->join($this->table_permission.' parent' ,'p.parentId = parent.permissionID','left');
		$query = $this->db->get($this->table_permission.' p');
		$result = $query->row();
		return $result;
	}
	
	/**
		@Function Name:	get_permissions_list
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@group_id   | numeric | group id 
		@return  array 
		@Purpose:		get all permissions, or permissions from a group for the purposes of listing them in a form
	*/
		
	function get_permissions_list($group_id = ''){
		$level=0;
		$ret = array();
		$parent_permissions=$this->get_permissions(0);
		if(!empty($parent_permissions)){
			foreach ($parent_permissions as $perm){
				$ret[] = array(
						'id' 	     => $perm->permissionID, 
						'permission' => $perm->permission,
						'key'        => $perm->key,
						'parentName' => $perm->parentName,
						'level'      => $level ,
						'childs'     => $perm->get_child_permission($level)
					);
				
			}
		}
		return $ret;
	}
	
	
	/**
		@Function Name:	get_child_permission
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@level   | numeric | level value 
		@return  array 
		@Purpose:		get the child permissions recursively 
	*/
	function get_child_permission($level=0){
		$ret = array();
	    $level++;
	    $child_permissions=$this->get_permissions($this->permissionID);
		if( $child_permissions ){ 
	    	foreach($child_permissions as $child) {
	    		$ret[] = array(
							'id'         => $child->permissionID, 
							'permission' => $child->permission,
							'key'        => $child->key,
	    					'level' 	 => $level,
							'parentName' =>$child->parentName,
	    					'childs'     => $child->get_child_permission($level)
					);
	    	}
	    }
	    
	    return $ret;	
	}
	
	/**
		@Function Name:	get_permission_grid_list
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@_container   | array  | output array 
		@_data   | array  | permission array 
		@return  array 
		@Purpose:		traverse permission array recursively and return data in tree structure 
	*/
	
	function get_permission_grid_list(&$_container = array(), $_data = array()){
		if(!empty($_data)){
			foreach ($_data as $k){
				$_container[$k['id']]=array(
						'id'         => $k['id'],
						'permission' => $k['permission'],
						'key'        => $k['key'],
						'parentName' => $k['parentName'],
						'level'      => $k['level'],  
						
					);
				
				if(!empty($k['childs'])){
					$this->get_permission_grid_list( $_container, $k['childs'] );
				}
			}
		}
	}
	
	
	/**
		@Function Name:	get_permission_grid_list
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@_container   | array  | output array 
		@_data   | array  | permission array 
		@return  array 
		@Purpose:		create ready for use in html option permission hierarchy
	*/
	
	
	public function get_permission_dropdown_list( &$_container = array(), $_data = array() )
	{
		if(!empty($_data)){
			foreach ($_data as $k){
				$space = '';
				if(isset($k['level'])){
					$space = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' , $k['level']);
				}
				if(!empty($k['childs'])){
					$_container[$k['id']] = $space . '<b>'.$k['permission'].'</b>';
				}else{
					$_container[$k['id']] = $space . '<b>'.$k['permission'].'</b>';
				}	
				
				if(!empty($k['childs'])){
					$this->get_permission_dropdown_list( $_container, $k['childs'] );
				}
			}
		}
	}
	
	/**
		@Function Name:	get_permission_list
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@_container   | array  | output array 
		@_data   | array  | permission array 
		@selected_permission   | array  | selected permissions
		@checkbox   | boolean  | flag whether to show the checkbox or not 
		@return  array 
		@Purpose:		generate ul li list of permissions 
	*/
	
	public function get_permission_list( &$_container , $_data = array(),$selected_permission=array(),$checkbox=true)
	{
		if(!empty($_data)){
			foreach ($_data as $k){
				$_container .= '<li>';
				$class='';
				$selected=false;
				if(is_array($selected_permission)){
					$selected=(in_array($k['id'],$selected_permission))?true:false;
				}
				$class='class="'.(($selected)?'enable':'disable').'"';
					if($checkbox){
						$class='';
						$_container .= '<input type="checkbox" '.(($selected)?'checked="checked"':'').' name="permission[]" value="'.$k['id'].'" />';
					}
				$_container .= '<span '.$class.' >'.$k['permission'].'</span>';
				if(!empty($k['childs'])){
					$_container .= '<ul>';
					$this->get_permission_list( $_container, $k['childs'],$selected_permission,$checkbox );
					$_container .= '</ul>';
				}
				$_container .= '</li>';
			}
		}
		
	}
		
	/**
		@Function Name:	insert_permission
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@data   | array | array of single record 
		@return  integer
		@Purpose: insert data 
	
	*/
	
	function insert_permission($data=array()){
		$this->db->insert($this->table_permission,$data);
		return $this->db->insert_id(); 
	}
	
		
	/**
		@Function Name:	update_permission
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@data   | array | array of single record 
		$id     |integer | primary key 
		@return  integer
		@Purpose: insert data 
	
	*/
	
	function update_permission($id,$data=array()){
		$this->db->where('permissionID',$id);
		$this->db->update($this->table_permission,$data);
	}
		
	
/********************************************************
*
* Permission Map  Table Related functions 	
*
**********************************************************/		
	
	/**
		@Function Name:	get_user_permission_array
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		$group_id     |integer | group id 
		@return  object array
		@Purpose: insert data 
	
	*/
	
	function get_user_permission_array($group_id=0){
		$result=array();
		$this->db->select('map.groupID, map.permissionID,p.key');
		$this->db->join($this->table_permission.' p','map.permissionID=p.permissionID');
		$this->db->where('map.groupID',$group_id);
		$query = $this->db->get($this->table_permission_map.' map');
		foreach( $query->result() as $res){
			$result[$res->permissionID] = $res->key;
		}
		return $result;
		
	}
	
	
	/**
		@Function Name:	insert_map
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@data     | array | array of data 
		@return   integer 
		@Purpose:		insert the new record and return last increment id 
	
	*/
	
	function insert_map($data){
		$this->db->insert($this->table_permission_map,$data);
		return $this->db->insert_id(); 
		
	}
	
	
	
	/**
		@Function Name:	delete_map
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@data     | array | array of data 
		@return   boolean 
		@Purpose:	update record
	
	*/
	function delete_map($group_id){
		$this->db->where('groupID',$group_id);
		$this->db->delete($this->table_permission_map);
		return TRUE;
	}
	
	
/********************************************************
*
* Permission Groups Table Related functions 	
*
**********************************************************/	
	
	/**
		@Function Name:	get_groups
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@permission    | boolean | permission flag 
		@Purpose:		get all user groups ,if permission is set true ,also get the group permissions 
	*/
	
	function get_groups($permission=false){
		$result = array();
		$this->db->select('groupID,groupName,groupKey');
		$query = $this->db->get($this->table_permission_groups);
		$result=$query->result();
		if( $query->num_rows() > 0 ){
			$result = $query->result();	
		}
		return $result;
		
	}
	
	/**
		@Function Name:	get_single_group
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@permission     | boolean | permission flag 
		@Purpose:		get all user groups ,if permission is set true ,also get the group permissions 
	*/
	
	function get_single_group($id,$permission=false){
		$result = array();
		$this->db->where('groupID',$id);
		$query = $this->db->get($this->table_permission_groups);
		$result = $query->row();
		if($query->num_rows()>0){
			/*
			if($permission){
				$result->instructor = $this->get_permissions($result->groupID);
			}
			*/
		}
		return $result;
	}
	
	
	/**
		@Function Name:	insert_group
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@data     | array | array of data 
		@return   integer 
		@Purpose:		insert the new record and return last increment id 
	*/
	function insert_group($data)
	{
		$this->db->insert($this->table_permission_groups,$data);
		return $this->db->insert_id(); 
	}
		
	/**
		@Function Name:	update_group
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@data     | array | array of data 
		@return   void
		@Purpose:	update the record 
	*/
	
	function update_group($id,$data)
	{	
		$this->db->where('groupID',$id);
		$this->db->update($this->table_permission_groups,$data);
	}
	
	
	/**
		@Function Name:	check_duplicate
		@Author Name:	ben binesh
		@Date:			Oct 30, 2013
		@id           | numeric| primary key of record 
		$name         |string | key of  group
		@return  integer
		@Purpose:		count duplicate records with matching filter
	
	*/
	
	function check_duplicate_group($id=0,$name=''){
		if($name)
			$this->db->where('groupKey',$name);
		if($id)
			$this->db->where('groupID !=',$id);
		$count =  $this->db->count_all_results($this->table_permission_groups);
		return $count;
	}
	
}//end of class
//end of file 
	
	