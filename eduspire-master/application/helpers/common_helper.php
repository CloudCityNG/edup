<?php 
/**
@Page/Module Name/Class: 		common_helper.php
@Author Name:			 		binesh
@Date:					 		Aug, 19 2013
@Purpose:		        		contain all functions which are used appliation wide 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	/**
		@Function Name:	paging
		@Author Name:	binesh
		@Date:			Aug, 20 2013
		@url            | String | base url 
		@query_string   | String| server query string 
		@total_records  | numeric| total number of records 
		@per_page       | numeric| record par page 
		@start_index    | numeric| start offset 
		@return         |string | pagination links 
		@Purpose:		load pagination configuration and return the pagination links 
	
	*/
	
	function paging( $url , $query_string = '' , $total_records = 0 , $per_page = 0 , $start_index = 0 )
	{
			
		$ci=& get_instance();
		$ci->load->library('pagination');
		$config['base_url']   = $url;
		$config['total_rows'] = $total_records;
		if($query_string != '')
		{
			$config['first_url']  =  $url.'?'.$query_string;
			$config['suffix']     = '?'.$query_string;
		}
		$config['per_page']       = $per_page;
		$config['uri_segment']    = $start_index;
		$config['full_tag_open']  = '<div id="pagination">';
		$config['full_tag_close'] = '</div>';
		$ci->pagination->initialize($config);
		return  $ci->pagination->create_links();   
	}
	
	/**
		@Function Name:	 set_flash_message
		@Author Name:	 binesh
		@Date:			 Aug, 20 2013
		@message        | String | message string 
		@type           | String | type of message (success/error/notice)
		@return         |void 
		@Purpose:		add message to session 
	
	*/
	function set_flash_message($message='',$type='success'){
		
		$ci=& get_instance();
		$ci->session->set_userdata( 'flash_message', array(
														$type => $message)
		);
	}
	
	/**
		@Function Name:	 get_flash_message
		@Author Name:	 binesh
		@Date:			 Aug, 20 2013
		@return         |String | message string   
		@Purpose:		get the flash message from the session
	
	*/
	
	function get_flash_message(){
	//show flash message 
		$ci=& get_instance();
		if( $flash_message =$ci->session->userdata('flash_message') ){
			if( is_array( $flash_message ) && count( $flash_message ) > 0 ){
				foreach( $flash_message as $type => $value ){
					echo '<div class=" alert-box '.$type.'">'.$value.'</div>';
				}
			}
			$ci->session->unset_userdata('flash_message');
		}
	}
	
	
	/**
		@Function Name:	get_dropdown_array
		@Author Name:	binesh
		@Date:			Aug, 20 2013
		@table_name     | String | table name 
		@where_condition| String | where condition 
		@order_by       | String | ordery by column
		@order          | String | ASC/DESC
		@field_name1    | String | first field 
		@field_name2    | String | second field 
		@field_name3    | String | third field 
		@empty          | boolean | empty flag
		@empty_text     | array   | empty array
		@return         | array  | array of recorsds
		@Purpose:		return the array or record to be used in dropdowns 
	
	*/
	
	function get_dropdown_array($table_name='',$where_condition='',$order_by='',$order='ASC',$field_name1,$field_name2,$field_name3,$empty=true,$empty_array=array())
	{	
        $result=array();
		if($empty){
			$result = array_merge($result,$empty_array);
		}
		$ci=& get_instance();
        $ci->load->database(); 
		
       if($field_name3!=''){
			$ci->db->select("$field_name1,$field_name2,$field_name3");
		}else{
        $ci->db->select("$field_name1,$field_name2");
		}
		$ci->db->order_by($order_by,$order);
		if($where_condition!='')
		{
			$query = $ci->db->get_where($table_name,$where_condition); 
		}
		else
		{
			$query   = $ci->db->get($table_name);
		}
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $row){
				if($field_name3!='')
					$result[$row->$field_name1]=$row->$field_name2.' '.$row->$field_name3	;
				else
					$result[$row->$field_name1]=$row->$field_name2	;
				
			}
		}
		return $result;
	}
	
	/**
		@Function Name:	 upload_file
		@Author Name:	 ben binesh
		@Date:			 Aug, 22 2013
		@return         | Array /String | array on successful upload ,error string on file upload error 
		@Purpose:		get the flash message from the session
	
	*/
	
	function upload_file($field='',$path='')
    {	
		$ci=& get_instance();
        $config['upload_path'] = $path ;
        $config['allowed_types'] = 'jpeg|jpg|png|gif|tiff|bmp';
        $ci->load->library('upload', $config);
        $ci->upload->initialize($config);

        if ( ! $ci->upload->do_upload($field))
        {
            $error = $ci->upload->display_errors();
            return $error;
        }
        else
        {
            $upload_data =  $ci->upload->data();
            return $upload_data;
        }
    }
	
	/**
		@Function Name:	 get_content
		@Author Name:	 ben binesh
		@table_name	     |String| table name 
		@select	         |String| columns to be select
		@where_condition |String| where condtion 
		@order_by       | String | ordery by column
		@order          | String | ASC/DESC
		@Date:			 Aug, 22 2013
		@return         | Array /String | array on successful upload ,error string on file upload error 
		@Purpose:		get the value form database 
	
	*/
	function get_content($table_name='',$select='*',$where_condition='',$order_by='',$order='ASC',$limit=0)
	{
		$ci=& get_instance();
		$ci->load->database();
		$ci->db->select($select);
		if($order_by!='')
			$ci->db->order_by($order_by,$order);
		if($where_condition!='')
		{
			if($limit)
				$query = $ci->db->get_where($table_name,$where_condition,$limit); 	
			else
				$query = $ci->db->get_where($table_name,$where_condition); 	
			
		}
		else
		{
			if($limit)
				$query = $ci->db->get($table_name,$limit);
			else
				$query = $ci->db->get($table_name);
		}
		return $query->result();
	}
	
	/**
		@Function Name:	 get_single_value
		@Author Name:	 ben binesh
		@table_name	     |String| table name 
		@select	         |String| columns to be select
		@field_name      |String| where condtion 
		@Date:			 Sept, 09 2013
		@return         | Array /String | array on successful upload ,error string on file upload error 
		@Purpose:		get single the value form database 
	
	*/
	
	function get_single_value($table_name,$field_name,$where_condition)
	{
		
		$ci=& get_instance();
		$ci->load->database();   
		$ci->db->select($field_name);
		$query = $ci->db->get_where($table_name,$where_condition);
		if($query->num_rows() >=1)
		{	
			return  $query->row()->$field_name; 
		} 
		else
		{
			 return '';
		}
	}
	
	
	
	/**
		@Function Name:	 upload_file
		@Author Name:	 binesh
		@Date:			 Aug, 22 2013
		@return         | Array /String | array on successful upload ,error string on file upload error 
		@Purpose:		get the flash message from the session
	
	*/
	function get_seo_url($route='',$id=0,$title=''){
		return base_url().$route.'/'.url_title($title,'-',TRUE).'/'.$id;
	}	
	
	
	
	/**
		@Function Name:	 send_mail
		@Author Name:	 binesh
		@Date:			 Aug, 23 2013
		@to_email       | string | email address of receiver  
		@from_name       | string | name  of sender  
		@from_email       | string | email address of sender  
		@subject       | string | subject of email 
		@message       | string | message 
		@return         |boolean 
		@Purpose:		send email 
	
	*/
	
	function send_mail($to_email='',$from_name='',$from_email='',$subject='',$message='',$bcc='',$attachement='')
	{
                
		$ci=& get_instance();
		$ci->load->library('email');
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = SMTP_HOST;
		$config['smtp_port'] = SMTP_PORT;
		$config['smtp_user'] = SMTP_USER;
		$config['smtp_pass'] = SMTP_PASS;
		$config['mailtype'] = 'html';
		$ci->email->initialize($config);
		$ci->email->set_newline("\r\n");
		$ci->email->from($from_email, $from_name);
		if($to_email)
			$ci->email->to($to_email);
		
		if($bcc)
		{
			$ci->email->bcc($bcc);
		}
		
		if($attachement)
			$ci->email->attach($attachement);
		
		$ci->email->subject($subject);
		$ci->email->message($message);
		if(!$ci->email->send())
		{
			return false;
		}
		else
		{
			return true;
		}
        
	}
	
	/**
		@Function Name:	 get_hours_array
		@Author Name:	 binesh
		@Date:			 Aug, 26 2013
		@emtpty         |boolean| emmpty flag 
		@empty_array    |Array | first value to add to result array 
		@return         | Array | array 
		@Purpose:		get the hours array for dropdown 
	
	*/
	
	function get_hours_array( $empty = false , $empty_array = array() ){
		$hours_array = array();
		if( $empty ){
			$hours_array = array_merge( $hours_array , $empty_array );
		}
		
		for( $i = 1; $i <= 12; $i++ ){
			$hours_array[] = $i;
		}
		return $hours_array;
	}
	
	
	/**
		@Function Name:	 get_minute_array
		@Author Name:	 binesh
		@Date:			 Aug, 26 2013
		@emtpty         |boolean| emmpty flag 
		@empty_array    |Array | first value to add to result array 
		@return         | Array | array 
		@Purpose:		get the minutes array for dropdown 
	
	*/
	
	function get_minute_array($empty=false,$empty_array=array()){
		$minute_array = array();
		if( $empty ){
			$minute_array = array_merge( $minute_array , $empty_array );
		}
		
		for( $i = 0; $i <= 55; $i = $i+5 ){
			$minute_array[$i] = $i;
		}
		return $minute_array;
	}	
	
	/**
		@Function Name:	 get_ampm_array
		@Author Name:	 binesh
		@Date:			 Aug, 26 2013
		@emtpty         |boolean| empty flag 
		@empty_array    |Array | first value to add to result array 
		@return         | Array | array 
		@Purpose:		get the AM PM  array for dropdown 
	
	*/
	
	function get_ampm_array( $empty = false , $empty_array = array() ){
		$ampm_array = array();
		if( $empty ){
			$ampm_array = array_merge( $ampm_array , $empty_array );
		}
		
		$ampm_array['am']='am';
		$ampm_array['pm']='pm';
		
		return $ampm_array;
	}
	
	/**
		@Function Name:	 get_years_array
		@Author Name:	 binesh
		@Date:			Sept, 05 2013
		@start          | integer| Start year
		@end            | integer| end year
		@empty          |boolean| empty flag 
		@empty_array    |Array | first value to add to result array 
		@return         | Array | array 
		@Purpose:		get the years array for dropdown 
	
	*/
	
	function get_years_array($start=1960,$end = 2000 , $empty = false , $empty_array = array() ){
		$years_array = array();
		if( $empty ){
			$years_array = array_merge( $years_array , $empty_array );
		}
		
		for( $i = $start; $i <= $end; $i++ ){
			$years_array[$i] = $i;
		}
		return $years_array;
	}
	
	
	/**
		@Function Name:	 get_months_array
		@Author Name:	 binesh
		@Date:			 Sept, 05 2013
		@empty          |boolean| empty flag 
		@empty_array    |Array | first value to add to result array 
		@return         | Array | array 
		@Purpose:		get the months array for dropdown 
	
	*/
	
	function get_months_array( $empty = false , $empty_array = array() ){
		$months_array = array();
		$months = array(
                1 => 'Jan',
                2 => 'Feb',
                3 => 'Mar',
                4 => 'Apr',
                5 => 'May',
                6 => 'Jun',
                7 => 'Jul',
                8 => 'Aug',
                9 => 'Sept',
                10 => 'Oct',
                11 => 'Nov',
                12 => 'Dec'
		);
		if( $empty ){
			$months_array = array_merge( $months_array , $empty_array );
		}
		foreach( $months as $key=>$value ){
			$months_array[$key] = $value;
		}
		return $months_array;
	}
	
	/**
		@Function Name:	 get_days_array
		@Author Name:	 binesh
		@Date:			Sept, 06 2013
		@empty          |boolean| empty flag 
		@empty_array    |Array | first value to add to result array 
		@return         | Array | array 
		@Purpose:		get the days array for dropdown 
	
	*/
	
	function get_days_array($empty = false , $empty_array = array() ){
		$days_array = array();
		if( $empty ){
			$days_array = array_merge( $days_array , $empty_array );
		}
		
		for( $i = 1; $i <= 31; $i++ ){
			$days_array[$i] = $i;
		}
		return $days_array;
	}
	
	/**
		@Function Name:	 get_numbers_array
		@Author Name:	 binesh
		@Date:			Sept, 05 2013
		@start          | integer| Start 
		@end            | integer| end 
		@empty          |boolean| empty flag 
		@empty_array    |Array | first value to add to result array 
		@return         | Array | array 
		@Purpose:		get the numbers array for dropdown 
	
	*/
	
	function get_numbers_array($start=0,$end = 10 , $empty = false , $empty_array = array() ){
		$numbers_array = array();
		if( $empty ){
			$numbers_array = array_merge( $numbers_array , $empty_array );
		}
		
		for( $i = $start; $i <= $end; $i++ ){
			$numbers_array[$i] = $i;
		}
		return $numbers_array;
	}
	
	/**
		@Function Name:	 format_date
		@Author Name:	 binesh
		@Date:			 Aug, 26 2013
		@format         |string|  date/time format
		@return         | string | 
		@Purpose:		format date and time to specified format 
	
	*/
	function format_date($date='',$format='m/d/Y'){
		if($date =='' || $date=='0000-00-00' || $date=='0000-00-00 00:00:00') {
			return '';
		}
		return date($format,strtotime($date));
	}	
	
	
	/**
		@Function Name:	 get_current_url
		@Author Name:	 binesh
		@Date:			 Aug, 26 2013
		@return         | string | 
		@Purpose:		return the current rquested url 
	
	*/
	
	function get_current_url(){
		$ci=& get_instance();
		$query = $_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : '';
		return $ci->config->site_url().$ci->uri->uri_string(). $query; 

	}
	
	/**
		@Function Name:	 is_logged_in
		@Author Name:	 binesh
		@Date:			 Aug, 26 2013
		@return         | boolean | 
		@Purpose:		check user is logged in or not 
	
	*/
	function is_logged_in(){
		$ci=& get_instance();
		$ci->load->model('login_model');
		return $ci->login_model->is_logged_in();
	}
	
	
	/**
		@Function Name:	 pagination_summary
		@Author Name:	 binesh
		@Date:			 Aug, 26 2013
		@return         | string  | 
		@Purpose:		return the pagination summary as  1	to 10 of 1294"
	
	*/
	
	function pagination_summary(){
		$output='';
		$ci=& get_instance();
		$show_summary=true;
		// If our item count or per-page total is zero there is no need to continue.
		if ($ci->pagination->total_rows == 0 OR $ci->pagination->per_page == 0)
		{
			$show_summary=false;
		}
		
		// Calculate the total number of pages
		$num_pages = ceil($ci->pagination->total_rows / $ci->pagination->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages == 1)
		{
			$show_summary=false;
		}
				
		if($show_summary): 
			$output .= (($ci->pagination->cur_page-1)*$ci->pagination->per_page)+1; 
			$output .=' to ';
			 $result_value = $ci->pagination->cur_page*$ci->pagination->per_page; 
				if($result_value > $ci->pagination->total_rows ){
					$result_value = $ci->pagination->total_rows ;
				}
				$output .=$result_value;		
			
			$output .=' of ';
		else: 
		
		endif; 
		$output .=' Total '	;
		$output .= $ci->pagination->total_rows; 
		$output .=' Records '	;
		return $output;
	}
	
	/**
		@Function Name:	 get_excerpt
		@Author Name:	 binesh
		@Date:			 Aug, 30 2013
		@text           | String | input string 
		@length         | Numeric | length 
		@return         | string   
		@Purpose:	      trim the string to the specified length 
	
	*/
	
	
	function get_excerpt($str='',$limit=20,$end_char = '&#8230;'){
		$str = strip_tags($str);
		if (trim($str) == '')
		{
			return $str;
		}

		preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

		if (strlen($str) == strlen($matches[0]))
		{
			$end_char = '';
		}

		return rtrim($matches[0]).$end_char;
		
	}
	
	/**
		@Function Name:	 get_event_image
		@Author Name:	 binesh
		@Date:			 Aug, 30 2013
		@text           | String | input string 
		@return         | String/NULL  | image on success ,NULL on failure
		@Purpose:	     get the first image from the page content
	
	*/
	
	function get_event_image($text) {
		$first_img = '';
		ob_start();
		ob_end_clean();
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $text, $matches);
		
		if(isset($matches [0] [0])){
			$first_img = $matches [0] [0];
		}
		if(empty($first_img)){ //Defines a default image
			$first_img = NULL;
		}
		return $first_img;
	}
	
	/**
		@Function Name:	 get_report_type_array
		@Author Name:	 binesh
		@Date:			 Sept ,03 2013
		@return         | array
		@Purpose:	     array of report type 
	
	*/
	
	function get_report_type_array(){
		$report_type_array=array();
		$report_type_array[RPT_CSV] = RPT_CSV;
		$report_type_array[RPT_PDF] = RPT_PDF;
		return $report_type_array;
	}
	
	/**
		@Function Name:	 get_yesNo_array
		@Author Name:	 binesh
		@Date:			 Sept ,05 2013
		@return         | array
		@Purpose:	     array of report type 
	
	*/
	
	function get_yesNo_array(){
		$yesNoe_array=array();
		$yesNo_array[STATUS_YES] = 'Yes';
		$yesNo_array[STATUS_NO] = 'No';
		return $yesNo_array;
	}
	
	/**
		@Function Name:	show_yesNo_text
		@Author Name:	binesh
		@Date:			Sept, 05 2013
		@yesNo  | numeric| yesno value 
		@return  string
		@Purpose:		return access level string 
	
	*/
	function show_yesNo_text($yesNo = 0,$reverse=false){
		$yesNo_array =get_yesNo_array();
		if($reverse){
			///tooggle 1,0 
			$yesNo=($yesNo+1)%2;
		}
		return (isset($yesNo_array[$yesNo]))?$yesNo_array[$yesNo]:'';
	}
	
	/**
		@Function Name:	 get_grade_level_array
		@Author Name:	 binesh
		@Date:			 Sept ,05 2013
		@return         | array
		@Purpose:	     array of report type 
	
	*/
	
	function get_grade_level_array($empty=true,$empty_array=array(),$first_element=''){
		$grade_level_array = array();
                if($first_element=='')
                    $grade_level_array[''] = 'Not Applicable';
		if( $empty ){
			$grade_level_array = array_merge( $grade_level_array , $empty_array );
		}
		$grade_level_array['K']='K';
		for( $i=1; $i<=12;$i++ ){
			$grade_level_array[$i] = $i;
		}
		
		return $grade_level_array;
	}
	
	
	
	/**
		@Function Name:	crop_image
		@Author Name:	binesh
		@Date:			Sept, 06 2013
		@image          | string| image with full path 
		@height          | numeric| image height
		@width          | numeric| image width
		@zc          | numeric| zoom crop flag
		@return  string
		@Purpose:		return resize image using the timthumb
	
	*/
	
	function crop_image($image='',$height=100,$width=100,$zc=1){
		if($images=''){
			return "";
		}
		return base_url() ."scripts/timthumb.php?src=$image&h=$height&w=$width&zc=$zc";
   }
   
	/**
		@Function Name:	set_response
		@Author Name:	binesh
		@Date:			Sept, 11 2013
		@error          | boolean | error flag 
		@error_message  | string| error message 
		@response       | string| response
		@return         | string|  json string 
		@Purpose:		return the json string on ajax response 
	
	*/
   
	function set_response($error=TRUE,$error_message=NULL,$response=NULL){
		$data = array(
			'error'=>$error,
			'error_message'=>$error_message,
			'response'=>$response,
		);
	echo json_encode($data);
   }
   
   /**
		@Function Name:	get_hearabout_array
		@Author Name:	binesh
		@Date:			Sept, 12 2013
		@yesNo  | numeric| hear about options array
		@return  array
		@Purpose:		 return hear about options array for subscribe page 
	
	*/
	function get_hearabout_array(){
		
		$hearabout_array = array(
                0 => 'At a workshop',
                1 => 'Word of mouth',
                2 => 'E-mail/Announcements',
				5 => 'Mailer/flyer',
				6 => 'Social media (Facebook, Twitter, etc.)',
                3 => 'PETE & C',
				7 => 'Other conference',
				4 => 'Other... Please specify.',
        );
		
		
		return $hearabout_array;
	}
	
	/**
		@Function Name:	show_hearabout_text
		@Author Name:	binesh
		@Date:			Sept, 12 2013
		@hearabout  | numeric| hearabout value 
		@return  string
		@Purpose:	return hear about string 
	
	*/
	function show_hearabout_text($hearabout = 0){
		$hearbout_array =get_hearabout_array();
		return (isset($hearbout_array[$hearabout]))?$hearbout_array[$hearabout]:'';
	}
   
   
	/**
	  @Function Name: is_checkout_logged_in
	  @Author Name: Janet Rajani
	  @Date:   Sep, 16 2013 
	  @Purpose:  check if the current user is logged in or not with his email 
	 
	*/
	function is_checkout_logged_in(){
		$ci=& get_instance();
		$ci->load->model('checkout_model');
		return $ci->checkout_model->checkout_logged_in();
	}
	
	/**
		@Function Name:	encrypt_password
		@Author Name:	ben binesh
		@Date:			Sept, 24 2013
		@password      | string| password string 
		@return  string
		@Purpose:	return encrypted passwod string 
	
	*/
	
	function encrypt_password($password=0){
		$stored_seed = substr(md5(time()),0,8);
		$password=  md5($stored_seed.$password).$stored_seed;
		return $password;
	}
	
	/**
		@Function Name:	validate_password
		@Author Name:	ben binesh
		@Date:			Sept, 24 2013
		@password      | string| password string 
		@stored_password    | string| strored password string 
		@return  boolean 
		@Purpose:	validate the passwrod string 
	
	*/
	function validate_password($password='',$stored_password=''){
		$stored_seed = substr($stored_password,-8);
		if (md5($stored_seed.$password).$stored_seed == $stored_password) {
			return TRUE;
		}else{
			return FALSE;
		}

	} 
	
	/**
		@Function Name :	get_receipt_url
		@Author Name:	ben binesh
		@Date:			Oct, 04 2013
		@course_id     | numeric | course id 
		@email    | string| user email 
		@return  boolean 
		@Purpose:	get transaction receipt url  
	
	*/
	
	function get_receipt_url($course_id=0,$email=''){
		
		$transaction_id = get_single_value('pp_transactions','ppID',"payer_email = '".$email."' AND item_number1=".$course_id." AND product_type= ".PRODUCT_TYPE_COURSE.' AND show_receipt = 1');
		if($transaction_id){
			return base_url().'user/view_receipt/'.$transaction_id;
		}
		return false;


		
	}
	
	
	/**
		@Function Name :	is_show_assignment 
		@Author Name:	ben binesh
		@Date:			Oct, 04 2013
		@assignment     | object  | assignment object 
		@course     | object  | course object 
		@return  boolean 
		@Purpose:	to check wether to show the current assignment to user or not  of not 
	
	*/
	function is_show_assignment($assignment=Object,$course=Object,$user=object){
		
		if(ASGN_IPAD_CONFIGURATION == $assignment->assignType){
			//check for credit and non-credit
			return check_credit($user->act48,$course->csID);
		}		
		
		return true;
	}	
	
	
	/**
		@Function Name : get_assignment_url 
		@Author Name:	 ben binesh
		@Date:			 Oct, 04 2013
		@course_id      | numeric | course id 
		@email          | string| user email 
		@return         string 
		@Purpose:	return the assignment url according to the assignment type 
	
	*/
	
	function get_assignment_url( $assignment=object, $user_id=0 ){
		$ci=& get_instance();
		
		switch($assignment->assignType)
		{
			case ASGN_IPAD_CONFIGURATION:
				// if assignment is ipad type
				// return the link of choose ipad page 
				//check if ipad already selected or not 
				if($assignment->ledger){
					//assignment completed 
					//get the ipad info 
					$ci->load->model('order_model');
					$order=$ci->order_model->get_single_record(0,$user_id);
					$output='';
					if(!empty($order))
					{
						$output.= $order->oiProdName.' '.$order->oiProdVariantValue1;
						//if order upgrade is selected and order is completed 
						if( $order->upgrade_info && ORDER_COMPLETED==$order->orderStatus ){
							$output.=' - '.$order->upgrade_info;
						}
					}
					else
					{
						$output =$assignment->assignTitle;
					}
					
					
					return $output;
				}
				return anchor(base_url().'ipad/index/'.$assignment->assignID,$assignment->assignTitle);
			
			break;
			
			case ASGN_QUESTIONNAIRE:
				if($assignment->ledger)
				{
					return anchor('questionnaire/index/'.$assignment->assignID.'/'.$assignment->assignQuestionnaire,$assignment->assignTitle);
				}
				else
				{
					//check if assignment is activated or not 
					$difference = date_difference_days(date('Y-m-d'),$assignment->assignActiveDate);
					if( 0 >= $difference){
						return anchor('questionnaire/users_survey_answer/'.$assignment->assignID.'/'.$user_id,$assignment->assignTitle);
					}else{
						return  $assignment->assignTitle;
					}	
					
				}	
				
			break;	
			default:
				return anchor(get_seo_url('assignment/view',$assignment->assignID,$assignment->assignTitle),$assignment->assignTitle);
			break;
			
		}
		
		
		
	}
	
	
	/**
		@Function Name : date_difference_days 
		@Author Name:	 ben binesh
		@Date:			 Oct, 14 2013
		@date_first     | String | first  date 
		@date_second    | String | second date
		@return         numeric 
		@Purpose:	return the date difference in days 
	
	*/
	
	function date_difference_days($date_first='',$date_second=''){
		$ts1 = strtotime($date_first);
		$ts2 = strtotime($date_second);
		$seconds_diff = $ts2 - $ts1;
		return floor($seconds_diff/3600/24);
		
	}
	
	
