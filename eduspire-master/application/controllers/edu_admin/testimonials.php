<?php 
/**
@Page/Module Name/Class:                        testimonials.php
@Author Name:			 		Janet Rajani
@Date:					 	Jan, 14 2014
@Purpose:		        		Contain all general functions for the testimonial detail
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Testimonials extends CI_Controller {
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
                $this->load->model('course_schedule_model');
                $this->_user_id=$this->session->userdata('user_id');
	}
   
     /**
		@Function Name:	index
		@Author Name:	Janet Rajani 
		@Date:		Jan, 14 2014
		@Purpose:	Display all testimonials
	
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
        $this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
        $this->css[]='js/fancybox/source/jquery.fancybox.css';
       $data['layout']         = '';
       $data['tTestimonial']   = $this->input->get('tTestimonial'); 
       $data['tStatus']        = $this->input->get('tStatus');
       $num_records            = $this->testimonials_model->count_all_testimonials( $data['tTestimonial'],$data['tStatus']);
       
       $base_url               = base_url().'edu_admin/testimonials/index';
       $start                  = $this->uri->segment($this->uri->total_segments());
       if( !is_numeric( $start ) )
       {
           $start = 0;
       }
       $per_page               = PER_PAGE; 
       //Approve/reject process 
       $status_array = array();
       $status_array = $this->input->post('comment_status_');
            if(($this->input->post('process_responses')) && !empty($status_array))
            {
                foreach($this->input->post('comment_status_') as $tID=>$status)
                {
                    $result= $this->questionnaire_report_model->update_comment_status('', $status, '', $tID);
                    //If testimonial is unassigned to instructor then remove from testimonials_approved table
                    if($status!=3)
                    {
                      $testimonial_data = $this->testimonials_model->get_single_record_testimonial('','',$status,$tID);
                      $this->testimonials_model->delete_testimonials_approved($testimonial_data->tID);
                    }
                    //End unassignmed testimonial to instructor
                }
                    //reload the parent window
                    $data['reload']=true;
            }
        //End approve/reject     
       //$data['all_comments']       = $this->testimonials_model->get_all_testimonials( $data['tTestimonial'],$start ,$per_page);
       $all_comments       = $this->testimonials_model->get_all_testimonials( $data['tTestimonial'],$data['tStatus'],$start ,$per_page);
       $testimonial_all_detail = array();
       foreach($all_comments as $key=>$all_comment)
       {
           $tCourse                              = $this->course_schedule_model->get_course_detail($all_comment['tCourse']);
           $testi_course_detail['cdCourseTitle'] = $tCourse->cdCourseTitle;
           $testi_course_detail['cdCourseID']    = $tCourse->cdCourseID;
           $testi_course_detail['csLocation']    = $tCourse->csLocation;
           $testi_course_detail['csStartDate']   = $tCourse->csStartDate;
           $testi_course_detail['csCity']        = $tCourse->csCity;
           $testi_course_detail['csState']       = $tCourse->csState;
           $testi_course_detail['csCourseType']  = $tCourse->csCourseType;
           
           $testimonial_all_detail[] = array_merge($all_comment,$testi_course_detail);
       }
       $data['all_comments'] = $testimonial_all_detail;
       
       $data['pagination_links']   = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
       
       $data['tStatus']   = ($this->input->get('tStatus')  != '' )?$this->input->get('tStatus'):'';
       
       $data['main']               = 'edu_admin/testimonials/index';
       $this->load->vars($data);
       $this->load->view('template');
    }

     
         /**
		@Function Name:	        view_responses
		@Author Name:	        Janet Rajani
		@Date:			Jan 14, 2014
		@return                 none
		@Purpose:	        load the comments of user
	
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
                @Date:		Jan, 14 2014
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

            $data['main']               = 'edu_admin/testimonials/instructor_list';
            $this->load->vars($data);
            $this->load->view('popup');
        }
      
}