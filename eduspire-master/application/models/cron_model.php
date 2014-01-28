<?php  
/**
@Page: 		        cron_model.php
@Author Name:		Alan Anil
@Date:			    11-10-2013
@Purpose:		    File using for database functions for sending emails to users.
@Table referred:	auto_email, auto_email_log.	
@Table updated:	    NA.
@Most Important Related Files: NA
 */

//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description 
//----------------------------------------------------------------------------------------   
//---------------------------------------------------------------------------------------- 

class Cron_model extends CI_Model {
	
 	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	/**
		@Function Name:	get_registered_users
		@Author Name:	alan anil
		@Date:			Oct 10 2013
		@data   | array | array of single record 
		@return  integer
		@Purpose:		Get registred users. 
	
	*/
	
	function get_registered_users($userEmail=''){
		$this->db->select('uID,urEmail,urStatus,urCourse,urFirstName,urLastName');  
		$this->db->where('urEmail',$userEmail);
		$query = $this->db->get('course_reservations');
		return $row = $query->row_array();
		 
	}
	/**
		@Function Name:	verify_user
		@Author Name:	alan anil
		@Date:			Oct 10 2013
		@data   | array | array of single record 
		@return  integer
		@Purpose:		Send email to user. 
	
	*/
	
	function verify_user(){
		$this->db->select('uID,urEmail,urStatus'); 
		$query = $this->db->get('course_reservations');
		return $query->result();
	}
	
	/**
		@Function Name:	get_email_templates
		@Author Name:	alan anil
		@Date:			Oct 10 2013
		@data   | array | array of single record 
		@return  integer
		@Purpose:		Send email to user. 
	
	*/
	
	function get_email_templates(){
		$this->db->select('*'); 
		$this->db->where('aeActive',1);
		$query = $this->db->get('auto_email'); 
		return $query->result();
	}
	
	/**
		@Function Name:	add_auto_email_log
		@Author Name:	alan anil
		@Date:			Oct 182013
		@data   | array | array of single record 
		@return  integer
		@Purpose:		insert data 
	
	*/
	function add_auto_email_log($data){	
		$this->db->insert('auto_email_log', $data);  
	}
	
	
	
	
	 	
}	