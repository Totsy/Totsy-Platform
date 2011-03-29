<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->script('TableTools.min.js');?>
<?=$this->html->script('ZeroClipboard.js');?>
<?=$this->html->style('jquery_ui_blitzer.css')?>
<?=$this->html->style('TableTools');?>
<?=$this->html->style('table');?>
<?=$this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?php use admin\models\Event;?>
<div class="grid_16">
	<h2 id="page-heading">Order Management</h2>
</div>
<div class="grid_6">
	<div class="box">
	<h2>
		<a href="#" id="toggle-queue">Currently in Queue</a>
	</h2>
	<div class="block" id="queue">
		<?php if (!empty($queue)): ?>
			<table border="0" cellspacing="5" cellpadding="5">
				<tr>
					<th>#</th>
					<th>Date</th>
					<th>Event Orders Queued</th>
					<th>Event POs Queued</th>
					<th>View</th>
				</tr>
			<?php $i = 0; ?>
			<?php foreach ($queue as $data): ?>
				<?php $i++?>
					<tr>
						<td><?=$i?></td>
						<td><?=date('m-d-Y', $data['created_date']->sec)?></td>
						<td><?=count($data['orders'])?></td>
						<td><?=count($data['purchase_orders'])?></td>
						<td><?=$this->html->link('View Details', array('Queue::view', 'args' => $data['_id'])); ?></td>
					</tr>
			<?php endforeach ?>
			<?php if ($i == 0): ?>
				<td colspan="5"><center>Nothing in the queue!</center></td>
			<?php endif ?>
			</table>
		<?php else: ?>
			<h4>Nothing in the queue!</h4>
		<?php endif ?>
	</div>
	</div>
</div>
<div class="grid_10">
	<div class="box">
	<h2>
		<a href="#" id="toggle-recent">Recently Processed</a>
	</h2>
	<div class="block" id="recent">
		<?php if (!empty($recent)): ?>
			<table id ="summary_table" border="0" cellspacing="5" class="datatable" cellpadding="5">
				<thead>
				<tr>
					<th>Date</th>
					<th>Orders Processed</th>
					<th>Order Lines</th>
					<th>POs Processed</th>
					<th>View</th>
				</tr>
				</thead>
			<tbody>
			<?php $i = 0; ?>
			<?php foreach ($recent as $data): ?>
				<?php $i++?>
					<tr>
						<td><?=date('m-d-Y', $data['processed_date']->sec)?></td>
						<?php if (!empty($data['summary']['order']['count'])): ?>
							<td><?=$data['summary']['order']['count']?></td>
						<?php else: ?>
							<td>0</td>
						<?php endif ?>
						<?php if (!empty($data['summary']['order']['lines'])): ?>
							<td><?=$data['summary']['order']['lines']?></td>
						<?php else: ?>
							<td>0</td>
						<?php endif ?>
						<?php if (!empty($data['summary']['purchase_orders'])): ?>
							<td><?=count($data['summary']['purchase_orders'])?></td>
						<?php else: ?>
							<td>0</td>
						<?php endif ?>
						<td><?=$this->html->link('View Details', array('Queue::view', 'args' => $data['_id'])); ?></td>
					</tr>
			<?php endforeach ?>
			</tbody>
			</table>
		<?php else: ?>
			<h4>Nothing in the queue!</h4>
		<?php endif ?>
	</div>
	</div>
</div>

<!-- <div class="grid_6">
	<div class="box">
	<h2>
		<a href="#" id="toggle-order-search">Event Search</a>
	</h2>
	<div class="block" id="order-search">
		<fieldset>
			<p>Search for a specific event(s)</p>
			<?=$this->form->create(); ?>
				<?=$this->form->text('search', array(
					'id' => 'search',
					'style' => 'float:left; width:200px; margin: 0px 10px 0px 0px;'
					));
				?>
				<?=$this->form->submit('Search'); ?>
			<?=$this->form->end(); ?>
		</fieldset>
	</div>
	</div> -->
</div>

<div class="clear"></div>

<div class="grid_16">
	<?=$this->form->create(null, array('url' => 'Queue::add')); ?>
	<?php if (!empty($events)): ?>
		<table id="eventTable" class="datatable" border="1">
			<thead>
				<tr>
					<th>Event Name</th>
					<th>Event End Date</th>
					<th>Event PO #</th>
					<th><center>Queue Order</center></th>
					<th><center>Queue PO</center></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($events as $event): ?>
					<tr>
						<td><?=$event->name?></td>
						<td><?=date('m-d-Y', $event->end_date->sec)?></td>
						<td><?=Event::poNumber($event)?></td>
						<td>
						<?php if (in_array((string) $event->_id, $processedOrders)): ?>
							<center>Processed</center>
						<?php else: ?>
							<center><input type="checkbox" name="orders[]" value="<?=$event->_id?>" /></center>
						<?php endif ?>
						</td>
						<td>
						<?php if (in_array((string) $event->_id, $processedPOs)): ?>
							<center>Processed</center>
						<?php else: ?>
							<center><input type="checkbox" name="pos[]" value="<?=$event->_id?>" /></center>
						<?php endif ?>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	<?php endif ?>
</div>
<div class="grid_16">
<?=$this->form->submit('Submit'); ?>
<?=$this->form->end(); ?>
</div>
<div id="clear"></div>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#eventTable').dataTable({
			"sDom": 'T<"clear">lfrtip'
		}
		);
	} );
</script>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#summary_table').dataTable({
			"sDom": 'T<"clear">lfrtip'
		}
		);
	} );
</script>