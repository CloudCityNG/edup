<?php 
/**
@Page/Module Name/Class: 		events.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display subscription form
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
Chronological development
***********************************************************************************
Ref No.  |   Author name	| Date	 	| Modification description
***********************************************************************************
RF1	  |  Janet Rajani	 | 29 Nov,2013  | changed radio to drop-down
RF2       |  Janet Rajani        |  Dec,24 2013  | minor  |  Added captcha 
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
<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>	
<?php get_text(3); ?>

<div id="form">
<div class="error_msg">
  <?php if(isset($errors) && count($errors)>0 ): 
		foreach($errors as $error){
    	echo '<p>'.$error.'</p>';	
		}
	   endif; ?>					
</div>
<form class="form" action="" method="post" >
<div class="stayInformed">
	<ul class="updateForm">
    	<li>
        	<label></label>
            <div class="formRight"><span class="required">*</span> fields are required</div>
        </li>
        <li><label>First Name <span class="required">*</span></label>
        <div class="formRight"><input type="text" name="newsFirstName" value="<?php echo $this->input->post('newsFirstName');?>" />
		     <div class="error"><?php echo form_error('newsFirstName','',''); ?></div>
		   </div>
           </li>
           <li>
           	<label>Last Name <span class="required">*</span></label>
            <div class="formRight">
            	<input type="text" name="newsLastName" value="<?php echo $this->input->post('newsLastName');?>" />
		    <div class="error"><?php echo form_error('newsLastName','',''); ?></div>
            </div>
           </li>
           <li>
           	<label>Email <span class="required">*</span></label>
            <div class="formRight"><input type="text" name="newsEmail" value="<?php echo $this->input->post('newsEmail');?>" />
		   <div class="error"><?php echo form_error('newsEmail','',''); ?></div>
		   </div>
           </li>
           <li>
           		<label>Where did you hear about us?</label>
                <div class="formRight">
                	 <?php $hearabout_array = get_hearabout_array();
                                    $hear_about_array['']= 'Select';
                                    foreach($hearabout_array as $hearKey=>$hearValue):
                                        $hear_about_array[$hearKey]= $hearValue;
                                    endforeach; 
                           // echo form_dropdown('newsReferralMethod',$hear_about_array,'',"id='method_".$hearKey."' 
                             //   onchange=show_hide_div('4','".$hearKey."','display_other_option')");
                              echo form_dropdown('newsReferralMethod',$hear_about_array,'',"class='news_reffered' onchange=show_hide_div('4','display_other_option','news_reffered')");
                            ?>
                        <span class="display_other_option" style='display:none'>
                            <input type="text" name="newsReferralMethodOther" value="<?php echo $this->input->post('newsReferralMethodOther');?>" maxlength="255" />
                            <!--End RF1-->
                        </span>
                </div>
           </li>
           <li>
           		<label>What do you teach?</label>
                <div class="formRight">
                	<?php 
				$grade_subject_array=get_dropdown_array('tracks',$where_condition=array('cnfID'=>STATUS_PUBLISH),$order_by='nestedMenuOrder',$order='ASC','trName','trName','',true,array(''=>'Select'));
				echo form_dropdown('newsTeachesSubject',$grade_subject_array,$this->input->post('newsTeachesSubject'));
				
			?>
		
                </div>
           </li>
           <li>
           		<label>Grade level</label>
                <div class="formRight">
                	<?php 
				$grade_subject_array=get_dropdown_array('levels',$where_condition=array(),$order_by='lvlName',$order='ASC','lvlName','lvlName','',true,array(''=>'Select'));
				echo form_dropdown('newsGradeLevel',$grade_subject_array,$this->input->post('newsGradeLevel'));
			?>
                </div>
           </li>
           <li>
           		<label>In Which IU is you school district?</label>
                <div class="formRight">
                	<?php 
				$selected_iu = $this->input->post('newsIU');
				$iu_array=get_dropdown_array('iu_unit',$where_condition=array('iuPublish'=>STATUS_PUBLISH),$order_by='iuID',$order='ASC','iuName','iuName','',true,array(''=>'Select'));	
				echo form_dropdown('newsIU',$iu_array,$selected_iu,'id="iuUnitDropDown"');
			?>
                </div>
           </li>
           <li>
           		<label>School District</label>
                <div class="formRight">
                	 <div id="iuBasedDistrictContainer">
                                <?php 
                                 $selected_dis_unit =  $this->input->post('school_district');
                                $school_district_array=get_dropdown_array('district',$where_condition=array('disPublish'=>STATUS_PUBLISH),$order_by='disName',$order='ASC','disName','disName','',true,array(''=>'Select'));
                                echo form_dropdown('school_district',$school_district_array,$selected_dis_unit); ?>
                </div>
                </div>
           </li>
           <!--RF2--> 
           <li>   
                <label>Verification Code <span class="required">*</span></label>
                <div class="formRight">
                    <?php
                    //RF2
                    $vals = array(
                            'word' => random_string('alnum', 5),
                            'img_path'     => UPLOADS.'/captcha/',
                            'img_url'     => base_url().'uploads/captcha/',
                            'font_path'     => base_url().'system/fonts/texb.ttf',
                            'img_width'     => '120',
                            'img_height' => '28',
                            'expiration' => '3600'
                    );
                    $cap = create_captcha($vals);
                    $this->session->set_userdata('captcha_word',$cap['word']);
                    
                    ?>
                    <input id="captcha" name="captcha" type="text" />
                    <span  class="captcha">
                     <?php echo $cap['image']; ?>
                    </span>
                    <div class="error"><?php echo form_error('captcha','',''); ?></div>
                </div>
            </li>
            <!--End RF2--> 
           <li>
           		<label></label>
                <div class="formRight">
                	<input type="submit" value="Sign Up!" class="submit"/>
                </div>
           </li>
    </ul>
	</div>
	</form>
</div><!--#form-->
