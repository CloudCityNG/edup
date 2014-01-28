<?php 
/**
@Page/Module Name/Class:            view_response_email.php
@Author Name:                       Janet Rajani
@Date:                              Oct, 2 2013
@Purpose:		            Send the user an email
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<script>
    <?php 
	if(isset($url)){
		echo('top.location.href ="'.$url.'";');
	}	
	if(isset($reload)){
		echo('parent.location.reload(true);');
	}
	
 ?>
</script>
<div class="popupTitle"><h1>Send email</h1></div>
<div class="error_msg">
    <div class="flash_message">
        <?php get_flash_message(); ?>
    </div>
</div>
<div class="result_container">
        <form method="post" action="">
            <ul class="updateForm">
                 <li>
		   <label>To<span class="required">*</span></label>
		   <label class="right">
                    <input type="text" name="email_address" value="<?php echo $this->input->post('email_address')?$this->input->post('email_address'):$email; ?>"  maxlength="255" size="40" readonly/>
		   </label>
		</li> 
		
		<li>
		   <label>Subject:</label>
		   <label class="right">
			<input type="text" name="subject" maxlength="255" size="40"/>
		   </label>
		</li> 
                <li>
		   <label>Message<span class="required">*</span></label>
		   <label class="right">
			<textarea name="message" rows="5"></textarea>
                        <div class="error">
                            <?php echo validation_errors('<p>', '</p>');?>
                        </div>
		   </label>
		</li> 
                <li>
		   <label></label>
		   <label class="right">
			<input type="submit" value="Send" name="email_user" class="submit"/>
		   </label>
		</li> 
            </ul>
        </form>
</div>