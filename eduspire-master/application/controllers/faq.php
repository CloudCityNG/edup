<?php
/**
@Page/Module Name/Class: 		faq.php
@Author Name:			 		ben binesh
@Date:					 		Aug, 19 2013
@Purpose:		        		Contain all controller functions faq
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
Chronological Development
***********************************************************************************
Ref No.  |   Author name	| Date		| Severity 	| Modification description
***********************************************************************************
***********************************************************************************/
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Faq extends CI_Controller {
	
	
	Public $page_title;
	public function index()
	{
            use_ssl(FALSE);
		
		$data = array();
		/**
			meta information
		*/
		$data['meta_title']='FAQ';
		$data['meta_descrption']='faq';
		$data['main'] = 'faq';
		$this->js[]='js/jquery-ui.js';
		//$this->css[]='css/jquery-ui.css';
		
		$this->load->model('faq_model');
		$audience = VISITORS;
		if(is_logged_in()){
			$audience = '';
		}
		$this->page_title='Frequently Asked	Questions';
		
		$data['content'] = $this->faq_model->get_records('',$audience,STATUS_PUBLISH,0, $limit = 9999,'*') ;
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	
	
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */