<?php
/**
@Page/Module Name/Class: 		course_definition_model.php
@Author Name:			 		binesh
@Date:					 		Aug, 19 2013
@Purpose:		        		Contain all data management functions for the course generes
@Table referred:				course_genres
@Table updated:					course_genres
@Most Important Related Files	NIL
*/

//Chronological development
//***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
/***********************************************************************************
//RF1.	  |  ben binesh		 | Nov ,16 2013  | major	   | add the BYOC session
							                                 related functions 
  
//***********************************************************************************/


class Course_definition_model extends CI_Model {
	
	public $table_name='course_definitions';
	public $table_session='byoc_session';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@title   	   | String  | title of course
		@status        | numeric | status of record 
		@start         | numeric | start offset of record 
		@limit         | numeric | limit of record 
		@return        | array 
		@Purpose:	   get  multiple records 
	
	*/
		
	function get_records($title = '', $genre = 0, $facilitator = 0, $course_id = '' ,$status = '',$start = 0 , $limit = 10){
		$result=array();
		
		$this->db->select("
				cd.cdID,cd.cdPublish,cd.cdCourseID,cd.cdCourseTitle,cd.cdGenre, cd.cdFeatured,
				cg.cgTitle,cg.cgCourseCredits,	
				(
					SELECT 
						COUNT( uID ) 
					FROM 
						course_reservations
					JOIN 
						course_schedule ON csID = urCourse
					JOIN 
						course_definitions ON cdID = csCourseDefinitionId
					WHERE 
						cdID = cd.cdID
						AND
							urStatus=".STATUS_REGISTERED."	
					
				) AS registered_count,
				,
				(
					SELECT SUM(
						(

							SELECT 
								COUNT( id ) 
							FROM 
								users
							WHERE 1 
							AND 
								(
									FIND_IN_SET(csID,memberships) <> 0
								) 
							AND 
								accessLevel =".MEMBER."
						)
					) AS enrolees_count
					FROM 
						course_definitions
					JOIN
						course_schedule ON cdID = csCourseDefinitionId
					WHERE
						cdID = cd.cdID
				) AS enrolees_count
				
		",FALSE);
		$this->db->join('course_genres cg','cg.cgID = cd.cdGenre','LEFT');
		
		if($title)
			$this->db->like('cd.cdCourseTitle',$title);
		if($genre)
			$this->db->where('cd.cdGenre',$genre);	
			
		if($course_id)
			$this->db->where('cd.cdCourseID',$course_id);		
		if($status != '')
			$this->db->where('cd.cdPublish',$status);
		$this->db->order_by('cg.cgCourseCredits DESC , cd.cdCourseTitle ASC');	
		$query = $this->db->get($this->table_name.' cd', $limit , $start );
		return  $query->result();;
	}
	
	
	
	/**
		@Function Name:	count_records
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@title         | String | title of course
		@status        | numeric| status of recored 
		@return        | integer
		@Purpose:	   count  multiple records 
	
	*/
	
	function count_records($title = '', $genre = 0, $facilitator = 0, $course_id = '' ,$status = ''){
		$this->db->join('course_genres cg','cg.cgID = cd.cdGenre','LEFT');
		
		if($title)
			$this->db->like('cd.cdCourseTitle',$title);
		if($genre)
			$this->db->where('cd.cdGenre',$genre);	
		
		if($course_id)
			$this->db->where('cd.cdCourseID',$course_id);		
		if($status != '')
			$this->db->where('cd.cdPublish',$status);
		return $this->db->count_all_results($this->table_name.' cd');
		
	}
	
	/**
		@Function Name:	get_single_record
		@Author Name:	binesh
		@Date:			Aug, 19 2013
		@id            | numeric| primary key of record 
		@return        | array
		@Purpose:	   get the single record 
	
	*/
	function get_single_record($id=0,$select='*'){
		$result=array();
		$this->db->select($select);
		$this->db->where('cdID',$id);
		$query = $this->db->get($this->table_name);
		return $query->row();
		
	}
	
	/**
		@Function Name:	check_duplicate
		@Author Name:	binesh
		@Date:			Aug, 19 2013
		@id            | numeric| primary key of record 
		@course_id     | String | course id
		@return        | integer
		@Purpose:	   count duplicate records with matching filter
	
	*/
	
	function check_duplicate($id=0,$course_id=''){
		if($course_id)
			$this->db->where('cdCourseID',$course_id);
		if($id)
			$this->db->where('cdID !=',$id);
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
		@return  integer
		@Purpose:		udate data 
	
	*/
	function update($id,$data=array()){
		$this->db->where('cdID',$id);
		$this->db->update($this->table_name,$data);
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
		$this->db->delete($this->table_name, array('cdID' => $id)); 
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
		@return  string
		@Purpose:		return status string 
	
	*/
	function show_status($status = 0){
		$status_array =self::get_status_array();
		return (isset($status_array[$status]))?$status_array[$status]:'Unpublished';
	}
	
	/**
		@Function Name:	get_landing_page
		@Author Name:	binesh
		@Date:			Aug, 19 2013
		@genre_id  | numeric| course type id 
		@data   | array | array of single record 
		@return  array
		@Purpose:		return status string 
	
	*/
	function get_landing_page($genre_id=0,$select='*'){
		$this->db->select($select);
		$this->db->where('cdGenre',$genre_id);
		$this->db->where('cdPublish',STATUS_PUBLISH);
		$this->db->order_by('cdID','ASC');
		$query=$this->db->get($this->table_name,1);
		return $query->row();
	}
	
	
/*********************************************************
	* Rf1	
	/ Build you own course session related functions 
	/
**********************************************************/	
	
	/**
		@Function Name:	get_sessions
		@Author Name:	ben binesh
		@Date:			Nov, 06 2013
		@Purpose:		show the multiple records and filter 
	
	*/		
	
	function get_sessions($year='',$start=0,$limit=50,$current=false)
	{
		$this->db->select('*');
		if($year != '')
			$this->db->where('YEAR(bsStartDate)',$year);
			
		if($current)
			$this->db->where('bsEndDate >',DATE('Y-m-d'));
			
		$this->db->order_by('bsStartDate', 'DESC');
		if($limit < 0 )	
		{
			$query = $this->db->get($this->table_session);
		}
		else
		{
			$query = $this->db->get($this->table_session, $limit , $start );
		}
		return  $query->result();;	
		
	}
	
	
	/**
		@Function Name:	get_sessions
		@Author Name:	ben binesh
		@Date:			Nov, 06 2013
		@Purpose:		show the multiple records and filter 
	
	*/		
	
	function count_sessions($year='')
	{
		if($year != '')
			$this->db->where('YEAR(bsStartDate)',$year);
		$count = $this->db->count_all_results($this->table_session);
		return $count;
		
	}
	
	
	/**
		@Function Name:	get_session_dropdown
		@Author Name:	ben binesh
		@Date:			Nov, 06 2013
		@Purpose:		return the course session as key value pair array 
	
	*/	
	
	function get_session_dropdown(){
		$session_array=array(''=>'Select');
		$sessions=self::get_sessions($year='',0,10);
		if( $sessions ){
			foreach( $sessions as $session )
			{
				$session_array[$session->bsID] = format_date($session->bsStartDate,DATE_FORMAT).'-'.format_date($session->bsEndDate,DATE_FORMAT);
			}
		}
		return $session_array;
	}
	
	
	function get_single_session($id=0)
	{
		$this->db->where('bsID',$id);	
		$query = $this->db->get($this->table_session);
		return $query->row();
	}
	
	/**
		@Function Name:	insert_session
		@Author Name:	ben binesh
		@Date:			Nov, 06 2013
		@data   | array | array of single record 
		@return  integer
		@Purpose:		insert data 
	
	*/
	function insert_session($data=array())
	{
		$this->db->insert($this->table_session,$data);
		return $this->db->insert_id(); 
	}
	
	
	/**
		@Function Name:	update_session
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		update data 
	
	*/
	function update_session($id,$data=array()){
		$this->db->where('bsID',$id);
		$this->db->update($this->table_session,$data);
		return true;
	}
	
	
	/**
		@Function Name:	delete_session
		@Author Name:	ben binesh
		@Date:			Nov, 06 2013
		@id  | numeric| primary key of record 
		@return  boolean
		@Purpose:		delete data 
	
	*/
	function delete_session($id){
		$this->db->delete($this->table_session, array('bsID' => $id)); 
		return true;
	}
	
	
	function get_courses(
					$title = '',
					$genre = 0,
					$location='',
					$course_id,
					$status = STATUS_PUBLISH,
					$start = 0 ,
					$limit = 10,
					$featured=FALSE
				)
	{
		$result=array();
		$this->db->select("
				cd.cdID,cd.cdPublish,cd.cdCourseID,cd.cdCourseTitle,cd.cdGenre,
				cg.cgTitle,cg.cgCourseCredits,cg.cgImage");
		$this->db->join('course_genres cg','cg.cgID = cd.cdGenre','LEFT');
		
		if($title)
			$this->db->like('cd.cdCourseTitle',$title);
		if($genre)
			$this->db->where('cd.cdGenre',$genre);	
			
		if($course_id)
			$this->db->where('cd.cdCourseID',$course_id);		
			
		if($featured)
			$this->db->where('cd.cdFeatured',FEATURED);	
			
		if($status != '')
			$this->db->where('cd.cdPublish',$status);
			
		if(!$featured)
			$this->db->group_by('cd.cdGenre');	
		
		$this->db->order_by('cg.cgDisplayOrder ASC , cd.cdCourseTitle ASC');	
		
		
		if(0 < $limit)
			$query = $this->db->get($this->table_name.' cd', $limit , $start );
		else
			$query = $this->db->get($this->table_name.' cd');
		return  $query->result();;		
				
	}
	
	
	function count_courses(
				$title = '',
				$genre = 0,
				$location='',
				$course_id=0,
				$status = STATUS_PUBLISH,
				$featured=FALSE
				)
	{
		if($title)
			$this->db->like('cd.cdCourseTitle',$title);
		if($genre)
			$this->db->where('cd.cdGenre',$genre);	
		
		if($course_id)
			$this->db->where('cd.cdCourseID',$course_id);

		if($featured)
			$this->db->where('cd.cdFeatured',FEATURED);	
			
		if($status != '')
			$this->db->where('cd.cdPublish',$status);
		$count = $this->db->count_all_results($this->table_name.' cd');
		return $count;
		
	}
	
	
	
}//end of class
//end of file 
	
	