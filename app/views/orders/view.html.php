<?php
	$this->title("Order Confirmation");
?>
<?php
	$brandNew = ($order->date_created->sec > (time() - 10)) ? true : false;
	$new = ($order->date_created->sec > (time() - 120)) ? true : false;

?>
	<div class="grid_16">
	<h2 class="page-title gray">My Orders</h2>
	<hr />
</div>

<div class="grid_4">
	<div class="roundy grey_inside">
		<h3 class="gray">My Account</h3>
		<hr />
		<ul class="menu main-nav">
		<li><a href="/account" title="Account Dashboard">Account Dashboard</a></li>
		<li><a href="/account/info" title="Account Information">Account Information</a></li>
		<li><a href="/account/password" title="Change Password">Change Password</a></li>
		<li class="active"><a href="/addresses" title="Address Book">Address Book</a></li>
		<li><a href="/orders" title="My Orders">My Orders</a></li>
		<li><a href="/Credits/view" title="My Credits">My Credits</a></li>
		<li><a href="/Users/invite" title="My Invitations">My Invitations</a></li>
		</ul>
	</div>
	<div class="clear"></div>
	<div class="roundy grey_inside">
		<h3 class="gray">Need Help?</h3>
		<hr />
		<ul class="menu main-nav">
		    <li><a href="/tickets/add" title="Contact Us">Help Desk</a></li>
			<li><a href="/pages/faq" title="Frequently Asked Questions">FAQ's</a></li>
			<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
			<li><a href="/pages/terms" title="Terms Of Use">Terms Of Use</a></li>
		</ul>
	</div>
	<div class="clear"></div>
	<div class="roundy grey_inside">
		<?=$this->html->image('being_green/carbonzero.gif', array('style' => 'margin-right: 10px; margin-bottom:20px; float:left;'
											)); ?>
											<p>A tree was planted with your first order. It is watered with every additional order so it can grow big and strong to help our earth!
													<br>
													<strong style="color:#E00000;font-weight:normal"></strong><br />
													<?=$this->html->link('Learn how every purchase helps', array('Pages::being_green')); ?>
												</p>

		</div>
</div>

	
	


<?php if ($order): ?>
<div class="grid_11 omega roundy grey_inside b_side">
<h2 class="page-title gray">My Orders</h2>
	<hr />
		<table class="cart-table" cellspacing="0" cellpadding="0" border="0" width="695">
				<tr>
					<td colspan="4">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td valign="top">
									<?php //if ($new): ?>
<div style="display:none;">									<div class="rounded" style="color:#009900; margin:0px 10px 0px 0px; float: left; display:block; background:#ebffeb; border:1px solid #ddd; width:180px; text-align:center; padding:20px;">Shipping / Billing Info</div>
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
										<div style="background:#f7f7f7; padding:10px; border:1px solid #ddd;"><h2>Thank you! Your order has been successfully placed! <span style="float:right;">Order #<?=$order->order_id;?></span></h2></div>
										<div style="clear:both;"></div>
										<!--
