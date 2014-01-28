<?php 
/**
@Page/Module Name/Class: 	    index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display user list 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
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
});
				
</script>	
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
	<h3>Filters</h3>
	<form name="search-form" action="<?php echo base_url().'edu_admin/user/index' ?>">
    	<ul class="manageContent1">
        	<li><label>Name</label><input type="text" name="name" value="<?php echo $name; ?>"/></li>
            <li><label>Email</label><input type="text" name="email" value="<?php echo $email; ?>"/></li>
            <li><label>Account Type</label><?php echo form_dropdown('access_level',$this->user_model->get_access_level_array(true),$access_level); ?></li>
            <li><label>Status</label><?php echo form_dropdown('status',$this->user_model->get_status_array(true),$status); ?></li>
        </ul>
		<div class="formButton">
			<input type="submit" class="submit" value="Search"/>
			<?php echo anchor('edu_admin/user/','Reset','class="submit"'); ?> 
			<input type="submit" name="export" class="submit tooltip" title="Reports are also filtered" value="Export To CSV"  /> 
			</div>
</form>


</div>
<div class="result_container">
<div class="addRecord">
	<?php echo anchor('edu_admin/user/create/','Add User','class="submit"'); ?>
	<?php echo anchor('edu_admin/user/instructor/','Add Instructor','class="submit"'); ?>
	<?php echo anchor('edu_admin/user/member/','Add Member','class="submit"'); ?>
	
</div>
		<?php if(isset($results) && count($results)>0): ?>
			<form method="post" name="grid-form">
			<input type="hidden" name="mass_action" value="1"/>
			<table id="grid" class="table striped" cellspacing="0" cellpadding="0" width="100%">
			
			<tr>
				<th><input type="checkbox" name="check_all" id="check_all" value="1" onclick="checkall(this.form)" /></th>
				<th>Name</th>
				<th>District</th>
				<th>Status</th>
				<th>Account</th>
				<th>Action</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
			<td ><input name="chk_ids[]" type="checkbox" class="checkbox" value="<?php echo $result->id; ?>" /></td>
			<td>
				<div><?php echo $result->firstName.' '.$result->lastName; ?>(<?php echo $result->userName; ?>)</div>
				<div><?php echo $result->email ?></div>
				<div>Last Login :<?php echo format_date($result->lastLogin,DATE_FORMAT.' '.TIME_FORMAT); ?></div>
				
				
			</td>
			<td><?php
				if(is_numeric($result->districtAffiliation)){
					echo get_single_value('district','disName','disID = '.$result->districtAffiliation) ;
				}else{
					 echo $result->districtAffiliation; 
				}
			?></td>
			<td>
			<?php echo $this->user_model->show_status($result->activationFlag); ?>
			<div>
			<?php  //check for activation links  
				if( '' == $result->userName ){
					if( ACCOUNT_INACTIVE == $result->activationFlag){
						//send activation link 
						$label='Unactivated: Send Invitation';
					}else{
						// resend activation link 
						$label='Pending: Resend Invitation';
						
					}
					echo anchor('edu_admin/user/send_activation/'.$result->id,$label,'class="editButton send-activation" popup-text="Do you want to send activation link to '.$result->firstName.' '.$result->lastName.'"');
				}

			?>
			</div>
			</td>
			<td><?php echo $this->user_model->show_access_level($result->accessLevel); ?></td>
			<td>
			<?php echo anchor('edu_admin/user/view/'.$result->id,'<img src="/images/view.png" title="view" alt="view"/>'); ?>
			<?php echo anchor('edu_admin/user/update/'.$result->id,'<img src="/images/edit.png" title="edit" alt="edit"/>'); ?>
			<?php echo anchor('edu_admin/user/delete/'.$result->id,'<img src="/images/delete.png" title="delete" alt="delete"/>','onclick=\'return confirm("Do you want to delete this record?")\''); ?><br/>
			<?php echo anchor('edu_admin/user/change_password/'.$result->id,'change password'); ?>
			</td>
			</tr>
			<?php endforeach; ?>
			<tr>
			
			<td colspan="7" class="massaction">
				<input type="submit" class="submit" name="activate" value="Activate" onclick=" return check()"/>
				<input type="submit" class="submit" name="deactivate" value="Deactivate"  onclick=" return check()"/>
			</td>	
			
			</tr>
			<tr>
				<td colspan="7" class="summary">
				<?php echo $pagination_links; ?>
				<div class="pagnination_summary">
					<?php echo pagination_summary();?>
				</div>
				</td>
			</tr>		
			
			</table>
			</form>	
		
		<?php else: ?>
			<p class="no_recored_fount">No record found</p>
		<?php endif; ?>
		
</div>


