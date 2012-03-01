<?php if ($order): ?>
	<h2>My Orders</h2>
	<hr />
							<div style="padding:10px;">
							<h2 style="font-size: 14px; color: #e00000;" class="holiday_message">Thank you! Your order has been successfully placed!</h2>
							
							<div class="holiday_message">Order #<?php echo $order->order_id;?></div>
	

							<div>
							<div class="holiday_message">
							
							<?php if (!empty($orderEvents[$key]['ship_message'])): ?>
								
									<?php echo $orderEvents[$key]['ship_message']?>
							
							<?php endif ?>
								Estimated Ship Date:
								<?php if (!empty($orderEvents[$key]['ship_date'])): ?>
									<?
									//echo date('M d, Y', strtotime($orderEvents[$key]['ship_date']));
									echo $orderEvents[$key]['ship_date']
								?>
									
								<?php else: ?>
									 <?php echo $shipDate; ?>
								<?php endif ?></div>
								<?php foreach ($itemsByEvent as $key => $event): ?>
									<?php if(!empty($openEvent[$orderEvents[$key]['_id']])): ?>
									
										
										<?php foreach ($event as $item): ?>
											<?php if(empty($item['cancel'])): ?>
												
											
											<br />
											<strong><?php echo $orderEvents[$key]['name']?></strong><br />
											
														<?php echo $item['description']?>
														<br />
														Color: <?php echo $item['color']?>
														<br />
														Size: <?php echo $item['size']?>
														
														<?php 
														$convertdate = date("Y-m-d h:i:s", 1322071200);
														//echo $orderdate;
														
														if($order->date_created->sec>1322006400){
															
														}
														?><br />
														
														Price: $<?php echo number_format($item['sale_retail'],2); ?><br />
														
														Quantity: <?php echo $item['quantity']?><br />
														
														Total Price: $<?php echo number_format(($item['quantity'] * $item['sale_retail']),2)?><br />
											
											<?php endif ?>
										<?php endforeach ?>
									<?php endif ?>
								<?php endforeach ?>
<hr />	

	<div class="clear"></div>
		<strong>Shipping Address</strong>
		<hr />
		<?php echo $order->shipping->firstname;?> <?php echo $order->shipping->lastname;?>							
		<br />
		<?php echo $order->shipping->address; ?><?php echo $order->shipping->address_2; ?>
		<br />
		<?php echo $order->shipping->city; ?>, <?php echo $order->shipping->state; ?><?php echo $order->shipping->zip; ?>
		<br />
		<hr />	
		<strong>Payment Method</strong>
		<hr />
		<?php echo strtoupper($order->card_type)?> XXXX-XXXX-XXXX-<?php echo $order->card_number?>
		
		<strong>Order Information</strong>
		<hr />
		Order Subtotal: <span class="fr">$<?php echo number_format($order->subTotal,2); ?></span>
		<br />
		<?php if ($order->credit_used): ?>
		Credit Applied: <span class="fr">-$<?php echo number_format(abs($order->credit_used),2); ?></span>
			<br />
		<?php endif ?>
		<?php if (($order->promo_discount) && empty($order->promocode_disable)): ?>
		Promotion Discount [<?php echo $order->promo_code?>]: <span class="fr">-$<?php echo number_format(abs($order->promo_discount),2); ?></span>
			<br />
		<?php endif ?>
		<?php if ($order->discount): ?>
		Discount: <span class="fr">-$<?php echo number_format(abs($order->discount),2); ?></span>
			<br />
		<?php endif ?>
		Sales Tax: <span class="fr">$<?php echo number_format($order->tax,2); ?></span>
		<br />
		Shipping: <span class="fr">$<?php echo number_format($order->handling,2); ?></span>
		<?php if ( array_key_exists('overSizeHandling', $order->data()) && $order->overSizeHandling !=0): ?>
	        <br />
	        Oversize Shipping: <span class="fr">$<?php echo number_format($order->overSizeHandling,2); ?></span>
	    <?php endif; ?>
		<br />
		<hr/>
			<strong style="font-weight:bold;color:#606060; font-size:16px;">Total:</strong> <strong style="font-weight:bold;color:#009900; font-size:16px; float:right;">$<?php echo number_format($order->total,2); ?></strong>
		</div>											
	<div class="clear"></div>
	<br />
	<hr/>
		<p style="text-align: center; font-size:12px; margin-top:10px;" class="holiday_message">Thank you for shopping on Totsy.com!</p>
		

<div class="holiday_message">
		<p>A tree was planted with your first order. It is watered with every additional order so it can grow big and strong to help our earth!<br>
			<strong style="color:#E00000;font-weight:normal"></strong><br />
			<?php echo $this->html->link('Learn how every purchase helps', array('Pages::being_green')); ?>
		</p>
		</div>
<?php else: ?>
<div class="holiday_message">
	<strong>Sorry, we cannot locate the order that you are looking for.</strong>
	</div>
<?php endif ?>
</div>

<!--- ECOMMERCE TRACKING -->
<?php
	$brandNew = ($order->date_created->sec > (time() - 10)) ? true : false;
	$new = ($order->date_created->sec > (time() - 120)) ? true : false;

?>
<?php if ($brandNew): ?>
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-675412-20']);
	  _gaq.push(['_trackPageview']);
	  _gaq.push(['_addTrans',
	    '<?php echo $order->order_id?>',           // order ID - required
	    '',  // affiliation or store name
	    '<?php echo $order->total?>',          // total - required
	    '<?php echo $order->tax?>',           // tax
	    '<?php echo $order->handling?>',              // shipping
	    '<?php echo $order->shipping->city?>',       // city
	    '<?php echo $order->shipping->state?>',     // state or province
	    'US'             // country
	  ]);

	   // add item might be called for every item in the shopping cart
	   // where your ecommerce engine loops through each item in the cart and
	   // prints out _addItem for each

	  <?php foreach($itemsByEvent as $event): ?>
			<?php foreach($event as $item): ?>
				 _gaq.push(['_addItem',
				'<?php echo $order->order_id?>',			// order ID - required
				'<?php echo $item['sku']?>',			// SKU/code - required
				'<?php echo $item['description']?>',		// product name
				'<?php echo $item['color']?>',		// category or variation
				'<?php echo $item['sale_retail']?>',        // unit price - required
				'<?php echo $item['quantity']?>'         // quantity - required
				 ]);
			<?php endforeach ?>
		<?php endforeach ?>
	  _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
<?php endif ?>
