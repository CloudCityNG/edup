<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>

<div id="form">



		<div class="error_msg">

			  <?php if(isset($errors) && count($errors)>0 ): 

					foreach($errors as $error){

						echo '<p>'.$error.'</p>';	

					}

				endif; ?>					

		</div>

	<form class="form" action="" method="post" >

    <div class="contactForm">

			  <?php get_text(4); ?>

			 

		<div class="row clearfix">

		   <div class="left_area">First Name <span class="required">*</span></div>

		   <div class="right_area">

		   <input type="text" name="contFirstName" value="<?php echo $this->input->post('contFirstName');?>" />

		    <div class="error"><?php echo form_error('contFirstName','',''); ?></div>

		   </div>

		</div>

		

		<div class="row clearfix">

		   <div class="left_area">Last Name <span class="required">*</span></div>

		   <div class="right_area">

		   <input type="text" name="contLastName" value="<?php echo $this->input->post('contLastName');?>" />

		    <div class="error"><?php echo form_error('contLastName','',''); ?></div>

		   </div>

		</div>	

		<div class="row clearfix">

		   <div class="left_area">Email <span class="required">*</span></div>

		   <div class="right_area">

		   <input type="text" name="contEmail" value="<?php echo $this->input->post('contEmail');?>" />

		   <div class="error"><?php echo form_error('contEmail','',''); ?></div>

		</div>

		</div>

		<div class="row clearfix">

		   <div class="left_area">Message </div>

		   <div class="right_area">

			<textarea name="contMessage"  class="tinymce-editor"><?php echo $this->input->post('contMessage');?></textarea>

			<div class="error"><?php echo form_error('contMessage','',''); ?></div>

		   </div>

		</div>

		

		<div class="row clearfix">

                    <?php

                    //RF1

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

		   <div class="left_area">Verification Code <span class="required">*</span> </div>

		   <div class="right_area">

			<input id="captcha" name="captcha" type="text" />

                       <span class="captcha">

                     <?php echo $cap['image']; ?>

                       </span>

                        <div class="error"><?php echo form_error('captcha','',''); ?></div>

                    <!--End RF1-->

		   </div>

		</div>

		

		<div class="row clearfix">  

			<div class="left_area">&nbsp;</div>

			<div class="right_area">

				<input type="submit" value="Send" class="submit"/>

			</div>

		</div>

        </div>

	</form>

</div><!--#form-->

