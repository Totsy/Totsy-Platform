<?php $this->title("Order Confirmation"); ?>

<?php if ($order): ?>
	<h2>My Orders</h2>
	<hr />
	<table class="cart-table" cellspacing="0" cellpadding="0" border="0" style="width:100%">
		<tr>
			<td colspan="4">
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td valign="top">
						<div style="display:none;">
							<div class="rounded" style="color:#009900; margin:0px 10px 0px 0px; float: left; display:block; background:#ebffeb; border:1px solid #ddd; width:180px; text-align:center; padding:20px;">Shipping / Billing Info</div>
							<div id="arrow-right">
							  <div id="arrow-right-1"></div>
							  <div id="arrow-right-2"></div>
							</div><!--arrow-right-->
							<div class="rounded" style="color:#009900; margin:0px 10px 0px 0px; float: left; display:block; background:#ebffeb; border:1px solid #ddd; width:180px; padding:20px; text-align: center;">Payment</div>
							<div id="arrow-right">
								<div id="arrow-right-1"></div>
								<div id="arrow-right-2"></div>
							</div><!--arrow-right-->
							<div class="rounded" style="color:#009900; margin:0px 0px 0px 0px; float:left; display:block; background:#ebffeb; border:1px solid #ddd; width:188px; padding:20px; text-align:center;">Confirmation</div>
						</div>
						<div style="background:#f7f7f7; padding:10px; border:1px solid #ddd;">
							<h2>Thank you! Your order has been successfully placed! <span style="float:right;">Order #<?php echo $order->order_id;?></span>
							</h2>
						</div>
						<div style="clear:both;"></div>
						</td>
					</tr>
					<tr>
						<td valign="top">
					</tr>
					<tr>
						<td colspan="4"><!-- start order detail table -->
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<?php foreach ($itemsByEvent as $key => $event): ?>
									<?php if(!empty($openEvent[$orderEvents[$key]['_id']])): ?>
										<tr>
											<td colspan="2" style="padding:5px; text-align::left;"><?php echo $orderEvents[$key]['name']?></td>
											<?php if (!empty($orderEvents[$key]['ship_message'])): ?>
												<td>
													<?php echo $orderEvents[$key]['ship_message']?>
												</td>
											<?php endif ?>
											<td colspan="3" style="padding:5px; text-align:right;">
												Estimated Ship Date:
												<?php if (!empty($orderEvents[$key]['ship_date'])): ?>
													<?
													//echo date('M d, Y', strtotime($orderEvents[$key]['ship_date']));
													echo $orderEvents[$key]['ship_date']
												?>
													
												<?php else: ?>
													 <?php echo $shipDate; ?>
												<?php endif ?>
											</td>
										</tr>
										<tr style="background:#ddd;">
											<td style="padding:5px; width:70px;"><strong>Item</strong></td>
											<td style="padding:5px; width:340px;"><strong>Description</strong></td>
											<td style="padding:5px; width:100px;"><strong>Price</strong></td>
											<td style="padding:5px; width: 50px;"><strong>Qty</strong></td>
											<td style="padding:5px; width:100px;"><strong>Subtotal</strong></td>
										</tr>
										<?php foreach ($event as $item): ?>
											<?php if(empty($item['cancel'])): ?>
												<tr>
												<?php
													if (!empty($item['primary_image'])) {
														$image = '/image/'. $item['primary_image'] . '.jpg';
													} else {
														$image = "/img/no-image-small.jpeg";
													}
												?>
													<td style="padding:5px;" title="item">
														<?php echo $this->html->image("$image", array('width' => "60", 'height' => "60", 'style' => "margin:2px; padding:2px; background:#fff; border:1px solid #ddd;")); ?>
													</td>
													<td style="padding:5px" title="description">
														<?php echo $item['description']?>
														<br>
														Color: <?php echo $item['color']?>
														<br>
														Size: <?php echo $item['size']?>
														
														<?php 
														$convertdate = date("Y-m-d h:i:s", 1322071200);
														//echo $orderdate;
														
														if($order->date_created->sec>1322006400){
															if($missChristmasCount>0){
															?>
															<br>
															This item is not guaranteed to be delivered on or before 12/25.* 
															
															<?php
															}else{
															?>
															<br>
															This item will be delivered on or before 12/23*
															
															<?php
															}
														}
														?>

													</td>
													<td style="padding:5px; color:#009900;" title="price">
														$<?php echo number_format($item['sale_retail'],2); ?>
													</td>
													<td style="padding:5px;" title="quantity">
														<?php echo $item['quantity']?>
													</td>
													<td title="subtotal" style="padding:5px; color:#009900;">
														$<?php echo number_format(($item['quantity'] * $item['sale_retail']),2)?>
													</td>
												</tr>
											<?php endif ?>
										<?php endforeach ?>
									<?php endif ?>
								<?php endforeach ?>
							</table>
						</td><!-- end order detail table -->
					</tr>
				</table>
			</td>
		</tr> <!-- end body of email -->
	</table>

	<div class="clear"></div>
	<div class="grid_3">
		<strong>Shipping Address</strong>
		<hr />
		<?php echo $order->shipping->firstname;?> <?php echo $order->shipping->lastname;?>							
		<br />
		<?php echo $order->shipping->address; ?><?php echo $order->shipping->address_2; ?>
		<br />
		<?php echo $order->shipping->city; ?>, <?php echo $order->shipping->state; ?><?php echo $order->shipping->zip; ?>
		<br />
		<br />	
	</div>
	<div class="grid_3">
		<strong>Payment Method</strong>
		<hr />
		<?php echo strtoupper($order->card_type)?> XXXX-XXXX-XXXX-<?php echo $order->card_number?>
	</div>
	<div class="grid_5">
		<strong>Order Information</strong>
		<hr />
		Order Subtotal: <span class="fr">$<?php echo number_format($order->subTotal,2); ?></span>
		<br>
		<?php if ($order->credit_used): ?>
		Credit Applied: <span class="fr">-$<?php echo number_format(abs($order->credit_used),2); ?></span>
			<br>
		<?php endif ?>
		<?php if (($order->promo_discount) && empty($order->promocode_disable)): ?>
		Promotion Discount [<?php echo $order->promo_code?>]: <span class="fr">-$<?php echo number_format(abs($order->promo_discount),2); ?></span>
			<br>
		<?php endif ?>
		<?php if ($order->discount): ?>
		Discount: <span class="fr">-$<?php echo number_format(abs($order->discount),2); ?></span>
			<br>
		<?php endif ?>
		Sales Tax: <span class="fr">$<?php echo number_format($order->tax,2); ?></span>
		<br>
		Shipping: <span class="fr">$<?php echo number_format($order->handling,2); ?></span>
		<?php if ( array_key_exists('overSizeHandling', $order->data()) && $order->overSizeHandling !=0): ?>
	        <br>
	        Oversize Shipping: <span class="fr">$<?php echo number_format($order->overSizeHandling,2); ?></span>
	    <?php endif; ?>
		<br>
		<hr/>
			<strong style="font-weight:bold;color:#606060; font-size:16px;">Total:</strong> <strong style="font-weight:bold;color:#009900; font-size:16px; float:right;">$<?php echo number_format($order->total,2); ?></strong>
		</div>											
	<div class="clear"></div>
	<br>
	<hr/>
	<div class="grid_11">
		<p style="text-align: center; font-size:18px; margin-top:10px;">Thank you for shopping on Totsy.com!</p>
	</div>	
