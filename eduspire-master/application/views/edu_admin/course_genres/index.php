<div class="adminTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<div class="flash_message">
  <?php get_flash_message(); ?>
</div>
<div class="top_form">
  <form name="search-form" action="<?php echo base_url().'edu_admin/course_genres/index' ?>">
    <h3>Filters</h3>
    <ul class="manageType">
      <li>
        <label>Title</label>
        <input type="text" name="title"value="<?php echo $title; ?>"/>
      </li>
      <li>
        <label>Status</label>
        <?php echo form_dropdown('status',$this->course_genres_model->get_status_array(true),$status); ?></li>
    </ul>
    <div class="formButton">
    
        <input type="submit" class="submit" value="Search"/>
        <?php echo anchor('edu_admin/course_genres/','Reset','class="submit"'); ?> </div>
    
  </form>
</div>
</div>
<div class="result_container">
<div class="adminGrid">
  <div class="addRecord">
    <?php if(is_allowed('edu_admin/course_genres/create')): ?>
    <?php echo anchor('edu_admin/course_genres/create/','Add Record','class="submit"'); ?>
    <?php endif; ?>
  </div>
<?php if(isset($results) && count($results)>0): ?>
                    
  <table class="table striped" cellspacing="0" width="100%" id="grid">
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Display Order</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
    <?php $i=0; ?>
    <?php foreach($results as $result): ?>
    <?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
    <tr class="<?php echo $tr_class; ?>">
      <td><?php echo $result->cgID; ?></td>
      <td><?php echo $result->cgTitle; ?></td>
      <td><?php echo $result->cgDisplayOrder; ?></td>
      <td><?php echo $this->course_genres_model->show_status($result->cgPublish); ?></td>
      <td><?php if(is_allowed('edu_admin/course_genres/update')): 
				
				echo anchor('edu_admin/course_genres/update/'.$result->cgID,'<img src="/images/edit.png" title="edit" alt="edit"/>'); 
				 endif ;
			
			 if(is_allowed('edu_admin/course_genres/delete')): 
				 echo anchor('edu_admin/course_genres/delete/'.$result->cgID,'<img src="/images/delete.png" title="delete" alt="delete"/>','onclick=\'return confirm("Do you want to delete this record?"
				)\''); 
			 endif ;?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  </div>
  <?php echo $pagination_links; ?>
  <div class="pagnination_summary"> <?php echo pagination_summary();?> </div>
  <?php else: ?>
  <p class="no_record">No record found</p>
  <?php endif; ?>
</div>
