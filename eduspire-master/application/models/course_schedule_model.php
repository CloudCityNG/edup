<?php
/**
@Page/Module Name/Class: 		course_schedule_model.php
@Author Name:			 		ben binesh
@Date:					 		Aug,19 2013
@Purpose:		        		Contain all data management functions for the course schedules
@Table referred:				course_schedule
@Table updated:					course_schedule
@Most Important Related Files	NIL
 */
//Chronological development

/***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
//***********************************************************************************
//RF1.	  |  ben binesh		 |  Oct,09 2013  | minor	|  add some function related the the build your own course 

//***********************************************************************************/
class Course_schedule_model extends CI_Model {
	
	//public $assignments=array();
	public $table_name='course_schedule';
	public $table_instructor='course_instructor';
	public $table_course_date='course_schedule_date';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@title   | String | title of course
		@status  | numeric| status of record 
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@return  array 
		@Purpose:		get  multiple records 
	
	*/
		
	function get_records(
						$title       = '',
						$course_id   = '',
						$genre       = 0, 
						$start_date  = '',
						$end_date    = '',
						$status      = '',
						$course_type = '', 
						$address     = '', 
						$start       = 0 ,
						$limit       = 10,
						$one_credit  = TRUE,
						$definition_id=0,
						$session_id=0,
						$course_date=''
			)
	{
		
		$result=array();
		$this->db->select("
				cs.csID,cs.csGenreId,cs.csCourseType,cs.csCourseDefinitionId,cs.csRegistrationStartDate,cs.csRegistrationEndDate,cs.csPaymentStartDate,cs.csPublish,cs.csMaximumEnrollees,cs.csStartDate,cs.csEndDate,cs.csLocation,cs.csState,cs.csCity,
				cd.cdID,cd.cdCourseID,cd.cdCourseTitle,
				bs.bsStartDate,bs.bsEndDate,
				(
					SELECT
						COUNT(1) 
					FROM 
						course_reservations
					WHERE
						urCourse = cs.csID
						AND 
						urStatus=".STATUS_REGISTERED."
				) AS registered_count
				
				,
				(
					SELECT
						COUNT( * ) 
					FROM
						users
					WHERE
						1
					AND
					(
						FIND_IN_SET(cs.csID,memberships) <> 0
						
					)
				AND
					accessLevel =".MEMBER."
				)AS enrollee_count
				
		",FALSE);
		$this->db->join('course_genres cg','cg.cgID = cs.csGenreId','LEFT');
		$this->db->join('course_definitions cd','cs.csCourseDefinitionId = cd.cdID','LEFT');
		$this->db->join('byoc_session bs','cs.csCourseSession = bs.bsID','LEFT');
		
		if($title)
			$this->db->like('cd.cdCourseTitle',$title);
		if($course_id)
			$this->db->where('cd.cdCourseID',$course_id);	
		if($genre)
			$this->db->where('cs.csGenreId',$genre);	
			
		if(!$one_credit)
			$this->db->where('cs.csGenreId != ',BYOC_ID);	
		
		if($start_date)
			$this->db->where('cs.csRegistrationStartDate',$start_date);
		if($end_date)
			$this->db->where('cd.csRegistrationEndDate',$end_date);	
		
		if($course_date !='')
		{
			if(COURSE_CURRENT==$course_date)
			{	
				$this->db->where('cs.csEndDate > DATE_SUB(NOW(), INTERVAL 1 MONTH)','',FALSE);
				$this->db->order_by('cs.csStartDate','ASC');
			}
			elseif(COURSE_ARCHIVED==$course_date)
			{
				$this->db->where('cs.csEndDate < DATE_SUB(NOW(), INTERVAL 1 MONTH)','',FALSE);
				$this->db->order_by('cs.csStartDate','DESC');		
			}
			else
			{
				$this->db->order_by('cs.csStartDate','DESC');;
			}
			
			
		}else{
		
			$this->db->order_by('cs.csID','DESC');
		}	
		
		if($definition_id)
			$this->db->where('cs.csCourseDefinitionId',$definition_id);

		if($session_id)
			$this->db->where('cs.csCourseSession',$session_id);			
			
		if($address)
			$this->db->where("  
						(
							
							cs.csLocation LIKE '%$address%' 
							OR
							cs.csAddress LIKE '%$address%' 
							OR
							cs.csCity    LIKE '%$address%' 
							OR
							cs.csState LIKE '%$address%' 
							OR
							cs.csZIP LIKE '%$address%'
							
						)
			");		
		if($status !== '')
			$this->db->where('cs.csPublish',$status);
		if($course_type != '')
			$this->db->where('cs.csCourseType',$course_type);	
		
		
		if($limit > 0){
			$query = $this->db->get($this->table_name.' cs', $limit , $start );
		}else{
			$query = $this->db->get($this->table_name.' cs');
		}
		if( $query->num_rows() > 0 ){
			$result = $query->result();	
			foreach( $result as $key => $value ){
				$result[$key]->instructor = $this->get_instructor_array( $result[$key]->csID );
			}
			
		}
		return $result;
		
	}
	
	/**
		@Function Name:	count_records
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@title   | String | title of course
		@status  | numeric| status of record 
		@return  integer
		@Purpose:		count  multiple records 
	
	*/
	
	function count_records(
						$title       = '',
						$course_id   = '',
						$genre       = 0, 
						$start_date  = '',
						$end_date    = '',
						$status      = '', 
						$course_type = '',
						$address     = '',
						$one_credit  = TRUE,
						$definition_id=0,
						$session_id=0,
						$course_date=''
			)
	{
		$this->db->join('course_genres cg','cg.cgID = cs.csGenreId','LEFT');
		$this->db->join('course_definitions cd','cs.csCourseDefinitionId = cd.cdID','LEFT');
		
		if($title)
			$this->db->like('cd.cdCourseTitle',$title);
		if($course_id)
			$this->db->where('cd.cdCourseID',$course_id);	
		if($genre)
			$this->db->where('cs.csGenreId',$genre);	
		if($start_date)
			$this->db->where('cs.csRegistrationStartDate',$start_date);
		if($end_date)
			$this->db->where('cd.csRegistrationEndDate',$end_date);		
			
		if($definition_id)
			$this->db->where('cs.csCourseDefinitionId',$definition_id);	
		
		if($session_id)
			$this->db->where('cs.csCourseSession',$session_id);	
			
		if($address)
			$this->db->where(" 
						AND (
							
							cs.csLocation LIKE '%$address%' 
							OR
							cs.csAddress LIKE '%$address%' 
							OR
							cs.csCity    LIKE '%$address%' 
							OR
							cs.csState LIKE '%$address%' 
							OR
							cs.csZIP LIKE '%$address%'
							
						)
			");		
		if($status != '')
			$this->db->where('cs.csPublish',$status);
		if($course_type != '')
			$this->db->where('cs.csCourseType',$course_type);	
		

		if($course_date !='')
		{
			if(COURSE_CURRENT==$course_date)
			{	
				$this->db->where('cs.csEndDate > DATE_SUB(NOW(), INTERVAL 1 MONTH)','',FALSE);
			}
			elseif(COURSE_ARCHIVED==$course_date)
			{
				$this->db->where('cs.csEndDate < DATE_SUB(NOW(), INTERVAL 1 MONTH)','',FALSE);
			}
			else
			{
				
			}
			
			
		}	
		if(!$one_credit)
			$this->db->where('cs.csGenreId != ',BYOC_ID);
			
		$count = $this->db->count_all_results($this->table_name.' cs');
		return $count;
		
	}
	
	/**
		@Function Name:	get_single_record
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_record($id=0){
		$result = array();
		$this->db->where('csID',$id);
		$query = $this->db->get($this->table_name);
		$result = $query->row();
		if($query->num_rows()>0){
			$result->instructor = $this->get_instructor_array($result->csID);
		}
		return $result;
	}
	
	
	
	/**
		@Function Name:	insert
		@Author Name:	ben binesh
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
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		udate data 
	
	*/
	function update($id,$data=array()){
		$this->db->where('csID',$id);
		$this->db->update($this->table_name,$data);
		
		return true;
	}
	
	/**
		@Function Name:	delete
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@id  | numeric| primary key of record 
		@return  boolean
		@Purpose:		delete data 
	
	*/
	function delete($id){
		$this->db->delete($this->table_name, array('csID' => $id)); 
		return true;
	}
	
	/**
		@Function Name:	update_multipe
		@Author Name:	ben binesh
		@Date:			Oct, 10 2013
		@id  | numeric| primary key of recored as csv
		@data   | array | array of single record 
		@return  integer
		@Purpose:		udate data 
	
	*/
	function update_multiple($ids='',$data=array()){
		$this->db->where('csID in ( '.$ids .' )');
		$this->db->update($this->table_name,$data);
		return true;
	}
	
	
	/**
		@Function Name:	   get_courses
		@Author Name:	   ben binesh
		@Date:			   Aug, 19 2013
		@genre_id          | numeric| course type 
		@location          | string | course location IU
		@user              | numeric | user associated with course 
		@start             | numeric | start offset of record
		@limit             | numeric | record limit 
		@register_end_date | string | registration end  date 
		@status            | numeric  | course status 
		@show_expired      | boolean  | flag to get the expired/old  courses
		@instructor_id     | numeric   | course instructor id 
		@definition_id     | numeric   | course definition id 
		@session_id        | numeric   | course session id 
		@featured        | numeric   | featured courses flag
		@group_by        | numeric   | group by flag
		@return            object array
		@Purpose:		   get the courses in the front-end 
	
	*/
	
	
	function get_courses(
			$genre_id=0,
			$location='',
			$user_id=0,
			$start=0,
			$limit=10,
			$register_end_date = NULL,
			$status = STATUS_PUBLISH,
			$show_expired=true,
			$instructor_id=0,
			$definition_id=0,
			$session_id=0,
			$featured=FALSE,
			$group_by=FALSE,
			$order_by='',
			$order='DESC'
	)
	{
		
		
		//check for user id 
		$course_ids =0;
		if($user_id){
			//if user id is given 
			//get the user course ids 
			$course_ids=get_single_value('users','memberships','id = '.$user_id);
			if('' == $course_ids){
				$course_ids =0;
			}
		}
		
		$this->db->select(
				'				cs.csID,cs.csGenreId,cs.csCourseType,cs.csCourseDefinitionId,cs.csRegistrationStartDate,cs.csRegistrationEndDate,cs.csPaymentStartDate,cs.csPublish,cs.csCity,cs.csState,,cs.csLocation,cs.csAddress,cs.csStartDate,cs.csEndDate,cs.csPrice,
				cd.cdID,cd.cdCourseID,cd.cdCourseTitle,
				cg.cgImage,
				(
					SELECT
						COUNT(1) 
					FROM 
						course_reservations
					WHERE
						urCourse = cs.csID
						AND 
						urStatus='.STATUS_REGISTERED.'
				) AS registered_count
				
		'); 
		$this->db->join('course_genres cg','cg.cgID = cs.csGenreId','LEFT');
		$this->db->join('course_definitions cd','cs.csCourseDefinitionId = cd.cdID','LEFT');
		
		//if instructor id is supplied 
		if($instructor_id){
			$this->db->join('course_instructor ci','cs.csID = ci.ciCsID');
			$this->db->where('ci.ciUID',$instructor_id);
		}
		
		if($register_end_date){
			$this->db->where('csStartDate >=',$register_end_date);
		}
		
		if(!$show_expired){
			$this->db->where('csEndDate >=',date('Y-m-d'));
		}
		
		if($location){
			$this->db->where('cs.csIURegion',$location);
		}
		
		if($genre_id)
			$this->db->where('cs.csGenreId',$genre_id);

		if($definition_id)
			$this->db->where('cs.csCourseDefinitionId',$definition_id);	

		if($session_id)
			$this->db->where('cs.csCourseSession',$session_id);	
			
		if($featured)
			$this->db->where('cs.csFeatured',FEATURED);		
			
		if($user_id)	
			$this->db->where('cs.csID IN ('.$course_ids.')');
			
		if($status != '')
			$this->db->where('cs.csPublish',$status);
		
		
		if($group_by)
			$this->db->group_by('cs.csCourseDefinitionId');
		
		if($order_by)
			$this->db->order_by($order_by,$order);
		else
			$this->db->order_by('csEndDate','DESC');
		
		
		if($limit > 0){
			$query = $this->db->get($this->table_name.' cs',$limit,$start);	
		}else{
			$query = $this->db->get($this->table_name.' cs');	
		}
		return $query->result(get_class($this));
	}
	
	
	/**
		@Function Name:	count_courses
		@Author Name:	   ben binesh
		@Date:			   Aug, 19 2013
		@genre_id          | numeric| course type 
		@location          | string | course location IU
		@user              | numeric | user associated with course 
		@register_end_date | string | registration end  date 
		@status            | numeric  | course status 
		@show_expired      | boolean  | flag to get the expired/old  courses
		@instructor_id     | numeric   | course instructor id 
		@definition_id     | numeric   | course definition id 
		@session_id        | numeric   | course session id 
		@return            object array
		@Purpose:		   get the courses in the front-end 
	
	*/
	
	
	function count_courses(
						$genre_id=0,
						$location='',
						$user_id=0,
						$register_end_date = NULL,
						$status = STATUS_PUBLISH,
						$show_expired = true,
						$instructor_id=0,
						$definition_id=0,
						$session_id=0,
						$featured=FALSE,
						$group_by=FALSE
					)
	{
		//check for user id 
		$course_ids = 0;
		if($user_id){
			//if user id is given 
			//get the user course ids 
			$course_ids = get_single_value('users','memberships','id = '.$user_id);
			if('' == $course_ids){
				$course_ids =0;
			}
		}
		
		$this->db->join('course_genres cg','cg.cgID = cs.csGenreId','LEFT');
		$this->db->join('course_definitions cd','cs.csCourseDefinitionId = cd.cdID','LEFT');
		
		//if instructor id is supplied 
		if($instructor_id){
			$this->db->join('course_instructor ci','cs.csID = ci.ciCsID');
			$this->db->where('ci.ciUID',$instructor_id);
		}
		
		if($register_end_date){
			$this->db->where('csStartDate >=',$register_end_date);
		}
		
		if($location){
			$this->db->where('cs.csIURegion',$location);
		}
		
		if($genre_id)
			$this->db->where('cs.csGenreId',$genre_id);	
		
		if($definition_id)
			$this->db->where('cs.csCourseDefinitionId',$definition_id);		
		
		if($session_id)
			$this->db->where('cs.csCourseSession',$session_id);	
		
		if($featured)
			$this->db->where('cs.csFeatured',FEATURED);	
			
		if($user_id)	
			$this->db->where('cs.csID IN ('.$course_ids.')');
		
		//if instructor id is supplied 
		if($instructor_id){
			$this->db->join('course_instructor ci','cs.csID = ci.ciCsID');
			$this->db->where('ci.ciUID',$instructor_id);
		}
		
		if(!$show_expired){
			$this->db->where('csEndDate >=',date('Y-m-d'));
		}	
		
		
		
		if($status != '')
			$this->db->where('cs.csPublish',$status);
		$count = $this->db->count_all_results($this->table_name.' cs');	
		return $count;
	}
	
	/**
		@Function Name:	get_course_detail
		@Author Name:	   ben binesh
		@Date:			   Oct, 19 2013
		@couse_id          | numeric| course id 
		@dates       |boolean|  date flag
		@return            object array
		@Purpose:		   get the courses in the front-end 
	
	*/
	
	function get_course_detail($course_id=0,$dates=true){
		$result=array();
		$this->db->select('
				cs.*,
				cd.cdID,cd.cdCourseID,cd.cdCourseTitle,
				cg.cgTitle,cg.cgCourseCredits	
				
		');
		$this->db->join('course_genres cg','cg.cgID = cs.csGenreId','LEFT');
		$this->db->join('course_definitions cd','cs.csCourseDefinitionId = cd.cdID','LEFT');
		$this->db->where('cs.csID',$course_id);		
		$query  = $this->db->get($this->table_name.' cs');	
		$result =  $query->row();
		if(!empty($result)){
				$result->course_dates=self::get_schedule_dates($course_id);
				$result->enrolees_count=self::get_enrollee_count($course_id);
				$result->registered_count=self::get_registerant_count($course_id);
				
		}	
		return $result;
	}
	
	
	function get_registerant_count($course_id=0,$email=''){
		
		$this->db->where('urCourse',$course_id);
		$this->db->where('urStatus',STATUS_REGISTERED);
		if($email !='')
			$this->db->where('urEmail',$email);
		$count = $this->db->count_all_results('course_reservations');
		return $count;
	}
	
	
	
	function get_enrollee_count($course_id=0,$email=''){
		$this->db->where("FIND_IN_SET($course_id,memberships) <> ",0,false);
		$this->db->where('accessLevel',MEMBER);
		if($email !='')
			$this->db->where('email',$email);
		$count = $this->db->count_all_results('users');
		
		return $count;
	}
	
	/**
		@Function Name:	get_enrollees
		@Author Name:	ben binesh
		@Date:			Aug, 30 2013
		@name          | String | name of user 
		@email         | String | email
		@course_id     |integer| course id
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@return  array 
		@Purpose:		get multiple records for the frontend  
	
	*/
		
	function get_enrollees($name = '',$course_id = 0, $start = 0 , $limit = 10,$order_by='u.lastName ASC',$credit=FALSE){
		$this->db->select('
			u.id, u.userName, u.firstName, u.lastName, u.email, u.lastLogin, u.activationFlag, u.act48,	 
			p.districtAffiliation, p.phone, p.level, p.gradCoursesTaking, p.gradeSubject ,p.birthDate,p.address,p.city,p.state,p.zip,p.districtID,p.iuID,
			o.orderID, o.orderNumber, o.orderCustID, o.orderName,o.orderEmail,
			oi.oiID, oi.oiProdName,oi.oiProdID , oi.oiProdPrice, oi.oiProdVariantValue1,
			(
				SELECT
					oiProdName
				FROM 
					orders_items
				WHERE
					oiOrderNumber = o.orderNumber
					AND 
					oiID != oi.oiID 
					LIMIT 1
				
			) AS upgrade_info
			,
			(
				SELECT
					oiProdID
				FROM 
					orders_items
				WHERE
					oiOrderNumber = o.orderNumber
					AND 
					oiID != oi.oiID 
					LIMIT 1
				
			) AS upgrade_id
			
		');
		
		$this->db->join('users_profiles p','u.id = p.user_id','LEFT');	
		$this->db->join('orders o','u.id = o.orderCustID','LEFT');
		$this->db->join('orders_items oi','o.orderNumber = oi.oiorderNumber','LEFT');
		
		if($name && 3 >= strlen($name))
		{
			$this->db->like("u.firstName",$name,false);
			$this->db->or_like("u.lastName",$name,false);
			$this->db->or_like("u.userName",$name,false);
			$this->db->or_like("u.email",$name,false);
		}
		elseif($name)
		{
			$this->db->where("
				MATCH
					(u.firstName,u.lastName,u.userName,u.email) 
				AGAINST 
					('$name' IN BOOLEAN MODE)
		",'',false);
		}
		
		$this->db->where('accessLevel',MEMBER);
		if($course_id){
			$this->db->where("FIND_IN_SET($course_id,u.memberships) <> ",0,false);
		}
		if($credit){
			if($course_id){
			$this->db->where("FIND_IN_SET($course_id,u.act48) = ",0,false);
			}
		}	
		
		$this->db->group_by('u.id');
		if($order_by)
			$this->db->order_by($order_by);
		if($limit > 0)
			$query = $this->db->get('users u', $limit , $start );
		else
			$query = $this->db->get('users u');
		return $query->result();
	}
	
	/**
		@Function Name:	get_enrollees_email
		@Author Name:	ben binesh
		@Date:			Nov, 07  2013
		@course_id     |integer| course id
		@return  array 
		@Purpose:		get multiple records for the frontend  
	
	*/
	
	function get_enrollees_email($course_id=0){
		$this->db->select('email');
		$this->db->where('accessLevel',MEMBER);
		if($course_id){
			$this->db->where("FIND_IN_SET($course_id,memberships) <> ",0,false);
		}	
		$this->db->group_by('u.id');
		$query = $this->db->get('users u');
		return $query->result();
	}
	
	
	
	
	
	/**
		@Function Name:	get_status_array
		@Author Name:	ben binesh
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
	
	
	/**
		@Function Name:	get_coursetype_array
		@Author Name:	ben binesh
		@emtpty |boolean| empty flag
		@Date:			Aug, 19 2013
		@return  integer
		@Purpose:		get array of course type
		
	*/
	function get_coursetype_array($empty=false,$empty_array=array(''=>'')){
		$status_array=array();
		if($empty){
			$status_array = array_merge($status_array,$empty_array);
		}
		$status_array[COURSE_ONLINE]   = 'Online';
		$status_array[COURSE_OFFLINE] = 'Offline';
		
		return $status_array;
	}
	/**
		@Function Name:	show_course_type
		@Author Name:	ben binesh
		@Date:			Aug, 19 2013
		@status  | numeric| status of recored 
		@return  string
		@Purpose:		return course type string 
	
	*/
	function show_course_type($status = 0){
		$status_array =self::get_coursetype_array();
		return (isset($status_array[$status]))?$status_array[$status]:'Offline';
	}
	
	
	/**
		@Function Name:	assignments
		@Author Name:	ben binesh
		@Date:			Oct, 08 2013
		@author_id   | numeric| author id of assignments 
		@user_id   | numeric| user id who completed the assignment 
		@return  array
		@Purpose:	return course assignments 
	
	*/
	function assignments($author_id=0,$user_id=0){
		return $this->assignment_model->geUserAllAssignments($author_id,$this->csID,0,-1,$user_id);
	}
	
	
	function check_enrollee($course_id=0,$email=''){
		return self::get_enrollee_count($course_id,$email);
	}
	
	
	function check_registerant($course_id=0,$email=''){
		$this->db->where('urCourse',$course_id);
		$this->db->where('urStatus',STATUS_REGISTERED);
		$this->db->where('urEmail',$email);
		$query = $this->db->get('course_reservations');
		
		return $query->row();
	}
	
	
	
	/**
		@Function Name:	check_enrollee_limit
		@Author Name:	ben binesh
		@course         | Object | course object 
		@Date:			Oct 14, 2013
		@return         |boolean |
		@Purpose:		check if enrolee limit is reached or not 
		
	*/
	function check_enrollee_limit( $course = object ){
		
		if($course->enrolees_count >= $course->csMaximumEnrollees )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		
		}
	}
	
	
	
	/**
		@Function Name:	get_status_array
		@Author Name:	ben binesh
		@emtpty |boolean| empty flag
		@Date:			Aug, 19 2013
		@return  integer
		@Purpose:		get array of status
		
	*/
	function get_course_date_array($empty=false,$empty_array=array(''=>'')){
		$status_array=array();
		if($empty){
			$status_array = array_merge($status_array,$empty_array);
		}
		$status_array[COURSE_CURRENT]   = 'Current/Future';
		$status_array[COURSE_ARCHIVED] = 'Archived';
		
		return $status_array;
	}
	
/**********************************************************************
*** Course Instructor Related Functions
******************************************************************/	
	/**
	* Ref1 
	*/
	
	/**
		@Function Name:	get_instructor_array
		@Author Name:	ben binesh
		@Date:			Sept, 23 2013
		@data   | array | array of  record 
		@return  array
		@Purpose:		get instructor array 
	
	*/
	
	function get_instructor_array($course_id=0){
		$result=array();
		$this->db->select('u.id,u.firstName,u.lastName');
		$this->db->join('users u' ,'ci.ciUID =u.id');
		$this->db->where('ci.ciCsID',$course_id);
		$query = $this->db->get($this->table_instructor.' ci');
		if($query->num_rows() > 0){
			$results =$query->result();
			foreach($results as $res){
				$result[$res->id]=$res->firstName.' '.$res->lastName;
			}
		}
		return $result;
	}
	
	
	/**
		@Function Name:	get_instuctors
		@Author Name:	ben binesh
		@Date:			Oct 10, 2013
		@data          | array | array of  record 
		@return      object array
		@Purpose:		get instructor array 
	
	*/
	function get_instuctors($course_id=0)
	{
		$result = array();
		$this->db->select('u.id,u.firstName,u.lastName');
		$this->db->join('users u' ,'ci.ciUID =u.id');
		$this->db->where('ci.ciCsID',$course_id);
		$query  = $this->db->get($this->table_instructor.' ci');
		$result = $query->result();
		return $result;
	}
	
	/**
		@Function Name:	insert_instructor
		@Author Name:	ben binesh
		@Date:			Sept, 23 2013
		@data   | array | array of  record 
		@return  integer 
		@Purpose:		insert new record 
	
	*/
	
	function insert_instructor($data=array()){
		$this->db->insert($this->table_instructor,$data);
		return $this->db->insert_id(); 
	}
	
	/**
		@Function Name:	delete_instructor
		@Author Name:	ben  binesh
		@Date:			Sept, 23 2013
		@course_id  | integer | course id 
		@return  void 
		@Purpose:		delte instructor
	
	*/
		
	function delete_instructor($course_id=0)
	{
		$this->db->where('ciCsID',$course_id);
		$this->db->delete($this->table_instructor); 
	}
	
		
	
	
/*********************************************************
/ Course schedule related dates function 
/
**********************************************************/
	
	/**
		@Function Name:	insert_date
		@Author Name:	ben  binesh
		@Date:			Sept, 23 2013
		@return  numeric  
		@Purpose:		add course dates
	
	*/
		
	function insert_date($data){
		$this->db->insert($this->table_course_date,$data);
		return $this->db->insert_id(); 
	}
	
	/**
		@Function Name:	delete_date
		@Author Name:	ben  binesh
		@Date:			Sept, 23 2013
		@course_id      |numeric| course id
		@return  void
		@Purpose:		delete course delete
	
	*/
	
	function delete_date($course_id=0){
		$this->db->delete( $this->table_course_date, array( 'csdCourseScheduleId' => $course_id ) ); 
		return TRUE;
	}
	
	/**
		@Function Name:	update_dates
		@Author Name:	ben  binesh
		@Date:			Oct 10, 2013
		@course_ids     |string | course ids in csv format 
		@return        boolean 
		@Purpose:	  update the mulitple courses dates 
	
	*/
	
	function update_dates($course_ids='', $data=array())
	{
		$this->db->where('csdCourseScheduleId in ( '.$course_ids .' )');
		$this->db->update($this->table_course_date,$data);
		return TRUE;
	}
	
	/**
		@Function Name:	get_schedule_dates
		@Author Name:	ben  binesh
		@Date:			Sept, 23 2013
		@course_id     |numeric| course id
		@return        numeric  
		@Purpose:	   return array of course dates 
	
	*/
	
	function get_schedule_dates($course_id=0){
		$this->db->order_by('csdID','ASC');
		$query = $this->db->get_where( $this->table_course_date,array('csdCourseScheduleId'=>$course_id) );
		return $query->result();
	}
	
	

	
	
}//end of class
//end of file 
	
	