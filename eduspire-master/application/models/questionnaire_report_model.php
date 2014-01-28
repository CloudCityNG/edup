<?php
/**
@Page/Module Name/Class:                        questionnaire_report_model.php
@Author Name:			 		janet rajani
@Date:					 	Sept, 30  2013
@Purpose:		        		Survey related
@Table referred:				
@Table updated:					
@Most Important Related Files	NIL
*/

class Questionnaire_report_model extends CI_Model {
	public $table_name           = 'assignments';
        public $table_name_def    = 'questionnaire_defs';
        public $table_name_result    = 'questionnaire_results';
        public $table_name_testimonials    = 'testimonials';
        public $table_name_course_instructor    = 'course_instructor';
 
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
                
	}
	
	/*
		@Function Name:         count_questionnaire_report
		@Author Name:           Janet Rajani
		@Date:			Oct, 3 2013
		@Purpose:		count questionnaire report 
	
	*/
	function count_questionnaire_report($assignTitle = '',$instructor_id=''){
		
                $this->db->select('a.assignID');
		
                if($assignTitle)
			$this->db->like('assignTitle',$assignTitle);
		$this->db->join('course_schedule cs','cs.csID = a.assignCnfID','LEFT');
                $this->db->join('course_definitions cd','cd.cdID = cs.csCourseDefinitionId','LEFT');
                //If instructor is seeing the reports, then only show his assignment report
                if($instructor_id)
                {
                    $this->db->join($this->table_name_course_instructor. ' ci','ci.ciCsID = cs.csID ','RIGHT');
                }
                $where = 'a.assignType = '. ASGN_QUESTIONNAIRE;
                $this->db->group_by('a.assignID');
		$query = $this->db->get_where($this->table_name.' a', $where);
                
		return $query->num_rows();
	}
       
        /**
		@Function Name:	        get_single_record
		@Author Name:	        Janet Rajani
		@Date:			Oct, 4 2013
		@id                     | numeric| primary key of record 
		@return                 array
		@Purpose:		get the single record 
	
	*/
	function get_single_record($assignID=0)
        {
		
                $this->db->select('a.assignTitle, a.assignDueDate, cd.cdCourseID,cd.cdCourseTitle, cs.csStartDate, cs.csLocation, cs.csCity, cs.csState');
              
                $this->db->join('course_schedule cs','cs.csID = a.assignCnfID','LEFT');
                $this->db->join('course_definitions cd','cd.cdID = cs.csCourseDefinitionId','LEFT');
                $this->db->where('assignID',$assignID);
		$query = $this->db->get($this->table_name.' a');
                return $query->row();
	}
        /**
		@Function Name:	        get_single_record_question
		@Author Name:	        Janet Rajani
		@Date:			Nov, 8 2013
		@id                     | numeric| primary key of record 
		@return                 array
		@Purpose:		get all questions of a questionnaire/survey
	
	*/
	function get_single_record_question($assignID=0){
                $this->db->select('q.qID, q.qAnswers, q.qOrder, q.qType, q.qTitle, q.qQuestion');
		$this->db->where('a.assignID',$assignID);
                $this->db->join($this->table_name_def.' q','q.qParent=a.assignQuestionnaire','LEFT');
                $this->db->order_by('q.qOrder','ASC');
		$query = $this->db->get($this->table_name.' a');
		return $query->result();
	}
        
        /**
		@Function Name:	get_single_record_answers
		@Author Name:	Janet Rajani
		@Date:			Nov, 8 2013
		@id  | numeric| primary key of record 
		@return  array
		@Purpose:		get all answers given by a user for a questionnaire/survey
	
	*/
	function get_single_record_answers($assignID=0,$qrUserID=0)
        {
		$this->db->where('qrAssignID',$assignID);
                $this->db->where('qrUserID',$qrUserID);
		$query = $this->db->get($this->table_name_result);
		return $query->row_array();
	}
        
        /**
		@Function Name:	        get_single_answer
		@Author Name:	        Janet Rajani
		@Date:			Oct, 23 2013
		@return                 array
		@Purpose:		get the single answer from result table
	*/
	function get_single_answer($qrID='',$qr='')
        {
                $this->db->select('qr'.$qr);
		$this->db->where('qrID',$qrID);
		$query = $this->db->get($this->table_name_result);
		return $query->row('qr'.$qr);
	}
	/**
		@Function Name:	get_records
		@Author Name:	Janet Rajani
		@Date:			Sep, 30 2013
		@title   | String | title of survey
		@start  | numeric| start offset of record 
		@limit  | numeric| limit of record 
		@return  array 
		@Purpose:		get questionnaire title, and total no of questions inside it
	
	*/
		
	function get_questionnaire_report($assignTitle = '',$instructor_id='', $start = 0 , $limit = NULL,$course_id=0)
        {
		$this->db->select('a.assignID, a.assignQuestionnaire, a.assignTitle, a.assignCnfID, a.assignDueDate, cd.cdCourseID,cd.cdCourseTitle, cs.csStartDate, cs.csLocation, cs.csCity, cs.csState');
		
                if($assignTitle)
			$this->db->like('assignTitle',$assignTitle);
		
                $this->db->join('course_schedule cs','cs.csID = a.assignCnfID','LEFT');
                $this->db->join('course_definitions cd','cd.cdID = cs.csCourseDefinitionId','LEFT');
                //If instructor is seeing the reports, then only show his assignment report
                if($instructor_id)
                {
                    $this->db->join($this->table_name_course_instructor. ' ci','ci.ciCsID = cs.csID ','RIGHT');
                    $this->db->where('ci.ciUID',$instructor_id);
                }
                $where = 'a.assignType = '. ASGN_QUESTIONNAIRE;
                if($course_id)
                        $this->db->where('cs.csID',$course_id);
				
                $this->db->group_by('a.assignID');
                $this->db->order_by('a.assignDueDate', 'DESC');
		$query = $this->db->get_where($this->table_name.' a', $where, $limit , $start );
                
		return $query->result();
	}
        
        /*
		@Function Name:         get_questionnaire_answers_report
		@Author Name:           Janet Rajani
		@Date:			Oct 4 2013
		@Purpose:		Get all answers report related to a questionnaire
	
	*/
	function get_questionnaire_report_question($assignID='',$assignQuestionnaire='',$userID='')
        {
		$this->db->select('a.assignID,a.assignTitle,qd.qType, qd.qOrder, a.assignQuestionnaire, qd.qQuestion,qd.qTitle, qd.qAnswers, qr.* ');
		
                $this->db->join($this->table_name_def.' qd','qd.qParent = a.assignQuestionnaire','LEFT');
                $this->db->join($this->table_name_result.' qr','qr.qrAssignID = a.assignID','LEFT');
                $this->db->where('a.assignID',$assignID);
                $this->db->where('a.assignType',ASGN_QUESTIONNAIRE);
                if($userID)
                $this->db->where('qr.qrUserID',$userID);
                if($assignQuestionnaire)
                $this->db->where('a.assignQuestionnaire',$assignQuestionnaire);
                $this->db->group_by('qd.qID');
                $this->db->order_by('qd.qOrder','ASC');
		$query = $this->db->get_where($this->table_name.' a');
                 
		return $query->result();
                
	}
        /*
		@Function Name:         get_assignment_ans_report
		@Author Name:           Janet Rajani
		@Date:			Oct 4 2013
		@Purpose:		Get each questions average answers for particular assignment
	
	*/
	function get_assignment_answer($assignID='',$assignQuestionnaire='',$i)
        {
	
                $this->db->select('qr'.$i.' AS answer, qrAssignID, qrUserID, count(`qrID`) AS total_answer_count,qrID');
                $this->db->where('qrAssignID',$assignID);
                $this->db->group_by('qr'.$i);
                $query = $this->db->get($this->table_name_result);
                $total_answers_count = $query->result_array();
                
                //To add field name in the result
                $result_with_field_name = array();
                foreach($total_answers_count as $total_answers_count_res)
                {
                      
                    $result_with_field_name[] = 
                              array('answer'=>$total_answers_count_res['answer'],
                                    'qrAssignID'=>$total_answers_count_res['qrAssignID'],
                                    'total_answer_count'=>$total_answers_count_res['total_answer_count'],
                                    'qrID'=>$total_answers_count_res['qrID'],
                                    'qr_field'=>'qr'.$i);
                 }
                 return $result_with_field_name;
	}
        
         /*
		@Function Name:         get_users_list
		@Author Name:           Janet Rajani
		@Date:			Nov 6 2013
		@Purpose:		Fetch user list who choses this answer
	
	*/
	function get_users_list($qrAssignID='',$qr='', $answer='')
        {
            $this->db->select('qr.qrUserID, u.firstName, u.lastName');
            $this->db->join('users u','qr.qrUserID=u.id','LEFT');
            $this->db->where($qr,$answer);
            $this->db->where('qrAssignID',$qrAssignID);
            $query = $this->db->get($this->table_name_result.' qr');
           return $query->result();
        }
        
         /*
		@Function Name:         calculate_average
		@Author Name:           Janet Rajani
		@Date:			Oct 4 2013
		@Purpose:		calculate the average of answers
	
	*/
	function calculate_average($answer_no,$assignID)
        {
            $this->db->select('qr'.$answer_no.' AS answer, count(`qrID`) AS total_answer_count');
            $this->db->where('qrAssignID',$assignID);
            $this->db->group_by('qr'.$answer_no);
            $query = $this->db->get($this->table_name_result);
            return $query->result();
            
        }
         /*
		@Function Name:         get_all_comments
		@Author Name:           Janet Rajani
		@Date:			Oct 4 2013
		@Purpose:		get all comments
	
	*/
	function get_all_comments($qrAssignID,$qr,$qrID)
        {
            $this->db->select('qr.qr'.$qr. ', u.firstName, u.lastName, u.id, qr.qrUserID, qr.qrID,t.tStatus, t.tCourse, t.tID');
            $this->db->where('qr.qrAssignID',$qrAssignID);
            $this->db->where('qr.qr'.$qr.' !=','');
            $this->db->where('t.tRefQr','qr'.$qr);
            $this->db->join('users u','u.id=qr.qrUserID','RIGHT');

            $this->db->join('testimonials t','t.tRefQrID=qr.qrID','RIGHT');
            $query = $this->db->get($this->table_name_result.' qr');
            return $query->result_array();

        }
        
        /*
		@Function Name:         update_comment_status
		@Author Name:           Janet Rajani
		@Date:			Oct 24 2013
		@Purpose:		update comment approve/reject status
	
	*/
	function update_comment_status($tRefQrID='',$status='',  $qr='', $tID='')
        {
            //Update testimonial table
            $data=array('tStatus'=>$status);
            if($qr)
            $this->db->where('tRefQr','qr'.$qr);
            if($tRefQrID)
            $this->db->where('tRefQrID',$tRefQrID);
            if($tID)
            $this->db->where('tID',$tID);
            $update_status = $this->db->update($this->table_name_testimonials,$data);
            
            return true;
        }
        
         /*
		@Function Name:         total_user_completed_questionnaire
		@Author Name:           Janet Rajani
		@Date:			Oct 14 2013
		@Purpose:		Calculate total people completed the questionnaire
	
	*/
	function total_user_completed_questionnaire($assignID)
        {
            $this->db->select('qr'.$answer_no.' AS answer, count(`qrID`) AS total_answer_count');
            $this->db->where('qrAssignID',$assignID);
            $this->db->group_by('qr'.$answer_no);
            $query = $this->db->get($this->table_name_result);
           return $query->result();

            
        }
        
        /*
		@Function Name:         insert
		@Author Name:           Janet Rajani
		@Date:			Oct 4 2013
		@Purpose:		inserting into table
	
	*/
	function insert($data=array())
        {
		$this->db->insert($this->table_name,$data);
		return $this->db->insert_id(); 
	}
	
	/**
		@Function Name:	update
		@Author Name:	Janet Rajani
		@Date:		Oct, 1 2013
		@id             | numeric| primary key of record 
		@data           | array | array of single record 
		@return         integer
		@Purpose:	date data 
	
	*/
	function update($id,$data=array())
        {
		$this->db->where('qID',$id);
		$this->db->update($this->table_name,$data);
		
		return true;
	}
        /**
		@Function Name:	  update_comment_in_result
		@Author Name:	  Janet Rajani
		@Date:		  Oct, 24 2013
		@id               | numeric| primary key of record 
		@data             | array | array of single record 
		@return           integer
		@Purpose:	  update comments 
	
	*/
	function update_comment_in_result($qrID='',$qr='',$data=array())
        {
                //Update comment in result table
		$this->db->where('qrID',$qrID);
		$this->db->update($this->table_name_result,$data);
                
	}
        
	/**
		@Function Name:	    update_comment_in_testimonial
		@Author Name:	    Janet Rajani
		@Date:		    Oct, 25 2013
		@id                 | numeric| primary key of record 
		@data               | array | array of single record 
		@return             integer
		@Purpose:	    update comments in testimonial
	
	*/
	function update_comment_in_testimonial($qrID='',$qr='',$data=array())
        {
                
                //Update comment in testimonials table
		$this->db->where('tRefQrID',$qrID);
                $this->db->where('tRefQr','qr'.$qr);
		$this->db->update($this->table_name_testimonials,$data);
		return $this->questionnaire_report_model->get_single_answer($qrID,$qr);
	}
        
	/**
		@Function Name:	  delete
		@Author Name:	  Janet Rajani
		@Date:		  Oct, 1 2013
		@id               | numeric| primary key of record 
		@return           boolean
		@Purpose:	  delete data 
	
	*/
	function delete($id)
        {
		$this->db->delete($this->table_name, array('qID' => $id)); 
		return true;
	}
        
      
}//end of class
//end of file 
	
	