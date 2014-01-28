<?php 
/**
@Page/Module Name/Class:                        quetionnaire.php
@Author Name:			 		Janet Rajani
@Date:					 	Sept, 30 2013
@Purpose:		        		Contain all general functions for questionnaire report display
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Questionnaire extends CI_Controller {
	public $js;
	public function __construct() 
	{
		parent::__construct();
                use_ssl(FALSE);
                $js = array();
                
		$this->_user_id=$this->session->userdata('user_id');
		// load form, url, general helper
		// load database user model
		// load library form_validation
		
                $this->load->model('login_model');
                $this->load->model('questionnaire_report_model');
                $this->load->model('questionnaire_model');
                $this->load->model('assignment_model');
                $this->load->model('user_model');
                $this->load->model('user_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->helper('url');
                //Check permission
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
                $this->_user_id=$this->session->userdata('user_id');
	}
   
     /**
		@Function Name:	index
		@Author Name:	Janet Rajani 
		@Date:		Oct, 29 2013
		@Purpose:	Display all questionnaire
	
	*/
    function index($assignID='',$qID='')
    {
        if(!$assignID || !$qID)
        {
            redirect('home/error404');
        }
        $data['layout']         = '';

        $this->css[]   = 'css/jRating.jquery.css';
        $this->js[]    = 'js/jRating.jquery.js';
        
        //get all question and answers of this questionnaire
        $data['results']        = $this->questionnaire_model->get_question_answers( $qID );
     
        $i=1;
        $j=0;
        $data_results = array();
        if($this->input->post('submit_questionnaire'))
        {
           foreach($data['results'] as $validate)
           {
                //If questional is mandatory and is not a section/title
                if(($validate->qOptional==0)&&($validate->qQuestion!=NULL)):
                 $this->form_validation->set_rules($validate->qID,' ','trim|required');
                endif;
           }
           /*Save answers*/
           if($this->form_validation->run()==TRUE)
           {
                //Entry in assignment ledger
               $assignment_ledger_data = array(
                                        'alCnfID'=>$this->input->post('course_id'),
                                        'alAssignID'=>$assignID,
                                        'alAssignType'=>ASGN_QUESTIONNAIRE,
                                        'alUserID'=>$this->_user_id,
                                        'alAppliedDate'=>date('Y-m-d H:s:i')
                                        ); 
                $this->assignment_model->insert_ledger($assignment_ledger_data);
                //End entry in assignment ledger        
                foreach($this->input->post() as $answers_given)
                {
                    //If the submitted value is for the last button then exit
                    if($i==count($this->input->post())-1)
                    {
                        break;
                    }
                    //For inserting in results table
                    //if answer given then answer else insert blank answer
                    if($answers_given)
                    {
                        $data_results['qr'.$i] = $answers_given;
                        $data_results['qrAssignID'] = $assignID;
                        $data_results['qrUserID'] = $this->_user_id;
                        $data_results['qrCnfID'] = $this->input->post('course_id');
                    }
                    else
                    {
                        $data_results['qr'.$i] = NULL;
                        $data_results['qrAssignID'] = $assignID;
                        $data_results['qrUserID'] = $this->_user_id;
                        $data_results['qrCnfID'] = $this->input->post('course_id');
                    }
                    //End inserting in results
                    $i++;
                } 
                //Get last inserted qr id from questionnaire_results table to insert in testimonials table
                $qrID = $this->questionnaire_model->insert_results($data_results);
                //To insert in testimonials
                foreach($this->input->post() as $testimonials)
                {
                    //If we don't increment $j here the loop get exit without incrementing it properly, 
                    //we need proper numbering for testimonials table
                    $j++;
                    //If the submitted value is for the last button then exit
                    if($j==count($this->input->post())-1)
                    {
                        break;
                    }
                    //if answer given then answer else insert blank answer
                    if($testimonials)
                    {
                        //For inserting in testimonials
                       if(!is_numeric($testimonials)):
                            $testimonial_data = array(
                                'tStatus'=>0,
                                'tRefQr'=>'qr'.$j,
                                'tRefQrID'=>$qrID,
                                'tTestimonial'=>$testimonials,
                                'tTestimonialAuthor'=>$this->_user_id,
                                'tLastEdited'=>date('Y-m-d H:s:i'),
                                'tCourse'=>$this->input->post('course_id')
                            );   
                       else:    
                            continue;
                            $j++;
                        endif;
                        //End inserting in testimonials
                        set_flash_message('Submitted successfully','success');
                         $this->questionnaire_model->insert_testimonials($testimonial_data);
                         
                    }
                }
                //End insert in testimonial
                //To reset all values
                redirect(current_url());
            }
            //Insert all answers in questionnaire_results table. 
            /*End save answers*/
        }
        
        $data['main']               = 'questionnaire/index';
        $this->load->vars($data);
        $this->load->view('template');
    }
    /*
		@Function Name:	users_survey_answer
		@Author Name:	Janet Rajani
		@Date:		Nov 19, 2013
		@return         none
		@Purpose:	survey/questionnaire questions with the answer given by this user
	*/
	
	function users_survey_answer($qrAssignID='',$qrUserID='')
        {
                if(!$qrAssignID || !$qrUserID)
                {
                    redirect('home/error404');
                }
                //for star rating include js and css
                $this->css[]='css/jRating.jquery.css';
                $this->js[] ='js/jRating.jquery.js';
                $data=array();
                $questions      = $this->questionnaire_report_model->get_single_record_question($qrAssignID);
                $answers        = $this->questionnaire_report_model->get_single_record_answers($qrAssignID,$qrUserID);
                $user_detail    = $this->user_model->get_single_record($qrUserID);
                $data['user_name']   = $user_detail->firstName.' '.$user_detail->lastName;
                $i =1;
                $question_answer_array = array();
                $answer_array = array();
                foreach($questions as $question):
                    
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
                   
                    $user_answer_number = '';
                    if($answers)
                    {
                        $answer_number = $answers['qr'.$i];
                        foreach($answer_array as $answer_options_key=>$answer_options_value):
                            //If there is only one answer in array 
                            if(1==count($answer_array)):
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
                    }
                    $i++;
                    //Array contains question as array key and answer given by user as value of the array
                    $question_answer_array[][$quest_key] = $user_answer_number;
                endforeach;
                $data['results'] = $question_answer_array; 
                
                $data['main']            = 'questionnaire/users_survey_answer';
                $this->load->vars($data);
                $this->load->view('template');
        }
}