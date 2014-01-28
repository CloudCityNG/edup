<?php 
// ********************************************************************************************************************************
//Page name			:- 			assignment_model.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			File used for showing/add all assignments regarding any course.  
//Date				:- 			05-09-2013
//Table Refered		:-  		N/A
//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description 
//----------------------------------------------------------------------------------------  
// Ref1    ben.binesh		Oct 03             normal 	add the assignment management functions for the adminstrator backend  
//// Ref2    ben.binesh		Oct 07             normal 	add the assignment ledger related functions  backend  	
//// Ref3    ben.binesh		Nov 12             normal 	add assignment completed checking  code to the function geUserAllAssignments()
//Ref4    ben.binesh		Dec 26             normal 	add function for remove ledger entry 																
//Ref5    ben.binesh		Dec 26             normal 	add condition to show active assignment only 							
//---------------------------------------------------------------------------------------- 

class Assignment_model extends CI_Model {
	 
	public $table_assignment='assignments';
	public $table_assignment_ledger='assignment_ledger';
	
	public function __construct()
	{
		parent::__construct(); 
	}
	 
	/**
		@Function Name:	getUserCourse
		@Author Name:	alan anil
		@Date:			Sep, 05 2013
		@data   | array | array of single record 
		@return  integer
		@Purpose:		insert data 
	
	*/
	
	function getUserCourse($userId){
		$this->db->select('membershipLastUsed');   
		if($userId != '')
		$this->db->where('id',$userId);
		$query = $this->db->get('users');
		
		$row = $query->row_array();
		return $row['membershipLastUsed'];
	}
	/**
		@Function Name:	insertAssignments
		@Author Name:	alan anil
		@Date:			Sep, 05 2013
		@data   | array | array of single record 
		@return  integer
		@Purpose:		insert data 
	
	*/
	function insertAssignments($data){	
		$this->db->insert('assignments', $data);  
	} 
 	
 	/**
		@Function Name:	geUserAllAssignments
		@Author Name:	alan anil
		@Date:			Sep, 05 2013
		@userId   | numeric | user id 
		@lastMember   | numeric | user last member ship course
		@start          | numeric | start offset of record 
		@limit          | numeric | limit of record ,give -1 to remove limit
		@checkComplted  | numeric |  user id who completed the assignment .it will check the ledger entry  in		 assignent_ledger for particular user 
		@active         | boolean | flag for active assignments							 
		@return  integer
		@Purpose:		get user assignments  
	
	*/
	/*
		REf3
		Ref5 //add active argument 
	*/
 	function geUserAllAssignments($userId, $lastMember, $start = 0 , $limit = 10,$checkCompleted = 0,$active = false){
		/*
			Ref3
		*/
		$assginment_select='assignID,  assignAuthor, assignCnfID, creationDate, assignTitle, assignActiveDate, assignActiveTime, assignType,assignDueDate, assignDueTime, assignPoints,assignQuestionnaire';

		
		if($checkCompleted)
		{	
			$assginment_select .= ',
				(SELECT 
					alID 
				FROM
					'.$this->table_assignment_ledger.'
				WHERE
				alAssignID=assignId 
				AND 
					alUserID='.$checkCompleted.'
				)AS ledger
			';
		}
		$this->db->select($assginment_select); 
		/*
			Ref5
		*/
		if($active)
		{
			$this->db->where('assignActiveDate <= ',date('Y-m-d'));
			//$this->db->where('assignActiveTime <= ',date('H:i:s'));
		}
		
		/*
			End Ref5
		*/		
		/*
			End Ref3
		*/
		
		if($userId)
			$this->db->where('assignAuthor',$userId);
		if($lastMember != '')
		$this->db->where('assignCnfID',$lastMember);
		if($limit > 0 ){
		$query = $this->db->get('assignments', $limit , $start );
		}else{
			$query = $this->db->get('assignments');
		}
		return $query->result();
		
		
	}
	 
	
	/**
		@Function Name:	countNumRecords
		@Author Name:	Alan Anil
		@Date:			Sep, 09 2013
		@Purpose:		count  multiple records 
	
	*/
	
	function countNumRecords($userId='', $getLstMember){
		if($userId)
			$this->db->like('assignAuthor',$userId);
		if($getLstMember != '')
			$this->db->where('assignCnfID',$getLstMember);
		return $this->db->count_all_results('assignments');
		
	}
 	
