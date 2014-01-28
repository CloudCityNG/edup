<?php 
/**
@Page/Module Name/Class:            index.php
@Author Name:                       Janet Rajani
@Date:                              Sept, 30 2013
@Purpose:		            display all survey questions
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1>Questionnaire Title</h1></div>
<div id="form">
    
	<form name="search-form" action="" method="post">
            <ul class="updateForm">
                 <li>
		<label>Title<span class="required">*</span></label>
                <label class="right">
                  <input type="text" name="qTitle" value="<?php echo isset($qTitle)?$qTitle:$this->input->post('qTitle'); ?>"/>
                  <div class="error">
                    <?php echo validation_errors('<p>', '</p>');?>
                  </div>
                </label>
		</li>
		<li>
                <label></label>
                <label class="right">
                    <input type="submit" class="submit" value="Submit"/>
                    <?php echo anchor('edu_admin/questionnaire/','Cancel','class="submit"'); ?> 
                </label>
		</li>
              </ul>
	</form>
</div>