<?php 
/**
@Page/Module Name/Class: 		index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display course registrant details
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="adminTitle"><h1>Manage Course Reservations </h1></div>
<script>
jQuery(document).ready(function($) {
	$( ".fancybox" ).click(function() {
		$.fancybox.open({
			href : jQuery(this).attr('href'),
			type : 'iframe',
			padding : 5,
                        margin: [115, 0, 10, 0] // top, right, bottom, left
		});
		return false;
	});
	$(document).on('click','.send-payment-link',function(){
		alink=jQuery(this).attr('href');
		edu_popup_msg(jQuery(this).attr('popup-text'));
		edu_show_popup();
		$(document).on('click','#ok-btn',function(){
			edu_close_popup();
			location.href=alink;
		});
	
		return false;
			
	
	});
});
				
</script>


<?php if(isset($course) && !(empty($course))): ?>
	<h2>Registrants:- <?php echo $course->cdCourseID; ?> : <?php echo $course->cdCourseTitle; ?> 
		<?php if(COURSE_OFFLINE == $course->csCourseType): ?>
		( <?php echo $course->csAddress  ?>  <?php echo $course->csCity  ?> <?php echo $course->csState  ?>)	
		<?php endif; ?>
	</h2>
	
<?php endif; ?>
<div class="top-content clearfix">
<?php if(isset($content) && !(empty($content))):?>
	<?php echo $content[0]->cpDescription; ?>
<?php endif; ?>



<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
	<h3>Filters</h3>
	<form name="search-form" action="<?php echo base_url().'edu_admin/course_reservation/index' ?>">
		<ul class="manageContent">
		<li>
		<label>Name:</label><input type="text" name="name" value="<?php echo $name; ?>"/></li>
		
		<li><label>Email:</label><input type="text" name="email" value="<?php echo $email; ?>"/></li>
		<li><input type="hidden" name="course_id" value="<?php echo $course_id; ?>"/>
		</li>
		</ul>
		<div class="formButton">
			
			<input type="submit" class="submit" value="Search"/>
			<?php echo anchor('edu_admin/course_reservation/?course_id='.$this->input->get_post('course_id'),'Reset','class="submit"'); ?> 
			<input type="submit" name="export" class="submit tooltip" title="Reports are also filtered" value="Export To CSV"  /> 
			
		</div>
	
	</form>
	</fieldset>	
	</div>
</div>
<div class="result_container">
	<div class="addRecord">
	
	<?php 
	if(is_allowed('edu_admin/course_reservation/create')):
	echo anchor('edu_admin/course_reservation/create/?course_id='.$this->input->get_post('course_id'),'Add Record','class="submit" title="reset"'); 
	endif;
	?> 
	
</div>
		<?php if(isset($results) && count($results)>0): ?>
			<table class="table striped" cellspacing="0" id="grid" width="100%">
			<tr>
				<td colspan="3"><?php echo $pagination_links; ?>
				</td>
				<td colspan="3">
				<div class="pagnination_summary">
					<?php echo $pagination_summary = pagination_summary();?>
				</div>
				</td>
			</tr>	
			<tr>
				<th>ID</th>
				<th>Contact</th>
				<th>District</th>
				<th>Non-Credit</th>
				<th>Status</th>
				<th>Action</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php if(STATUS_REGISTERED == $result->urStatus):	 ?>	
			
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td><?php echo $result->uID; ?></td>
			<td>
				<div><?php echo $result->urFirstName.' '.$result->urLastName; ?></div>
				<div><?php echo $result->urEmail; ?></div>
				<div><?php echo $result->urPhone; ?></div>
			</td>
			<td>
			<?php
				if(is_numeric($result->urDistrict)){
					echo get_single_value('district','disName','disID = '.$result->urDistrict) ;
				}else{
					 echo $result->urDistrict; 
				}
			?></td>
			<td><?php echo show_yesNo_text($result->urCredits,true); ?></td>
			<td>
				<div>
					<?php $waiting_count = $result->csMaximumEnrollees-($result->serial_number+$course->enrolees_count); 
						if( 0 <= $waiting_count){
							echo 'Guaranteed('.($result->serial_number+$course->enrolees_count).')';
						}else{
							//echo 'Waiting('.($waiting_count*-1).')';
							echo 'Waiting';
						}
					?>	
				<div>
				<div><?php echo format_date($result->urTimestamp,DATE_FORMAT.' '.TIME_FORMAT); ?></div>
			</td>
			
			<td>
			<?php 
				if(is_allowed('edu_admin/course_reservation/update')):
					echo anchor('edu_admin/course_reservation/update/'.$result->uID.'?course_id='.$this->input->get_post('course_id'),'<img src="/images/edit.png" title="edit" alt="edit"/>'); 
				endif;	
				?>
			<?php
			if(is_allowed('edu_admin/course_reservation/delete')):
				echo anchor('edu_admin/course_reservation/delete/'.$result->uID,'<img src="/images/delete.png" title="delete" alt="delete"/>','onclick=\'return confirm("Do you want to delete this record?"
			)\''); 
				endif;	
			?><br/>
			
			<?php
			if(STATUS_REGISTERED == $result->urStatus):		
				if(is_allowed('edu_admin/course_reservation/enroll')):
					echo anchor('edu_admin/course_reservation/enroll/'.$result->uID,'Enroll','class="enroll fancybox"'); 
				endif;	
				
				if(is_allowed('edu_admin/course_reservation/pay')):
					echo '|'.anchor('edu_admin/course_reservation/pay/'.$result->uID.'?redirect='.urlencode(get_current_url()),'Send Payment Link','class="send-payment-link " popup-text="Do you want to send payment link this user? "'); 
					if($result->urPaymentLink){
						echo '<div>Last Sent On:'.date(DATE_FORMAT,$result->urPaymentLink).'</div>';
					}
					
				endif;
				
			endif;	
			?>
			
			</td>
			</tr>
			<?php endif; ?>
			<?php endforeach; ?>
			<tr><td colspan="3"><?php echo $pagination_links; ?>
				</td>
				<td colspan="3">
				<div class="pagnination_summary">
					<?php echo $pagination_summary;?>
				</div>
				</td>
			</tr>		
			</table>
		
		<?php else: ?>
			<p class="no_recored_fount">No record found</p>
		<?php endif; ?>
		
</div>
