<?php
/**
@Page/Module Name/Class: 		permission.php
@Author Name:			 		ben binesh
@Date:					 		Oct , 18 2013
@Purpose:		        		contain all controller functions for manage user permissions 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */ 
class Permission extends CI_Controller {

    // set defaults
    var $permissions = array();
	var $_id;
    
	public function __construct()
	{
		parent::__construct();
                use_ssl(FALSE);
		$js=array();
		$this->_id=0;
		$this->load->model('permission_model');
		
		$this->load->helper('common');
		$this->load->helper('form');
		if(!is_logged_in()){
			redirect("login/signin?redirect=".urlencode(get_current_url()));
		}else{
			//check the sufficient access level 
			$this->_current_request = 'edu_admin/'.$this->router->class.'/index';
			if(!is_allowed($this->_current_request))
			{		
				set_flash_message('You don\'t have sufficient permission to access this page  ','warning');
				redirect('home/error');
			}
		}
		
	}
    
	/**
		@Function Name:	index 
		@Author Name:	ben binesh
		@Date:			Oct , 18 2013
		@Purpose:		load all permissions 
	
	*/
	function index()
	{
		$data = array();
        $this->page_title="Manage Permissions";
		$data['meta_title']="Manage Permissions";
		$permissions_data = $this->permission_model->get_permissions_list();
		$permissions_list=array();
		$this->permission_model->get_permission_grid_list($permissions_list,$permissions_data);
		$data['results']=$permissions_list;
		$data['main']='edu_admin/permission/index';
		$this->load->vars($data);
		$this->load->view('template');
		
	}
	
	
	/**
		@Function Name:	create 
		@Author Name:	ben binesh
		@Date:			Oct , 18 2013
		@Purpose:		load the add/edit form for permissions
	
	*/
	
