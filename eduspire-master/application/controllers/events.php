<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Events extends CI_Controller {
	
	public $page_title;
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$this->load->model('news_model');
		$this->load->helper(array('form','url'));
		$this->page_class="news";
		
	}
	
	
	public function index()
	{
		$data = array();
		/**
			meta information
		*/
		$js = array();
                $this->js[] ='js/frontend.js';
		$num_records          = $this->news_model->count_records( '', STATUS_PUBLISH );
		$base_url             = base_url().'/events/index';
		$start                = $this->uri->segment($this->uri->total_segments());
		if( !is_numeric( $start ) ){
			$start = 0;
		}
		$per_page            = 5; 
		$data['events']     = $this->news_model->get_records( '',STATUS_PUBLISH, $start , $per_page );
		$data['pagination_links'] = paging( $base_url , $this->input->server("QUERY_STRING") , $num_records , $per_page , $this->uri->total_segments());  
		$this->page_title="News";
		$data['layout']='two-column-right';
		$data['meta_title']='News';
		$data['meta_descrption']='News';
		$data['main'] = 'events/index';
		$data['archives'] = $this->news_model->get_records( '',STATUS_PUBLISH, 0 ,5,'nwID,nwTitle,nwDate');
		$data['sidebar'] = 'events';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	function view($title='',$id=0){
		$data = array();
		if(!$id){
			show_404();
		}
		$data['layout']='two-column-right';
		$data['event'] =$this->news_model->get_single_record($id);
		
		if(empty($data['event'])){
			show_404();
		}
		$this->page_title=$data['event']->nwTitle;
		$data['meta_title'] = $data['event']->nwTitle;
		$data['meta_descrption'] = get_excerpt($data['event']->nwDescription,'150');
		$data['main'] = 'events/single';
		$data['archives'] = $this->news_model->get_records( '',STATUS_PUBLISH, 0 ,5,'nwID,nwTitle,nwDate');
		$data['sidebar'] = 'events';
		$this->load->vars($data);
		$this->load->view('template');
	}
	
	
	
	
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */