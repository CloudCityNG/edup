<?php 
/**
@Page/Module Name/Class:                        quetionnaire_report.php
@Author Name:			 		Janet Rajani
@Date:					 	Nov, 25 2013
@Purpose:		        		Contain all general functions for questionnaire report display
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Questionnaire_report extends CI_Controller 
{
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
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->helper('url');
                $this->load->model('user_model');
                
                $this->_user_id=$this->session->userdata('user_id');
	}
   
     /**
		@Function Name:	index
		@Author Name:	Janet Rajani 
		@Date:		Nov, 25 2013
		@Purpose:	Display all questionnaire
	
	*/
    function index($instructor_id=0,$course_id=0)
    {
        //Check user permission
        if(!is_logged_in())
        {
                redirect("login/signin?redirect=".urlencode(get_current_url()));
        }
        else
        {	
                //check the sufficient access level 
                $this->_current_request = $this->router->class.'/'.$this->router->method;
                if(!is_allowed($this->_current_request))
                {		
                        set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
                        redirect('home/error');
                }
        }
        //End check user permission
        $data['layout']      = '';
        $this->page_title    = 'Questionnaire Reports';
        $data['meta_tilte']  = 'Questionnaire Reports';
        $data['assignTitle'] = $this->input->get('assignTitle'); 
      
        $data['results']     = $this->questionnaire_report_model->get_questionnaire_report('',$instructor_id,0,NULL,$course_id);
        $data['meta_title']  = 'Questionnaire Reports';
        $data['main']        = 'questionnaire_report/index';
        $this->load->vars($data);
        $this->load->view('template');
    }

    /**
            @Function Name:	report_question
            @Author Name:	Janet Rajani 
            @Date:		Nov, 25 2013
            @Purpose:	        Report of questions inside a questionnaire

    */
    function report_question($assignID='',$assignQuestionnaire='')
    {
        //Check user permission
        if(!is_logged_in())
        {
                redirect("login/signin?redirect=".urlencode(get_current_url()));
        }
        else
        {	
                //check the sufficient access level 
                $this->_current_request = $this->router->class.'/'.$this->router->method ;
                if(!is_allowed($this->_current_request))
                {		
                        set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
                        redirect('home/error');
                }
        }
        //End check user permission
        if(!is_numeric(trim($assignID)) || !is_numeric(trim($assignQuestionnaire)))
        {
            redirect('home/error404');
        }
       $data['layout']        = '';
       $this->js[]            = 'js/fancybox/source/jquery.fancybox.pack.js';
       $this->css[]           = 'js/fancybox/source/jquery.fancybox.css';
       
       //Get all questions and answers of a assignment
       $data['results']       = $this->questionnaire_report_model->get_questionnaire_report_question($assignID,$assignQuestionnaire);
       $assignment            = $this->questionnaire_report_model->get_single_record($assignID);
       
       //Course detail heading
            $course_title     = '<b>';
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
       $this->page_title        = 'Questionnaires Reports: '. $assignment->assignTitle;
       $key                     = '';
       $i=0;
       foreach($data['results'] as $result)
       {
            if(empty($result->qQuestion))
            {
                        $key =	'<h2>'.$result->qTitle.'</h2>';
            }
            else
            {
                        $key	= $result->qQuestion;
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
       
       $data['main']            = 'questionnaire_report/report_question';
       $this->load->vars($data);
       $this->load->view('template');
    }
    
        /**
		@Function Name:         view_responses
		@Author Name:           Janet Rajani
		@Date:			Oct 16, 2013
		@return                 none
		@Purpose:               load the comments of user
	
	*/
	function view_responses($qrAssignID='',$qr='', $qrID='')
        {
		$data                 = array();
                $this->page_title     = 'Questionnaire Text Viewer';
		$this->load->model('questionnaire_report_model');
		$data['all_comments'] = $this->questionnaire_report_model->get_all_comments($qrAssignID,$qr,$qrID);
                $data['qr']           = $qr;
               
		$data['main']         = 'questionnaire_report/view_responses';
		$this->load->vars($data);
		$this->load->view('popup');
	}
}