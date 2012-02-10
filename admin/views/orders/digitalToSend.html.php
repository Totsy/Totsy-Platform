<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->script('TableTools.min.js');?>
<?php echo $this->html->script('ZeroClipboard.js');?>
<?php echo $this->html->style('jquery_ui_blitzer.css')?>
<?php echo $this->html->style('TableTools');?>
<?php echo $this->html->style('timepicker'); ?>
<?php echo $this->html->style('table');?>
<?php echo $this->html->script('jquery-ui-1.8.2.custom.min.js');?>
<?php echo $this->html->script('jquery-ui-timepicker.min.js');?>

<div class="grid_16">
	<h2 id="page-heading">
		Follows Digital Items Progress
	</h2>
</div>

<div class="grid_16">
	<table border="1">
		<thead>
			<tr>
				<th>Order ID</th>
				<th>Order Date</th>
				<th>User Email</th>
				<th>Description</th>
				<th width="10%">Quantity</th>
				<th width="15%">Sent To Customer</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($lineItems)): ?>
			<?php
				$x=0;
				foreach ($lineItems as $item):
			?>
			<tr>
				<td>
					<a href="/orders/view/<?php echo $item['full_order_id'];?>"><?php echo $item['order_id']; ?></a>
				</td>
				<td>
					<?php echo date('m-d-Y', $item['date_created']->sec);?>
				</td>
				<td>
					<?php echo $item['email']; ?>
				</td>
				<td>
					<?php echo $item['description']; ?>
				</td>
				<td width="10%">
					<?php echo $item['quantity']; ?>
				</td>
				<td width="15%">
					<a href="/orders/markedDigitalItem?order_id=<?php echo $item['full_order_id'];?>&item_id=<?php echo $item['item_id'];?>" onclick="return markItemAsSent();">Yes</a>
				</td>
			</tr>
			<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>