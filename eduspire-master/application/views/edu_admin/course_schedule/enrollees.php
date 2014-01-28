<?php 
/**
@Page/Module Name/Class: 		enrollees.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display course enrollees details
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 //Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  
  RF1      Alan Anil           Nov 20 2013     Normal          show assignments final grades and link to show grades details.
//---------------------------------------------------------------------------------------- 
 */
?>
<script>
jQuery(document).ready(function($) {
	$(document).on('click','.send-activation',function(){
		alink=jQuery(this).attr('href');
		edu_popup_msg(jQuery(this).attr('popup-text'));
		edu_show_popup();
		$(document).on('click','#ok-btn',function(){
			edu_close_popup();
			location.href=alink;
		});
	
		return false;
			
	});
	
	$(".fancybox").click(function() {
		$.fancybox.open({
			href : jQuery(this).attr('href'),
			type : 'iframe',
			padding : 5
		});
		return false;
	});
});
				
</script>	
<?php if(isset($course) && !empty($course)): ?>
<div class="adminTitle"><h1>Enrollees: <?php echo $course->cdCourseID; ?>:<?php echo $course->cdCourseTitle; ?> 
<?php echo  $course->csCity ?>,  <?php echo $course->csState; ?>
<div><?php echo format_date($course->csStartDate,DATE_FORMAT); ?>
		-
	<?php echo format_date($course->csEndDate,DATE_FORMAT); ?>
</div></h1></div>
<?php endif; ?>

<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
	<form name="search-form" action="<?php echo base_url().'edu_admin/course_schedule/enrollees'; ?>">
		<ul class="manageContent">
		<input type="hidden" name="course_id" value="<?php echo $this->input->get_post('course_id') ?>"/>
		<li>
		<label>Search User:</label><input type="text" name="name" value="<?php echo $name; ?>"/></li>
		
		
		</ul>
		<div class="formButton">
		<input type="submit" class="submit" value="Search"/>
		<?php echo anchor('edu_admin/course_schedule/enrollees?course_id='.$this->input->get_post('course_id'),'Reset','class="submit"'); ?> 
		
		
		<input type="submit" name="export" class="submit tooltip" title="Reports are also filtered" value="Export To Csv"  />
		<input type="submit" name="grade_export" class="submit tooltip" title="Reports are also filtered" value="Current Grade Export" />
		
		<?php echo anchor('courses/email_enrollee/'.$course->csID,'Email All','class="submit fancybox"'); ?> 
		
		
		</div>
		
	</form>	
</div>
</div>
<div class="result_container">
		<div class="addRecord"><?php echo anchor('edu_admin/course_schedule/index/','Back','class="button"'); ?> 
	<?php echo anchor('edu_admin/user/member/?course_id='.$course->csID,'Add Member','class="button btn-warning"'); ?>
	</div>
	
