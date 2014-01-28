<?php 
/**
@Page/Module Name/Class: 		email_template_model.php
@Author Name:			 	Janet Rajani
@Date:					Sep,2 2013
@Purpose:		        	Managing all emails
@Table referred:			email_templates
@Table updated:				email_templates
@Most Important Related Files	NIL
Chronological development
//***********************************************************************************
//| Ref No.|  Author name	 | Date	             | Severity | Modification description
//***********************************************************************************
//RF1	  |   Alan Anil	  | Oct 8 2013 | Changes in function to add cron emails in email lists.
//RF1	  |   Alan Anil	  | Oct 10 2013  | Function for updating cron email templates records.
//***********************************************************************************  
 */

class Email_template_model extends CI_Model 
{
	public $table_name='email_templates';
	public $auto_email= 'auto_email';
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	Janet Rajani
		@Date:			Sep, 2 2013
		@title   | String | title of course
		@status  | numeric| status of recored 
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@return  array 
		@Purpose:		get  multiple records 
	
	*/
	// RF1 Start	
	function get_records($title = '',$subject='',$start = 0 , $limit = 10)
        { 
		$sqlQuery = "SELECT 
		                        etID, etSubject, etTitle, etCopy, etTarget, etTemplate, etCredit, etReimbursed ,etAuthor
					FROM 
					            email_templates
					UNION
					SELECT 
				 	            aeID AS etID, aeSubject AS etSubject, aeSystemTitle AS etTitle, aeCopy AS etCopy,aeTarget AS etTarget,
								aeTriggerDays AS etTemplate, aeID AS etCredit, aeSubject AS etReimbursed ,aeActive AS etAuthor
					
					FROM      
					            auto_email
					            LIMIT $start , $limit";
		$query = $this->db->query($sqlQuery);
		return $query->result();
	}
	// RF1 End.
	 
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
		$query = $this->db->get($this->table_name);
		return $query->row();
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
	
	function count_records($title = '',$subject='')
        {
		if($title)
			$this->db->like('etTitle',$title);
		if($subject)
			$this->db->where('etSubject',$subject);	
		//RF1	
		$emailTemplateRecords = $this->db->count_all_results($this->table_name);
		// To count no of cron emails.  
		$autoEmailsRecords    = $this->db->count_all_results($this->auto_email);
                // Combine all results.
		$totalCount = $emailTemplateRecords + $autoEmailsRecords;
		return $totalCount;	
		//RF1 end. 
	}
	
	/**
		@Function Name:	insert
		@Author Name:	Janet Rajani
		@Date:			Sep, 2 2013
		@data   | array | array of single record 
		@return  integer
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
		@Date:			Sep, 2 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		udate data 
	
	*/
	function update($id,$data=array())
        {
		$this->db->where('etID',$id);
		$this->db->update($this->table_name,$data);
		return true;
	}
// RF2 Start	
	/**
		@Function Name:	get_single_cron_record
		@Author Name:	Alan Anil
		@Date:			Sep, 7 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_cron_record($id=0)
        {
		$this->db->where('aeID',$id);
		$query = $this->db->get($this->auto_email);
		return $query->row();
	}
	
	/**
		@Function Name:	update_cron_email
		@Author Name:	Alan Anil
		@Date:			Sep, 7 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		Insert and update records. 
	
	*/
	function update_cron_email($id=0,$data=array())
    {
			if($id) {
				$this->db->where('aeID',$id);
				$this->db->update($this->auto_email,$data);
			}
			else {
				$this->db->insert($this->auto_email,$data);
			}
		return true;
	}
// RF2 End	
	
}//end of class
//end of file 