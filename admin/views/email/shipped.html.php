<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<html>
<body>

	<style type="text/css">
	
		td{font-family:Arial,sans-serif;color:#888888;font-size:14px;line-height:18px}
		img{border:none}
	
	</style>

</style>
<center>
	<table cellspacing="0" cellpadding="0" border="0" width="592">
		<tr>
			<td colspan="4" style="text-align:center;padding:10px">
			</td>
		</tr>
			<tr >
				<td colspan="4">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td colspan="4" style="text-align:center;padding:10px">
								<br>
								To ensure delivery to your inbox, please add <a href="#" title="Support Email Address" style="color:#E00000">support@totsy.com</a> to your address book
							</td>
						</tr>
						<tr>
							<!-- Remember to add absolute file paths to all images for production -->
							<td width="180">
								<?php echo $this->html->link(
									$this->html->image(
										"$data[domain]/img/email/email-logo.jpg",
										array(
											'width'=>'180',
											'height'=>'116'
										)),
										'',
										array(
											'id' => 'Totsy',
											'escape'=> false
										)
									);
								?>
							</td>
							<td width="65">
								<?php echo $this->html->link(
									$this->html->image(
										"$data[domain]/img/email/sales-btn.jpg",
										array(
											'width'=>'65',
											'height'=>'116',
											'title' => 'Current Totsy Sales'
										)),
										"$data[domain]/sales", 
										array(
											'id' => 'Totsy',
											'escape'=> false
										)
									);
								?>
							</td>
							<td width="111">
								<?php echo $this->html->link(
									$this->html->image(
										"$data[domain]/img/email/account-btn.jpg",
										array(
											'width'=>'111',
											'height'=>'116',
											'title' => 'Access My Account',
											'alt' => 'Access My Account'
										)),
										"$data[domain]/account",
										array(
											'id' => 'Totsy', 
											'escape'=> false
										)
									);
								?>
							</td>
							<td width="236">
								<?php echo $this->html->link(
									$this->html->image(
										"$data[domain]/img/email/invite-btn.jpg",
										array(
											'width'=>'236',
											'height'=>'116',
											'alt' => 'Invite Friends to Totsy'
										)),
										"$data[domain]/Users/invite", 
										array(
											'id' => 'Totsy',
											'escape'=> false,
											'title' => 'Invite Friends to Totsy'
										)
									);
								?>
							</td>
						</tr>
						<tr> <!-- start body of email -->
							<td colspan="4">
								<table cellpadding="0" cellspacing="0" width="100%" style="border-left:1px solid #666666; border-right:1px solid #666">
									<tr>
										<td colspan="4" style="padding:0 10px 10px 10px">
											<?php echo $this->html->image(
															"$data[domain]/img/email/order-shipped.jpg",
															array(
																'width'=>'570',
																'height'=>'77',
																'alt' => 'order-shipped'
															));
											?>
										</td>
									</tr>
						<tr>
							<td style="padding:20px" valign="top">
								<p style="font-weight:bold">Order: <?php echo $order->order_id;?></p>
								<p>Dear <?php echo $order->billing->firstname?> <?php echo $order->billing->lastname?>,<br> 
									Your order (or a portion of it) has been shipped to you.<br>
									A summary of your order is available from your account page.
									<?php echo $this->html->link('Click Here', "$data[domain]/orders/view/$order->order_id"); ?> 
									to view your order.<br> 
									Track your order: <?php echo $order->ship_method?>
									<?php echo $this->shipment->link($details['Tracking Number'], array('type' => 'UPS'))?>
								</p>
							</td>
						</tr>
						<tr>
							<td style="color:#666666;padding-top:5px;padding-bottom:5px;padding-left:20px" valign="top">
						</tr>
						<tr>
							<td style="color:#666666;padding:0 20px;" colspan="4"><!-- start order detail table -->
									<table cellpadding="0" cellspacing="0" border="0" width="100%">
										<tr>
											<td width="75" style="padding-bottom:15px;text-transform:uppercase"><strong>Item</strong></td>
											<td width="100" style="padding-bottom:15px;text-transform:uppercase"><strong>Description</strong></td>
											<td width="50" style="padding-bottom:15px;text-transform:uppercase"><strong>Price</strong></td>
											<td width="50" style="padding-bottom:15px;text-transform:uppercase"><strong>Qty</strong></td>
											<td width="50" style="padding-bottom:15px;text-transform:uppercase"><strong>Subtotal</strong></td>
										</tr>
										<tr style="background-color:#e8e8e8">
											<td colspan="5" height="5"></td>
										</tr>
										<?php $items = $order->items->data(); ?>

										<?php foreach ($items as $item): ?>
											<tr style="background-color:#e8e8e8;text-align:center">
											<?php
												if (!empty($item['primary_image'])) {
													$image = $data['domain'].'/image/'. $item['primary_image'] . '.jpg';
												} else {
													$image = "$data[domain]/img/no-image-small.jpeg";
												}
											?>
											<td height="100" style="border-right:1px solid #666666;padding-top:5px;padding-bottom:5px" title="item">
												<?php echo $this->html->image("$image", array('width' => "95", 'height' => "88")); ?>
											</td>
											<td height="100" style="border-right:1px solid #666666;padding-top:5px;padding-bottom:5px;text-align:left;padding-left:5px" title="description">
												<strong style="font-weight:bold;color:#666666"><?php echo $item['description']?></strong>
												<br>
												<strong style="font-weight:bold">Color:</strong><strong style="font-size:10pt;font-weight:normal"><?php echo $item['color']?></strong>
												<br>
												<strong style="font-weight:bold">Size:</strong><strong style="font-size:10pt;font-weight:normal"><?php echo $item['size']?></strong>
											</td>
											<td height="100" style="border-right:1px solid #666666;padding-top:5px;padding-bottom:5px" title="price">
												<strong syle="text-weight:bold">
													$<?php echo number_format($item['sale_retail'],2); ?>
												</strong>
											</td>
												<td height="100" style="border-right:1px solid #666666;padding-top:5px;padding-bottom:5px;text-align:center" title="quantity">
													<?php echo $item['quantity']?>
												</td>
												<td height="100" title="subtotal">
													<strong syle="text-weight:bold">$<?php echo number_format(($item['quantity'] * $item['sale_retail']),2)?></strong>
												</td>
										<?php endforeach ?>
										</tr>
										<tr style="background-color:#e8e8e8">
											<td colspan="5" height="5"></td>
										</tr>

									</table>
							</td><!-- end order detail table -->
						</tr>
						<tr>
							<td colspan="4"><!-- start totals table -->
							</td><!-- end totals table -->
						</tr>
						<tr>
							<td style="padding:0 20px 0 20px"><hr></td>
						</tr>
						<tr>
							<td colspan="4" style="padding:0 0 0 20px">
								<table style="padding:0 0 0 20px">
									<tr>
										<td style="padding:20px" valign="top">
											Order Subtotal:
											<br>
											<?php if ($order->credit_used): ?>
											Credit Applied:
												<br>
											<?php endif ?>
											Sales Tax:
											<br>
											Shipping:
											<br><br><br>
											<strong style="font-weight:bold;color:#606060">Total:</strong> 
										</td>
										<td style="padding:20px;text-align:right" valign="top">
											$<?php echo number_format($order->subTotal,2); ?>
											<br>
											<?php if ($order->credit_used): ?>
												-$<?php echo number_format(abs($order->credit_used),2); ?>
												<br>
											<?php endif ?>
											$<?php echo number_format($order->tax,2); ?>
											<br>
											$<?php echo number_format($order->handling + $order->overSizeHandling - $order->handlingDiscount - $order->overSizeHandlingDiscount, 2); ?>
											<br><br><br>
											<strong style="font-weight:bold;color:#606060">$<?php echo number_format($order->total,2); ?></strong>
										</td>
									</tr>
								</table>
								<table style="padding:0 0 0 20px">
									<tr>
										<td style="padding:20px;">
											Payment Info:
										</td>
										<td width="200">
											 <?php echo strtoupper($order->card_type)?> ending with <?php echo $order->card_number?>
										</td>
									</tr>
									<tr>
										<td style="padding:20px;">
											Shipping Address:
										</td>
										<td>
											<address class="shipping-address">
												<?php echo $order->shipping->firstname;?> <?php echo $order->shipping->lastname;?><br>
												<?php echo $order->shipping->address; ?> <?php echo $order->shipping->address_2; ?><br />
												<?php echo $order->shipping->city; ?>, <?php echo $order->shipping->state; ?>
												<?php echo $order->shipping->zip; ?>
											</address>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="padding:0 20px 0 20px"><hr></td>
						</tr>
						<tr>
							<td colspan="4">
								<table>
									<tr>
										<td style="padding:0 0 0 20px;color:#888888">
											<br>
											<p>A TREE HAS BEEN PLANTED WITH THIS ORDER.
												<br>
												<strong style="color:#E00000;font-weight:normal"></strong>
												<?php echo $this->html->link('Find out how every purchase makes a difference.', array('Pages::being_green')); ?>
											</p>
										</td>
										<td>
											<?php echo $this->html->image(
												"$data[domain]/img/email/tree-logo.jpg",
												array(
													'width'=>'40',
													'height'=>'30',
													'alt' => 'Totsy'
												));
											?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="padding:0 0 0 20px">
							<br>
							Thank you,
							<br>
							<strong style="color:#E00000;font-weight:normal">Totsy</strong>
							</td>
						</tr>
					</table>
				</td>
			</tr> <!-- end body of email -->
			<tr>
			</tr>
	</table>
</center>
</body>
</html>
