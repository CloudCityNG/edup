<?php

	/**

	@Page/Module Name/Class: 		gradesSheet.php

	@Author Name:			 		Alan Anil

	@Date:					 		Nov, 14 2013

	@Purpose:		        		Contain all data related to grades of users on the basis of course 

	@Table referred:				users, users_profiles,assignments, assignment_leadger.

	@Table updated:					NA

	@Most Important Related Files	NA

	 */

  

?>

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

<div class="publicTitle"><h1> Grade Sheet</h1></div>

<div class="result_container"> 

<?php    

// User assignment grade sheet.

if(isset($courses) && !empty($courses)): ?>

<?php foreach($courses as $cor):

		// check course id to current selected course id.	

		if($courseId == $cor->csID): ?>

		<div class="course-div clearfix">

			<h3><?php echo $cor->cdCourseID; ?>:<?php echo $cor->cdCourseTitle;  ?></h3>	

			<div><?php

				if(COURSE_ONLINE == $cor->csCourseType) 

					echo '(Online)'; 

				else

					echo $cor->csCity.', '.$cor->csState; ?>

            </div>

			<div><?php echo format_date($cor->csStartDate,DATE_FORMAT); ?>-<?php echo format_date($cor->csEndDate,DATE_FORMAT); ?>

            </div>

		</div>

        <?php $getGradeNum           = $this->assignment_model->get_user_grade($userId,$cor->csID);

			  if(isset($getGradeNum['fgComputedGrade']) && $getGradeNum['fgComputedGrade'] != '') 

				{ ?>			

                    <div class="letterHeaderCall">

                        Your <a href="<?php echo base_url().'/letterhead/index/'.$courseId; ?>" target="_blank">FINAL GRADE

                        </a> is available for viewing/printing.

                    </div>

         <?php  } ?>

        <div class="fRight">

        	<h3> Total Points:  

        		  <?php $getUserPointsDetails  = $this->assignment_model->get_total_user_assign($userId, $courseId);

				  	if($getUserPointsDetails['totalPoints'] == '')

						echo '0';

					else	

						echo $getUserPointsDetails['pointsGot'].' / '.$getUserPointsDetails['totalPoints']; 

				  ?>

        	</h3>

        </div> 

        <table class="table striped" id="grid"  width="100%"> 

            <tr>       

            	<th>Assignment</th>            

                <th>Pts Earned </th> 

                <th>Comments</th> 

                <th>Submitted</th>

            </tr>  

        <?php  

		$results  = $this->assignment_model->get_user_assignments($userId, $courseId);  

	    $rowCss = 0;

		if(!empty($results))

		  { 

			foreach($results as $getUserList)

		    { 

				// fetch user assignment list and their grades and points.  

		        $getAssignPointsDetails= $this->assignment_model->get_points_earned($userId, $getUserList->alAssignID); 

			 ?>

             <?php $tr_class = ($rowCss++%2==0)?'even':'odd'; ?>

				<tr class="<?php echo $tr_class; ?>">  

                     <td>

                    	<?php echo $getUserList->assignTitle;?>

                    </td>

                    <td>

                    	<?php echo $getAssignPointsDetails['pointsGot'].' / ' .$getAssignPointsDetails['totalPoints'];?>

                    </td> 

                    <td>

                  <?php if(isset($getAssignPointsDetails['alCommentStudent']) && $getAssignPointsDetails['alCommentStudent'] != '') { 

					?><a href="/member/comment/<?php echo $getUserList->alAssignID; ?>"  class="fancybox"> See Comments </a><?php } ?>

                     

                    </td> 

                    <td>

						<?php echo isset($getAssignPointsDetails['alDateSubmitted'])?format_date($getAssignPointsDetails['alDateSubmitted'],'m/d/y h:i A'):$getAssignPointsDetails['alDateSubmitted']; ?>

                    </td>

                </tr> 

            <?php  

		   }

		 }

		 else { ?>

		 <tr><td colspan="4" align="center"><p class="no_recored_fount">No record found</p></td></tr>

		 <?php

		 }

		   ?> 

           <tr>       

            	<th>Assignment</th>            

                <th>Pts Earned </th> 

                <th>Comments</th> 

                <th>Submitted</th>

            </tr>      

        </table>  

       <?php endif; ?>

<?php endforeach; ?>  

<?php endif; ?> 

    

</div>    