	/**
		@Function Name:	getAssignDetails
		@Author Name:	Alan Anil
		@Date:			Sep, 09 2013
		@Purpose:		count  multiple records 
	
	*/
	
	function getAssignDetails($getAssignId){
		if($getAssignId != '')
		$this->db->where('assignID',$getAssignId);
		$query = $this->db->get('assignments');
		return $query->result();
		
	}
	
	/**
		@Function Name:	updateAssignments
		@Author Name:	Alan anil
		@Date:			Sep, 09 2013
		@Purpose:		udate data 
	
	*/
	function updateAssignments($AssignId,$data=array()){
		$this->db->where('assignID',$AssignId);
		$this->db->update('assignments',$data);
		return true;
	}
	
	/**
		@Function Name:	deleteAssignments
		@Author Name:	Alan anil	
		@Date:			Sep, 09 2013 
		@Purpose:		delete data 
	
	*/
	function deleteAssignments($id){
		if($id != '')
		$this->db->delete('assignments', array('assignID' => $id)); 
		
		return true;
	}
	/**
		@Function Name:	manual_grade_assignments
		@Author Name:	Alan anil	
		@Date:			Sep, 13 2013 
		@Purpose:		show assignment user data 
	
	*/
	function manual_grade_assignments($id, $start = 0 , $limit = 10){
		$this->db->select('alUserID, alGrade');   
		if($id != '')
		$this->db->where('alAssignID',$id);
		$query = $this->db->get('assignment_ledger'); 
		return $query->result();
	}
	  
	/**
		@Function Name:	show_graded
		@Author Name:	Alan anil	
		@Date:			Sep, 13 2013 
		@Purpose:		show assignment user data 
	
	*/
	function show_graded($id){ 
		if($id != '')
			$this->db->where('alAssignID',$id);
		return $this->db->count_all_results('assignment_ledger');
	}
	/**
		@Function Name:	get_user_details_assign
		@Author Name:	Alan anil	
		@Date:			Sep, 13 2013 
		@Purpose:		show user details for assignments.
	
	*/
	function get_user_details_assign($id){ 
		$this->db->select('firstName,lastName');  
		$this->db->where('accessLevel',40);
		$query = $this->db->get('users');
		return $query->result();
	}
	
