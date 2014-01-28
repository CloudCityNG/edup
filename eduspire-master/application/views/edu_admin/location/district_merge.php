<?php 
/**
@Page/Module Name/Class: 	   district_form.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display add/edit form for district
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>

<h1>Merge District <?php echo $district->disName ?>(<?php echo $district->iuName; ?>)</h1>
<div class="top_links clearfix">
	 <?php echo anchor('edu_admin/location','Back','class="submit"'); ?> 
</div>


<div id="form">
				
	<div class="error_msg">
	  <?php echo validation_errors('<p>', '</p>');?>
	   <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	<form class="form" action="" method="post" >
		<p><b>Select the distruct below from which you want to replace the <?php echo $district->disName ?>(<?php echo $district->iuName; ?>) </b> </p>
		<div class="row clearfix">
		   <?php if(isset($districts) && !empty($districts)): ?>
		   <div class="scroll">
			<?php foreach($districts as $district):?>	
				<div>
				<input id="district_<?php echo $district->disID; ?>" type="radio" name="district"  value="<?php echo $district->disID ?>" /><label for="district_<?php echo $district->disID ?>"><?php echo $district->disName.'( '.$district->iuName.')'; ?></label>
				</div>
				<?php endforeach; ?>
		   </div>
		   <?php endif; ?>
		   </div>
		</div> 
		
		
		<div class="row clearfix">  
		   <div class="left_area">&nbsp;</div>
		   <div class="right_area">
			<input type="submit" name="merge" value="Merge" class="submit"/>
			
		   </div>
		</div>
	</form>
	
</div>