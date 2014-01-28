<?php
/**
@Page/Module Name/Class:                        testimonials_model.php
@Author Name:			 		janet rajani
@Date:					 	Nov, 6  2013
@Purpose:		        		testimonial assigning to instructor
@Table referred:                                testimonials,assignments,course_schedule,course_definitions,
                                                course_instructor,tracks,users_profiles,users
@Table updated:                                 testimonials_approved			
@Most Important Related Files	NIL
*/

class Testimonials_model extends CI_Model 
{
        public $table_name                      = 'testimonials';
        public $table_name_test_approv          = 'testimonials_approved';
        public $table_name_assignments          = 'assignments';
        public $table_name_course_sch           = 'course_schedule';
        public $table_name_course_def           = 'course_definitions';
        public $table_name_course_genres        = 'course_genres';
        public $table_name_course_instructor    = 'course_instructor';
        public $table_name_tracks               = 'tracks';
        public $table_name_user_profiles        = 'users_profiles';
        public $table_name_users                = 'users';
        public $table_name_result               = 'questionnaire_results';
        
	public function __construct()
	{
		parent::__construct();
	}
	
        /*Instructor will approve testimonials to be shown on his profile*/
        
        /*
		@Function Name:         count_instructor_testimonials
		@Author Name:           Janet Rajani
		@Date:			Nov, 6 2013
		@Purpose:		count all approved testimonials
	*/
	function count_instructor_testimonials($instructor_id = '', $instructor_approved='')
        {
            if($instructor_approved)
                $this->db->where('instructor_approved',$instructor_approved);
            $this->db->where('assigned_instructor',$instructor_id);
            return $this->db->count_all_results($this->table_name_test_approv);
	}
        /**
		@Function Name:	get_records_instructor_testimonials
		@Author Name:	Janet Rajani
		@Date:			Nov, 12 2013
		@return  array 
		@Purpose:		get all testimonials approved and assigned to this instructor by admin
	*/
		
	function get_records_instructor_testimonials($instructor_id = '',$instructor_approved='', $start = 0 , $limit = 10)
        {
            $this->db->select('t.tID, t.tTestimonial, tTestimonialAuthor, ta.instructor_approved');
            $this->db->join($this->table_name.' t','t.tID = ta.tID','LEFT');
           
            $where = 't.tStatus = 3 ';
            $where .= ' AND ta.assigned_instructor = '.$instructor_id;
            
            //If filter by approve/disapprove status
            if($instructor_approved)
                $where .= ' AND ta.instructor_approved = '.$instructor_approved;
            $this->db->order_by('t.tID');
            $query = $this->db->get_where($this->table_name_test_approv.' ta', $where, $limit , $start );
            return $query->result();
	}
        /**
		@Function Name:	get_status_array
		@Author Name:	Ben binesh
		@emtpty |boolean| empty flag
		@emtpty_array |array| empty array
		@Date:			Sept, 13 2013
		@return  array
		@Purpose:		get array of status for instructor approval
		
	*/
	function get_status_array($empty=false,$empty_array=array(''=>''),$admin=false)
        {
		$status_array=array();
		if($empty)
                {
			$status_array = array_merge($status_array,$empty_array);
		}
		$status_array[TESTIMONOAL_APPROVED]     = 'Approved';
		$status_array[TESTIMONOAL_REJECTED]     = 'Disapproved';
                //IF the drop down is for the admin side then show two more options
		if($admin)
                {
                    $status_array[TESTIMONIAL_INSTRUCTOR_APPROVED]  = 'Inst. Approved';
		    $status_array[TESTIMONOAL_NOT_DEFINED]          = 'Not Approved';
                }
		return $status_array;
	}
	
        /**
		@Function Name:	        update_testimonial_approval
		@Author Name:	        Janet Rajani
		@Date:			Nov, 12 2013
		@Purpose:		update testimonial approval table after instructor 
                                        approve testimonials to display on his profile 
	
	*/
	function update_testimonial_approval($data_insert=array(), $tID='', $instructor_id='')
        {
                //Update comment in result table
		$this->db->where('tID',$tID);
                $this->db->where('assigned_instructor',$instructor_id);
		$this->db->update($this->table_name_test_approv,$data_insert);
	}
        