</div>
		<?php if(isset($results) && count($results)>0): 
			$send_activation=false;
		?>
		
		<form method="post" name="grid-form">
		<input type="hidden" name="mass_action" value="1"/>
		
		<table class="table striped" cellspacing="0" id="grid" width="100%">
			<tr>
				<td colspan="8" class="summary">
				<div class="pagnination_summary"><?php echo $num_records ?> Enrollees<div>
				</td>
			</tr>	
			<tr>
				<th>Name</th>
				<th>School District</th>
				<th>Ipad</th>
				<th>Ipad Options</th>
				<th>Non-Credit</th>
				<th>Grade</th>
				<th>Last Login </th>
				<th>Action</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td><?php echo $result->lastName; ?> <?php echo $result->firstName; ?> (<?php echo $result->userName; ?>)<br/>
			<?php  echo $result->email; ?><br/>
			<?php  echo $result->phone; ?>
			<div>
			<?php  //check for activation links  
				if( '' == $result->userName ){
					$send_activation=true;
					if( ACCOUNT_INACTIVE == $result->activationFlag){
						//send activation link 
						$label='Unactivated: Send Invitation';
					}else{
						// resend activation link 
						$label='Pending: Resend Invitation';
						
					}
					echo '<input type="hidden" name="ids[]" value="'.$result->id.'"/>';
					echo anchor('edu_admin/user/send_activation/'.$result->id.'?ref=enrollees&course_id='.$this->input->get_post('course_id'),$label,'class="editButton send-activation" popup-text="Do you want to send activation link to '.$result->firstName.' '.$result->lastName.'"');
					if($result->activationFlag > 0){
						echo '<br/>( Sent '.date(DATE_FORMAT,$result->activationFlag).')';
					}
				}

			?>
			</div>
			</td>
			<td>
			<?php
				if(is_numeric($result->districtAffiliation)){
					echo get_single_value('district','disName','disID = '.$result->districtAffiliation) ;
				}else{
					 echo $result->districtAffiliation; 
				}
			?>
			</td>
			
			<td>
				
				<?php
					
				//checking before showing the ipad info 
				//check if this have a ipad assignment ledger entry 
				$ledger_id = get_single_value('assignment_ledger','alID',"alAssignType = '".ASGN_IPAD_CONFIGURATION."' AND alCnfID=".$course->csID." AND alUserID= ".$result->id);
				if($ledger_id):
				?>
					<?php if($result->oiProdName): ?>
					<?php echo $result->oiProdName ?><br/>
					(<?php echo $result->oiProdVariantValue1 ?>)<br/>
					<?php endif; ?>
				<?php endif; ?>
			</td>
			
			<td>
			<?php 
			if($ledger_id):	
				if($result->upgrade_id)
				{
					//check the transaction for upgrade 
					$where_array=array(
						'payer_email'=>$result->email,
						'item_number1'=>$result->upgrade_id,
						'product_type'=>PRODUCT_TYPE_IPAD,
						'payment_status'=>PAYMENT_COMPLETED,
						
					);
					$transaction_id = get_single_value('pp_transactions','ppID',$where_array);
					if($transaction_id)
						echo $result->upgrade_info;
				}
			 endif;
			?>
			</td>
			
			<td  width="50">
			<?php echo show_credit_status($result->act48,$course_id); ?>
			</td>
			<td  width="50">
            <?php 
//RF 1			
			$getGradeNum = $this->assignment_model->get_user_grade($result->id,$course_id);
			 
			if(!isset($getGradeNum['fgComputedGrade']) || !isset($getGradeNum['fgGrade'])) {
						echo "NO GRADE";
			}
			else {
				if(isset($getGradeNum['fgComputedGrade']) && isset($getGradeNum['fgGrade'])) {
					$totalGrade = ''; 
					$gradeGot   = '';
					 
							$totalGrade = $getGradeNum['fgComputedGrade'];
							$gradeGot   = $getGradeNum['fgGrade']; 
					if($totalGrade == 0) {
						$percentage = 'A';	 
					}
					else if($gradeGot > $totalGrade) {
						$percentage = 'A';
					}
					else {
						$percentage  = $this->assignment_model->percentage($gradeGot, $totalGrade,0);  
					}
					if(isset($getGradeNum['fgApproved']) && $getGradeNum['fgApproved'] == 0) {
					 $percentage = 'Not Final'; 
					}	
							
					echo anchor('/edu_admin/course_schedule/final_grades/'.$result->id.'_'.$course_id, $percentage); 
				}
			} 
// RF1 End ?>
			</td>
			<td  width="100"> <?php echo format_date($result->lastLogin,DATE_FORMAT.' '.TIME_FORMAT); ?></td>
			<td>
			<?php 
			if(is_allowed('edu_admin/user/index')):
				echo anchor('edu_admin/user/member/'.$result->id.'/'.$this->input->get_post('course_id').'?redirect='.urlencode(get_current_url()),'<img src="/images/edit.png" title="edit" alt="edit"/>'); 
			endif;
			?>
			<?php 
				if(is_allowed('edu_admin/order/index')):
					echo anchor('edu_admin/order/index/?&email='.$result->email,'Orders');
				endif;
				?>
			</td>
			</tr>
			<?php endforeach; ?>
			<tr>
			
			<td colspan="8" class="massaction">
				<?php if($send_activation): ?>
				<input type="submit" class="submit" name="activate" value="Send Activation Email"  />
			<?php endif; ?>	
			</td>	
			
			</tr>
			
			</table>
			</form>	
		<?php else: ?>
			<p class="no_record">No record found</p>
		<?php endif; ?>
		
</div>
