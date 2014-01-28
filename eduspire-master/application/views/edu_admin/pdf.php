<div class="result_container">
		<?php if(isset($results) && count($results)>0): ?>
			<table class="table">
			<tr>
				<td colspan="6" class="summary">
				<div class="pagnination_summary">
					
				</div>
				</td>
			</tr>	
			
			<tr>
				<?php $columns=$results[0] ?>
				<?php foreach($columns as $column): ?>
				<th><?php echo $column; ?></th>
				
				<?php endforeach; ?>
			</tr>
			
			<?php 
			$i=0;
			foreach($results as $result): 
			$i=$i+1;
			if($i==1){
				//skip the first element it contain the column heading 
				continue;
			}
			
			?>
			<tr>
			<?php $record = $result; ?>
			<?php foreach($record as $r): ?>
				<td><?php echo $r; ?></td>
				
				<?php endforeach; ?>
			</tr>
			
			<?php endforeach; ?>
			</table>
			</form>	
		
		
		<?php endif; ?>
		
</div>