        /**
		@Function Name:	        get_course_array
		@Author Name:	        Janet Rajani
		@Date:			Nov, 13 2013
		@Purpose:		Array of course id and title
	
	*/
	function get_course_array()
        {
            $this->db->select('cs.csID, cd.cdCourseID, cd.cdCourseTitle, cd.cdID, cs.csCourseDefinitionId');
            $this->db->join($this->table_name_course_sch.' cs','cd.cdID = cs.csCourseDefinitionId','LEFT');
            $this->db->group_by('cd.cdID');
            $query = $this->db->get($this->table_name_course_def.' cd');
            $results = $query->result_array();
            //Default value
            $return_array[''] ='All';
            foreach($results as $result)
            {
                $return_array[$result['cdID']] = $result['cdCourseTitle'];
            }
            
            return $return_array;
         }
         
         /**
		@Function Name:	count_searched_testimonials
		@Author Name:	Janet Rajani
		@Date:		Nov, 13 2013
		@Purpose:	Get testimonials which user wants to see and which are approved by admin/instructor
	
	*/
         function count_searched_testimonials($level='', $gradeSubject='', $course_id='')
         {
             $this->db->select('t.tTestimonial');
            //this is to get grade subject
            $this->db->join($this->table_name_user_profiles.' up','up.user_id=t.tTestimonialAuthor','RIGHT');
            //used to get user name for home page
            $this->db->join($this->table_name_users.' u','u.id=up.user_id','LEFT');
            $this->db->join($this->table_name_course_sch.' cs','cs.csID = t.tCourse','LEFT');
            $this->db->join($this->table_name_course_def.' cd','cd.cdID = cs.csCourseDefinitionId','LEFT');
            //trID is the grade subject in user_profile table
            $this->db->join($this->table_name_tracks.' tr','tr.trID = up.gradeSubject','LEFT');
           
            if($course_id)
                 $this->db->where('cd.cdID',$course_id);
            if($gradeSubject)
            {
                   $this->db->where('up.gradeSubject',$gradeSubject);
            }
            if($level)
            {
                $this->db->where('find_in_set("'.$level.'",up.level) <>', 0);
            }
           
            $this->db->where('t.tStatus',2);
            return $this->db->count_all_results($this->table_name.' t');
         }
         
          /**
		@Function Name:	get_searched_testimonials
		@Author Name:	Janet Rajani
		@Date:		Nov, 13 2013
		@Purpose:	Get testimonials which user wants to see and which are approved by admin/instructor
                @id : 
                $level K,1,2..12
                $gradeSubject: Math,Music..
                $course_id: Course Schedule ID
                $genres_id: Course Generes(Type)
                $status : Testimonial approval type(0:Not app,1:Reject,2:Course appr,3:instructor Appr)
	
	*/
         function get_searched_testimonials($level='', $gradeSubject='', $course_id='',$start=0,$offset=15,$genres_id='', $status='')
         {
            $this->db->select('t.tTestimonial,t.tStatus, t.tTestimonialAuthor, u.firstName, u.lastName, cd.cdCourseTitle,tr.trName,cs.csID, cg.cgID,cg.cgTitle');
            //this is to get grade subject
            $this->db->join($this->table_name_user_profiles.' up','up.user_id=t.tTestimonialAuthor','RIGHT');
            //used to get user name for home page
            $this->db->join($this->table_name_users.' u','u.id=up.user_id','LEFT');
            $this->db->join($this->table_name_course_sch.' cs','cs.csID = t.tCourse','LEFT');
            $this->db->join($this->table_name_course_def.' cd','cd.cdID = cs.csCourseDefinitionId','LEFT');
            //trID is the grade subject in user_profile table
            $this->db->join($this->table_name_tracks.' tr','tr.trID = up.gradeSubject','LEFT');
           //This course genres table used to show testimonials on course page
            $this->db->join($this->table_name_course_genres.' cg','cg.cgID = cd.cdGenre','LEFT');

            if($course_id)
            {
                $this->db->where('cd.cdID',$course_id);
            }
            if($gradeSubject)
            {
                   $this->db->where('up.gradeSubject',$gradeSubject);
            }
            if($level)
            {
                $this->db->where('find_in_set("'.$level.'",up.level) <>', 0);
            }
            //This course genres table used to show testimonials on course page
            if($genres_id)
            {
                $this->db->where('cg.cgID',$genres_id);
            }
            //status=2 :Approved for course, status=3: Appr for Instructor,status=1:Rejected,status=0:Not Appr
            if($status)
            {
                $this->db->where('t.tStatus',$status);
            }
            $this->db->where('t.tStatus <>',0);
            $this->db->where('t.tStatus <>',1);
            $this->db->group_by('t.tID');
            
            //show random tetimonial on home, course page only
            if(1==$offset)
            {
                $this->db->order_by('RAND( )');
            }
            else
            {
                //Show testimonial in descending order on testimonial page
                $this->db->order_by('t.tID','DESC');
                //show only testimonial approved for course on testimonials page
                $this->db->where('t.tStatus',2);
            }
            
            $query = $this->db->get($this->table_name.' t',$offset,$start);
            return $query->result();
         }
        
