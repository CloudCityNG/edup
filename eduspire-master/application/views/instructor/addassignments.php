<?php 
// ********************************************************************************************************************************
//Page name			:- 			addassignments.php
//Author Name		:- 			Alan Anil
//Purpose 			:- 			File used for adding/editing assignments.  
//Date				:- 			05-09-2013
//Table Refered		:-  		N/A
//*********************************************************************************************************************************
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  

//---------------------------------------------------------------------------------------- 
?>
<script>
	jQuery(document).ready(function($) { 
		//load the date picker 
		loadDatePicker();  
	}); 
	/**
		@Function Name:	loadDatePicker
		@Author Name:	ben binesh
		@Date:			Aug, 26 2013
		@Purpose:		load the date picker 
	
	*/
	
	function loadDatePicker(){
		$( ".datepicker" ).datepicker();
	}
</script>
<!--check assigment is new or edit existing assignment.-->
<?php if(isset($assignData)): ?>
	<h1>Edit Assignment</h1>
<?php else: ?>
	<h1>Add Assignment</h1>
<?php endif; ?> 
<div class="flash_message">
<?php get_flash_message(); ?>
</div> 
<div id="form">
	<!--Show error messages if occured--> 
	<div class="error_msg">
	  <?php echo validation_errors('<p>', '</p>');?>
	   <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
    <!--Form for add/edit assignment-->
	<form class="form" action="" method="post"> 
		<div class="row clearfix">
		   <div><span class="required">*</span>Assignment Title</div>
		   <div>
			<textarea name="assignmentTitle"  rows="3"><?php echo isset($assignData[0]->assignTitle)?($assignData[0]->assignTitle):$this->input->post('assignmentTitle');?></textarea>
		   </div>
		</div> 
		 
		<div class="row clearfix">
		   <div>Activation Date</div>
		   <div>
		   <input type="text" name="activationDate" 
           value="<?php if(isset($assignData)) { 
												   $actDate        = $assignData[0]->assignActiveDate;
												   $finalActDate   = date("m/d/Y", strtotime($actDate));
											   } 
		   echo isset($assignData)?($finalActDate):$this->input->post('activationDate'); ?>" 
           class="datepicker" maxlength="255" size="40"/>
           <h5>Activation Date and Activation Time define when the assignment becomes visible to the students.</h5>
		   </div>
		</div>
         
        <div>
		   <div>Activation Time</div>
		   <div>  
            <?php
			$startMin = '';
			if(isset($assignData)) {
				$start_time = explode(':',$assignData[0]->assignActiveTime);
				$ampm='am';
				if($start_time[0] > 12 || $start_time[0]=='00' ){
					$ampm='pm';
				}
				if($start_time[0] > 12){
					$start_time[0]=$start_time[0]-12;
				}
				$startHours = $start_time[0];
				if(isset($start_time[1]) && $start_time[1] != '')
				 $startMin   = $start_time[1];
		    }
			else { $startHours = '';$ampm = '';$startMin = ''; }	
			       echo form_dropdown('activationTime',get_hours_array(true,array('0'=>'-hh-')),$startHours ); ?>
            <?php  echo form_dropdown('activationMin',get_minute_array(true,array('0'=>'-mm-')) ,$startMin); ?>
            <?php  echo form_dropdown('activationAP',get_ampm_array(true,array('0'=>'-a/p-')),$ampm ); ?>
		   </div>
		</div> 
		<div class="row clearfix">
		   <div>Due Date </div>
		   <div>
		   <input type="text" name="dueDate" 
           value="<?php if(isset($assignData)) { 
												   $duDate        = $assignData[0]->assignDueDate;
												   $finalDuDate   = date("m/d/Y", strtotime($duDate));
											   } 
		   echo isset($finalDuDate)?($finalDuDate):$this->input->post('dueDate');?>" 
           class="datepicker" maxlength="255" size="40"/>
		   </div>
		</div>
		
        <div>
		   <div>Due Time</div>
		   <div>  
           <?php   
		   $dueMin = '';
          if(isset($assignData)) {
				$end_time = explode(':',$assignData[0]->assignDueTime);
				$dampm='am';
				if($end_time[0] > 12 || $end_time[0]=='00' ){
					$dampm='pm';
				}
				if($end_time[0] > 12){
					$end_time[0]=$end_time[0]-12;
				}
				$dueHours = $end_time[0];
				if(isset($end_time[1]) && $end_time[1] != '')
				$dueMin   = $end_time[1];
		   }
			else { $dueHours = '';$dampm = '';$dueMin = ''; }	
						           echo form_dropdown('dueTime',get_hours_array(true,array('0'=>'-hh-')),$dueHours ); ?>
							<?php  echo form_dropdown('dueMin',get_minute_array(true,array('0'=>'-mm-')) ,$dueMin); ?>
							<?php  echo form_dropdown('dueAP',get_ampm_array(true,array('0'=>'-a/p-')),$dampm ); ?>
		   </div>
		</div> 
		<div class="row clearfix">
		   <div><span class="required">*</span>Point Value </div>
		   <div>
			<input type="text" name="pointValue" 
            value="<?php echo isset($assignData[0]->assignPoints)?($assignData[0]->assignPoints):$this->input->post('pointValue');?>" 
            maxlength="255" size="40"/>
		   </div>
		</div> 
		<div class="row clearfix">   
		   <div>&nbsp;</div>
		   <div>
           <?php if(isset($assignData)) { ?><input type="hidden" name="assignId" value="<?php echo $assignData[0]->assignID;?>" />  	
            <input type="submit" value="Edit" class="submit"/>
		   <?php 
		   } else {  ?> <input type="submit" value="Add" class="submit"/> <?php } ?>
		   </div>
		</div>
	</form>
	
</div>