/**
		@Function Name:	traverse_array
		@Author Name:	ben binesh
		@Date:			Oct, 24 2013

		@result      | array|array to be traverse 
		@return  boolean /string 
		@Purpose:	Return the value within a array if any
	
	*/
	function traverse_array($result=array())
    {
		    $ret_value=false;
            if(empty($result)){
                return  $ret_value;
            }
            foreach($result as $key=>$value)
            {
                if($value!=='')
                {
                   return $value;
				}
            }
             return $ret_value;
	}
	
	
	/**
		@Function Name : is_allowed 
		@Author Name:	 ben binesh
		@Date:			 Nov, 4  2013
		@permission      |string / numeric | permission value 
		@return         boolean 
		@Purpose:	   check the permission value against the user's given permissions 
		               return true is allowed otherwise false 
	
	*/
	
	function is_allowed($permission='')
	{
		if( $permission == '' ){
			return FALSE;
		}
		$ci=& get_instance();
		
		//get user permission from session 
		//user permission are in key values pair 
		$user_permission = $ci->session->userdata('permission');
		if( empty( $user_permission ) )
		{
			return FALSE;
		}
		//if permission given as numeric key check the keys 
		if(is_numeric( $permission ) )
		{
			$user_permission=array_keys($user_permission);
		}
		else
		{	
			//if permission given as url string as /sdf/sdfsd/sdfsd 
			$user_permission= array_values( $user_permission ); 
			 	
		}
		//check the pemission against user's given permission
		if( in_array( $permission, $user_permission  ) )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		} 
		
	}
	/**
		@Function Name : sendEmailByMandrill 
		@Author Name:	 Alan Anil
		@Date:			 Dec, 4  2013 
		@return         NA
		@Purpose:	   Send emails from mandrill 
	
	*/
	function sendEmailByMandrill($params)
	{ 
		$ci=& get_instance();
		$ci->load->library('Mandrill');
		//include "/application/libraries/Mandrill.php";
		$mandrill = new Mandrill('_TDULM0hZF3ov5EezjNg1Q');
		$result = $mandrill->messages->send($params);
		print_r($result);
		
	}
	
	/**
		@Function Name : get_text
		@Author Name:	 Ben Binesh
		@Date:			 Dec, 18  2013 
		@return         String
		@Purpose:	   get info text 
	
	*/
	
	function get_text($id)
	{
		$output='';
		$ci=& get_instance();
		$ci->load->model('text_model');
		$text = $ci->text_model->get_single_record($id,STATUS_PUBLISH);
		if(!empty($text)){
			$output .= '<div class="info-text-container">';
			$output .= '<div class="info_text" id="info_text_'.$text->ctID.'" >';
			$output .= $text->ctText;
			$output .= '</div>';
			//add edit link 
			if(is_allowed('edu_admin/text/index')):
			$output .= '<div class="edit_link">';
			$output .= anchor('edu_admin/text/update/'.$text->ctID.'?redirect='.urlencode(get_current_url()),'EDIT');
			$output .= '</div>';
			endif;
			$output .= '</div>';
				
		}
		echo $output;
	}
	
	/**
		@Function Name:	format_phone_number
		@Author Name:	ben binesh
		@Date:			Dec 23 2013
		@phone_name     | String | phone number 
		@return         | array  | array of records
		@Purpose:		return formated phone number string as 123-456-789
	
	*/
	
	function format_phone_number($phone_number='')
	{			
		if(''==$phone_number)
			return '';
		
		//sanitize the phone number 
		$phone_number=str_replace(array('(',')','+','-',' ','.'),'',$phone_number);
		
		if(  preg_match( '/^(\d{3})(\d{3})(\d{4})$/', $phone_number,  $matches ) )
		{
			$result = $matches[1] . '-' .$matches[2] . '-' . $matches[3];
			return $result;
		}
	}
	
	
	
	/**
		@Function Name:	get_dropdown_array
		@Author Name:	binesh
		@Date:			Aug, 20 2013
		@table_name     | String | table name 
		@where_condition| String | where condition 
		@order_by       | String | ordery by column
		@order          | String | ASC/DESC
		@field_name1    | String | first field 
		@field_name2    | String | second field 
		@field_name3    | String | third field 
		@empty          | boolean | empty flag
		@empty_text     | array   | empty array
		@return         | array  | array of recorsds
		@Purpose:		return the array or record to be used in dropdowns 
	
	*/
	
	function get_course_dropdown($table_name='',$where_condition='',$order_by='',$order='ASC',$select='*',$empty=true,$empty_array=array(),$include_course=FALSE)
	{	
        $result=array();
		if($empty){
			$result = array_merge($result,$empty_array);
		}
		$ci=& get_instance();
        $ci->load->database();

		if($include_course){
			$ci->db->join('course_definitions','csCourseDefinitionId = course_definitions.cdID','LEFT');
		}		
		$ci->db->select($select);
		
		$ci->db->order_by($order_by,$order);
		if($where_condition!='')
		{
			$query = $ci->db->get_where($table_name,$where_condition); 
		}
		else
		{
			$query   = $ci->db->get($table_name);
		}
		
		if($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$course_location=$row->csCity.', '.$row->csState;
				if(COURSE_ONLINE==$row->csCourseType)
					$course_location='Online';
					if($include_course){
						$result[$row->csID]=$row->cdCourseTitle.'('.$course_location.'-'.format_date($row->csStartDate,DATE_FORMAT).')';
					}else{
						$result[$row->csID]=$course_location.'-'.format_date($row->csStartDate,DATE_FORMAT);
					}
					
				
				
			}
		}
		return $result;
	}
	
	/**
		@Function Name:	get_admin_emails
		@Author Name:	ben binesh
		@Date:			Dec 30 2013
		@Purpose:		return email address of eduspire admins 
	
	*/
	
	
	function get_admin_emails()
	{	
		$emails = array();
		$ci=& get_instance();
        $ci->load->database(); 
		$ci->db->select('email');
		$ci->db->where('receiveSystemEmails',STATUS_YES);
		$query   = $ci->db->get('users');
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $row){
				$emails[]=$row->email;
			}
		}
		return $emails;		
	}
	
	/**
		@Function Name:	show_credit_status
		@Author Name:	ben binesh
		@Date:			Jan 07 2014
		@act48:			|string|non credit course CSV values
		@course_id     	|numeric| course id 
		@reverse        |boolean| reverse status
		@Purpose:		return email address of eduspire admins 
	
	*/
	function show_credit_status($act48=0,$course_id=0,$reverse=FALSE){
		$status='No';
		if($act48)
		{
			$course_ids=explode(',',$act48);
			if(in_array($course_id,$course_ids)){
				$status= 'Yes';
			}
			else
			{
				$status= 'No';
			}
		}
		else
		{
			$status='No';
		}
		if($reverse){
			if('Yes'==$status)
				$status='No';
			else
				$status='Yes';
		}
		return $status;
		
	}
	/**
		@Function Name:	check_credit
		@Author Name:	ben binesh
		@Date:			Jan 07 2014
		@act48:			|string|non credit course CSV values
		@course_id     	|numeric| course id 
		@Purpose:		return email address of eduspire admins 
	
	*/
	
	function check_credit($act48=0,$course_id=0)
	{
		if($act48)
		{
			$course_ids=explode(',',$act48);
			if(in_array($course_id,$course_ids)){
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		else
		{
			return TRUE;
		}
	}
	
	