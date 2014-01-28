<div class="publicTitle"><h1>User Emulator</h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
	<h3>Filters</h3>
	<form name="search-form" action="">
    	<ul class="manageContent">
        	<li><input type="text" name="name" placeholder="search user" value="<?php echo $name; ?>"/></li>
        </ul>
        <div class="formButton"><input type="submit" class="submit" value="Search"/></div>
		
	</form>
	
</div>
<div class="result_container">
		<?php if(isset($results) && count($results)>0): ?>
			
			<input type="hidden" name="mass_action" value="1"/>
			<table id="grid" class="table striped" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<th>Name</th>
				<th>Access Level</th>
				<th>School District</th>
				<th>&nbsp;</th>
			</tr>
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>">
		
			<td>
				<div><a href="<?php echo get_seo_url('profile',$result->id,$result->firstName.' '.$result->lastName); ?>"><?php echo $result->firstName.' '.$result->lastName; ?></a>
				(<?php echo $result->userName;?>)
				</div>
				<div><?php echo $result->email ?></div>
				
				
			</td>
			<td><?php echo $this->user_model->show_access_level($result->accessLevel); ?></td>
			<td>
			<?php 
			if(is_numeric($result->districtAffiliation)){
					echo get_single_value('district','disName','disID = '.$result->districtAffiliation) ;
				}else{
					 echo $result->districtAffiliation; 
			}
			?>	
			</td>
			<td><?php echo anchor('edu_admin/user/emulate/'.$result->id,'Emulate');  ?></td>
			</tr>
			<?php endforeach; ?>
			<tr>
				<td colspan="7" class="summary">
				<?php echo $pagination_links; ?>
				<div class="pagnination_summary">
					<?php echo pagination_summary();?>
				</div>
				</td>
			</tr>		
			
			</table>
			
		
		<?php else: ?>
			<p class="no_recored_fount">No record found</p>
		<?php endif; ?>
		
</div>




