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
		FulFilled Digital Items
	</h2>
</div>

<div class="grid_16">
	<table border="1">
		<thead>
			<tr>
				<th>Order ID</th>
				<th>Order Date</th>
				<th>Date Sent to Customer</th>
				<th>User Email</th>
				<th>Description</th>
				<th width="10%">Quantity</th>
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
					<?php echo date('m-d-Y', $item['date_sent']->sec);?>
				</td>
				<td>
					<a href="/users/view/<?php echo $item['user_id'];?>"><?php echo $item['email']; ?></a>
				</td>
				<td>
					<?php echo $item['description']; ?>
				</td>
				<td width="10%">
					<?php echo $item['quantity']; ?>
				</td>
			</tr>
			<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>