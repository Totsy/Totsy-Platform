<h2>My Orders</h2>
	<hr />
	<?php if (!empty($lifeTimeSavings)) : ?>
	<div class="holiday_message"><p style="font-size:14px;">Your Total Lifetime Savings: <strong style="color:#009900;">$<?php echo number_format((float) $lifeTimeSavings, 2);?></strong></p></div>
		<?php endif ?>

<?php if ($orders->data()): ?>		
<div data-role="collapsible-set">
<?php foreach ($orders as $order): ?>
<?php if(empty($order->cancel)): ?>
	<div data-role="collapsible" data-collapsed="true">
	<h3>Order #<?php echo $order->order_id; ?><br />
	<span style="font-size:11px; color:#999!important; text-decoration:none!important;">Order placed on <?php echo date('M d, Y', $order->date_created->sec); ?></span>
	</h3>
	
	<p style="text-align:center;"><strong>Order Summary</strong> - <?php if (!empty($order->order_id)): ?><a href="#" onclick="window.location.href='/orders/view/<?php echo $order->order_id; ?>';return false;">View Details</a><?php endif ?></p>
	<hr />
	<p><?php if ($order->items): ?><?php $items = $order->items->data() ?><?php endif ?></p>
	<p><?php foreach ($items as $item): ?>
							<?php if(empty($item["cancel"])) : ?>
								<strong><?php echo $item['description']?></strong><br />
								<span style="font-size:12px;">Color: <?php echo $item['color']?></span><br />
								<span style="font-size:12px;">Size: <?php echo $item['size']?></span><br />
								<span style="font-size:12px;">Quantity: <?php echo $item['quantity']?></span><br /><br />
							<?php endif ?>
							
						<?php endforeach ?></p>
	<p><?php if (!empty($trackingNumbers) || !empty($order->tracking_numbers)): ?>
								Tracking Number(s):
								<?php if ($trackingNumbers): ?>
									<?php if (!empty($trackingNumbers["$order->_id"])): ?>
										<?php foreach ($trackingNumbers["$order->_id"] as $trackingNumber): ?>
											<?php echo $this->shipment->link($trackingNumber['code'], array('type' => $trackingNumber['method']))?>
										<?php endforeach ?>
									<?php endif ?>
								<?php endif ?>
								<?php if (!empty($order->tracking_numbers)): ?>
									<?php foreach ($order->tracking_numbers as $number): ?>
										<?php echo $this->shipment->link($number, array('type' => 'UPS'))?>
									<?php endforeach ?>
								<?php endif ?>
							<?php else: ?>
								<?php if ($shipDate["$order->_id"] > time()): ?>
									Estimated Ship Date: <br/><?php echo date('M d, Y', $shipDate["$order->_id"]); ?> 
								<?php else: ?>
								<?php endif ?>
						<?php endif ?></p>
	</div>
<?php endif ?>
<?php endforeach ?>
</div>

<?php else: ?>
	<div class="holiday_message"><center><strong>You do not have any orders. <a href="/sales" title="Go Shopping">Go Shopping</a></strong></center></div>
<?php endif ?>

<p></p>
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>