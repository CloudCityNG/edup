<?php 
/**
@Page/Module Name/Class: 	    index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display slides list 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="flash_message">
  <?php get_flash_message(); ?>
</div>
<div class="top_form">
  <h3>Filters</h3>
  <form name="search-form" action="<?php echo base_url().'edu_admin/slide/index' ?>">
    <ul class="manageSlide">
      <li>
        <label>Title</label>
        <input type="text" name="title" value="<?php echo $title; ?>"/>
      </li>
      <li>
        <label>Status</label>
        <?php echo form_dropdown('status',$this->slide_model->get_status_array(true),$status); ?></li>
    </ul>
    <div class="formButton">
      <input type="submit" class="submit" value="Search"/>
      <?php echo anchor('edu_admin/slide/','Reset','class="submit"'); ?> </div>
  </form>
</div>
</div>
<div class="result_container"> 
<div class="addRecord">
      <?php
			echo anchor('edu_admin/slide/create/','Add Record','class="submit"');
		
	?>
    </div>
 <?php if(isset($results) && count($results)>0): ?>
                    
  <div class="adminGrid">
    
    <table class="table striped" cellspacing="0" width="100%" id="grid">
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Url</th>
        <th>Order</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <?php $i=0; ?>
      <?php foreach($results as $result): ?>
      <?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
      <tr class="<?php echo $tr_class; ?>">
        <td><?php echo $result->csID; ?></td>
        <td><?php echo $result->csTitle; ?></td>
        <td><?php echo $result->csUrl; ?></td>
        <td><?php echo $result->csOrder; ?></td>
        <td><?php echo $this->slide_model->show_status($result->csPublish); ?></td>
        <td><?php echo anchor('edu_admin/slide/update/'.$result->csID,'<img src="/images/edit.png" title="edit" alt="edit"/>');
				
				echo anchor('edu_admin/slide/delete/'.$result->csID,'<img src="/images/delete.png" title="delete" alt="delete"/>' ,'onclick=\'return confirm("Do you want to delete this record?"
			)\''); 
			
			?></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <?php echo $pagination_links; ?>
    <div class="pagnination_summary"> <?php echo pagination_summary();?> </div>
  </div>
  <?php else: ?>
  <p class="no_recored_fount">No record found</p>
  <?php endif; ?>
</div>
