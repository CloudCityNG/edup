<?php
/**
@Page/Module Name/Class: 		user_model.php
@Author Name:			 		ben binesh
@Date:					 		Aug, 30 2013
@Purpose:		        		Contain all data management function for users 
@Table referred:				users, users_profiles
@Table updated:					users, users_profiles
@Most Important Related Files	NIL
//Chronological development
//***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
/*********************************************************************************** 
//RF1     |  Alan Anil       |Nov 20 2013    | Normal   | Function for getting user course details.  
//***********************************************************************************/

class User_model extends CI_Model {
	
	
	
	public $table_name='users';
	public $table_profile='users_profiles';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
		@Function Name:	get_records
		@Author Name:	ben binesh
		@Date:			Aug, 30 2013
		@name   | String | name of user 
		@email   | String | email
		@access_level  | numeric| access level identifier
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@return  array 
		@Purpose:		get  multiple records 
	
	*/
		
	function get_records($name = '',$email = '',$access_level = '',$start = 0 , $limit = 10,$status=''){
		$select='';
		$select .='id,userName,firstName,lastName,email,accessLevel,lastLogin,activationFlag,
		p.districtAffiliation, p.phone, p.level, p.gradCoursesTaking, p.gradeSubject ,p.birthDate,p.address,p.city,p.state,p.zip,p.districtID,p.iuID';
		
		
		if($name && 3 >= strlen($name))
		{
			$this->db->like("firstName",$name,false);
			$this->db->or_like("lastName",$name,false);
			$this->db->or_like("userName",$name,false);
		}
		elseif($name)
		{
			
			$select .= ",MATCH
							(email,firstName,lastName,userName) 
						AGAINST 
							('$name') as score";
							
			$this->db->where("
				MATCH
					(email,firstName,lastName,userName) 
				AGAINST 
					('$name' IN BOOLEAN MODE)
			",'',false);
			$this->db->order_by('score','DESC');				
		}	
		$this->db->select($select,FALSE);
		if($email)
			$this->db->where('email',$email);	
		
		if($status !== ''){
			if(ACCOUNT_PENDING == $status ){
				$this->db->where('activationFlag >= ',$status);	
			}else{
				$this->db->where('activationFlag',$status);	
			}
		}

		$this->db->join($this->table_profile.' p','id = p.user_id','LEFT');			
		if($access_level != '')
			$this->db->where('accessLevel',$access_level);
			
		
		$this->db->order_by('lastName','ASC');
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
		@Date:			Aug, 30 2013
		@name   | String | name of user 
		@email   | String | email
		@access_level  | numeric| access level identifier
		@return  integer
		@Purpose:		count  multiple records 
	
	*/
	
	function count_records($name = '',$email = '',$access_level = '',$status=''){
		
		if($name && 3 >= strlen($name))
		{
			$this->db->like("firstName",$name,true);
			$this->db->or_like("lastName",$name,false);
			$this->db->or_like("userName",$name,false);
		}
		elseif($name)
		{	
			$this->db->where("
				MATCH
					(email,firstName,lastName,userName) 
				AGAINST 
					('$name' IN BOOLEAN MODE)
			",'',false);
			
		}	
		if($email)
			$this->db->where('email',$email);	
			
		if($access_level != '')
			$this->db->where('accessLevel',$access_level);
			
		if($status != ''){
			if(ACCOUNT_PENDING == $status ){
				$this->db->where('activationFlag >= ',$status);	
			}else{
				$this->db->where('activationFlag',$status);	
			}
		}		
		return $this->db->count_all_results($this->table_name);
		
	}
	
	
	/**
		@Function Name:	get_users
		@Author Name:	ben binesh
		@Date:			Aug, 30 2013
		@name   | String | name of user 
		@email   | String | email
		@access_level  | numeric| access level identifier
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@return  array 
		@Purpose:		get multiple records for the frontend  
	
	*/
		
	function get_users(
					$name = '',
					$course_id = 0,
					$access_level = '',
					$status='',
					$start = 0 ,
					$limit = 10,
					$instructor=false
	)
	{
		$select='';
		$select .= 'u.id,u.userName, u.firstName, u.lastName, u.profileImage, u.email, u.accessLevel,u.activationFlag,
			p.gradeSubject, p.level, p.role,p.districtAffiliation, p.gradeSubject';
		if($name && 3 >= strlen($name))
		{
			$this->db->like("u.email",$name,false);
			$this->db->or_like("u.firstName",$name,false);
			$this->db->or_like("u.lastName",$name,false);
			$this->db->or_like("u.userName",$name,false);
		}
		elseif($name)
		{
			
			$select .= ",MATCH
							(u.firstName,u.lastName,u.userName,u.email) 
						AGAINST 
							('$name') as score";
							
			$this->db->where("
				MATCH
					(u.firstName,u.lastName,u.userName,u.email) 
				AGAINST 
					('$name' IN BOOLEAN MODE)
			",'',false);
			$this->db->order_by('score','DESC');				
		}			
		$this->db->select($select,FALSE);
				
		$this->db->join($this->table_profile.' p','u.id = p.user_id','LEFT');	
		
		if($access_level != '')
			$this->db->where('u.accessLevel',$access_level);
		if($status !== '')
			$this->db->where('u.activationFlag',$status);	
		
		
		if($course_id){
			if($instructor){
				$this->db->join('course_instructor ci','u.id =ci.ciUID');
				$this->db->where('ci.ciCsID',$course_id);
			}
			else
			{
				$this->db->where("(
							FIND_IN_SET($course_id,u.memberships) <> 0
							)
				",'',false);
			}
		}
		$this->db->order_by('u.lastName','ASC');
		$query = $this->db->get($this->table_name.' u', $limit , $start );
		return $query->result();
	}
	
	/**
		@Function Name:	count_records
		@Author Name:	ben binesh
		@Date:			Aug, 30 2013
		@name   | String | name of user 
		@email   | String | email
		@access_level  | numeric| access level identifier
		@status  | numeric| activation status 
		@return  integer
		@Purpose:		count  multiple records 
	
	*/
	
	function count_users(
				$name = '',
				$course_id = 0,
				$access_level = '',
				$status='',
				$instructor=true
		)
	{
		if($name && 3 >= strlen($name))
		{
			$this->db->like("u.email",$name,false);
			$this->db->or_like("u.firstName",$name,false);
			$this->db->or_like("u.lastName",$name,false);
			$this->db->or_like("u.userName",$name,false);
		}
		elseif($name) {
			$this->db->where("
				MATCH
					(u.firstName,u.lastName,u.userName,u.email) 
				AGAINST 
					('$name' IN BOOLEAN MODE)
		",'',false);	
		}
		$this->db->join($this->table_profile.' p','u.id = p.user_id','LEFT');	
		
			
		if($access_level != '')
			$this->db->where('u.accessLevel',$access_level);
		if($status != '')
			$this->db->where('u.activationFlag',$status);	
		if($course_id){
			if($instructor){
				$this->db->join('course_instructor ci','u.id =ci.ciUID');
				$this->db->where('ci.ciCsID',$course_id);
			}
			else
			{
				$this->db->where("(
							FIND_IN_SET($course_id,u.memberships) <> 0
							)
				",'',false);
			}
		}
		return $this->db->count_all_results($this->table_name.' u');
		
	}
	
	
	/**
		@Function Name:	get_single_record
		@Author Name:	ben binesh
		@Date:			Aug, 30 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get the single record 
	
	*/
	function get_single_record($id=0,$select='*',$profile=false){
		$this->db->select($select);
		if($profile)//get profile data too 
			$this->db->join($this->table_profile.' p','u.id = p.user_id','LEFT');	
		$this->db->where('u.id',$id);
		$query = $this->db->get($this->table_name.' u');
		return $query->row();
	}
	
	/**
		@Function Name:	check_duplicate
		@Author Name:	ben binesh
		@Date:			Aug, 30 2013
		@id  | numeric| primary key of record 
		@email  | String | email; 
		@name  | String | name
		@return  integer
		@Purpose:		count duplicate records with matching filter
	
	*/
	
	function check_duplicate( $id=0, $email='', $name='' ){
		if($email)
			$this->db->where('email',$email);
		if($name)
			$this->db->where('userName',$name);	
		if($id)
			$this->db->where('id !=',$id);
		$count =  $this->db->count_all_results($this->table_name);
		return $count;
	}
	
	/**
		@Function Name:	get_user_byEmail
		@Author Name:	ben binesh
		@Date:			Sept, 05 2013
		@email      | string | email 
		@return  array 
		@Purpose:		get user data by email 
	
	*/
	
	function get_user_byEmail($email,$select='*'){
		$this->db->select($select);
		$this->db->where('email',$email);
		$query = $this->db->get($this->table_name);
		return $query->row();
	}
	
	/**
		@Function Name:	get_user_byUserName
		@Author Name:	ben binesh
		@Date:			Sept, 05 2013
		@email      | string | username
		@return  array 
		@Purpose:		get user data by username
	
	*/
	
	function get_user_byUserName($name='',$select='*'){
		$this->db->select($select);
		$this->db->where('userName',$name);
		$query = $this->db->get($this->table_name);
		return $query->row();
	}
	
	
	/**
		@Function Name:	get_user_byUserName
		@Author Name:	ben binesh
		@Date:			Sept, 05 2013
		@email      | string | username
		@return  array 
		@Purpose:		get user data by username
	
	*/
	function get_user_data($select='*',$id=0,$email='0',$pwd_code='',$activation_code=''){
		$this->db->select($select);
		if($id)
			$this->db->where('id',$id);
		if($email)
			$this->db->where('email',$email);
		if($pwd_code)
			$this->db->where('forgotPWCode',$pwd_code);
		if($activation_code)
			$this->db->where('activationCode',$activation_code);	
		$query = $this->db->get($this->table_name);
		return $query->row();	
			
	}
	
	
	/**
		@Function Name:	insert
		@Author Name:	ben binesh
		@Date:			Aug, 30 2013
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
		@Date:			Aug, 30 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		update data 
	
	*/
	function update($id,$data=array()){
		$this->db->where('id',$id);
		$this->db->update($this->table_name,$data);
		return true;
	}
	
	/**
		@Function Name:	delete
		@Author Name:	ben binesh
		@Date:			Aug, 30 2013
		@id  | numeric| primary key of record 
		@return  boolean
		@Purpose:		delete data 
	
	*/
	function delete($id){
		//delete user data 
		$this->db->delete($this->table_name, array('id' => $id)); 
		//delete profile data 
		$this->db->delete($this->table_profile,array('user_id' => $id)); 
		
		return true;
	}
	
	
	/**
		@Function Name:	update_multipe
		@Author Name:	ben binesh
		@Date:			Aug, 30 2013
		@id  | numeric| primary key of record 
		@data   | array | array of single record 
		@return  integer
		@Purpose:		update data 
	
	*/
	function update_multiple($ids=array(),$data=array()){
		$this->db->where('id in ( '.$ids .' )');
		$this->db->update($this->table_name,$data);
		return true;
	}
	
	
	/**
		@Function Name:	check_password
		@Author Name:	ben binesh
		@Date:			Sept, 09 2013
		@id          |numeric | user id 
		@password    | string | user password
		@return  boolean 
		@Purpose:		check the valid password on user 
	
	*/
	
	function check_password($id='',$password=''){
		
		$this->db->select('password');
		$this->db->where('id',$id);
		$query = $this->db->get($this->table_name);
		$data  = $query->row();
		if(empty($data)){
			return false;
		}
		return validate_password($password,$data->password);
	}
	
	
	function  update_membership($id=0,$course_id=0,$onlylast=false){
		$memberships = get_single_value($this->table_name,'memberships','id = '.$id);
		$this->db->set('membershipLastUsed',$course_id);
		if(!$onlylast){
			if($memberships)//if memberships is not null  
				$this->db->set('memberships', "concat(memberships,',".$course_id."')", FALSE);
			else
				$this->db->set('memberships',$course_id );
		}
		$this->db->where('id', $id);
		$this->db->update($this->table_name);
		return ;
	}
	
	
	/**
		@Function Name:	get_access_level_array
		@Author Name:	ben binesh
		@empty |boolean| empty flag
		@Date:			Aug, 30 2013
		@return  integer
		@Purpose:		get array of access level
		
	*/
	function get_access_level_array($empty=false,$empty_array=array(''=>''))
	{
		$this->load->model('permission_model');
		$access_levels = $this->permission_model->get_groups();
		$access_level_array=array();
		if($empty){
			$access_level_array = array_merge($access_level_array,$empty_array);
		}
		foreach($access_levels as $access_level){
			$access_level_array[$access_level->groupID]   = $access_level->groupName;
		}
		
		return $access_level_array;
	}
	/**
		@Function Name:	show_access_level
		@Author Name:	ben binesh
		@Date:			Aug, 30 2013
		@access_level  | numeric| access level of record 
		@return  string
		@Purpose:		return access level string 
	
	*/
	function show_access_level($access_level = 0){
		$access_level_array =self::get_access_level_array();
		return (isset($access_level_array[$access_level]))?$access_level_array[$access_level]:'User';
	}
	
	/**
		@Function Name:	get_status_array
		@Author Name:	binesh
		@emtpty |boolean| empty flag
		@Date:			Sept, 05 2013
		@return  integer
		@Purpose:		get array of account status 
		
	*/
	function get_status_array($empty=false,$empty_array=array(''=>'')){
		$status_array=array();
		if($empty){
			$status_array = array_merge($status_array,$empty_array);
		}			
		$status_array[ACCOUNT_ACTIVE]   = 'Active';
		$status_array[ACCOUNT_INACTIVE] = 'Inactive';
		$status_array[ACCOUNT_PENDING]  = 'Pending';
		
		return $status_array;
	}
	/**
		@Function Name:	show_access_level
		@Author Name:	ben binesh
		@Date:			Aug, 30 2013
		@access_level  | numeric| access level of recored 
		@return  string
		@Purpose:		return access level string 
	
	*/
	function show_status($status = 0){
		$status_array =self::get_status_array();
		return (isset($status_array[$status]))?$status_array[$status]:'Pending';
	}
	
	
	
	
	
/************************************************
*
******** Users Profile  Related functions
*
**************************************************/	
	
	/**
		@Function Name:	set_user_profile
		@Author Name:	ben binesh
		@Date:		    Sept, 05 2013
		@id            |integer| id of user 
		@data          |array| array of values 
		@return        array
		@Purpose:		return user profile data
	
	*/
	function set_user_profile( $id=0, $data=array() ){
		//check for existing profile
		if($profile = self::get_user_profile($id,'user_id')){
			//update the profile data
			$this->db->where('user_id',$id);
			$this->db->update($this->table_profile,$data);
		}else{
			//insert the profile data
			$this->db->insert($this->table_profile,$data);
			return $this->db->insert_id(); 
		}
		
		
	}
	
	/**
		@Function Name:	get_user_profile
		@Author Name:	ben binesh
		@Date:			Sept, 05 2013
		@id            |integer| id of user 
		@select        | string | select the user 
		@return        array
		@Purpose:	   return user profile data
	
	*/
	function get_user_profile( $id=0, $select='*' ){
		$this->db->select($select);
		$this->db->where('user_id',$id);
		$query = $this->db->get($this->table_profile);
		return $query->row();	
		
	}
//RF1	
	/**
		@Function Name:	get_user_courses
		@Author Name:	Alan Anil
		@Date:			Nov, 14 2013
		@id            |integer| id of user  
		@return        array
		@Purpose:	   return user course data
	
	*/
	function get_user_courses($id=0){
		$this->db->select('fgCnfID');
		$this->db->where('fgUserID',$id); 
		$query = $this->db->get('final_grades');   
		return  $query->result();	
		
	}
//RF1 end	

	function send_activation_email($user=object)
	{
		
		$activation_code = random_string('alnum', 12);
		$update_array=array(
			'activationCode'=>$activation_code,
			'activationFlag'=>time(),
		);
		$this->user_model->update($user->id,$update_array);
		$user_activation_link=base_url().'user/activate?email='.$user->email.'&activation_code='.$activation_code.'&user='.base64_encode($user->id);
		$email_template = get_content('email_templates','*','etID = 1');
		if(!empty($email_template)){
			$email_template=$email_template[0];
			$searchReplaceArray = array(
				 '[AccountActivationUrl]'   =>anchor($user_activation_link,$user_activation_link), 
				 '[firstName]'   =>$user->firstName,
				 '[lastName]'   =>$user->lastName,
				 '[maximumResponse]'   =>FWPWD_EXPIRE_TIME,
				
				);
			$email_message = str_replace(
			  array_keys($searchReplaceArray), 
			  array_values($searchReplaceArray),$email_template->etCopy); 
			//get admin emails 
			$emails = get_admin_emails();  
			send_mail($user->email,SITE_NAME,SENDER_EMAIL,$email_template->etSubject,$email_message,$emails);	  
		}
		return true;
	}

	
}//end of class
//end of file 
	
	