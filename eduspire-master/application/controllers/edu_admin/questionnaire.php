<?php 
/**
@Page/Module Name/Class:                        questionnaire.php
@Author Name:			 		Janet Rajani
@Date:					 	Sept, 30 2013
@Purpose:		        		Contain all general functions for questionnaire manage (create, update etc..)
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
		// load form, url, general helper
		// load database user model
		// load library form_validation
		
                $this->load->model('login_model');
                $this->load->model('questionnaire_model');
                $this->load->model('course_schedule_model');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->helper('url');
		$this->load->library('session');
                
	}
   
     /**
		@Function Name:	index
		@Author Name:	Janet Rajani 
		@Date:		Sept, 30 2013
		@Purpose:	Display all questionnaire
	
	*/
    function index()
    {
        //Check permission
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
        $this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
        $this->css[]='js/fancybox/source/jquery.fancybox.css';
        $this->page_title = 'Questionnaires';
        $data['layout']         = '';
        $data['qID']            = $this->input->get('qID'); 
        $data['qTitle']         = $this->input->get('qTitle'); 
        $num_records            = $this->questionnaire_model->count_records_questionnaire( $data['qTitle']);
        $base_url               = base_url().'edu_admin/questionnaire/index';
        $start                  = $this->uri->segment($this->uri->total_segments());
        if( !is_numeric( $start ) )
        {
            $start = 0;
        }
        $per_page               = PER_PAGE; 
        //It will give all questionnaire title with the count of no of questions in it
        $results        = $this->questionnaire_model->get_records_questionnaire( $data['qTitle'], $start , $per_page );

        $counter=0;
        $questionnaire_submit_detail = array();
        foreach($results as $questionnaire_detail_submit_info=>$value)
        {
           //get if the questionnaire is submitted by any student? 
           $total_submission =  $this->questionnaire_model->check_used_questionnaire($value->qID);
           //if questionnaireID matches it means it is submitted by atleast one student
            if($total_submission->assignQuestionnaire == $value->qID)
            {
                //If questionnaire is submitted by students then add that count in array
                $questionnaire_submit_detail[$counter]=array('totalQuestions'=>$value->totalQuestions,'qID'=>$value->qID,'qTitle'=>$value->qTitle,'total_submission'=>$total_submission->total_submission);
$counter++;
            }
            else
            {
                //If no one fills the questionnaire then count will be added as 0 in array
                $questionnaire_submit_detail[$counter]=array('totalQuestions'=>$value->totalQuestions,'qID'=>$value->qID,'qTitle'=>$value->qTitle,'total_submission'=>0);
             $counter++;
            }
      } 
       $data['results']=$questionnaire_submit_detail;
       $data['pagination_links']   = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
       
       $data['main']               = 'edu_admin/questionnaire/index';
       $this->load->vars($data);
       $this->load->view('template');
    
    }
    /**
		@Function Name:	questionnaire_title
		@Author Name:	Janet Rajani 
		@Date:		Oct, 1 2013
		@Purpose:	Save the title of Questionnaire
	
	*/
    function questionnaire_title($qID='')
    {
        //Check permission
        if(!is_logged_in())
        {
                redirect("login/signin?redirect=".urlencode(get_current_url()));
        }else
        {	
                //check the sufficient access level 
                $this->_current_request = 'edu_admin/'.$this->router->class.'/'.$this->router->method ;
                if(!is_allowed($this->_current_request))
                {		
                        set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
                        redirect('home/error');
                }
        }
        //End permission
      $this->page_title ='Questionnaire Title';
      $this->qID= $qID;
      $is_new_title = true;
      if($qID)
      {
          $is_new_title = false;
      }
      $questionnaire_val    = $this->questionnaire_model->get_single_record($qID,'qTitle');  
      if($questionnaire_val)
      {
        $data['qTitle']       = $questionnaire_val->qTitle;
      }
       if(count($_POST)>0)
       {
           //Validate if title is unique by callback_duplicate_title_check
           $this->form_validation->set_rules('qTitle','Title','trim|required|callback_duplicate_title_check');
       
         if($this->form_validation->run()==TRUE )
         {
                $data_array=array('qTitle'=>$this->input->post('qTitle'));
                //If new title then insert else update existing
                if($is_new_title)
                {
                    $this->questionnaire_model->insert($data_array);
                    set_flash_message('New questionnaire title created','success');
                }
                else
                {
                    $this->questionnaire_model->update($qID, $data_array);
                    set_flash_message('Questionnaire title updated','success');
                }
                
		redirect('edu_admin/questionnaire');
         }
       }
        $data['layout']         = '';
        $this->load->helper('form');
  
        $data['main']   ='edu_admin/questionnaire/questionnaire_title';
        $this->load->vars($data);
        $this->load->view('template');
    }
    
    /**
        @Function Name:	duplicate_title_check
        @Author Name:	Janet Rajani
        @Date:		Oct, 1 2013
        @Purpose:	Check if the questionnaire title is unique
	
	*/
	
	function duplicate_title_check($qTitle='')
	{	
                $qID    = $this->qID;
		if (($this->questionnaire_model->check_if_title_unique($qTitle,$qID)>0))
		{	
                        $this->form_validation->set_message('duplicate_title_check', 'The title "'.$qTitle.'" already exists');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
        /**
		@Function Name:	update_questions
		@Author Name:	Janet Rajani
		@Date:		Oct, 1 2013
		@Purpose:	Add/edit questions in a Questionnaire
	
	*/
	
	function update_questions($questionnaire_id='', $question_id='')
	{	
            //Check permission
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
                $this->page_title= 'Possible Question Types';
		$data['layout'] = '';
                $data['questionnaire_id'] = $questionnaire_id;
                $data['result'] = $this->questionnaire_model->get_single_record($question_id);
                if(count($_POST)>0)
                {
                    //To save question order in db. This will be helpful to match to answer table (results)
                    $qOrder_max =  $this->questionnaire_model->get_questionnaire_max_order($questionnaire_id);
                    $qOrder     = $qOrder_max->qOrder+1;
                    //Validation
                    
                    $this->form_validation->set_rules('qType','Question Type','trim|required');
                    //If its a section then please give title to section. Otherwise question is mandatory
                    if($this->input->post('qType')==SECTION)
                    {
                        $this->form_validation->set_rules('qTitle','Title','trim|required');
                    }//if question is multiple answer type then give options
                    elseif($this->input->post('qType')==CHECKBOX_GROUP || $this->input->post('qType')==SELECT || $this->input->post('qType')==STAR_RATING || $this->input->post('qType')==RADIO_SECTION)
                    {
                        $this->form_validation->set_rules('qQuestion','Question','trim|required');
                        $this->form_validation->set_rules('qAnswers','Answers','trim|required');
                    }
                    else
                    {
                        $this->form_validation->set_rules('qQuestion','Question','trim|required');
                    }
                    
                    $this->form_validation->set_message('required','%s must not be blank');
                    
                    if($this->form_validation->run()==TRUE)
                    {
                        
                        $save_data = array(
                        'qType'=>$this->input->post('qType'),  
                        'qTitle'=>$this->input->post('qTitle'),
                        'qQuestion'=>$this->input->post('qQuestion'),
                        'qAnswers'=>$this->input->post('qAnswers'),
                        'qHelp'=>$this->input->post('qHelp'),
                        'qOptional'=>$this->input->post('qOptional'),
                        'qParent'=>$questionnaire_id,
                        'qDateCreated'=>date('Y-m-d H:i:s'),
                        'qOrder'=>$qOrder
                        );
                        //If editing question then
                        if($question_id=='')
                        {
                            $this->questionnaire_model->insert($save_data);
                            set_flash_message('Question created successfully','success');
                            redirect('edu_admin/questionnaire/manage_questions/'.$questionnaire_id);
                        //Adding new question
                        }
                        else
                        {
                            $this->questionnaire_model->update($question_id,$save_data);
                            set_flash_message('Question updated successfully','success');
                            redirect('edu_admin/questionnaire/manage_questions/'.$questionnaire_id.'/'.$question_id);
                        }
                    }
                }
                $data['main']   ='edu_admin/questionnaire/update_questions';
                $this->load->vars($data);
                $this->load->view('template');
	}
        /**
		@Function Name:	manage_questions
		@Author Name:	Janet Rajani 
		@Date:		Oct, 3 2013
		@Purpose:	Display all questions of a questionnaire
	
	*/
    function manage_questions($qID='')
    {
        if(!$qID)
        {
            redirect('home/error404');
        }
       $this->page_title= 'Questionnaire Questions';
       $data['layout']         = '';
       $data['questionnaire_id']    = $qID;
       $data['results']             = $this->questionnaire_model->get_records_questions($qID);
       //Check if questionnaire is not submitted by any user
       $total_submission            = $this->questionnaire_model->check_used_questionnaire($qID);
       $data['total_submission']    = $total_submission->total_submission;
       //Save question order
       if(count($_POST)>0)
       {
           foreach($this->input->post('qOrder') as $qOrderKey=>$qOrderValue)
           {
               $this->questionnaire_model->update($qOrderKey, array('qOrder'=>$qOrderValue));
               set_flash_message('Order saved','success');
           }
           redirect('edu_admin/questionnaire/manage_questions/'.$qID);
       }
       //End save question order
       $data['main']                = 'edu_admin/questionnaire/manage_questions';
       $this->load->vars($data);
       $this->load->view('template');
    }
    /**
		@Function Name:	delete_questionnaire
		@Author Name:	Janet Rajani 
		@Date:		Oct, 3 2013
		@Purpose:	Display all questions of a questionnaire
	
	*/
    function delete_questionnaire($qID='')
    {
       
        $questionnaire_deleted = $this->questionnaire_model->delete($qID);
        if($questionnaire_deleted)
        {
            set_flash_message('Questionnaire deleted','success');
            redirect('edu_admin/questionnaire');
        }
        $data['main']               = 'edu_admin/questionnaire/manage_questions';
        $this->load->vars($data);
        $this->load->view('template');
    }
    /**
		@Function Name:	delete_question
		@Author Name:	Janet Rajani 
		@Date:		Oct, 3 2013
		@Purpose:	Delete selected question
	
	*/
    function delete_question($questionnaire_id='',$qID='')
    {
       
       $questionnaire_deleted = $this->questionnaire_model->delete($qID);
       
       if($questionnaire_deleted)
       {
           set_flash_message('Question deleted','success');
           redirect('edu_admin/questionnaire/manage_questions/'.$questionnaire_id);
       }
       
    }
 
    /**
		@Function Name:	preview_survey_form
		@Author Name:	Janet Rajani 
		@Date:		Dec, 10 2013
		@Purpose:	Preview questionnaire form to admin after creating
	
	*/
    function preview_survey_form($qID='')
    {
        if(!$qID)
        {
            redirect('home/error404');
        }
        $data['layout']         = '';

        $this->css[]='css/jRating.jquery.css';
        $this->js[] ='js/jRating.jquery.js';
        //get all question and answers of this questionnaire
        $data['results']        = $this->questionnaire_model->get_question_answers( $qID );
        
        $data['main']               = 'edu_admin/questionnaire/preview_survey_form';
        $this->load->vars($data);
        $this->load->view('popup');
    }
}