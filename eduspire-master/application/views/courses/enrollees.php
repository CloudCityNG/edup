<?php 
/**
@Page/Module Name/Class: 		enrollees.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display course enrollees details
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1>Enrollees</h1></div>
<?php if(isset($course) && !empty($course)): ?>
	<h2>
	<?php 
		$course_location=$course->csCity.', '.$course->csState;
			if(COURSE_ONLINE == $course->csCourseType)
				$course_location='Online';
	echo $course->cdCourseID.' '.$course->cdCourseTitle.'('.format_date($course->csStartDate,DATE_FORMAT).'-'.format_date($course->csEndDate,DATE_FORMAT).'-'.$course_location.')'; ?>
	</h2>
	<?php endif; ?>
<script>
  jQuery(document).ready(function($) {
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
<div class="top_links clearfix">
	<ul class="admin-top-links">
		<li><?php echo anchor('courses/export_enrollees?course_id='.$course->csID.'&redirect='.get_current_url(),'CSV Export','class="editButton submit fLeft"'); ?> </li>
		<li><?php echo anchor('courses/export_grade?course_id='.$course->csID.'&redirect='.get_current_url(),'Current Grade Export','class="editButton  submit fLeft buttonSpace"'); ?> </li>
		<li><?php echo anchor('courses/email_enrollee/'.$course->csID,'Email All','class="editButton fancybox submit fLeft"'); ?></li>
		
		
	</ul> 
</div>

<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="result_container">
		<?php if(isset($results) && count($results)>0): ?>
		
		
			<table class="table striped" cellspacing="0" width="100%" id="grid">
			<tr>
				<td colspan="5" class="summary">
				<div class="pagnination_summary fRight"><?php echo $num_records ?> Enrollees<div>
				</td>
			</tr>	
			<tr>
				<th>Name</th>
				<th>District</th>
				<th>Grade Level/Teaching Discipline</th>
				<th>Non-Credit</th>
				<th>Ipad</th>
				</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td><a href="<?php echo get_seo_url('profile',$result->id,$result->firstName.' '.$result->lastName); ?>"><?php echo $result->firstName.' '.$result->lastName; ?></a><br>
			<?php  echo $result->email; ?><br/>
			last Login:<?php  echo format_date($result->lastLogin,DATE_FORMAT.''.TIME_FORMAT);?>
			</td>
			<td>
			<?php
				if(is_numeric($result->districtAffiliation)){
					echo get_single_value('district','disName','disID = '.$result->districtAffiliation) ;
				}else{
					 echo $result->districtAffiliation; 
				}
			?></td>
			<td><?php echo $result->level;  ?><br/>
			<?php 
				if($result->gradeSubject):
					echo get_single_value('tracks','trName','trID = '.$result->gradeSubject) ;
				endif;	
			?></td>
			<td><?php echo show_credit_status($result->act48,$course->csID); ?></td>
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
				<?php
				if($ledger_id):
				if($result->upgrade_id)
				{
					//check the transaction for upgrade 
					$transaction_id = get_single_value('pp_transactions','ppID',"payer_email = '".$result->email."' AND item_number1=".$result->upgrade_id." AND product_type= ".PRODUCT_TYPE_IPAD);
					if($transaction_id)
						echo $result->upgrade_info;
				}
				endif;
			?>
			</td> 
			</tr>
			<?php endforeach; ?>
			
			</table>
		
		<?php else: ?>
			<p class="no_record">No record found</p>
		<?php endif; ?>
		
</div>
