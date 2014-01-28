<?php
/**
@Page/Module Name/Class: 		login_model.php
@Author Name:			 	Janet Rajani
@Date:					Aug, 21 2013
@Purpose:		        	Contain all data management functions for the login/registration
@Table referred:			users
@Table updated:				users
@Most Important Related Files	NIL
 */

class Login_model extends CI_Model {
	
	public $table_name='course_reservations';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
		@Function Name: check
		@Author Name:	ben binesh
		@Date:			Aug, 26 2013
		@username   	string | username 
		@password    	string | password
		@data   		array | array of single record 
		@return  		array
		@Purpose:		insert data 
	
	*/
	
	function check($username='' , $password = '')
        {	

		$data = array();
		$conditions = array('userName' => $username);

		$this->db->select('id,userName,firstName,lastName,email,accessLevel,activationFlag,password');
		$query = $this->db->get_where('users',$conditions); 

		$data = $query->row();
		if(empty($data)){
			return false;
		}
		//validate password 
		if(validate_password($password,$data->password)){
			return $data;
		}
		return false;
        }
	
	/**
		@Function Name:	is_logged_in
		@Author Name:	ben binesh
		@Date:			Aug, 21 2013
		@return  		boolean 
		@Purpose:		check wether the current user is logged in or not 
	
	*/
	function is_logged_in()
        {
            if($this->session->userdata('user_id') == '')
            {
                return false;
            }
            else 
            {
                 return true;
            }
        } 
	
	/**
		@Function Name:	update_last_login
		@Author Name:	ben binesh
		@Date:			Sept 05 2013
		@return  		void
		@Purpose:		update the last login and login count 
	
	*/
	function update_last_login($id=0)
    {
		$this->db->where('id', $id);
		$this->db->set('lastLogin',date('Y-m-d H:i:s'));
		$this->db->set('loginCount', 'loginCount+1', FALSE);
		$this->db->update('users');
		return ;
    } 
	/**
		@Function Name:	insert
		@Author Name:	Janet Rajani
		@Date:			Aug, 21 2013
		@data   		array | array of single record 
		@return  		integer
		@Purpose:		insert data 
	
	*/
	
	function insert($data=array())
        {
		$this->db->insert($this->table_name,$data);
		return $this->db->insert_id(); 
	}
	
	/**
		@Function Name:	update
		@Author Name:	Janet Rajani
		@Date:			Aug, 21 2013
		@id  			numeric| primary key of record 
		@data   		array | array of single record 
		@return  		integer
		@Purpose:		udate data 
	
	*/
	function update($id,$data=array())
        {
		$this->db->where('cdID',$id);
		$this->db->update($this->table_name,$data);
		return true;
	}
	
	/**
		@Function Name:	delete
		@Author Name:	Janet Rajani
		@Date:			Aug, 21 2013
		@id  			numeric| primary key of record 
		@return  		boolean
		@Purpose:		delete data 
	
	*/
	function delete($id){
		$this->db->delete($this->table_name, array('cdID' => $id)); 
		return true;
	}
        
	/**
		@Function Name:	get_single_record
		@Author Name:	Janet Rajani
		@Date:			Sep, 2 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_record($id=0)
        {
		$this->db->where('etID',$id);
		$query = $this->db->get('email_templates');
		return $query->row();
	}
	
	
}//end of class
//end of file 
	
	