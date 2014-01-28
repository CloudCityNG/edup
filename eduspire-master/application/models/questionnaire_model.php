<?php
/**
@Page/Module Name/Class:                        questionnaire_model.php
@Author Name:			 		janet rajani
@Date:					 	Sept, 30  2013
@Purpose:		        		Survey related
@Table referred:				
@Table updated:					
@Most Important Related Files	NIL
*/

class Questionnaire_model extends CI_Model 
{
	public $table_name                      = 'questionnaire_defs';
        public $table_name_result               = 'questionnaire_results';
        public $table_name_test_approv          = 'testimonials_approved';
        public $table_name_assignments          = 'assignments';
        public $table_name_testimonials         = 'testimonials';
        public $table_name_course_schedule      = 'course_schedule';
        public $table_name_course_definitions   = 'course_definitions';
        public $table_name_users                = 'users';
        public $table_instructor                = 'course_instructor';
        
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
                
	}
	
	/*
		@Function Name:         count_records_questionnaire
		@Author Name:           Janet Rajani
		@Date:			Sep, 30 2013
		@Purpose:		count questionnaire multiple records 
	
	*/
	function count_records_questionnaire($qTitle = '')
        {
		if($qTitle)
			$this->db->like('qTitle',$qTitle);
                $where_condition = array('qParent'=>0,
                              'qTitle !='=>'');
		$this->db->where($where_condition);
		return $this->db->count_all_results($this->table_name);
	}
        /*
		@Function Name:         count_records_question
		@Author Name:           Janet Rajani
		@Date:			Sep, 30 2013
		@Purpose:		count question multiple records 
	
	*/
	function count_records_question($qParent, $qTitle = '',$qQuestion='')
        {
		if($qTitle)
			$this->db->like('qTitle',$qTitle);
                if($qQuestion)
			$this->db->like('qQuestion',$qQuestion);
                $where_condition = array('qParent'=>$qParent);
		$this->db->where($where_condition);
		return $this->db->count_all_results($this->table_name);
	}
        
	/**
		@Function Name:	        get_records
		@Author Name:	        Janet Rajani
		@Date:			Sep, 30 2013
		@title                  | String | title of survey
		@start                  | numeric| start offset of record 
		@limit                  | numeric| limit of record 
		@return                 array 
		@Purpose:		get questionnaire title, and total no of questions inside it
	
	*/
		
	function get_records_questionnaire($qTitle = '',$start = 0 , $limit = 10)
        {
		$this->db->select('COUNT(q.qID) as totalQuestions, q.qID, q.qTitle');
		
                $this->db->join($this->table_name. ' qr','q.qID = qr.qParent','LEFT OUTER');
		if($qTitle)
			$this->db->like('q.qTitle',$qTitle);
			
                $where = 'q.qParent = '. SURVEY_PARENT.' AND q.qTitle!=""';
                $this->db->group_by('q.qID');
		$query = $this->db->get_where($this->table_name.' q', $where, $limit , $start );
		return $query->result();
	}
        /**
		@Function Name:	       check_used_questionnaire
		@Author Name:	        Janet Rajani
		@Date:			Nov, 5 2013
		@return                 array 
		@Purpose:		Check if questionnaire is already filled by any student
	
	*/
        function check_used_questionnaire($qID='',$qrAssignID='')
        {
            $this->db->select('COUNT(qr.qrID) AS total_submission, a.assignQuestionnaire');
            $this->db->join($this->table_name_assignments.' a','qr.qrAssignID=a.assignID','LEFT');
            if($qID)
                $this->db->where('a.assignQuestionnaire',$qID);
            if($qrAssignID)
                $this->db->where('a.assignID',$qrAssignID);
            $query = $this->db->get($this->table_name_result.' qr');
            return $query->row();
        }
        /*
		@Function Name:         get_single_record
		@Author Name:           Janet Rajani
		@Date:			Oct 02 2013
		@Purpose:		Get record of one row
	
	*/
	function get_single_record($id=0,$select='*')
        {
		$this->db->select($select);
		$this->db->where('qID',$id);
		$query = $this->db->get($this->table_name);
		return $query->row();
		
	}
        
        /*
		@Function Name:         get_records_questions
		@Author Name:           Janet Rajani
		@Date:			Oct 02 2013
		@Purpose:		Get all questions inside a questionnaire
	
	*/
	function get_records_questions($qParent='')
        {
		$this->db->select('qID,qOrder, qParent, qTitle, qQuestion, qType, qHelp, qAnswers');
		
                $this->db->where('qParent', $qParent);
                $this->db->from($this->table_name);
                $this->db->order_by('qOrder');
		$query = $this->db->get();
                return $query->result();
                
	}

	/*
		@Function Name:         check_if_title_unique
		@Author Name:           Janet Rajani
		@Date:			Oct 01 2013
		@Purpose:		Check if the title is unique
	
	*/
	function check_if_title_unique($qTitle = '',$qID='')
        {
		if($qTitle)
			$this->db->where('qTitle',$qTitle);
                if($qID)
			$this->db->where('qID !=',$qID);
		
                $this->db->where('qParent',0);
		return $this->db->count_all_results($this->table_name);
	}
        /**
		@Function Name:	        insert
		@Author Name:	        Janet Rajani
		@Date:			Oct, 1 2013
		@data                   | array | array of single record 
		@return                 integer
		@Purpose:		insert data 
	
	*/
	
	function insert($data=array())
        {
		$this->db->insert($this->table_name,$data);
		return $this->db->insert_id(); 
	}
		
	/**
		@Function Name:	       update
		@Author Name:	       Janet Rajani
		@Date:		       Oct, 1 2013
		@id                    | numeric| primary key of record 
		@data                  | array | array of single record 
		@return                integer
		@Purpose:	       udate data 
	
	*/
	function update($id,$data=array())
        {
		$this->db->where('qID',$id);
		$this->db->update($this->table_name,$data);
		
		return true;
	}
	
	/**
		@Function Name:	        delete
		@Author Name:	        Janet Rajani
		@Date:		        Oct, 1 2013
		@id                     | numeric| primary key of record 
		@return                 boolean
		@Purpose:		delete data 
	
	*/
	function delete($id)
        {
		$this->db->delete($this->table_name, array('qID' => $id)); 
		return true;
	}
        /**
		@Function Name:	        get_access_level_array
		@Author Name:	        Janet Rajani
		@emtpty                 |boolean| empty flag
		@Date:			Oct, 2 2013
		@return                 integer
		@Purpose:		get array of access level
		
	*/
	function get_question_type($empty_array=array(''=>''))
        {
		$access_level_array=array();
		$access_level_array = array_merge($access_level_array,$empty_array);
		
		$access_level_array[SECTION]   = 'Section';
		$access_level_array[TEXT]      = 'Text';
		$access_level_array[TEXT_AREA] = 'Textarea';
		$access_level_array[CHECKBOX_GROUP]   = 'Checkbox';
		$access_level_array[SELECT]           = 'Drop Down';
		$access_level_array[RADIO_SECTION]    = 'Radio';
                $access_level_array[STAR_RATING]      = 'Star Rating';
        	
		return $access_level_array;
	}
        
        /* This is front-end section of the Questionnaire*/
        
       /*
		@Function Name:         get_question_answers
		@Author Name:           Janet Rajani
		@Date:			Oct, 29 2013
		@Purpose:		count questionnaire multiple records 
	
	*/
	function get_question_answers($qID = '')
        {
		$this->db->select('qID, qCnfID, qOrder, qType, qTitle, qQuestion, qHelp, qAnswers, qOptional');
                $this->db->where('qParent',$qID);
                $this->db->order_by('qOrder');
                $result = $this->db->get($this->table_name);
                return $result->result();
	}
        
        /**
		@Function Name:	        insert_results
		@Author Name:	        Janet Rajani
		@Date:			Oct, 31 2013
		@data                   | array | array of single record 
		@return                 integer
		@Purpose:		insert answers in results table

	*/
	function insert_results($data=array())
        {
		$this->db->insert($this->table_name_result,$data);
		return $this->db->insert_id(); 
	}
        
         /**
		@Function Name:	       insert_testimonials
		@Author Name:	       Janet Rajani
		@Date:			Nov, 6 2013
		@data                  | array | array of single record 
		@return                integer
		@Purpose:		insert testimonials in table
	
	*/
	function insert_testimonials($data=array())
        {
		$this->db->insert($this->table_name_testimonials,$data);
		return $this->db->insert_id(); 
	}
        /**
		@Function Name:	       insert
		@Author Name:	       Janet Rajani
		@Date:			Nov, 7 2013
		@data                  | array | array of single record 
		@return                integer
		@Purpose:		insert data into testimonials
	*/
	
	function insert_test_approv($data=array())
        {
		$this->db->insert($this->table_name_test_approv,$data);
		return $this->db->insert_id(); 
	}
        /*
		@Function Name:         count_records_testimonials
		@Author Name:           Janet Rajani
		@Date:			Nov, 6 2013
		@Purpose:		count all approved testimonials
	
	*/
	function count_records_testimonials($tCourse='')
        {
                if($tCourse)
			$this->db->like('tCourse',$tCourse);
		$this->db->where('tStatus',2);
		return $this->db->count_all_results($this->table_name_testimonials);
	}
        
        /**
		@Function Name:	        get_records_testimonials
		@Author Name:	        Janet Rajani
		@Date:			Nov, 6 2013
		@title                 | String | title of survey
		@start                 | numeric| start offset of record 
		@limit                 | numeric| limit of record 
		@return                array 
		@Purpose:		get questionnaire title, and total no of questions inside it
	*/
		
	function get_records_testimonials($tCourse = '', $start = 0 , $limit = 10)
        {
            $this->db->select('t.tID, t.tAuthor, t.tTestimonial, t.tCourse, cd.cdCourseID, cd.cdCourseTitle, u.firstName, u.lastName, cs.csID');
            $this->db->join($this->table_name_course_schedule.' cs','cs.csID = t.tCourse','LEFT');
            $this->db->join($this->table_name_course_definitions.' cd','cd.cdID = cs.csCourseDefinitionId','LEFT');
            $this->db->join($this->table_name_users.' u','u.id = t.tAuthor','LEFT');
            if($tCourse)
            {
                $this->db->like('cd.cdCourseID',$tCourse);
                $this->db->or_like('cd.cdCourseTitle',$tCourse);
            }
            
            $where = 't.tStatus = 2';
            $query = $this->db->get_where($this->table_name_testimonials.' t', $where, $limit , $start );
            return $query->result_array();
	}
        
        /**
		@Function Name:	       get_instuctors
		@Author Name:	       Janet Rajani
		@Date:			Nov 15, 2013
		@data                  array | array of  record 
		@return                array
		@Purpose:	       get instructor array with all instructor of 
                                       this course and if admin already assigned the testimonial? 
	
	*/
	function get_instuctors($tCourse=0,$tID=0)
	{
                //already_assigned_instructor are the instructors whom admin already assgned this testimonial
                $this->db->select('u.id,u.firstName,u.lastName, ta.assigned_instructor AS already_assigned_instructor');
		$this->db->join('users u' ,'ci.ciUID =u.id','LEFT');
                $this->db->join($this->table_name_test_approv.' ta','ci.ciUID=ta.assigned_instructor AND ta.tID='.$tID,'LEFT');
		$this->db->where('ci.ciCsID',$tCourse);
                //Will get all admin approved testimonials
                $query  = $this->db->get($this->table_instructor.' ci');
               
		$result = $query->result();
		return $result;
	}
        
        /**
		@Function Name:	        delete_assigned_instructor
		@Author Name:	        Janet Rajani
		@Date:			Nov, 15 2013
		@id                     | numeric| primary key of record 
		@return                 boolean
		@Purpose:		delete data 
	
	*/
	function delete_assigned_instructor($tID=0)
        {
		$this->db->delete($this->table_name_test_approv, array('tID' => $tID)); 
		return true;
	}
        
        /**
		@Function Name:	        get_questionnaire_max_order
		@Author Name:	        Janet Rajani
		@Date:			Nov, 20 2013
		@return                 boolean
		@Purpose:		get the maximum order number for this questionnaire 
	
	*/
        function get_questionnaire_max_order($questionnaire_id=0)
        {
            $this->db->select('MAX(qOrder) as qOrder');
            $this->db->where('qParent',$questionnaire_id);
            $query = $this->db->get($this->table_name);
            return $query->row();
        }
        
}//end of class
//end of file 