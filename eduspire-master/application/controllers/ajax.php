<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {
	
	
	
	
	public function index()
	{
		
	}
	
	
	/**
		@Function Name:	page 
		@Author Name:	binesh
		@id:	        | integer | id of page 
 		@Date:			Aug, 23 2013
		@Purpose:		load the static page 
	
	*/
	function page($id=0){
		
		
	}
	
	
	/**
		@Function Name:	course_schedule 
		@Author Name  :	ben binesh
		@definition_id:	|integer | course definition id
 		@Date:			Dec ,06 2013
		@Purpose:		load the course schedule 
	
	*/
	function course_schedule($definition_id=0){
		$this->load->helper('form');
		$course_array=get_dropdown_array('course_schedule',$where_condition=array('csCourseDefinitionId'=>$definition_id),$order_by='csID',$order='ASC','csID','csLocation','csStartDate',true,array(''=>'Select'));	
		echo form_dropdown('course_id',$course_array,$this->input->get_post('course_id'));	
		
	}
	
	
	/**

		@Function Name:	iu_districts 

		@Author Name:	Janet

		@iu_ID:	        | integer | id of Intermediate Unit 

 		@Date:		Sep, 11 2013

		@Purpose:	Load all district of selected IU

	

	*/

        function iu_districts($iu_ID =0)

        {
            $this->load->helper('form');
            $school_district_array=get_dropdown_array('district',$where_condition=array('disIuUnit'=>$iu_ID,'disID != '=>'1','disPublish'=>STATUS_PUBLISH),$order_by='disName',$order='ASC','disID','disName','',true,array(0=>'Other',''=>'Select'));

            

            echo form_dropdown('school_district',$school_district_array,'');

        }
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */