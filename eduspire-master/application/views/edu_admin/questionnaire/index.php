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
<script>
jQuery(document).ready(function($) {

        $(".fancybox").click(function() {
		$.fancybox.open({
			href : jQuery(this).attr('href'),
			type : 'iframe',
			padding : [5,5,5,10],
                        margin: [115, 0, 10, 0] // top, right, bottom, left
		});
		return false;
                });
});
</script>
<div class="publicTitle"><h1>Manage Survey</h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
	<h3>Filters</h3>
	<form name="search-form" action="<?php echo base_url().'edu_admin/questionnaire/index' ?>">
            <ul class="manageContent">
                <li>
                  <label>Title</label><input type="text" name="qTitle" value="<?php echo $qTitle; ?>"/>
                </li>
            </ul>
        <div class="formButton">
            <input type="submit" class="submit" value="Search"/>
            <?php echo anchor('edu_admin/questionnaire/','Reset','class="submit"'); ?>
        </div>
        </form>
</div>
<div class="addRecord">
	 <?php echo anchor('edu_admin/questionnaire/questionnaire_title/','Add Questionnaire','class="submit"'); ?> 
</div>
<div class="result_container">
        <?php if(isset($results) && count($results)>0): ?>
                <input type="hidden" name="mass_action" value="1"/>
                <table class="table striped" cellspacing="0" width="100%" id="grid">
               
                <tr>
                    <th >ID</th>
                    <th>Title</th>
                    <th>Questions</th>
                    <th></th>
                </tr>
                <?php $i=0; 
                 foreach($results as $result): 
                 $tr_class = ($i++%2==0)?'even':'odd'; ?>
                <tr class="<?php echo $tr_class; ?>">
                    <td ><?php echo $result['qID']; ?></td>
                    <td>
                            <div><?php echo $result['qTitle']; ?></div>
                    </td>
                    <!---The query is returning 1 if the total questions of the questionnaire are zero-->
                    <td><?php echo ($result['totalQuestions']==1)?'0':$result['totalQuestions']; ?></td>
                    <td>
                         <?php  
                         echo anchor('edu_admin/questionnaire/manage_questions/'.$result['qID'],' Questions '); 
                         echo anchor('edu_admin/questionnaire/preview_survey_form/'.$result['qID'],' <img src="/images/view.png" title="preview" alt="preview"/> ','class="fancybox"');
                         echo anchor('edu_admin/questionnaire/questionnaire_title/'.$result['qID'],' <img src="/images/edit.png" title="edit" alt="edit"/> '); 
                        //If the questionnaire assignment is not submitted by any student then admin can delete
                        if($result['total_submission']==0)
                        {
                            echo anchor('edu_admin/questionnaire/delete_questionnaire/'.$result['qID'],' <img src="/images/delete.png" title="delete" alt="delete"/> ','onclick=\'return confirm("Do you want to delete this record?")\'');
                        }
                      ?>
                    </td>
                </tr>
                <?php endforeach; ?>
               	
                </table>
                <?php echo $pagination_links; ?>
                <div class="pagnination_summary">
                        <?php echo pagination_summary();?>
                </div>
        <?php else: ?>
                <p class="no_recored_fount">No record found</p>
        <?php endif; ?>
</div>