	/**
		@Function Name:	show_assign_user_details
		@Author Name:	Alan anil	
		@Date:			Oct, 01 2013 
		@Purpose:		show user details for assignments on the basis of user id.
	
	*/
	function show_assign_user_details($userId)
	{
		$this->db->select('u.firstName,u.lastName,u.profileImage,u.email ,up.role, up.districtAffiliation, up.gradeSubject');   
		
		$this->db->from('users u');
   		$this->db->join('users_profiles up', 'u.id = up.user_id');
		
		if($userId != '')
		$this->db->where('u.id',$userId);
		$query = $this->db->get(); 
		
		return  $query->result(); 
	}
	/**
		@Function Name:	get_assgin_users
		@Author Name:	Alan anil	
		@Date:			Oct, 02 2013 
		@Purpose:		show users from assignment id.
	
	*/
	function get_assign_users($getAssignList)
	{
		$this->db->distinct();
	    $this->db->select('alUserID');   
		if($getAssignList != '')
		$this->db->where_in('alAssignID',$getAssignList);
		$query = $this->db->get('assignment_ledger');  
		$row = $query->result();
		return $row;
	}
	/**
		@Function Name:	get_total_user_assign
		@Author Name:	Alan anil	
		@Date:			Oct, 02 2013 
		@Purpose:		Get user all completed assignments from user id.
	
	*/
	function get_total_user_assign($userId, $courseId = 0)
	{
		$this->db->select('count(al.alAssignID) as completedAssign, sum(al.alGrade) as pointsGot , 
								sum(a.assignPoints) as totalPoints, al.alUserID');   
		
		$this->db->from('assignment_ledger al');
   		$this->db->join('assignments a', 'a.assignID = al.alAssignID');
		
		if($userId != '')
		$this->db->where('al.alUserID',$userId);
		
		if($courseId) {
			$this->db->where('al.alCnfID',$courseId); 
		}	
		$query = $this->db->get(); 
		$row = $query->row_array();
		return $row;
		
	}
	
	/**
		@Function Name:	get_total_user_assign
		@Author Name:	Alan anil	
		@Date:			Oct, 02 2013 
		@Purpose:		Get user all completed assignments from user id.
	
	*/
	function get_points_earned($userId,$assgnmentId)
	{
		$this->db->select('al.alGrade as pointsGot, a.assignPoints as totalPoints, al.alDateSubmitted, al.alCommentStudent');   
		
		$this->db->from('assignment_ledger al');
   		$this->db->join('assignments a', 'a.assignID = al.alAssignID');
		
		if($userId != '')
		$this->db->where('al.alUserID',$userId);
		
		if($assgnmentId != '')
		$this->db->where('al.alAssignID',$assgnmentId);
		
		$query = $this->db->get(); 
		$row = $query->row_array();
		return $row;
		
	}
	
	/**
	Chronological development
	//***********************************************************************************
	//| Ref No   | Name	| Date		| Purpose //***********************************************************************************
	//RF1	    | ben binesh	|  Oct 07, 2013	| add the assignment id filter to the functions 
	//***********************************************************************************


	*/
	function get_users_grade_details($userId=0,$assignment_id=0)
	{
		$this->db->select('al.alGrade, a.assignPoints , a.assignTitle');   
		
		$this->db->from('assignment_ledger al');
   		$this->db->join('assignments a', 'a.assignID = al.alAssignID');
		
		if($userId != '')
			$this->db->where('al.alUserID',$userId);
		/*
			Rf1
		*/
		if($assignment_id)
			$this->db->where('al.alAssignID',$assignment_id);
		
		$query = $this->db->get(); 
		
		return  $query->result();

	} 
	
	/**
		@Function Name:	percentage
		@Author Name:	Alan anil	
		@Date:			Oct, 02 2013 
		@Purpose:		Calculate percentage.
	
	*/
	function percentage($val1, $val2, $precision=0) 
	{
		$division = $val1 / $val2;
		
		$res = $division * 100;
		
		$res = round($res, $precision);
		$gardeValue = '';
		 
		if($res < 65 )
		$gardeValue = 'F';
		
		if($res >= 65 && $res < 70 )
		$gardeValue = 'D';
		
		if($res >= 70 && $res < 80 )
		$gardeValue = 'C';
		
		if($res >= 80 && $res < 90 )
		$gardeValue = 'B';
		
		if($res >= 90 && $res <= 100 )
		$gardeValue = 'A';
		return $gardeValue;
	}
	/**
		@Function Name:	get_track_user
		@Author Name:	Alan anil	
		@Date:			Oct, 02 2013 
		@Purpose:		Get user track.
	
	*/
	
	function get_track_user($getTrId)
	{
		$this->db->select('trName');   
		if($getTrId != '')
		$this->db->where('trID',$getTrId);
		$query = $this->db->get('tracks'); 
		$row = $query->row_array();
		return $row['trName']; 
	}
	/**
		@Function Name:	show_user_grade_sum
		@Author Name:	Alan anil	
		@Date:			Oct, 02 2013 
		@Purpose:		Get user grade, total grade.
	
	*/
	
	function show_user_grade_sum($userId, $activeCourse)
	{
		$this->db->select('sum(alGrade) as gradeGot , count(alID) totalRec');  
		$this->db->where('alCnfID',$activeCourse);
		$this->db->where('alAssignType',10);
		$this->db->where('alUserID',$userId);
		$query = $this->db->get('assignment_ledger'); 
		$row = $query->row_array();
		return $row;
		
		
	}
	/**
		@Function Name:	get_user_grade
		@Author Name:	Alan anil	
		@Date:			Oct, 07 2013 
		@Purpose:		Get user grade, total grade.
	
	*/
	
	function get_user_grade($userId, $activeCourse)
	{
		$this->db->select('fgComputedGrade ,fgGrade, fgApproved, fgCommentAdmin ,fgCommentStudent');  
		$this->db->where('fgUserID',$userId);
		$this->db->where('fgCnfID',$activeCourse);
		$query = $this->db->get('final_grades'); 
		$row = $query->row_array();
		return $row;
		 
	}
	/**
		@Function Name:	update_assignment_grade
		@Author Name:	Alan anil	
		@Date:			Oct, 02 2013 
		@Purpose:		Get user grade, total grade.
	
	*/
	
	function update_assignment_grade($userId,$assignId, $data=array())
	{ 
		if($userId != '' && $assignId != '') {
			$this->db->where('alAssignID',$assignId);
			$this->db->where('alUserID',$userId);
			$this->db->update('assignment_ledger',$data);
			return true;
		}
		else
		{
			return false;
		}
		
	  
	}
	/**
		@Function Name:	update_grade_entry
		@Author Name:	Alan anil	
		@Date:			Oct, 07 2013 
		@Purpose:		Get user grade, total grade.
	
	*/
	
	function update_grade_entry($assignUserId, $getLstMember, $gradeArr=array())
	{ 
	   
		if($assignUserId != '' && $getLstMember != '') { 
			$this->db->where('fgUserID',$assignUserId);
			$this->db->where('fgCnfID',$getLstMember);
			$this->db->update('final_grades',$gradeArr);
			return true;
		}
		else
		{
			return false;
		}
		
	  
	}
	
	/**
		@Function Name:	check_assignment_lock
		@Author Name:	Alan anil	
		@Date:			Oct, 02 2013 
		@Purpose:		Get user grade, total grade.
	
	*/
	
	function check_assignment_lock($assignId=0)
	{ 
		$this->db->select('count(alGrade) as unLockGrade');  
		$this->db->where('alAssignID',$assignId);
		$query = $this->db->get('assignment_ledger'); 
		$row = $query->row_array();
		return $row;
		  
	}
/**
		@Function Name:	get_no_of_users_assigments
		@Author Name:	Alan anil	
		@Date:			Oct, 02 2013 
		@Purpose:		Get user grade, total grade.
	
	*/
	
	function get_no_of_users_assigments($assignId=0,$userLastMember)
	{ 
		$this->db->select('count(alGrade) as unLockGrade');  
		$this->db->where('alAssignID',$assignId);
		$query = $this->db->get('assignment_ledger'); 
		$row = $query->row_array();
		return $row;
		  
	}	
	/**
		@Function Name:	update_assign_leadger
		@Author Name:	Alan anil	
		@Date:			Oct, 02 2013 
		@Purpose:		Get user grade, total grade.
	
	*/
	
	function update_assign_leadger($userId,$assignId,$getLstMember=0, $data=array())
	{ 
		if($userId != '' && $assignId != '') {
			$this->db->where('alAssignID',$assignId);
			$this->db->where('alUserID',$userId); 
			if($getLstMember){
				$this->db->where('alCnfID',$getLstMember);
			}	
			$this->db->update('assignment_ledger',$data);
			return true; 
		}	
	}
	/**
		@Function Name:	get_user_assignments
		@Author Name:	Alan anil	
		@Date:			Nov, 14 2013 
		@Purpose:		Get user grade, total grade.
	
	*/
	
	function get_user_assignments($userId,$getLstMember)
	{ 
		if($userId != '' && $getLstMember != '') { 
			$this->db->select('al.alCnfID,	al.alAssignID,	al.alGrade, a.assignTitle');   
			$this->db->from('assignments a');
			$this->db->join('assignment_ledger al', 'a.assignID = al.alAssignID', 'left outer');
			$this->db->where('a.assignCnfID',$getLstMember);
			$this->db->where('al.alUserID',$userId);
			$query = $this->db->get();
			return $query->result();
		}
		else
		{
			return false;
		} 
	}
	
	/**
		@Function Name:	show_assign_grades
		@Author Name:	Alan anil	
		@Date:			Nov, 20 2013 
		@Purpose:		Get user grade, total grade.
	
	*/
	
	function show_assign_grades($assignId)
	{   
		$this->db->select('count(alID) as completed');  
		$this->db->where('alAssignID',$assignId);
		$this->db->where('alDateSubmitted IS NOT NULL'); 
		$query = $this->db->get('assignment_ledger'); 
		$row = $query->row_array(); 
		$result['completed'] = $row['completed']; 
		
		$this->db->select('count(alID) as graded');  
		$this->db->where('alAssignID',$assignId); 
		$this->db->where('alDateSubmitted', NULL); 
		$query = $this->db->get('assignment_ledger'); 
		$row = $query->row_array(); 
		$result['graded'] = $row['graded']; 
		return $result;
		 
	}
	/**
		@Function Name:	get_course_from_assignid
		@Author Name:	Alan Anil
		@Date:			Sep, 09 2013
		@Purpose:		count  multiple records 
	
	*/
	
	function get_course_from_assignid($getAssignId){
		if($getAssignId != '')
		$this->db->select('assignCnfID');
		$this->db->where('assignID',$getAssignId);
		$query = $this->db->get('assignments');
		$row = $query->row_array();  
		return $row['assignCnfID'];
		
	}
	
	/**
		@Function Name:	get_all_course_users_id
		@Author Name:	Alan Anil
		@Date:			Jan, 08 2014
		@Purpose:		get user list 
	
	*/
	
	function get_all_course_users_id($getAssignId){
		if($getAssignId != '') {
			$this->db->select('id');
			$this->db->where_in('memberships'  ,$getAssignId);
			$this->db->where('accessLevel',MEMBER); 
			$query = $this->db->get('users'); 
			return $query->result();
		}
		
	}
	
	/**
		@Function Name:	find_user_entry_al
		@Author Name:	Alan Anil
		@Date:			Jan 08 2014
		@Purpose:		check user entry in assignment_ledger table. 
	
	*/
	
	function find_user_entry_al($userId, $courseID, $assignId){
		if($assignId != '') {
			$this->db->select('alID');   
			$this->db->where('alAssignID',$assignId); 
			$this->db->where('alCnfID',$courseID);
			$this->db->where('alUserID',$userId);
			$query = $this->db->get('assignment_ledger'); 
			$row = $query->row_array();
			return $row;
		}
		
	}
	
	/**
		@Function Name:	insert_assignments_ledger
		@Author Name:	alan anil
		@Date:			Jan, 08 2014
		@data   | array | array of single record 
		@return  integer
		@Purpose:		insert data 
	
	*/
	function insert_assignments_ledger($data){	
		$this->db->insert('assignment_ledger', $data);  
	} 
	/**
		@Function Name:	find_user_entry_fg
		@Author Name:	Alan Anil
		@Date:			Jan 11 2014
		@Purpose:		check user entry in final_grades table. 
	
	*/
	
	function find_user_entry_fg($userId, $courseID){
		$row = ''; 
		if($userId != '' && $courseID != '') {
			$this->db->select('fgUserID');   
			$this->db->where('fgUserID',$userId); 
			$this->db->where('fgCnfID',$courseID); 
			$query = $this->db->get('final_grades'); 
			$row = $query->row_array();
			return $row;
		}
		
		
		
	}
	
	/**
		@Function Name:	insert_final_grades_entry
		@Author Name:	alan anil
		@Date:			Jan, 10 2014
		@data   | array | array of single record 
		@return  integer
		@Purpose:		insert data into final_grades.
	
	*/
	function insert_final_grades_entry($data){	
		$this->db->insert('final_grades', $data);  
	} 
	
/******************************************************
* Ref 1
* Administrator related functions for assignments
*
*******************************************************/	
	/**
		@Function Name:	get_records
		@Author Name:	ben binesh
		@Date:			Oct, 03 2013
		@course_id      | numeric | couser id 
		@author_id      | numeric | assignment author 
		@type           | numeric | assignment type
		@title          | String | assignment title 
		@start          | numeric | start offset of record 
		@limit          | numeric | limit of record ,give -1 to remove limit
		@return  array 
		@Purpose:		get  multiple records 
	
	*/
	
	function get_records(
			$course_id=0,
			$author_id=0,
			$type=0,
			$title='',
			$start=0,
			$limit=20,
			$definition_id=0,
			$order_by='assignTitle ASC'
	)
	{
		$this->db->select('
			assignID, assignAuthor, assignCnfID, creationDate, assignType, assignTitle, assignActiveDate, assignActiveTime, assignDueDate, assignDueTime, assignPoints 
			
		');
		if( $course_id )
			$this->db->where('assignCnfID',$course_id);
		if($definition_id){
			$this->db->join('course_schedule','course_schedule.csID=assignCnfID');
			$this->db->where('course_schedule.csCourseDefinitionId',$definition_id);
		}
		if( $author_id )
			$this->db->where('assignAuthor',$author_id);
		if($type !== '')
			$this->db->where('assignType',$type);
		if($title)
			$this->db->like('assignTitle',$title);	
		$this->db->order_by($order_by);	
		
		if($limit > 0)
			$query = $this->db->get($this->table_assignment, $limit , $start );
		else
			$query = $this->db->get($this->table_assignment);

		return $query->result();
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	ben binesh
		@Date:			Oct, 03 2013
		@course_id      | numeric | couser id 
		@author_id      | numeric | assignment author 
		@type           | numeric | assignment type
		@title          | String | assignment title 
		
		@return  numeric
		@Purpose:		get  multiple records 
	
	*/
	function count_records($course_id=0,$author_id=0,$type=0,$title='',$definition_id=0){
		if( $course_id )
			$this->db->where('assignCnfID',$course_id);
		if( $author_id )
			$this->db->where('assignAuthor',$author_id);
		if( $type !== '')
			$this->db->where('assignType',$type);
		if( $title)
			$this->db->like('assignTitle',$title);

		if($definition_id){
			$this->db->join('course_schedule','course_schedule.csID=assignCnfID');
			$this->db->where('course_schedule.csCourseDefinitionId',$definition_id);
		}	
		
		$count = $this->db->count_all_results($this->table_assignment);
		return $count;	
	}
	
	/**
		@Function Name:	update_assignment
		@Author Name:	ben binesh
		@Date:			Oct, 03 2013
		@id             | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:	update data 
	
	*/
	function update_assignment($id,$data=array()){
		$this->db->where('assignID',$id);
		$this->db->update($this->table_assignment,$data);
		return true;
	}
	
	/**
		@Function Name:	get_single_assignment
		@Author Name:	ben binesh
		@Date:			Oct, 03 2013
		@id             | numeric| primary key of record 
		
		@return  array
		@Purpose:	get all data of single assignment
	
	*/
	function get_single_assignment($id=0){
		$this->db->where('assignID',$id);
		$query=$this->db->get($this->table_assignment,1);
		return $query->row();
	}
	
	/**
		@Function Name:	get_assignments_Byids
		@Author Name:	ben binesh
		@Date:			Oct, 03 2013
		@ids             | array| array of primary keys  
		
		@return  array
		@Purpose:	get all data of single assignment
	
	*/
	
	
	function get_assignments_Byids($ids=array()){
		$this->db->where_in('assignID', $ids);
		$query = $this->db->get($this->table_assignment);
		return $query->result();
		
	}
	/**
		@Function Name:	 get_assignment_type_array
		@Author Name:	 ben binesh
		@empty         |boolean| empty flag
		@emtpty_aray    |array| empty array 
		@Date:			 Oct, 03 2013
		@return          array
		@Purpose:		get array of assignments type
		
	*/
	function get_assignment_type_array($empty=false,$empty_array=array(''=>'')){
		$assignment_type_array=array();
		if($empty){
			$assignment_type_array = array_merge($assignment_type_array,$empty_array);
		}
		$assignment_type_array[ASGN_PRE_KEYNOTE]   = 'Pre-Keynote';
		$assignment_type_array[ASGN_POST_KEYNOTE] = 'Post-Keynote';
		$assignment_type_array[ASGN_TARGETED_DISCUSSION] = 'Targeted Discussion';
		$assignment_type_array[ASGN_LESSON_PLAN] = 'Lesson Plan';
		$assignment_type_array[ASGN_QUESTIONNAIRE] = 'Questionnaire';
		$assignment_type_array[ASGN_IPAD_CONFIGURATION] = 'iPad Configuration';
		$assignment_type_array[ASGN_TYPE_COURSE] = 'Course Evaluation';
		return $assignment_type_array;
	}
	
	
	
	/**
		@Function Name:	show_assignment_type
		@Author Name:	ben binesh
		@Date:			Oct, 03 2013
		@assignment_type  | numeric| assignment type value
		@return  string
		@Purpose:		return assignment type string 
	
	*/
	function show_assignment_type($assignment_type = 0){
		$assignment_type_array =self::get_assignment_type_array();
		return (isset($assignment_type_array[$assignment_type]))?$assignment_type_array[$assignment_type]:'';
	}
	
/******************************************************
* Ref 2
* Assignment ledger related functions 
*
*******************************************************/	
	
	/**
		@Function Name:	insert_ledger
		@Author Name:	ben binesh
		@Date:			Oct, 07 2013
		@data   | array | array of single record 
		@return  integer
		@Purpose:	update data 
	
	*/
	function insert_ledger($data=array())
	{
		$this->db->insert($this->table_assignment_ledger,$data);
		return $this->db->insert_id(); 
		
	}
	
	/*
		Ref 4
	*/
	
	/**
		@Function Name:	delete_ledger
		@Author Name:	ben binesh
		@Date:			Dec 26  2013
		@ledger_id      |numeric | primary key 
		@return  		void 
		@Purpose:	delete data 
	
	*/
	
	function delete_ledger($ledger_id=0){
		$this->db->delete($this->table_assignment_ledger, array('alID' => $ledger_id));
		return true;
	}
	
	
}//end of class
//end of file 
	
	