<div style="display:block; padding:10px; background:#feffd0; font-weight:bold; margin:10px 0px; border:1px solid #ddd; text-align:center;">
											Our shipping cut off for Christmas delivery has passed. Please note that unfortunately WE CAN NOT GUARANTEE HOLIDAY DELIVERY unless otherwise stated.  Thank you for understanding and for shopping with Totsy.
										</div> -->
									<?php //else: ?>
									<!--
										<br />
										<h2 class="gray mar-b">Tracking System <span style="float:right; font-weight:normal; font-size:11px;"><span style="font-weight:bold;">NOTE: </span>Orders may be split into multiple shipments with different tracking numbers.</span></h2>
										<hr />
										<div class="rounded" style="border:1px solid #ddd; display:block; width:141px; float:left; text-align:center; padding:10px; margin-right:10px; background:#ebffeb; color:#009900;">Order Placed</div>
										<?php if ($allEventsClosed): ?>
											<div class="rounded" style="border:1px solid #ddd; display:block; width:141px; float:left; text-align:center; padding:10px; margin-right:10px; background:#ebffeb; color:#009900;">All Events Closed</div>
										<?php else: ?>
											<div class="rounded" style="border:1px solid #ddd; display:block; width:141px; float:left; text-align:center; padding:10px; margin-right:10px;">All Events Closed</div>
										<?php endif ?>
										<?php if ($preShipment): ?>
											<div class="rounded" style="border:1px solid #ddd; display:block; width:143px; float:left; text-align:center; padding:10px; margin-right:10px; background:#ebffeb; color:#009900;">Pre-Shipment</div>
										<?php else: ?>
											<div class="rounded" style="border:1px solid #ddd; display:block; width:145px; float:left; text-align:center; padding:10px; margin-right:10px;">Pre-Shipment</div>
										<?php endif ?>
										<?php if ($shipped || $shipRecord): ?>
											<div class="rounded" style="border:1px solid #ddd; display:block; width:145px; float:left; text-align:center; padding:10px; margin-right:0px; background:#ebffeb; color:#009900;">Shipped</div>
										<?php else: ?>
											<div class="rounded" style="border:1px solid #ddd; display:block; width:145px; float:left; text-align:center; padding:10px; margin-right:0px;">Shipped</div>
										<?php endif ?>
										<div style="clear:both;"></div>
										<h2 style="margin:20px 0px 0px 0px;" class="gray mar-b">Order Summary
										<hr />
										-->
									<?php //endif ?>
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
													<td colspan="2" style="padding:5px; text-align::left;"><?=$orderEvents[$key]['name']?></td>
													<?php if (!empty($orderEvents[$key]['ship_message'])): ?>
														<td><?php echo $orderEvents[$key]['ship_message']?></td>
													<?php endif ?>
													<td colspan="3" style="padding:5px; text-align:right;">
														Estimated Ship Date:
														<?php if (!empty($orderEvents[$key]['ship_date'])): ?>
															<?=date('M d, Y', strtotime($orderEvents[$key]['ship_date']))?>
														<?php else: ?>
															 <?=date('M d, Y', $shipDate)?>
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
														<?=$this->html->image("$image", array('width' => "60", 'height' => "60", 'style' => "margin:2px; padding:2px; background:#fff; border:1px solid #ddd;")); ?>
													</td>
													<td style="padding:5px" title="description">
														<?=$item['description']?>
														<br>
														Color: <?=$item['color']?>
														<br>
														Size: <?=$item['size']?>
													</td>
													<td style="padding:5px; color:#009900;" title="price">
														$<?=number_format($item['sale_retail'],2); ?>
													</td>
														<td style="padding:5px;" title="quantity">
															<?=$item['quantity']?>
														</td>
														<td title="subtotal" style="padding:5px; color:#009900;">
															$<?php echo number_format(($item['quantity'] * $item['sale_retail']),2)?>
														</td>
														<?php endif ?>
												<?php endforeach ?>
												<?php endif ?>
											<?php endforeach ?>
											</tr>

										</table>
								</td><!-- end order detail table -->
							</tr>
							<tr>
								<td colspan="4"><!-- start totals table -->
								</td><!-- end totals table -->
							</tr>
							<!--
							<tr>
								<td style="padding:0px 0px 5px 0px; margin:0px;"><hr />test</td>
							</tr>
							<tr>
								<td colspan="4">
									<table style="width:200px; float: right;">
										<tr>
											<td valign="top">
												Order Subtotal:
												<br>
												<?php if ($order->credit_used): ?>
												Credit Applied:
													<br>
												<?php endif ?>
												<?php if (($order->promo_discount) && empty($order->promocode_disable)): ?>
												Promotion Discount:
													<br>
												<?php endif ?>
												<?php if (($order->discount)): ?>
												Discount:
													<br>
												<?php endif ?>
												Sales Tax:
												<br>
												Shipping:
												<?php if ( array_key_exists('overSizeHandling', $order->data()) && $order->overSizeHandling !=0): ?>
                                                    <br>
                                                    Oversize Shipping:
                                                <?php endif; ?>
												<br><br><br>
												<strong style="font-weight:bold;color:#606060">Total:</strong>
											</td>
											<td style="padding-left:15px; text-align:right;" valign="top">
												$<?=number_format($order->subTotal,2); ?>
												<br>
												<?php if ($order->credit_used): ?>
													-$<?=number_format(abs($order->credit_used),2); ?>
													<br>
												<?php endif ?>
												<?php if (($order->promo_discount) && empty($order->promocode_disable)): ?>
													-$<?=number_format(abs($order->promo_discount),2); ?>
													<br>
												<?php endif ?>
												<?php if (($order->discount)): ?>
													-$<?=number_format(abs($order->discount),2); ?>
													<br>
												<?php endif ?>
												$<?=number_format($order->tax,2); ?>
												<br>
												$<?=number_format($order->handling,2); ?> <br>
												<?php if ( array_key_exists('overSizeHandling', $order->data()) && $order->overSizeHandling !=0): ?>

                                                    $<?=number_format($order->overSizeHandling,2); ?>
                                                <?php endif; ?>
												<br><br><br>
												<strong style="font-weight:bold;color:#009900;">$<?=number_format($order->total,2); ?></strong>
											</td>
										</tr>
									</table>
									<table style="float:left;" valign="top">
										<tr>
											<td>
												<div style="display:block; margin-bottom:10px; width:320px;">
												  <strong>Shipping Address:</strong><br />												<?=$order->shipping->firstname;?> <?=$order->shipping->lastname;?><br>
                                                                                                <?=$order->shipping->address; ?> <?=$order->shipping->address_2; ?><br />
                                                                                                <?=$order->shipping->city; ?>, <?=$order->shipping->state; ?>
                                                                                                <?=$order->shipping->zip; ?>
												<hr /></div>
												<div style=" width:320px; display:block;"><strong>Payment Info:</strong> <br /><?=strtoupper($order->card_type)?> ending with <?=$order->card_number?></div>
											</td>
											<td>

											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="padding:0px 0px 5px 0px;"><hr></td>
							</tr>
							<tr>
								<td colspan="4">
									<table>
									 <?php if($new): ?>

									    <tr>
									        <td>
									            <?php echo $spinback_fb; ?>
									        </td>
									    </tr>
									    <?php endif;?>
										<tr>
											<td>
											<?=$this->html->image('being_green/carbonzero.gif', array(
												'align' => 'left', 'style' => 'margin-right: 15px; margin-bottom:10px;'
											)); ?>
												<p>A TREE WAS PLANTED WITH YOUR FIRST ORDER.<br />
												IT IS WATERED WITH EVERY ADDITIONAL ORDER SO IT CAN GROW BIG AND STRONG TO HELP OUR EARTH!
													<br>
													<strong style="color:#E00000;font-weight:normal"></strong><br />
													<?=$this->html->link('Find out how every purchase makes a difference.', array('Pages::being_green')); ?>
													Enjoy your order! We know you’re going to love it!
												</p>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
								  <br />
								Thank you again for your order,
								<br/>
								<strong>Totsy</strong>
								</td>
							</tr> -->
						</table>
					</td>
				</tr> <!-- end body of email -->
				<tr>
				</tr>
		</table>
																		
							<div>
									 <?php if($new): ?>

									            <?php echo $spinback_fb; ?>
									    <?php endif;?>
										
							</div>
												<div class="grid_4">
												  <strong>Shipping Address</strong><hr />												<?=$order->shipping->firstname;?> <?=$order->shipping->lastname;?><br>
                                                                                                <?=$order->shipping->address; ?> <?=$order->shipping->address_2; ?><br />
                                                                                                <?=$order->shipping->city; ?>, <?=$order->shipping->state; ?>
                                                                                                <?=$order->shipping->zip; ?><br><br>
												
												
											</div>
											<div class="grid_4"><strong>Payment Method</strong> <hr /><?=strtoupper($order->card_type)?> XXXX-XXXX-XXXX-<?=$order->card_number?></div>
											<div class="grid_3">
											<strong>Order Information</strong>
											<hr />
												Order Subtotal: <span class="fr">$<?=number_format($order->subTotal,2); ?></span>
												<br>
												<?php if ($order->credit_used): ?>
												Credit Applied: <span class="fr">-$<?=number_format(abs($order->credit_used),2); ?></span>
													<br>
												<?php endif ?>
												<?php if (($order->promo_discount) && empty($order->promocode_disable)): ?>
												Promotion Discount: <span class="fr">-$<?=number_format(abs($order->promo_discount),2); ?></span>
													<br>
												<?php endif ?>
												<?php if (($order->discount)): ?>
												Discount: <span class="fr">-$<?=number_format(abs($order->discount),2); ?></span>
													<br>
												<?php endif ?>
												Sales Tax: <span class="fr">$<?=number_format($order->tax,2); ?></span>
												<br>
												Shipping: 
												<?php if ( array_key_exists('overSizeHandling', $order->data()) && $order->overSizeHandling !=0): ?>
                                                    <br>
                                                    Oversize Shipping: <span class="fr">$<?=number_format($order->overSizeHandling,2); ?></span>
                                                <?php endif; ?>
												<!-- <br><br><br> --><br>
												<hr/>
												<strong style="font-weight:bold;color:#606060; font-size:16px;">Total:</strong> <strong style="font-weight:bold;color:#009900; font-size:16px; float:right;">$<?=number_format($order->total,2); ?></strong>
										
												</div>
												
