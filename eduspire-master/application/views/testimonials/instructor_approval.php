<?php 
/**
@Page/Module Name/Class: 		instructor_approval.php
@Author Name:			 		Janet Rajani
@Date:					 		Nov, 12 2013
@Purpose:		        		Instructor can add/remove admin approved testimonials to his profile from here
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1>Approve testimonials</h1></div>
<div class="flash_message">
<?php get_flash_message(); ?>
</div>
<div class="top_form">
	<h3>Filters</h3>
	<form name="search-form" action="<?php echo base_url().'testimonials/instructor_approval' ?>">
            <ul class="manageType">
            
                <li><label>Status</label>
                    <?php echo form_dropdown('instructor_approved',$this->testimonials_model->get_status_array(true),$instructor_approved); ?>
                </li>
            </ul>
            <div class="formButton">
                    <input type="submit" class="submit" value="Search"/>
                    <?php echo anchor('testimonials/instructor_approval','Reset','class="submit"'); ?> 
            </div>
	</form>
    </div>
    <div class="result_container">
        <?php if(isset($results) && count($results)>0): ?>
              <form method="post" name="grid-form">
              <input type="hidden" name="mass_action" value="1"/>
              <table class="table striped" cellspacing="0" id="grid">
                <tr>
                    <th>
                        <input type="checkbox" name="check_all" id="check_all" value="1" onclick="checkall(this.form)"/>
                    </th>
                    <th>ID</th>
                    <th>Testimonial</th>
                    <th>Status</th>
                </tr>
                <?php $i=0; ?>
                <?php foreach($results as $result): ?>
                <?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
                <tr class="<?php echo $tr_class; ?>">
                    <td>
                        <input name="chk_ids[]" type="checkbox" class="checkbox" value="<?php echo $result->tID;?>"/>
                    </td>
                    <td>
                        <?php echo $result->tID; ?>
                    </td>
                    <td>
                        <?php echo $result->tTestimonial; ?>
                    </td>
                    <td>
                        <?php echo ($result->instructor_approved==TESTIMONOAL_APPROVED)?'Approved':'Disapproved'; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                <td colspan="7" class="massaction">
                    <input type="submit" class="submit" name="activate" value="Approve" onclick=" return check()"/>
                    <input type="submit" class="submit" name="deactivate" value="Disapprove" onclick=" return check()"/>
                </td>	
                </tr>
              	
                </table>
                </form>
                <?php echo $pagination_links; ?>
                <div class="pagnination_summary">
                    <?php echo pagination_summary();?>
                </div>
        <?php else: ?>
                <p class="no_recored_fount">No record found</p>
        <?php endif; ?>
		
    </div>