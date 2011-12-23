<?php echo $this->html->style('table');?>
<div class="grid_16">
	<?php if (!empty($start_date)): ?>
		<h3>Showing Event '<?php echo $event_name?>' Details Running from <?php echo date('Y-m-d',$start_date)?> GMT to <?php echo date('Y-m-d',$end_date)?> GMT</h3>
	<?php endif ?>
	<?php if (!empty($hours_setup)): ?>
		<table id="summary_table" class="datatable" border="1">
			<thead style = "width: auto">
				<tr style = "width: auto">
					<th style = "width: 30px"></th>
					<?php foreach($stat as $key => $value) : ?>
					<th style = "width: auto"><?php echo $key?></th>
					<?php endforeach ?>
					<th>Average by hour</th>
				</tr>
				<tr style = "width: auto">
					<th style = "width: 30px"></th>
					<?php foreach($stat as $key => $value) : ?>
					<th style = "width: auto">
						Total / Quantity
					</th>
					<?php endforeach ?>
					<th></th>
				</tr>
			</thead>
		<?php foreach ($hours_setup as $hours): ?>
			<tr style = "width: auto">
				<td style = "width: auto"><?php echo $hours?>:00</td>
				<?php foreach($stat as $key => $value) : ?>
					<?php if(isset($stat[$key][$hours]['total'])) : ?>
						<td style = "width: auto">$<?php echo round($stat[$key][$hours]['total'],2)." / ".$stat[$key][$hours]['quantity']?></td>
					<?php else : ?>
						<td style = "width: auto"> / </td>
					<?php endif ?>
				<?php endforeach ?>
				<?php if(empty($total_hours[$hours]["average"])): ?>
					<td> 0 </td>
				<?php else :?>
					<td>$<?php echo $total_hours[$hours]["average"]?></td>
				<?php endif ?>
			</tr>
		<?php endforeach ?>
		<tfoot style = "width: auto">
		<tr>
			<td>Total</td>
			<?php foreach($total_days as $days) : ?>
				<td>$<?php echo round($days['total'],2)?> / <?php echo $days['quantity']?></td>
			<?php endforeach ?>
			<td>$<?php echo round($total,2)?> / <?php echo $total_quantity?></td>
		</tr>
		</tfoot>
	<?php endif ?>
</div>