<div class="clear"></div>
<br>
<hr/>
											
											<div class="grid_11">
												<p style="text-align: center; font-size:18px; margin-top:10px;">Thank you for shopping on Totsy.com!</p>
										</div>
		
</div>
<?php else: ?>
	<strong>Sorry, we cannot locate the order that you are looking for.</strong>
<?php endif ?>
		</div>
														
		

<!--- ECOMMERCE TRACKING -->
<?php if ($brandNew): ?>
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-675412-15']);
	  _gaq.push(['_trackPageview']);
	  _gaq.push(['_addTrans',
	    '<?=$order->order_id?>',           // order ID - required
	    '',  // affiliation or store name
	    '<?=$order->total?>',          // total - required
	    '<?=$order->tax?>',           // tax
	    '<?=$order->handling?>',              // shipping
	    '<?=$order->shipping->city?>',       // city
	    '<?=$order->shipping->state?>',     // state or province
	    'US'             // country
	  ]);

	   // add item might be called for every item in the shopping cart
	   // where your ecommerce engine loops through each item in the cart and
	   // prints out _addItem for each

	  <?php foreach($itemsByEvent as $event): ?>
			<?php foreach($event as $item): ?>
				 _gaq.push(['_addItem',
				'<?=$order->order_id?>',			// order ID - required
				'<?=$item['sku']?>',			// SKU/code - required
				'<?=$item['description']?>',		// product name
				'<?=$item['color']?>',		// category or variation
				'<?=$item['sale_retail']?>',        // unit price - required
				'<?=$item['quantity']?>'         // quantity - required
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