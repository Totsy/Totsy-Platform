<?=$this->html->script('jquery-1.4.2.min.js');?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style('table');?>
<?=$this->html->style('admin');?>


<h1>Order Administration</h1>
<br>
<?=$this->form->create(null, array('enctype' => "multipart/form-data")); ?>
	<?=$this->form->label('Upload Order File: '); ?>
	<?=$this->form->file('upload'); ?>
	<?=$this->form->submit('Submit'); ?>
<?=$this->form->end(); ?>
<?php if (!empty($updated)): ?>
	<table id="orderTable" class="datatable" border="1" style="width: 500px">
		<thead>
			<tr>
				<?php
					foreach (array_keys($updated[0]) as $key){
						echo "<th>$key</th>";
					}
				?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($updated as $order): ?>
				<tr>
					<td>
					<?=$this->html->link($order['Order'], array(
						'Orders::view',
						'args' => $order['Order']),
						array('target' => '_blank')); ?>
					</td>
					<td><?=$order['SKU']?></td>
					<td><?=$order['First Name']?></td>
					<td><?=$order['Last Name']?></td>
					<td><?=$order['Ship Method']?></td>
					<td><?=$order['Tracking Number']?></td>
					<td><?=$order['Confirmation Number']?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$('#orderTable').dataTable();
	} );
</script>