	function create($id=0){
		$data = array();
		$error = false;
		$errors = array();
		$is_new_record=true;
		
		$this->page_title='Add Permission';
		$data['meta_title']='Add Permission';
		if($id){
			$data['result']=$this->_load_permission($id);
			$this->_id=$data['result']->permissionID;
			$is_new_record=false;
			$this->page_title='Update Permission';
			$data['meta_title']='Update Permission';
			
		}
		if(count($_POST)>0){
			$this->load->library('form_validation');
			$this->form_validation->set_rules('permission', 'Permission Name', 'trim|required');
			$this->form_validation->set_rules('key', 'Permission Key', 'trim|required');
			if(!$id)
				$this->form_validation->set_rules('key', 'Permission Key', 'trim|required|is_unique[permissions.key]');
			$this->form_validation->set_message('required', '%s must not be blank');
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
						'permission' => $this->input->post('permission'),
						'key' => $this->input->post('key'),
						'parentID' => $this->input->post('parentID'),
				);
					
				if($is_new_record){
					$id = $this->permission_model->insert_permission($data_array);
					set_flash_message('Permission details has been inserted successfully','success');
				}else{
					$this->permission_model->update_permission($id,$data_array);
					set_flash_message('Permission details has been update successfully','success');
				}
				redirect('edu_admin/permission/index');
				
			}
		}
		$data['errors'] = $errors;
		
		$data['main']='edu_admin/permission/permission_form';
		$permissions_data = $this->permission_model->get_permissions_list();
		$permissions_list=array(0=>'None');
		$this->permission_model->get_permission_dropdown_list($permissions_list,$permissions_data);
		$data['permissions_list']=$permissions_list;
		$this->load->vars($data);
		$this->load->view('template');
		
	}
	
	/**
		@Function Name:	groups 
		@Author Name:	ben binesh
		@Date:			Oct , 18 2013
		@Purpose:		load user groups 
	
	*/
    
	function groups()
	{
		$data=array();
		$this->page_title   = 'Manage Groups';
		$data['meta_title'] = 'Manage Groups';
		$this->js[]='js/fancybox/source/jquery.fancybox.pack.js';
		$this->css[]='js/fancybox/source/jquery.fancybox.css';
		$data['results']=$this->permission_model->get_groups(true);
		$data['main'] = 'edu_admin/permission/groups';
		$this->load->vars($data);
		$this->load->view('template');
		
		
	}
	
	/**
		@Function Name:	create_group 
		@Author Name:	ben binesh
		@Date:			Oct , 18 2013
		@Purpose:		load add/edit form for user group
	
	*/
	
	function create_group($id=0){
		$error = false;
		$errors = array();
		$is_new_record=true;
		$this->js[]='js/admin.js';
		$data['selected_permission']=array();
		$this->page_title   = 'Create Group';
		$data['meta_title'] = 'Create Group';
		$data['permission']=$this->permission_model->get_permissions_list();
		if($id)
		{
			$data['result']  = $this->_load_group($id);
			$this->_id=$data['result']->groupID;
			$is_new_record  = false;
			$data['selected_permission'] = array_keys($this->permission_model->get_user_permission_array($id));
			$this->page_title   = 'Update Group';
			$data['meta_title'] = 'Update Group';
			
		}
		if(count($_POST)>0){
			$data['selected_permission']=$this->input->post('permission');
			$this->load->library('form_validation');
			$this->form_validation->set_rules('groupName', 'Group Name', 'trim|required');
			$this->form_validation->set_rules('groupKey', 'Group Key', 'trim|required');
			
			if( '' != $this->input->post('groupKey') ){
				$this->form_validation->set_rules('groupKey', 'groupKey', 'callback_duplicate_group_check');
			}
			$this->form_validation->set_message('required', '%s must not be blank');
			
			if ($this->form_validation->run() == TRUE && $error==false  )
            {
				$data_array = array(
						'groupName' => $this->input->post('groupName'),
						'groupKey' => url_title($this->input->post('groupKey'),'-',true),
				);
					
				if($is_new_record){
					$id = $this->permission_model->insert_group($data_array);
					set_flash_message('Group  details has been inserted successfully','success');
				}else{
					$this->permission_model->update_group($id,$data_array);
					set_flash_message('Group details has been update successfully','success');
				}
				
				$this->_add_group_permission($id);
				redirect('edu_admin/permission/groups');
				
			}
		}
		$data['errors'] = $errors;
		$data['main'] = 'edu_admin/permission/group_form';
		$this->load->vars($data);
		$this->load->view('template');
		
	}
	
	/**
		@Function Name:	duplicate_group_check
		@Author Name:	binesh
		@Date:			IOct, 16 2013
		@Purpose:		check the duplicate record in data base with same title  
	
	*/
	
	public function duplicate_group_check($title='')
	{	
		$id=$this->_id;
		if ($this->permission_model->check_duplicate_group($id,$title))
		{	
			$this->form_validation->set_message('duplicate_group_check', 'The  "'.$title.'"  is already created');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	
	
	/**
		@Function Name:	_load_group
		@Author Name:	ben binesh
		@Date:			Oct 18,2013
		@Purpose:		load the single record  
	
	*/
	function _load_group($id=0){
		if(!$id){
			redirect('home/error404');
		}
		$data = $this->permission_model->get_single_group($id,true);
		if(empty($data)){
			redirect('home/error404');
		}else{
			return $data;
		}
	}
	
	
	/**
		@Function Name:	_load_group
		@Author Name:	ben binesh
		@Date:			Oct 30,2013
		@Purpose:		load the single record  
	
	*/
	function _load_permission($id=0){
		if(!$id){
			show_404('page');
		}
		$data = $this->permission_model->get_single_permission($id);
		if(empty($data)){
			show_404('page');
		}else{
			return $data;
		}
	}
	
	/**
		@Function Name:	_add_group_permission
		@Author Name:	ben binesh
		@Date:			Oct 31,2013
		@Purpose:		add group permission mapping 
	
	*/
	
	
	protected function _add_group_permission($id)
	{
		if($permissions =$this->input->post('permission'))
		{
			//delete the older permissions 
			$this->permission_model->delete_map($id);
			
			foreach($permissions as $permission)
			{
				$this->permission_model->insert_map(
					array(
						'groupID'=>$id,
						'permissionID'=>$permission,
						
					)		
				);
			}
		
		}
	}
	
	
	/**
		@Function Name:	group_permissions
		@Author Name:	ben binesh
		@Date:			Oct 31,2013
		@Purpose:		load the permission page assign to specific group
	
	*/
	
	function group_permissions($id)
	{
		$data=array();
		$data['permission']=$this->permission_model->get_permissions_list();
		$data['result']  = $this->_load_group($id);
		$this->_id=$data['result']->groupID;
		$data['selected_permission'] = array_keys($this->permission_model->get_user_permission_array($id));
		
		
		$data['main']='edu_admin/permission/permissions';
		$this->load->vars($data);
		$this->load->view('popup');
		
	}
	
	

}//end of file 
