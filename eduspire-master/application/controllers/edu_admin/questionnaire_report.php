<?php 
/**
@Page/Module Name/Class:                        quetionnaire_report.php
@Author Name:			 		Janet Rajani
@Date:					 	Sept, 30 2013
@Purpose:		        		Contain all general functions for questionnaire report display
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Questionnaire_report extends CI_Controller {
	public $js;
	public function __construct() 
	{
		parent::__construct();
                use_ssl(FALSE);
                $js = array();
		// load form, url, general helper
		// load database user model
		// load library form_validation
		
                $this->load->model('login_model');
                $this->load->model('questionnaire_report_model');
                $this->load->model('questionnaire_model');
                $this->load->model('testimonials_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->helper('url');
                $this->load->model('user_model');
                
                $this->_user_id=$this->session->userdata('user_id');
	}
   
     /**
		@Function Name:	index
		@Author Name:	Janet Rajani 
		@Date:		Sept, 30 2013
		@Purpose:	Display all questionnaire
	
	*/
    function index()
    {
        //Check user permission
        if(!is_logged_in())
        {
                redirect("login/signin?redirect=".urlencode(get_current_url()));
        }
        else
        {	
                //check the sufficient access level 
                $this->_current_request = 'edu_admin/'.$this->router->class.'/'.$this->router->method ;
                if(!is_allowed($this->_current_request))
                {		
                        set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
                        redirect('home/error');
                }
        }
        //End user permission
       $this->page_title ='Questionnaire Reports';
       $data['layout']         = '';
      
       $data['assignTitle']    = $this->input->get('assignTitle'); 
       $num_records            = $this->questionnaire_report_model->count_questionnaire_report( $data['assignTitle']);
       
       $base_url               = base_url().'edu_admin/questionnaire_report/index';
       $start                  = $this->uri->segment($this->uri->total_segments());
       if( !is_numeric( $start ) )
       {
           $start = 0;
       }
       $per_page               = PER_PAGE; 
       
       $data['results']        = $this->questionnaire_report_model->get_questionnaire_report( $data['assignTitle'],'', $start , $per_page );
        
       $data['pagination_links']   = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
       $data['main']               = 'edu_admin/questionnaire_report/index';
       $this->load->vars($data);
       $this->load->view('template');
    }

    /**
            @Function Name:	questionnaire_question_report
            @Author Name:	Janet Rajani 
            @Date:		Oct, 3 2013
            @Purpose:	        Report of questions inside a questionnaire

    */
    function report_question($assignID='',$assignQuestionnaire='')
    {
        if(!is_numeric(trim($assignID)) || !is_numeric(trim($assignQuestionnaire)))
        {
            redirect('home/error404');
        }
        $data['layout']   = '';
        $this->js[]       ='js/fancybox/source/jquery.fancybox.pack.js';
        $this->css[]      ='js/fancybox/source/jquery.fancybox.css';
        
        //Get all questions and answers of a assignment
        $data['results']  = $this->questionnaire_report_model->get_questionnaire_report_question($assignID,$assignQuestionnaire);
        $assignment       = $this->questionnaire_report_model->get_single_record($assignID);
        
        //Course detail heading
        $course_title ='<b>';
        if($assignment->cdCourseID)
        $course_title .=$assignment->cdCourseID.':'.$assignment->cdCourseTitle;
        if($assignment->csStartDate)
        {
            $course_title .='(';
            $course_title .=format_date($assignment->csStartDate,DATE_FORMAT);
            $course_title .=')</b><br>';
        }
        if(($assignment->csCity)||($assignment->csState))
            $course_title .= $assignment->csLocation.'<br>'.$assignment->csCity.','.$assignment->csState;
       //End heading
       $data['heading']         = '<h1>'.$assignment->assignTitle.'</h1><h2>'. $course_title.'</h2>';
       
       $key                     = '';
       $i=0;
       $report_answer_responses = array();
       foreach($data['results'] as $result)
       {
           if(empty($result->qQuestion))
            {
	    		$key =	'<h2>'.$result->qTitle.'</h2>';
            }
            else
            {
	     		$key  = $result->qQuestion;
            }
            
            //In the old database of client, star rating have no options in questionnaire_defs table in answers field
            if($result->qType=='starRating')
            {
                $answer_array[] = array(0=>'1',1=>'2',2=>'3',3=>'4',4=>'5');
            }
            else
            {
                $answer_array[] = explode("\n",$result->qAnswers);
            }
            
            $data['answer_array'] = $answer_array;
            $report_answer_responses[][$key]=$this->questionnaire_report_model->get_assignment_answer($assignID,$assignQuestionnaire,$i+1);
            $i++;
       }
       $data['report_answer_responses'] = $report_answer_responses;
       
       $data['main']            = 'edu_admin/questionnaire_report/report_question';
       $this->load->vars($data);
       $this->load->view('template');
    }
    
    /**
		@Function Name:	view_responses
		@Author Name:	Janet Rajani
		@Date:			Oct 16, 2013
		@return  none
		@Purpose:	load the comments of user
	
	*/
	
	function view_responses($qrAssignID='',$qr='', $qrID='')
        {
                $this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
                $this->css[]='js/fancybox/source/jquery.fancybox.css';
		$data = array();
                $status_array = array();
		$this->load->model('questionnaire_report_model');
		$data['all_comments'] = $this->questionnaire_report_model->get_all_comments($qrAssignID,$qr,$qrID);
                $data['qr'] = $qr;
                
                $status_array = $this->input->post('comment_status_');
                if(($this->input->post('process_responses')) && !empty($status_array))
                {
                    foreach($this->input->post('comment_status_') as $tRefQrID=>$status)
                    {
                        $result= $this->questionnaire_report_model->update_comment_status($tRefQrID, $status, $qr);
                        //If testimonial is unassigned to instructor then remove from testimonials_approved table
                        if($status!=3)
                        {
                          $testimonial_data = $this->testimonials_model->get_single_record_testimonial($tRefQrID,'qr'.$qr,$status);
                          $this->testimonials_model->delete_testimonials_approved($testimonial_data->tID);
                        }
                        //End unassignmed testimonial to instructor
                    }
                        //reload the parent window
                        $data['reload']=true;
                }
		$data['main']='edu_admin/questionnaire_report/view_responses';
		$this->load->vars($data);
		$this->load->view('popup');
	}
        
        /*
		@Function Name:	view_users
		@Author Name:	Janet Rajani
		@Date:		Nov 6, 2013
		@return         none
		@Purpose:	list of users who choses this answer
                                $qrAssignID=assignment ID,$qr=Field name, $answer=Answer no chosen by user
	*/
	
	function view_users($qrAssignID='',$qr='', $answer='')
        {
                $data=array();
                $data['results'] = $this->questionnaire_report_model->get_users_list($qrAssignID,$qr, $answer);
                $data['qrAssignID'] = $qrAssignID;
                $data['main']='edu_admin/questionnaire_report/view_users';
		$this->load->vars($data);
		$this->load->view('popup');
        }
        /*
		@Function Name:	 users_survey_answer
		@Author Name:	 Janet Rajani
		@Date:		 Nov 8, 2013
		@return          none
		@Purpose:	 survey/questionnaire questions with the answer given by user
	*/
	
	function users_survey_answer($qrAssignID='',$qrUserID='')
        {
                if(!is_numeric(trim($qrAssignID)) || !is_numeric(trim($qrUserID)))
                {
                    redirect('home/error404');
                }
                $this->css[]='css/jRating.jquery.css';
                $this->js[] ='js/jRating.jquery.js';
                $data=array();
                $questions      = $this->questionnaire_report_model->get_single_record_question($qrAssignID);
                $answers        = $this->questionnaire_report_model->get_single_record_answers($qrAssignID,$qrUserID);
                $user_detail    = $this->user_model->get_single_record($qrUserID);
                $data['user_name']   = $user_detail->firstName.' '.$user_detail->lastName;
                $counter =1;
                $question_answer_array = array();
                
                foreach($questions as $question):
                    $answer_number = $answers['qr'.$counter];
                    if(empty($question->qQuestion))
                    {
                           //Need to differentiate title and question, so have to send tags with array key 
                          $quest_key = '<h2>'.$question->qTitle.'</h2>';
                    }
                    else
                    {
                           $quest_key	= $question->qQuestion;
                    }
                    //Get the answer options from questionnaire_defs table and explode to get all answer in array
                    
                    //In the old database of client, star rating have no options in questionnaire_defs table in answers field
                    if($question->qType=='starRating')
                    {
                        $answer_array = array(0=>'1',1=>'2',2=>'3',3=>'4',4=>'5');
                    }
                    else
                    {
                        $answer_array = explode("\n",$question->qAnswers);
                    }
                    
                    //Array contains question/title as array key and answer given by this user as array value
                    $counter++;
                    $user_answer_number = '';
                    
                    foreach($answer_array as $answer_options_key=>$answer_options_value):
                        //If there is only one answer in array 
                        if(count($answer_array)==1):
                            $user_answer_number = $answer_number;
                        //If morethan one answer(array of answer)
                        elseif((count($answer_array)>1)):
                            //if the answer number given by user matches the answer option 
                            if($answer_options_key+1==$answer_number):
                                $user_answer_number = $answer_options_value;
                            else:
                                continue;
                            endif;
                            //This is the case where user did not give any answer or question is section type
                        else: 
                            $user_answer_number = '';
                        endif; 
                    endforeach;
                    //Array contains question as array key and answer given by user as value of the array
                        $question_answer_array[][$quest_key] = $user_answer_number;
                endforeach;
                        
                $data['results'] = $question_answer_array; 
                
                $data['main']            = 'edu_admin/questionnaire_report/users_survey_answer';
                $this->load->vars($data);
                $this->load->view('template');
        }
        /**
		@Function Name:	        view_responses
		@Author Name:	        Janet Rajani
		@Date:			Oct 16, 2013
		@return                 none
		@Purpose:	         load the comments of user
	
	*/
	function view_response_email($id='')
        {
		$data = array();
		$this->load->model('user_model');
                $data['mail_sent'] = '';
                $email_responding_user = $this->user_model->get_single_record($id,'email,  firstName, lastName');
                if(!empty($email_responding_user))
                {
                    $data['email']=$email_responding_user->email;
                }
                else
                {
                     $data['email']='';
                }
                if($this->input->post('email_user'))
                {
                    $this->form_validation->set_rules('email_address','Email Address','trim|required');
                    $this->form_validation->set_rules('message','Message','trim|required');
                    if($this->form_validation->run()==TRUE)
                    {
                       //get admin emails 
                        $emails = get_admin_emails();
                        $mail = send_mail($email_responding_user->email, $email_responding_user->firstName, SENDER_EMAIL, $this->input->post('subject'), $this->input->post('message'),$emails);
                        if($mail)
                            set_flash_message('Mail sent','success');
                        else
                            set_flash_message('Mail not sent','warning');
                        //reload the parent window
                        $data['reload']=true;
                    }
                    
                }
             
		$data['main']='edu_admin/questionnaire_report/view_response_email';
		$this->load->vars($data);
		$this->load->view('popup');
	}
         /**
		@Function Name:	view_responses
		@Author Name:	Janet Rajani
		@Date:			Oct 16, 2013
		@return  none
		@Purpose:	load the comments of user
	
	*/
        function update_comment($qrID='',$qr='')
        {
            //Update comment in questionnaire_results table
                $data = array(
                                'qr'.$qr=>$this->input->post('comment'.$qrID)
                            );
                
                $this->questionnaire_report_model->update_comment_in_result($qrID,$qr,$data);
                //Update comment in questionnaire_results table
                $data = array(
                                'tTestimonial'=>$this->input->post('comment'.$qrID),
                                'tLastEdited'=>  date('Y-m-d H:i:s'),
                                'tAuthor'=>  $this->_user_id
                            );
                //return updated comment to show in popup
                echo $result =$this->questionnaire_report_model->update_comment_in_testimonial($qrID,$qr,$data);
        }
        /**
                @Function Name:	instructor_list
                @Author Name:	Janet Rajani 
                @Date:		Noc, 7 2013
                @Purpose:	Display all instructor list in popup who are teaching this course

        */
        function instructor_list($tCourse,$tID)
        {
            $data['layout']         = '';
            $data['results']        = $this->questionnaire_model->get_instuctors($tCourse,$tID);
            if($this->input->post('instructor'))
            {
                //First delete already assigned instructor
                $this->questionnaire_model->delete_assigned_instructor($tID);
                foreach($this->input->post('instructor') as $instructors)
                {
                    //If admin assigned this testimonial to the instructor, then save this
                    $insert_data = array('assigned_instructor'=>$instructors,
                        'tID'=>$tID,
                        'instructor_approved'=>TESTIMONOAL_APPROVED);
                    $this->questionnaire_model->insert_test_approv($insert_data);
                }
                //Update table to set Approve Instructor
                 $this->questionnaire_report_model->update_comment_status('',3,'',$tID);
                //End update
                 $data['reload']=true;
            }

            $data['main']               = 'edu_admin/questionnaire_report/instructor_list';
            $this->load->vars($data);
            $this->load->view('popup');
        }
}