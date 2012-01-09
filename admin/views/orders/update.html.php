<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->style('TableTools');?>


<h1>Order Administration</h1>
<br>
<?php echo $this->form->create(null, array('enctype' => "multipart/form-data")); ?>
	<input type="radio" name="send_email" value="1" id="send_email" checked> Send Email <br>
	<input type="radio" name="send_email" value="0" id="send_email"> Dont Send Email
	<br>
	<?php echo $this->form->label('Upload Order File: '); ?>
	<?php echo $this->form->file('upload'); ?>
	<?php echo $this->form->submit('Submit'); ?>
<?php echo $this->form->end(); ?>
<?php if (!empty($updated)): ?>
	<table id="orderTable" class="datatable" border="1">
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
					<?php echo $this->html->link($order['Order'], array(
						'Orders::view',
						'args' => $order['Order']),
						array('target' => '_blank')); ?>
					</td>
					<td><?php echo $order['SKU']?></td>
					<td><?php echo $order['First Name']?></td>
					<td><?php echo $order['Last Name']?></td>
					<td><?php echo $order['Ship Method']?></td>
					<td><?php echo $order['Tracking Number']?></td>
					<td><?php echo $order['Confirmation Number']?></td>
					<td><?php echo $order['Errors'][0]?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#orderTable').dataTable({
			"sDom": 'T<"clear">lfrtip'
		}
		);
	} );
</script>
