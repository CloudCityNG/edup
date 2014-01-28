<?php 
/**
@Page/Module Name/Class: 		form.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display add/edit form for course registrant
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
*/
?>

<script>
   
    jQuery(document).ready(function($) {
            
                    $(document).on('change',"#iuUnitDropDown",function(){
                
                    
			$.ajax({
				url: '<?php echo base_url().'login/iu_districts/';?>'+$(this).val(),
				success: function(data) {
					$("#iuBasedDistrictContainer").html(data);
				}	
			});
		});
             });
 </script>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="top_links clearfix">
	 <?php echo anchor('edu_admin/course_reservation/index/?course_id='.$this->input->get_post('course_id'),'Back','class="submit"'); ?> 

</div>


</script>

<div id="form">
	<div class="row clearfix">			
	<div class="error_msg error">
	  <?php if(isset($errors) && count($errors)>0 ): 
			foreach($errors as $error){
				echo '<p>'.$error.'</p>';	
			}
		endif; ?>					
	</div>
	</div>
	<form class="form" action="" method="post" >
		
		<input type="hidden" name="urCourse" value="<?php echo isset($result->urCourse)?$result->urCourse:$this->input->get_post('course_id'); ?>"/>
		<div class="row clearfix">
		   <div class="left_area">Email <span class="required">*</span></div>
		   <div class="right_area">
		   <input type="text" name="urEmail" value="<?php echo isset($result->urEmail)?$result->urEmail:$this->input->post('urEmail');?>" maxlength="255" size="40"/>
		   </div>
		</div>
		
		<div class="row clearfix">
		   <div class="left_area">First Name<span class="required">*</span></div>
		   <div class="right_area">
		   <input type="text" name="urFirstName" value="<?php echo isset( $result->urFirstName ) ? $result->urFirstName:$this->input->post('urFirstName');?>" maxlength="255" size="40"/>
		   <div class="error"><?php echo form_error('urFirstName','',''); ?></div>
		   </div>
		</div>
		
		<div class="row clearfix">
		   <div class="left_area">Last Name<span class="required">*</span></div>
		   <div class="right_area">
		   <input type="text" name="urLastName" value="<?php echo isset( $result->urLastName ) ? $result->urLastName:$this->input->post('urLastName');?>" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('urLastName','',''); ?></div>
		   </div>
		</div>
		
		
		<div class="row clearfix">
		   <div class="left_area">Phone<span class="required">*</span></div>
		   <div class="right_area">
		   <input type="text" name="urPhone" value="<?php echo isset( $result->urPhone ) ? $result->urPhone:$this->input->post('urPhone');?>" maxlength="255" size="40"/>
		    <div class="error"><?php echo form_error('urPhone','',''); ?></div>
		   </div>
		</div>
		
		<div class="row clearfix">
		   <div class="left_area">District</div>
		   <div class="right_area">
		   <?php  $school_district = isset( $result->urDistrict ) ? $result->urDistrict:$this->input->post('urDistrict');
			if(is_numeric($school_district)){
					$school_district= get_single_value('district','disName','disID = '.$school_district) ;
			}
		   ?>
		   <input type="text" name="urDistrict" value="<?php echo $school_district   ?>" maxlength="255" size="40"/>
			<div class="hint"> District from old database value </div>
		    <div class="error"><?php echo form_error('urDistrict','',''); ?></div>
		   </div>
		</div>
		
		<div class="row clearfix">
		   <div class="left_area">Intermediate Unit <span class="required">*</span></div>
		   <div class="right_area">
            <?php 
				$selected_iuUnit =  isset( $result->urIuID ) ? $result->urIuID:$this->input->post('dis_iu_unit');
				$iu_unit_array=get_dropdown_array('iu_unit',$where_condition=array(),$order_by='iuID',$order='ASC','iuID','iuName','',true,array(''=>'Select'));	
				echo form_dropdown('dis_iu_unit',$iu_unit_array,$selected_iuUnit,'id="iuUnitDropDown"');
			?>
			<div class="error"><?php echo form_error('dis_iu_unit','',''); ?></div>
		   </div>
		</div>
        <div class="row clearfix">
		   <div class="left_area">School District <span class="required">*</span></div>
		   <div class="right_area">
                        <div id="iuBasedDistrictContainer">
                                   <?php 
                                            $selected_dis_unit =  isset( $result->urDistrictID ) ? $result->urDistrictID:$this->input->post('school_district');
                                            $school_district_array=get_dropdown_array('district',$where_condition=array('disPublish'=>STATUS_PUBLISH),$order_by='disName',$order='ASC','disID','disName','',true,array(''=>'Select'));
                                            echo form_dropdown('school_district',$school_district_array,$selected_dis_unit); ?>
                         </div>
                        
			 <div class="error"><?php echo form_error('school_district','',''); ?></div>
		   </div>
		</div>
		
		
		<div class="row clearfix">
		   <div class="left_area">&nbsp;</div>
		   <div class="right_area">
			<?php ?>
			<input type="checkbox"  <?php echo (isset( $result->urCredits ) &&   $result->urCredits)?'checked="checked"':''; ?> name="urCredits" value="<?php echo isset( $result->uurCredits ) ? $result->urCredits:1; ?>">
			<span>I am taking this course for credits.</span>
		   </div>
		</div>
		<div class="row clearfix">
		   <div class="left_area">&nbsp;</div>
		   <div class="right_area">
			<?php ?>
			<input type="checkbox"  <?php echo (isset( $result->urDistrictReimburse ) &&   $result->urDistrictReimburse)?'checked="checked"':''; ?> name="urDistrictReimburse" value="<?php echo isset( $result->urDistrictReimburse ) ? $result->urDistrictReimburse:1; ?>">
			<span>My school district will reimburse me for the cost of this course.</span>
		   </div>
		</div>
		
		
		<div class="row clearfix">  
		   <div class="left_area">&nbsp;</div>
		   <div class="right_area">
			<input type="submit" value="<?php echo (isset($result->uID))?'Save':'Create'; ?>" class="submit"/>
			
		   </div>
		</div>
	</form>
	
</div>