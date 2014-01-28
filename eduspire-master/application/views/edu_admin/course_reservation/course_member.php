<table border="2">
<thead>
<tr><th colspan="5">Courser Registrants -<?php  echo date(DATE_FORMAT);?></th></tr>
</thead>
<?php
	$course_id=0;
	$j=0;
	$i=0;
	$registrant_count=0;
	$unregistrant_count=0;
	$unenrollees_count=0;
	$enrollees_count=0;
	
	$total_registrant_count=0;
	$total_unregistrant_count=0;
	$total_enrollees_count=0;
	$total_unenrollees_count=0;
	$show_stats=false;
	
 ?>
<?php foreach($results as $key => $result): ?>
	<?php if( $course_id != $result->urCourse ):
			$course_id = $result->urCourse;
			$registrant_count=0;
			$enrollees_count=0;
			$unregistrant_count=0;
			$unenrollees_count=0;
			$j=1;
	?>
	<tr>
	<th colspan="5">
	<?php
		$course_location = $result->csCity.', '.$result->csState ;
		if(COURSE_ONLINE == $result->csCourseType)
			$course_location='Online';
		
	?>
	<?php echo $result->cdCourseID.':'.$result->cdCourseTitle; ?>(<?php echo $course_location; ?>)<?php echo format_date($result->csStartDate,DATE_FORMAT).'-'.format_date($result->csStartDate,DATE_FORMAT); ?></th>
	</tr>
	<tr>
		<td>S.no</td>
		<td>Name</td>
		<td>Email</td>
		<td>District</td>
		<td>Status</td>
	</tr>
	
	<?php endif; ?>
	<?php 
	
		switch($result->urStatus)
		{
			case STATUS_ENROLLED:
				$enrollees_count++;
				$total_enrollees_count++;
			break;
			
			case STATUS_UNENROLLED:
				$unenrollees_count++;
				$total_unenrollees_count++;
			break;
			
			case STATUS_UNREGISTERED:
				$unregistrant_count++;
				$total_unregistrant_count++;
			break;
			
			
			default:
				$registrant_count++;
				$total_registrant_count++;
			break;
			
			
		}
		
	?>
	
	<tr>
		<td><?php echo $j++; ?></td>
		<td><?php echo $result->urFirstName.' '.$result->urLastName  ?></td>
		<td><?php echo $result->urEmail; ?></td>
		<td><?php
				if(is_numeric($result->urDistrict)){
					echo get_single_value('district','disName','disID = '.$result->urDistrict) ;
				}else{
					 echo $result->urDistrict; 
				}
			?></td>
		<td><?php echo $this->course_reservation_model->show_status($result->urStatus); ?></td>
	</tr>
	
	<?php
	
	if(isset( $results[$key+1] ) && $results[$key+1]->urCourse != $course_id )
		$show_stats=true;
	else
		$show_stats=false;
	//enable stats for last course 	
	if(!isset($results[$key+1]))
		$show_stats=true;
	if($show_stats):
	?>
	<tr><td colspan="3">Registrants</td> <td colspan="2"><?php echo $registrant_count ?></td></tr>
	<tr><td colspan="3">Unregistrants</td> <td colspan="2"><?php echo $unregistrant_count ?></td></tr>
	<tr><td colspan="3">Enrollees</td> <td colspan="2"><?php echo $enrollees_count ?></td></tr>
	<tr><td colspan="3">Unenrollees</td> <td colspan="2"><?php echo $unenrollees_count ?></td></tr>
	
	<?php endif; ?>
	
<?php endforeach; ?>
	<tr><th colspan="3">Total Registrants</th> <th colspan="2"><?php echo $total_registrant_count ?></th></tr>
	<tr><th colspan="3">Total Unregistrants</th> <th colspan="2"><?php echo $total_unregistrant_count ?></th></tr>
	<tr><th colspan="3">Total Enrollees</th> <th colspan="2"><?php echo $total_enrollees_count ?></th></tr>
	<tr><th colspan="3">Total Unenrollees</th> <th colspan="2"><?php echo $total_unenrollees_count ?></th></tr>
	
</table>