<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	public function index()
	{
		$data['main'] = 'welcome_message';
		$data['layout']='';
		$this->load->vars($data);
		$this->load->view('template');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */