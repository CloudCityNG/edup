<?php 
/**
@assignment/Module Name/Class: 	grades.php
@Author Name:			 		Alan Anil
@Date:					 		Nov, 20 2013
@Purpose:		        		display assignment  grades 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
//Chronological Development
//Ref No   Developer Name      Date            Severity        Description
//----------------------------------------------------------------------------------------  
  
//---------------------------------------------------------------------------------------- 
 */
?> 
<h1>Assignment Grades </h1>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div>
     <?php if(isset($userList) && count($userList)>0): ?>  
        <!--Form for showing user lists-->
        <form method="post" name="userLists" action="" onsubmit="return checkAllPointsVal();">
           <table class="table striped"> 
            <tr>  
                <th>Registrant</th>
                <th></th>
                <th>Submitted</th>
                <th>
                Pts Earned <br />
                <a onclick="fillAll('userLists');" style="cursor:pointer">Fill All Pts</a>
                </th>
                <th>Total Pts</th>
                <th>Comments</th> 
            </tr> 
            <?php  
			$pointsValCounter = 0;
			$rowCss = 0;
			// fetching multiple records
			foreach($userList as $getUserList)
		    {
				$getUserDetails        = $this->assignment_model->show_assign_user_details($getUserList->alUserID);
				$getUserPointsDetails  = $this->assignment_model->get_total_user_assign($getUserList->alUserID);
				$user                  = $this->user_model->get_single_record($getUserList->alUserID,'*',true);
				$getAssignPointsDetails= $this->assignment_model->get_points_earned($getUserList->alUserID,$assignId);
			 ?>
             <?php $tr_class = ($rowCss++%2==0)?'even':'odd'; ?>
				<tr class="<?php echo $tr_class; ?>"> 
                    <td>
                    <?php  
                        $profile_image=( '' != $user->profileImage)?$user->profileImage:'default.jpg';
                        $profile_image=base_url().'uploads/users/'.$profile_image;
                        ?>
                        <img src="<?php echo crop_image($profile_image); ?>" 
                        title="<?php echo $this->session->userdata('display_name'); ?>" 
                        alt="<?php echo $this->session->userdata('display_name'); ?>"/>
                    </td>
                    <td> <?php 
					if(isset($getAssignPointsDetails)):
						if(isset($getUserDetails[0]) && $getUserDetails[0]->firstName != '' && $getUserDetails[0]->lastName != ''):
							echo  $getUserDetails[0]->firstName.', '.$getUserDetails[0]->lastName ; 
						endif;
					endif; ?>  
                    </td>
                    <td>
						<?php echo isset($getAssignPointsDetails['alDateSubmitted'])?format_date($getAssignPointsDetails['alDateSubmitted'],'d/m/y h:i A'):$getAssignPointsDetails['alDateSubmitted']; ?>
                    </td>
                    <td>
                    	<input type="text" class="pointGot pointAval" name="pointGot_<?php echo $getUserList->alUserID;?>" 
                        id="pointGotId_<?php echo $pointsValCounter;?>" class="gradeBox" 
                    	value="<?php echo $getAssignPointsDetails['pointsGot']; ?>" /> 
						<?php echo ' / ' .$getAssignPointsDetails['totalPoints'];?>
                    </td>
                    <td>
                    	<?php echo $getUserPointsDetails['pointsGot'].' / '.$getUserPointsDetails['totalPoints']; ?> 
                    </td>
                    <td>
                    <textarea name="studentComment_<?php echo $getUserList->alUserID;?>" rows="4" cols="30" placeholder="Comments for student"><?php if(isset($getAssignPointsDetails['alCommentStudent']) && $getAssignPointsDetails['alCommentStudent'] != '') { 
					echo $getAssignPointsDetails['alCommentStudent']; } ?>
                    </textarea>
                    </td> 
                </tr>
           <?php 
		   $pointsValCounter++;
		   }?>    
           <input type="hidden" name="hiddenPoint" id="hiddenPointId" value="<?php echo $getAssignPointsDetails['totalPoints'];?>"  />			           <tr>  
                <th>Registrant</th>
                <th></th>
                <th>Submitted</th>
                <th> Pts Earned </th>
                <th>Total Pts</th>
                <th>Comments</th> 
            </tr> 
           </table>  
           <div class="top_links clearfix">
            <input type="submit" value="Save Changes"  name="save" class="submit"/> 
           </div> 
            <div class="top_links clearfix">
           	 <input type="submit" value="Publish Grades" name="submitToEduspire" class="submit"/> 
            </div> 
            <input type="hidden" name="assignIdVal" value="<?php echo $assignId;?>" /> 
        </form>
        <?php endif;?>
    </div>
<script>
	/**
		@Function Name:	fillAll
		@Author Name:	Alan Anil
		@Date:			Nov, 20 2013
		@Purpose:		For filling all points.
	
	*/ 
function fillAll(formname)
{   
	var totalVal    = $("input[class='pointGot pointAval']").length; 
	var checktoggle = document.getElementById("hiddenPointId").value ; 
      for(var i = 0; i < totalVal; i++) { 
        document.getElementById("pointGotId_" + i).value = checktoggle;   
      }  
	
} 
/**
		@Function Name:	checkAllPointsVal
		@Author Name:	Alan Anil
		@Date:			Nov, 20 2013
		@Purpose:		For checking validations.
	
	*/ 
function checkAllPointsVal()
{ 
	var totalVal1    = $("input[class='pointGot pointAval']").length; 
	var numCheckCounter = 0;  
	  for(var i = 0; i < totalVal1; i++) { 
      	var pointsVal = document.getElementById("pointGotId_" + i).value;
		if (isNaN(pointsVal)) { 
			numCheckCounter = 1;
		}
	       
      } 
	  if(numCheckCounter == 1){
		alert("Please enter only numbers in Pts Earned Field"); 
	  	return false;
	  }
	  else {
	  	return true;
	  }
		
}	   
</script>
<style>
.gradeBox { width:30px;}
</style>
