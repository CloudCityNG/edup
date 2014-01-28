<?php 
/**
@Page/Module Name/Class: 		index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display add/edit form for inventory(ipad)
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
  <form name="search-form" action="<?php echo base_url().'edu_admin/inventory/index' ?>">
    <ul class="store">
      <li>
        <label>Subcategory</label>
        <?php echo form_dropdown('subcategory',$this->inventory_model->get_subcategory_array(true),$subcategory); ?></li>
      <li>
        <label>Status</label>
        <?php echo form_dropdown('status',$this->inventory_model->get_status_array(true),$status); ?></li>
    </ul>
    <div class="formButton">
      <input type="submit" class="submit" value="Search"/>
      <?php echo anchor('edu_admin/inventory/','Reset','class="submit"'); ?> </div>
  </form>
  </fieldset>
</div>
</div>
<div class="result_container">
<div class="adminGrid">
  <div class="addRecord"> <?php echo anchor('edu_admin/inventory/create/','Add Product','class="submit"'); ?> </div>
  <?php if(isset($results) && count($results)>0): ?>
 
   <?php  $pagination_summary=pagination_summary();?> 
  <form method="post" name="grid-form">
    <input type="hidden" name="mass_action" value="1"/>
    <table class="table striped" cellspacing="0" width="100%" id="grid">
      <tr>
        <th><input type="checkbox" name="check_all" id="check_all" value="1" onclick="checkall(this.form)" /></th>
        <th>Title</th>
        <th>Price</th>
        <th>Sort Order</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <?php $i=0; ?>
      <?php foreach($results as $result): ?>
      <?php $tr_class = ($i++%2==0)?'even':'odd'; ?>
      <tr class="<?php echo $tr_class; ?>">
        <td ><input name="chk_ids[]" type="checkbox" class="checkbox" value="<?php echo $result->invID; ?>" /></td>
        <td><?php echo $this->inventory_model->show_subcategory($result->invSubcatID); ?>-<?php echo $result->invName; ?></td>
        <td><?php echo CURRENCY.$result->invPrice1; ?></td>
        <td><?php echo $result->invSortOrder; ?></td>
        <td><?php echo $this->inventory_model->show_status($result->invPublish); ?></td>
        <td><?php echo anchor('edu_admin/inventory/update/'.$result->invID,'<img src="/images/edit.png" title="edit" alt="edit"/>'); ?> <?php echo anchor('edu_admin/inventory/copy/'.$result->invID,'<img src="/images/copy.png" title="copy" alt="copy"/>'); ?> <?php echo anchor('edu_admin/inventory/delete/'.$result->invID,'<img src="/images/delete.png" title="delete" alt="delete"/>' ,'onclick=\'return confirm("Do you want to delete this record?"
			)\''); ?></td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="7" class="massaction"><input type="submit" class="submit" name="activate" value="Activate" onclick=" return check()"/>
          <input type="submit" class="submit" name="deactivate" value="Deactivate"  onclick=" return check()"/></td>
      </tr>
    </table>
    </div>
    <?php echo $pagination_links; ?>
    <div class="pagnination_summary"> <?php echo $pagination_summary;?> </div>
  </form>
  <?php else: ?>
  <p class="no_recored_fount">No record found</p>
  <?php endif; ?>
</div>