<div class="clear"></div>
<div style="color:#707070; font-size:12px; font-weight:bold; padding:10px;">
				<?php
				if($missChristmasCount>0&&$notmissChristmasCount>0){
				?>
				* Totsy ships all items together. If you would like the designated items in your cart delivered on or before 12/23, please ensure that any items that are not guaranteed to ship on or before 12/25 are removed from your cart and purchased separately. Our delivery guarantee does not apply when transportation networks are affected by weather. Please contact our Customer Service department at 888-247-9444 or email <a href="mailto:support@totsy.com">support@totsy.com</a> with any questions. 
				
				<?php
				}
				elseif($missChristmasCount>0){
				?>
				* Your items will arrives safely, but after 12/25.
				
				<?php
				}
				else{
				?>
				
				* Our delivery guarantee does not apply when transportation networks are affected by weather.
				
				<?php
				}
				?>
				
</div>
</div>

		<?php echo $this->html->image('being_green/carbonzero.gif', array('style' => 'margin-right: 10px; margin-bottom:20px; float:left;')); ?>
		<p>A tree was planted with your first order. It is watered with every additional order so it can grow big and strong to help our earth!<br>
			<strong style="color:#E00000;font-weight:normal"></strong><br />
			<?php echo $this->html->link('Learn how every purchase helps', array('Pages::being_green')); ?>
		</p>
<?php else: ?>
	<strong>Sorry, we cannot locate the order that you are looking for.</strong>
<?php endif ?>
</div>
