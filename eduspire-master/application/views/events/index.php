<?php 
/**
@Page/Module Name/Class: 		index.php
@Author Name:			 		ben binesh
@Date:					 		Sept, 26 2013
@Purpose:		        		display events list 
@Table referred:				NIL
@Table updated:					NIL
@Most Important Related Files	NIL
 */
?>
<div class="publicTitle"><h1><?php echo isset($this->page_title)?$this->page_title:' '; ?></h1></div>
<?php if(isset($events) && !(empty($events))): ?>
	<div class="section group">
	<?php echo $pagination_links; ?>
	</div>
	<?php  foreach($events as $event):?>
	<div id="entry-<?php echo $event->nwID; ?>" class="newsEvent">
			
			<?php  
			$image='';
			$image =  get_event_image($event->nwDescription) ;
			if($image):
			?> 
			
			<div class="entry-image">
			<?php /*
			<img src="<?php echo $image; ?>" title="<?php echo $event->nwTitle; ?>" alt="<?php echo $event->nwTitle; ?>"/>
			*/?>
			<?php  echo $image; ?>
			</div>
			
			<?php endif; ?>
                        <h2>
                            <a href="<?php echo get_seo_url('event',$event->nwID,$event->nwTitle); ?>"><?php echo $event->nwTitle; ?></a>
                        </h2>
			<div class="posted-date">Posted Date: <?php echo format_date($event->nwDate,DATE_FORMAT); ?></div>
			<div class="entry-content">
			<?php echo get_excerpt($event->nwDescription); ?>
			</div>
		</div>
	<?php  endforeach; ?>	
<?php else: ?>

<p class="no-record">No record found </p>

<?php endif; ?>
