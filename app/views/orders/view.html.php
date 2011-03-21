<?php
	$this->title("Order Confirmation");
?>
<?php

   $new = ($order->date_created->sec > (time() - 120)) ? true : false;

?>
	<h1 class="p-header">My Account</h1>
	<div id="left">
		<ul class="menu main-nav">
			<li class="firstitem17"><a href="/account" title="Account Dashboard"><span>Account Dashboard</span></a></li>
			<li class="item18"><a href="/account/info" title="Account Information"><span>Account Information</span></a></li>
			<li class="item18"><a href="/account/password" title="Change Password"><span>Change Password</span></a></li>
			<li class="item19"><a href="/addresses" title="Address Book"><span>Address Book</span></a></li>
			<li class="item20 active"><a href="/orders" title="My Orders"><span>My Orders</span></a></li>
			<li class="item20"><a href="/Credits/view" title="My Credits"><span>My Credits</span></a></li>
			<li class="lastitem23"><a href="/Users/invite" title="My Invitations"><span>My Invitations</span></a></li>
			<br />
			<h3 style="color:#999;">Need Help?</h3>
			<hr />
			<li class="first item18"><a href="/tickets/add" title="Contact Us"><span>Help Desk</span></a></li>
			<li class="first item19"><a href="/pages/faq" title="Frequently Asked Questions"><span>FAQ's</span></a></li>
		</ul>
	</div>


<?php if ($order): ?>
		<table cellspacing="0" cellpadding="0" border="0" width="695">
				<tr>
					<td colspan="4">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td valign="top">
									<?php if ($new): ?>
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
										<div style="clear:both; margin-bottom:15px;"></div>
										<div style="background:#f7f7f7; padding:10px; border:1px solid #ddd;"><h2>Thank you! Your order has been successfully placed! <span style="float:right;">Order #<?=$order->order_id;?></span></h2></div>
										<div style="clear:both;"></div>
										<!--
<div style="display:block; padding:10px; background:#feffd0; font-weight:bold; margin:10px 0px; border:1px solid #ddd; text-align:center;">
											Our shipping cut off for Christmas delivery has passed. Please note that unfortunately WE CAN NOT GUARANTEE HOLIDAY DELIVERY unless otherwise stated.  Thank you for understanding and for shopping with Totsy.
										</div> -->
									<?php else: ?>
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
									<?php endif ?>
								</td>
							</tr>
							<tr>
								<td valign="top">
							</tr>
							<tr>
								<td colspan="4"><!-- start order detail table -->
										<table cellpadding="0" cellspacing="0" border="0" width="100%">

											<?php foreach ($itemsByEvent as $key => $event): ?>
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
												<?php endforeach ?>
											<?php endforeach ?>
											</tr>

										</table>
								</td><!-- end order detail table -->
							</tr>
							<tr>
								<td colspan="4"><!-- start totals table -->
								</td><!-- end totals table -->
							</tr>
							<tr>
								<td style="padding:0px 0px 5px 0px; margin:0px;"><hr /></td>
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
												<?php if ($order->promo_discount): ?>
												Promotion Discount:
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
												<?php if ($order->promo_discount): ?>
													-$<?=number_format(abs($order->promo_discount),2); ?>
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
										<tr>
											<td>
												<p>A TREE HAS BEEN PLANTED WITH THIS ORDER.
													<br>
													<strong style="color:#E00000;font-weight:normal"></strong>
													<?=$this->html->link('Find out how every purchase makes a difference.', array('Pages::being_green')); ?>
												</p>
											</td>
											<td>
												<img src="/img/email/tree-logo.jpg" alt="tree-logo" width="40" height="30" />
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
							</tr>
						</table>
					</td>
				</tr> <!-- end body of email -->
				<tr>
				</tr>
		</table>

<?php else: ?>
	<strong>Sorry, we cannot locate the order that you are looking for.</strong>
<?php endif ?>