        /*Testimonial of instructor for students to view*/
         /*
		@Function Name:	    get_instructor_profile_testimonials
		@Author Name:	    Janet Rajani
		@Date:		    Nov, 18 2013
		@id                 |integer| id of instructor 
		@select             | string | select the instructor testimonials 
		@return             array
		@Purpose:	    return all testimonials approved by admin and instructor to display on instructor profile
	
	*/
	function get_instructor_profile_testimonials($id=0,$offset=0,$start=0)
        {
            $this->db->select('ta.assigned_instructor, t.tTestimonial');
            $this->db->join($this->table_name_test_approv.' ta','ta.tID = t.tID','LEFT');
            $this->db->where('ta.assigned_instructor',$id);
            //Approved by instructor
            $this->db->where('ta.instructor_approved',2);
            $this->db->order_by('RAND( )');
            $query = $this->db->get($this->table_name.' t',$offset,$start);
            return $query->result();
        }
        /*End Testimonial of instructor for students to view*/
        
        /**
		@Function Name:	        get_single_record_testimonial
		@Author Name:	        Janet Rajani
		@Date:			Dec, 18 2013
		@Purpose:		get single row of testimonials table
	
	*/
	function get_single_record_testimonial($tRefQr='',$qr='',$status='',$tID='')
        {
                $this->db->select('tID, tStatus, tTestimonial, tCourse');
		if($tRefQr)
                    $this->db->where('tRefQrID',$tRefQr);
                if($qr)
                    $this->db->where('tRefQr',$qr);
                if($status)
                    $this->db->where('tStatus',$status);
                if($tID)
                    $this->db->where('tID',$tID);
		$query = $this->db->get($this->table_name);
		return $query->row();
	}
        
        /**
		@Function Name:	      delete_testimonials_approved
		@Author Name:	      Janet Rajani
		@Date:		      Dec, 18 2013
		@id                   | numeric| primary key of record 
		@return               boolean
		@Purpose:	      delete data from testimonials_approved table
	
	*/
	function delete_testimonials_approved($tID)
        {
		$this->db->delete($this->table_name_test_approv, array('tID' => $tID)); 
		return true;
	}
        /*Starts : Testimonial to be approved and edited by admin */
        /*
		@Function Name:         get_all_testimonials
		@Author Name:           Janet Rajani
		@Date:			Jan 14 2014
		@Purpose:		get all testimonials
	
	*/
	function get_all_testimonials($tTestimonial=0,$tStatus='', $start = 0 , $limit = 10)
        {
            $this->db->select(' u.firstName, u.lastName, u.id, qr.qrUserID, qr.qrID,t.tStatus, t.tCourse, t.tID,
                qr.qrAssignID, t.tRefQr, t.tTestimonialAuthor, t.tTestimonial, ta.instructor_approved, ta.assigned_instructor');
            if($tTestimonial)
                $this->db->like('t.tTestimonial',$tTestimonial);
            if($tStatus!='')
                $this->db->where('t.tStatus',$tStatus);
            $this->db->join('users u','u.id=qr.qrUserID','RIGHT');

            $this->db->join($this->table_name.' t','t.tRefQrID=qr.qrID','RIGHT');
            $this->db->join($this->table_name_test_approv.' ta','ta.tID=t.tID','LEFT');
            
            $query = $this->db->get($this->table_name_result.' qr',$limit,$start);
            //echo $this->db->last_query();
            return $query->result_array();

        }
        /*
		@Function Name:         count_all_testimonials
		@Author Name:           Janet Rajani
		@Date:			Jan 14 2014
		@Purpose:		count all testimonials
	
	*/
        function count_all_testimonials($tTestimonial=0,$tStatus='')
        {
            $this->db->select(' t.tTestimonial');
            if($tTestimonial)
                $this->db->like('t.tTestimonial',$tTestimonial);
            if($tStatus!='')
                $this->db->where('t.tStatus',$tStatus);
            $this->db->join('users u','u.id=qr.qrUserID','RIGHT');

            $this->db->join($this->table_name.' t','t.tRefQrID=qr.qrID','RIGHT');
            $this->db->join($this->table_name_test_approv.' ta','ta.tID=t.tID','LEFT');
            
            return $this->db->count_all_results($this->table_name_result.' qr');

        }
        
        /*End : Testimonial to be approved and edited by admin */
}//end of class
//end of file 