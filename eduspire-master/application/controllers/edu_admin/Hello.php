<?php

class Hello extends CI_Controller{
    
    function __construct(){
        parent::__construct();
        use_ssl(FALSE);
        $this->load->library('rbac');
    }
    
    function index(){

        $cp = $this->rbac->create_operation("createPage", "create page");
        $rp = $this->rbac->create_operation("readPage", "read page");
        $up = $this->rbac->create_operation("updatePage", "update page");
        $dp = $this->rbac->create_operation("deletePage", "delete page");
        $ci = $this->rbac->create_operation("createIssue", "create issue");
        $ri = $this->rbac->create_operation("readIssue", "read issue");
        $ui = $this->rbac->create_operation("updateIssue", "update issue");
        $di = $this->rbac->create_operation("deleteIssue", "delete issue");
		
        $guest 	= $this->rbac->create_role("guest", "guest role");
        $member = $this->rbac->create_role("member", "member role");
        $owner 	= $this->rbac->create_role("owner", "owner role");
        $admin 	= $this->rbac->create_role("admin", "admin role");
        
        $admMan = $this->rbac->create_task("adminManagement", "adminManagement");
        
        $this->rbac->add_childs($guest, array($rp, $ri));
        $this->rbac->add_childs($member, array($guest, $cp, $ci, $up, $ui));
        $this->rbac->add_childs($owner, array($guest, $member, $cp, $ci, $up, $ui, $dp, $di));
        $this->rbac->add_childs($admin, array($owner, $member, $guest, $admMan));
        $this->rbac->add_childs($admMan, array($di, $dp));
        
        $this->rbac->assign("admin", 1); //admin
        $this->rbac->assign("member", 2); //someone.
        $this->rbac->assign("deleteIssue", 2);        
        $this->rbac->assign("guest", 3);
        $this->rbac->assign("member", 4);
        
        $list_rol = array('1' => 'admin', '2' => 'member (+deleteIssue)', '3' => 'guest', '4' => 'member');
                
        $test_arrays = array(	array('1' => 'readPage'),
        						array('2' => 'readPage'),
        						array('3' => 'readPage'),
        						array('1' => 'deleteIssue'),
        						array('2' => 'deleteIssue'),
        						array('4' => 'deleteIssue'),
        						array('3' => 'deleteIssue'),        						
        						array('1' => 'adminManagement'),
        						array('2' => 'adminManagement'),
        						array('3' => 'adminManagement'));

        foreach($test_arrays as $test)
        {
        	foreach($test as $id => $v)
        	{
				if ($this->rbac->check_user_access($id, $v))
				{
					echo "<span style='color:green'>YES, {$list_rol[$id]} can $v</span>";
				}
				else
				{
					echo "<span style='color:red'>NO, {$list_rol[$id]} can NOT $v</span>";
				}
				echo "<br />";
			}
        }
        
        echo '<br />';
		
		$perms = array();
        $this->rbac->get_item_operations(10, $perms);
        var_dump($perms);
        echo '<br /><br />';
   		$perms1 = array();
        $this->rbac->get_user_operations(2, $perms1);
        var_dump($perms1);
    }
	
	
	function perm(){
		$perms = array();
		$perms = $this->rbac->get_item_operations(10, $perms); 
		var_dump($perms);
	}
    
}


