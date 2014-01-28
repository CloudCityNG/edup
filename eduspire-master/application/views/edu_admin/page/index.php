<?php 
/**
@Page/Module Name/Class: 	    index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display cms pages list 
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
<form name="search-form" action="<?php echo base_url().'edu_admin/page/index' ?>">

  <ul class="manageContent">
    <li>
      <label>Title</label>
      <input type="text" name="title" value="<?php echo $title; ?>"/>
    </li>
    <li>
      <label>Url Key</label>
      <input type="text" name="url_key" value="<?php echo $url_key; ?>"/>
    </li>
    <li>
      <label>Status</label>
      <?php echo form_dropdown('status',$this->page_model->get_status_array(true),$status); ?></li>
  </ul>
  
   <div class="formButton">
    <input type="submit" class="submit" value="Search"/>
    <?php echo anchor('edu_admin/page/','Reset','class="submit"'); ?> 
  </div>
  </div>
 
</form>
</div>
</div>
<div class="result_container">
  <?php if(isset($results) && count($results)>0): ?>
  
  	<div class="addRecord"> <?php echo anchor('edu_admin/page/create/','Add Record','class="submit"'); ?></div>
    <table class="table striped" cellspacing="0" width="100%" id="grid">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Title</th>
        <th>Url Key</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <?php $i=0; ?>
      <?php foreach($results as $result): ?>
      <?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
      <tr class="<?php echo $tr_class; ?>">
        <td><?php echo $result->cpID; ?></td>
        <td><?php echo $result->cpName; ?></td>
        <td><?php echo $result->cpTitle; ?></td>
        <td><?php echo $result->cpUrlKey; ?></td>
        <td><?php echo $this->page_model->show_status($result->cpPublish); ?></td>
        <td><?php echo anchor('edu_admin/page/update/'.$result->cpID,'<img src="/images/edit.png" title="edit" alt="edit"/>'); ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php echo $pagination_links; ?>
  <div class="pagnination_summary"> <?php echo pagination_summary();?> </div>
  <?php else: ?>
  <p class="no_recored_fount">No record found</p>
  <?php endif; ?>
</div>
