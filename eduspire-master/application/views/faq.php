<div class="faqContent">
<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<script>
  $(function() {
    $( "#accordion" ).accordion({
      heightStyle: "content",
	  collapsible: true,
	  active: false
    });
	
	$(".expand_collapse_all").click(function(){
		if("expand_all"==$(this).attr('rel')){
			$(".expand_collapse_all").text('Collapse All');
			$(".expand_collapse_all").attr('rel','collapse_all');
			$('#accordion .ui-accordion-content').show();
			
		}else
		{
			$(".expand_collapse_all").text('Expand All');
			$(".expand_collapse_all").attr('rel','expand_all');
			$('#accordion .ui-accordion-content').hide();
		}
		
	})
	/*
	$('#accordion .ui-accordion-content').show();
	$(".expand_collapse_all").text('Collapse All');
	$(".expand_collapse_all").attr('rel','collapse_all');
	*/
	
});
</script>

<?php if(!empty($content)): ?>
<div class="expand_collapse"><a href="javascript:void(0)" rel="expand_all" class="expand_collapse_all">Expand All</a></div>


<div id="accordion">	
	<?php foreach($content as $faq):  ?>
	<?php  if(VISITORS == $faq->intendedAudience && 6 != $faq->faqID ):?>
		<h4><?php echo $faq->question ?></h4>
		<div class="faq"><?php echo $faq->answer; ?></div>	
	<?php endif;?>
	
	<?php endforeach; ?>
	
	
	<?php if(is_logged_in()): ?>
		<?php foreach($content as $faq):  ?>
			<?php  if(MEMBERS == $faq->intendedAudience && 12 != $faq->faqID ):?>
				<h4><?php echo $faq->question ?></h4>
				<div class="faq"><?php echo $faq->answer; ?></div>	
			<?php endif;?>
		<?php endforeach; ?>	
		
	<?php endif;?>
</div><!--#accordion-->

<div class="expand_collapse"><a href="javascript:void(0)" rel="expand_all" class="expand_collapse_all">Expand All</a></div>
</div>
<?php endif; ?>
