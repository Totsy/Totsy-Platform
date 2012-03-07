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

<script type="text/javascript" charset="utf-8">
	$(function() {
		var dates = $('#min_date, #max_date').datetimepicker({
			defaultDate: "+1w",
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 1,
			onSelect: function(selectedDate) {
				var option = this.id == "min_date" ? "minDate" : "maxDate";
				var instance = $(this).data("datetimepicker");
				var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
				dates.not(this).datepicker("option", option, date);
			}
		});
	});
</script>
<div class="grid_16">
	<h2 id="page-heading">Order Management</h2>
	<h5>Note: Authorize .Net keys that are not numeric are not real Auth.Net keys. <br/> These are from orders that can't be captured because the order total was $0. </h5>
</div>

<div id="clear"></div>


<div id="clear"></div>
<div class="grid_16">
	<div class="box">
	<h2>
		<a href="#" id="toggle-order-search">Search</a>
	</h2>
	<div class="block" id="order-search">
		<fieldset>
			<?php echo $this->form->create(); ?>
				<?php echo $this->form->text('search', array(
					'id' => 'search',
					'style' => 'float:left; width:400px; margin: 0px 10px 0px 0px;'
					));
				?>
				<?php echo $this->form->select('type', array(
					'order' => 'Order #',
					'email' => 'Customer Email',
					'name' => 'Shipping/Billing Name',
					'address' => 'Shipping/Billing Address',
					'event' => 'Event Name',
					'authKey' => 'Authorize.net Key',
					'item' => 'Item Description'
					), array('style' => 'float:left; width:250px; margin: 0px 20px 0px 0px;'));
				?>
				<?php echo $this->form->submit('Submit'); ?>
			<?php echo $this->form->end(); ?>
		</fieldset>
	</div>
	</div>
</div>

<div id="clear"></div>
<div id="clear"></div>
<div class="grid_16">
<?php if (!empty($orders)): ?>
	<table id="orderTable" class="datatable" border="1">
		<thead>
			<tr>
				<?php
				foreach ($headings as $heading) {
					echo "<th>$heading</th>";
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($orders as $order): ?>
				<tr>
					<td><?php echo date('m-d-Y', $order['date_created']->sec);?></td>
					<td>
						<?php echo $this->html->link($order['order_id'], array(
						'Orders::view',
						'args'=>$order['_id']),
						array('target' => '_blank'));
						?>
					</td>
					<td>
						<?php echo $order['authKey']?>
					</td>
					<td>
						<?php
							$events = array();
							foreach ($order['items'] as $item) {
								if (!empty($item['event_name'])) {
									if (!(in_array($item['event_name'], $events))) {
										$events[] = $item['event_name'];
										echo "$item[event_name]\n";
									}
								}
							}
						?>
					</td>
					<td>
						<div>
						<?php if (!empty($order['billing'])): ?>
							<?php echo $order['billing']['firstname']?>
							<?php echo $order['billing']['lastname']?><br>
							<?php echo $order['billing']['address']?>
							<?php if (!empty($order['billing']['city'])): ?>
								<?php echo $order['billing']['city']?>
							<?php endif ?>
							<?php echo $order['billing']['state']?> <?php echo $order['billing']['zip']?>
							<?php if (!empty($order['billing']['telephone'])): ?>
								<br><?php echo $order['billing']['telephone']?>
							<?php endif ?>
						<?php endif ?>
						</div>
					</td>
					<td>
						<?php if (!empty($order['shipping'])): ?>
							<?php echo $order['shipping']['firstname']?>
							<?php echo $order['shipping']['lastname']?><br>
							<?php echo $order['shipping']['address']?><br>
							<?php if (!empty($order['shipping']['address_2'])): ?>
								<?php echo $order['shipping']['address_2']?>
							<?php endif ?>
							<?php if (!empty($order['shipping']['city'])): ?>
								<?php echo $order['shipping']['city']?>
							<?php endif ?>
							<?php echo $order['shipping']['state']?> <?php echo $order['shipping']['zip']?>
						<?php endif ?>
					</td>
					<td>$<?php echo number_format($order['total'],2);?></td>
					<?php if (!empty($order['tracking_numbers'])): ?>
						<td>
						<?php foreach ($order['tracking_numbers'] as $number): ?>
							<?php echo $this->shipment->link($number, array('type' => $order['shippingMethod']))?>
						<?php endforeach ?>
						</td>
					<?php else: ?>
							<td>Not Shipped/No Tracking #</td>
					<?php endif ?>
					<td><?php echo date('M d, Y', $shipDate["$order[_id]"])?></td>
					<td>
						<?php 
							if(!empty($order['auth_error'])) {
								echo $order['auth_error'];
							} else {
								echo "No Message";
							}
						?>
					</td>
					<td>
						<?php echo $this->html->link('View', array(
						'Users::view',
						'args'=>$order['user_id']),
						array('target' => '_blank'));
						?>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
</div>
<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		TableToolsInit.sSwfPath = "/img/flash/ZeroClipboard.swf";
		$('#orderTable').dataTable({
			"sDom": 'T<"clear">lfrtip'
		}
		);
	} );
</script>
