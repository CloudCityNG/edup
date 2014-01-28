<?php
/**
@Page/Module Name/Class: 		course_definition_model.php
@Author Name:			 		binesh
@Date:					 		Aug, 19 2013
@Purpose:		        		Contain all data management functions for the course generes
@Table referred:				course_genres
@Table updated:					course_genres
@Most Important Related Files	NIL
 */

class data_model extends CI_Model {
	
	
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		//$this->odb = $this->load->database('old', TRUE);
		$this->load->model('course_schedule_model');
		
	}
	
	
	function csv(){
		$query = $this->db->get('users',10);
		return $query;
	}
	
	function import(){
		die();
		$this->odb->select('courses.*,inventory.*');
		$this->odb->join('inventory','courses.coID = inventory.invID');
		//$this->odb->where('coID',23);
		$query = $this->odb->get('courses');
		//echo $this->odb->last_query();
		$results =$query->result();
		//print_r($results);
		
		if($results){
			foreach($results as $rs){
				if($data=$this->course_schedule_model->get_single_record($rs->coID)){
					continue;
				}
				$course_type=0;	
				if($rs->coLocation=='Online' || $rs->coLocation=='Fall 2012' || $rs->coLocation=='Fall 2013'){
					$course_type =1;	
				}
				//get course genere
				$catID=get_single_value('course_definitions','cdGenre','cdID = '.$rs->invSubcatID);
				//insert the couses 
				$insert_data=array(
					'csID'=>$rs->coID,
					'csPublish'=>$rs->invPublish,
					'csGenreId'=>$catID,
					'csCourseDefinitionId'=>$rs->invSubcatID,
					'csCourseType'=>$course_type,
					'csLocation'=>$rs->coLocation,
					'csAddress'=>$rs->coAddress,
					'csCity'=>$rs->coCity,
					'csState'=>$rs->coState,
					'csZIP'=>$rs->coZIP,
					'csIURegion'=>'',
					'csRegistrationStartDate'=>$rs->coRegistrationDateStart,
					'csRegistrationEndDate'=>$rs->coRegistrationDateEnd,
					'csPaymentStartDate'=>$rs->coPaymentDateStart,
					'csMaximumEnrollees'=>$rs->invQuantity,
					'csPrice'=>$rs->invPrice1,
					'csNonCreditPrice'=>$rs->invPrice2,
					'csNonCreditComment'=>'',
				);
				
				$course_schedule_id = $this->course_schedule_model->insert($insert_data);
				//insert dates 
				
				if($rs->coLocation=='Fall 2013' || $rs->coLocation=='Fall 2012'){
					$date_array=array(
						'csdCourseScheduleId'=>$course_schedule_id,
						'csdStartDate'=>$rs->coDateStart,
						'csdEndDate'=>$rs->coDateEnd,
						
					);
					$this->course_schedule_model->insert_date($date_array);
				}
				else
				{
				
					if($rs->coLocation=='Online'){
						$date_array=array(
							'csdCourseScheduleId'=>$course_schedule_id,
							'csdStartDate'=>$rs->coDate1,
							'csdEndDate'=>$rs->coDate2,
							
						);	
						$this->course_schedule_model->insert_date($date_array);
					}else{
						$date_array=array(
							'csdCourseScheduleId'=>$course_schedule_id,
							'csdStartDate'=>$rs->coDate1,
							'csdStartTime'=>$rs->coTimeBegin1,
							'csdEndTime'=>$rs->coTimeEnd1,
							
						);
						$this->course_schedule_model->insert_date($date_array);	
						$date_array=array(
							'csdCourseScheduleId'=>$course_schedule_id,
							'csdStartDate'=>$rs->coDate2,
							'csdStartTime'=>$rs->coTimeBegin2,
							'csdEndTime'=>$rs->coTimeEnd2,
							
						);
						$this->course_schedule_model->insert_date($date_array);	

						$date_array=array(
							'csdCourseScheduleId'=>$course_schedule_id,
							'csdStartDate'=>$rs->coDate3,
							'csdStartTime'=>$rs->coTimeBegin3,
							'csdEndTime'=>$rs->coTimeEnd3,
							
						);
						$this->course_schedule_model->insert_date($date_array);

						$date_array=array(
							'csdCourseScheduleId'=>$course_schedule_id,
							'csdStartDate'=>$rs->coDate4,
							'csdStartTime'=>$rs->coTimeBegin4,
							'csdEndTime'=>$rs->coTimeEnd4,
							
						);
						$this->course_schedule_model->insert_date($date_array);	
						
						$date_array=array(
							'csdCourseScheduleId'=>$course_schedule_id,
							'csdStartDate'=>$rs->coDate4,
							'csdStartTime'=>$rs->coTimeBegin4,
							'csdEndTime'=>$rs->coTimeEnd4,
							
						);
						$this->course_schedule_model->insert_date($date_array);	
						
					}
				}
				
				
				
				
				
				sleep(2);	
			}
			
		}
	}
	
	
	function user(){
		die();
		$this->load->model('user_model');
		$this->odb->select('id,userName,firstName,lastName,memberships,membershipLastUsed');
		$this->odb->where('id < ',1500);
		//$this->odb->where('id ',1);
		$query = $this->odb->get('fts_users');
		//echo $this->odb->last_query();
		$results =$query->result();
		//print_R($results);
		//die();
		if($results){
			foreach($results as $rs){
				$update_array=array(
					'memberships'=>$rs->memberships,
					'membershipLastUsed'=>$rs->membershipLastUsed,
					
				);
				$this->user_model->update($rs->id,$update_array);	
				
		}
		}
		
	}
	
	function adjust_instructor(){
		//die();
		/*
		$this->db->select('cdID,cdFacilitator');
		$query = $this->db->get('course_definitions');
		$results=$query->result();
		//print_R($results);
		*/
		
		$this->db->select('id,userName,firstName,lastName,memberships,membershipLastUsed');
		$this->db->where('accessLevel',INSTRUCTOR);
		
		$query = $this->db->get('users');
		$results=$query->result();
		
		
		$this->load->model('course_schedule_model');
		foreach($results as $result){
			
			if($result->memberships){
			$memebership = $result->memberships;
			$memebership = explode(',',$memebership);
			foreach($memebership as $mem){
				if(is_numeric($mem)){
					
					if(!$count=$this->db->where('ciCsID',$mem)->where('ciUID',$result->id)->count_all_results('course_instructor')){
					$insert_array=array(
						'ciCsID'=>$mem,
						'ciUID'=>$result->id,
						
					);
					
					$this->course_schedule_model->insert_instructor($insert_array);
				}
				}
			}
			}
			
		}
		
	}
	
	
	function adjust_course_dates(){
		die();
		$query=$this->db->query('SELECT `csID` FROM `course_schedule');
		$result=$query->result();
		foreach($result as $row){
		
		$course_id=$row->csID;
		$course = $this->course_schedule_model->get_course_detail($course_id);
		
		
		//show course start date and end date 
		if(count($course->course_dates)>0){
			$course_dates=$course->course_dates;
			if(COURSE_OFFLINE == $course->csCourseType){
				$course_start_date = $course_dates[0]->csdStartDate;
				$course_end_date   = $course_dates[count($course_dates)-1]->csdStartDate;
				
			}else{
				$course_start_date = $course_dates[0]->csdStartDate;
				$course_end_date   = $course_dates[0]->csdEndDate;
			} 
			
		
			
			$this->course_schedule_model->update($course_id,array(
				'csStartDate'=>$course_start_date,
				'csEndDate'=>$course_end_date,
			));
			
		}
		}
	}
	
	
	function adjust_course(){
		die();
		$query=$this->db->query('select csID, cdGenre, csCourseDefinitionId from course_schedule join course_definitions on csCourseDefinitionId=cdID');
		$result=$query->result();
		foreach($result as $row){
			$update_array=array(
				'csGenreId'=>$row->cdGenre,
			);
			$this->course_schedule_model->update($row->csID,$update_array);
			
		}
	}
	
	
	function important_queries(){
		//adjust product type in orders items table 
		//update the product types 	to course 
			$sql=" update orders_items set oiProdType=1  where `oiProdName` is NULL ";
			$sql=" update orders_items set oiProdType=2  where `oiProdID` =182 ";
		//end 
		
		//Adjust the product type on orders table 
		$sql="SELECT `o`.`orderID`,`o`.`orderNumber`,`o`.`orderStatus`,`o`.`orderCustID`,`o`.`orderName`,`o`.`orderEmail`,`oi`.`oiID`,`oi`.`oiProdName`,`oi`.`oiProdID`,`oi`.`oiProdPrice`,`oi`.`oiProdVariantValue1`,oi.oiProdType
		FROM (`orders` o)
		JOIN `orders_items` oi ON `o`.`orderNumber` = `oi`.`oiorderNumber`
		GROUP BY `o`.`orderID`
		ORDER BY `o`.`orderID` DESC";
		
	
	}
	
	
	function adjust_product_type(){
		die();
		$sql="SELECT `o`.`orderID`,`o`.`orderNumber`,`o`.`orderStatus`,`o`.`orderCustID`,`o`.`orderName`,`o`.`orderEmail`,`oi`.`oiID`,`oi`.`oiProdName`,`oi`.`oiProdID`,`oi`.`oiProdPrice`,`oi`.`oiProdVariantValue1`,oi.oiProdType
		FROM (`orders` o)
		JOIN `orders_items` oi ON `o`.`orderNumber` = `oi`.`oiorderNumber`
		GROUP BY `o`.`orderID`
		ORDER BY `o`.`orderID` DESC ";
		$query = $this->db->query($sql);
		$results=$query->result();
		foreach($results as $result){
			$this->db->where('orderID',$result->orderID);
			$this->db->update('orders',array(
				'orderProductType'=>$result->oiProdType,
			));
			
		}
	}
	
	
	
	
	
}
//end of file 	