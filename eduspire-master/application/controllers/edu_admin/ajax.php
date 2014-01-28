<?php
/**
@Page/Module Name/Class: 		ajax.php
@Author Name:			 		ben binesh
@Date:							Aug, 13 2013
@Purpose:		        		Contain ajax request function for the backend  
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
//Chronological development

//***********************************************************************************
//| Ref No.  |   Author name	| Date		| Severity 	| Modification description
//***********************************************************************************


//***********************************************************************************

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {
	
	
	
	
	public function index()
	{
		return;
	}
	
	
	/**
		@Function Name:	page 
		@Author Name:	ben binesh
		@id:	        | integer | id of page 
 		@Date:			Aug, 23 2013
		@Purpose:		load the static page 
	
	*/
	function course_definition($genre_id = 0){
		$this->load->helper('form');
		$definition_array=get_dropdown_array('course_definitions',$where_condition=array('cdGenre'=>$genre_id),$order_by='cdCourseTitle',$order='ASC','cdID','cdCourseID','cdCourseTitle',true,array(''=>'Select'));	
		echo form_dropdown('csCourseDefinitionId',$definition_array,'','id="id_csCourseDefinitionId"');
	}
	
	/** test function */
	function import(){
		//$this->load->model('data_model');
		//$this->data_model->adjust_product_type();	
	}
	
	
	
	
}//End of class 

/* End of file  */
