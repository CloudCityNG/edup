<?php /**@Page/Module Name/Class: 		events.php@Author Name:			 		ben binesh@Date:					 		Sept, 26 2013@Purpose:		        		display latest events list in sidebar@Table referred:				NIL@Table updated:					NIL@Most Important Related Files	NILChronological development***********************************************************************************Ref No.  |   Author name	| Date	 	| Modification description***********************************************************************************RF1	  |  Janet Rajani	 | 29 Nov,2013  | changed radio to drop-downRF2       |  Janet Rajani  |  Dec,26 2013  | minor  |  Added captcha */?>
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
	<div class="sidebar">
	<!-- sidebar start here-->	
	<?php if(isset($archives) && !empty($archives)): ?> 
		<div class="archive">
		<h2>Archives</h2>	
		<ul>
		<?php foreach($archives as $event): ?>	
		<li>			<a href="<?php echo get_seo_url('event',$event->nwID,$event->nwTitle); ?>"><?php echo $event->nwTitle; ?></a>			<span>Posted Date: <?php echo format_date($event->nwDate,'M d, Y'); ?></span>					
		</li>	
		<?php endforeach; ?>	
		</ul>
		</div>		<!--.archive-->
		<?php endif; ?>
		<div class="form">
		<h2>Stay Informed!</h2>
		<?php get_text(3);?>
		<div id="form">
		<div class="row clearfix">
		<div class="error_msg">	
		<?php echo validation_errors('<p>', '</p>');?>	
		<?php if(isset($errors) && count($errors)>0 ): 	
		foreach($errors as $error){		
		echo '<p>'.$error.'</p>';						}
		endif; ?>	
		</div>	
		</div>
		<form action="<?php echo base_url().'subscribe'; ?>" method="post" >
		<div class="row clearfix">	
		<div class="left_area">First Name <span class="required">*</span></div>
		<div class="right_area">	
		<input type="text" name="newsFirstName" value="<?php echo $this->input->post('newsFirstName');?>" />		     <div class="error"><?php echo form_error('newsFirstName','',''); ?></div>
		</div>		</div>
		
		<div class="row clearfix">	
		<div class="left_area">Last Name <span class="required">*</span></div>	
		<div class="right_area">	
		<input type="text" name="newsLastName" value="<?php echo $this->input->post('newsLastName');?>" />		  
		<div class="error"><?php echo form_error('newsLastName','',''); ?></div>		   </div>
		</div>	
		<div class="row clearfix">		   <div class="left_area">Email <span class="required">*</span></div>		   <div class="right_area">		   <input type="text" name="newsEmail" value="<?php echo $this->input->post('newsEmail');?>" />		   <div class="error"><?php echo form_error('newsEmail','',''); ?></div>		   </div>		</div>	
		<div class="row clearfix">	
		<div class="left_area">Where did you hear about us?</div>	
		<div class="right_area">  
		<!--RF1-->				<?php $hearabout_array = get_hearabout_array();                                        $hear_about_array['']= 'Select';					foreach($hearabout_array as $hearKey=>$hearValue):                                            $hear_about_array[$hearKey]= $hearValue;                                        endforeach; 				 echo form_dropdown('newsReferralMethod',$hear_about_array,'',"class='news_reffered' onchange=show_hide_div('4','display_other_option','news_reffered')");                                ?>                            <div class="display_other_option" style='display:none'>                                <input type="text" name="newsReferralMethodOther" value="<?php echo $this->input->post('newsReferralMethodOther');?>" maxlength="255" />                                <!--End RF1-->                            </div>			</div>	
		</div>      
		<div class="row clearfix">	
		<div class="left_area">What do you teach?</div>	
		<div class="right_area">			<?php 				$grade_subject_array=get_dropdown_array('tracks',$where_condition=array('cnfID'=>1),$order_by='nestedMenuOrder',$order='ASC','trName','trName','',true,array(''=>'Select'));				echo form_dropdown('newsTeachesSubject',$grade_subject_array,$this->input->post('newsTeachesSubject'));			?>		   </div>
		</div>	
		<div class="row clearfix">
		<div class="left_area">Grade Level</div>		 
		<div class="right_area">			<?php 				$grade_subject_array=get_dropdown_array('levels',$where_condition=array(),$order_by='lvlName',$order='ASC','lvlName','lvlName','',true,array(''=>'Select'));				echo form_dropdown('newsGradeLevel',$grade_subject_array,$this->input->post('newsGradeLevel'));			?>		   </div>	
		</div>	
		<div class="row clearfix">	
		<div class="left_area">In Which IU is you school district?</div>
		<div class="right_area">			<?php 				$selected_iu = $this->input->post('newsIU');				$iu_array=get_dropdown_array('iu_unit',$where_condition=array('iuPublish'=>STATUS_PUBLISH),$order_by='iuID',$order='ASC','iuName','iuName','',true,array(''=>'Select'));					echo form_dropdown('newsIU',$iu_array,$selected_iu,'id="iuUnitDropDown"');			?>		   </div>
		</div>
		<div class="row clearfix">
		<div class="left_area">District </div>	
		<div class="right_area">          
		<div id="iuBasedDistrictContainer">					<?php 					 $selected_dis_unit =  $this->input->post('newsSchoolDistrict');					$school_district_array=get_dropdown_array('district',$where_condition=array('disPublish'=>STATUS_PUBLISH),$order_by='disName',$order='ASC','disName','disName','',true,array(''=>'Select'));					echo form_dropdown('newsSchoolDistrict',$school_district_array,$selected_dis_unit); ?>             
		</div>
		</div>
		</div> 
		<!--RF2-->  
		<div class="row clearfix">      
		<div class="left_area">Verification Code <span class="required">*</span></div> 
		<div class="right_area">  
		<?php                    $vals = array(                            'word' => random_string('alnum', 5),                            'img_path'     => UPLOADS.'/captcha/',                            'img_url'     => base_url().'uploads/captcha/',                            'font_path'     => base_url().'system/fonts/texb.ttf',                            'img_width'     => '115',                            'img_height' => '28',                            'expiration' => '3600'                    );                    $cap = create_captcha($vals);                    $this->session->set_userdata('captcha_word',$cap['word']);                                                           ?>                     <input id="captcha" name="captcha" type="text" />                     <span class="captcha">                     <?php echo $cap['image']; ?>                     </span>                    <div class="error"><?php echo form_error('captcha','',''); ?></div>                                    </div>
		
		</div>
		<!--End RF2--> 
		<div class="row clearfix">  	
		<input type="submit" value="Sign Up!" class="button"/>		</div>	</form></div><!--#form--></div><!--.block--></div><!--.sidebar-->