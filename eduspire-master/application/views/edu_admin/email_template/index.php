<?php 
/**
@Page/Module Name/Class: 		form.php
@Author Name:			 		janet rajani
@Date:					 		Sept, 26 2013
@Purpose:		        		display email templates list 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  
  RF1      Alan Anil           Nov 14 2013     Normal           Check email is immediate or timed.
  RF2      Ben Binesh          Dec 11 2013     Normal           remove the filter form as we are showing all email templates on one page 
//---------------------------------------------------------------------------------------- 
 */
?>

<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<?php /*
	Ref2 
	Form here
*/
?>

<div class="result_container">
<div class="addRecord">
	 <?php echo anchor('edu_admin/email_template/create/','Add Email','class="submit"'); ?> 
</div>
		<?php if(isset($results) && count($results)>0): ?>
        
			<table class="table striped" cellspacing="0" cellpadding="0" width="100%" id="grid">
			<tr> 
				<th>Title</th>
				<th>Recipients</th>
                <th>Send Dates</th>
                <th>Active</th>
			</tr>
			
			<?php $i=0; ?>
			<?php foreach($results as $result): ?>
			<?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
			<tr class="<?php echo $tr_class; ?>"> 
			<td>
			<?php 
			// RF1
				if (is_numeric($result->etTarget))
				echo anchor('edu_admin/email_template/update/'.$result->etID,$result->etSubject); 				
				
				else { 
					   // Check permisson to save changes user grades.
					   if(is_allowed('edu_admin/email_template/update_cron_email'))  
							echo anchor('edu_admin/email_template/update_cron_email/'.$result->etID,$result->etTitle);
							
				}
			// RF1 End	
			?>
            </td> 
            <td><?php 
				$recipients = 0;
				if (is_numeric($result->etTarget)) {
					echo  $result->etTitle; 	
				}			
				
				else   
					echo  $result->etTarget;
				?>
            </td>
			<td>
				<?php 
                    if (is_numeric($result->etTarget))
                    echo "Immediate"; 				
                    
                    else   
                        echo  $result->etTemplate;
                                      
                ?>
            </td> 
            <td>
            	<?php  
				if (is_numeric($result->etTarget)) {
					echo  'N/A'; 	
				}			
				
				else{ 
					if(1==$result->etAuthor) 
					echo  'Active';
					else
					echo 'Not Active';
				}
				?>
            </td>
			</tr>
			<?php endforeach; ?>
			
			</table>
		<?php echo $pagination_links; ?>
				<div class="pagnination_summary">
					<?php echo $pagination_summary= pagination_summary();?>
				</div>
		<?php else: ?>
			<p class="no_recored_fount">No record found</p>
		<?php endif; ?>
		
</div>
