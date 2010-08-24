<?php
	$this->title("Order Confirmation");
?>
<h1 class="p-header">My Orders</h1>
<?=$this->menu->render('left'); ?>


<?php if ($order): ?>
	<style type="text/css">

		td{font-family:Arial,sans-serif;color:#888888;font-size:14px;line-height:18px}
		img{border:none}

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
								<td colspan="4" style="padding:0 10px 10px 10px">
									<?php echo $this->html->image(
													"/img/email/order-confirmation.jpg",
													array(
														'width'=>'570',
														'height'=>'77',
														'alt' => 'order-confirmation'
													));
									?>
								</td>
							</tr>
							<tr>
								<td style="padding:20px" valign="top">
									<p style="font-weight:bold">Order: <?=$order->order_id;?></p><br>
									<p>Thank you so much for shopping with Totsy. Please find your order summary information below:
									<br>
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
														$image = '/image/'. $item['primary_image'] . '.jpg';
													} else {
														$image = "/img/no-image-small.jpeg";
													}
												?>
												<td height="100" style="border-right:1px solid #666666;padding-top:5px;padding-bottom:5px" title="item">
													<?=$this->html->image("$image", array('width' => "95", 'height' => "88")); ?>
												</td>
												<td height="100" style="border-right:1px solid #666666;padding-top:5px;padding-bottom:5px;text-align:left;padding-left:5px" title="description">
													<strong style="font-weight:bold;color:#666666"><?=$item['description']?></strong>
													<br>
													<strong style="font-weight:bold">Color:</strong><strong style="font-size:10pt;font-weight:normal"><?=$item['color']?></strong>
													<br>
													<strong style="font-weight:bold">Size:</strong><strong style="font-size:10pt;font-weight:normal"><?=$item['size']?></strong>
												</td>
												<td height="100" style="border-right:1px solid #666666;padding-top:5px;padding-bottom:5px" title="price">
													<strong syle="text-weight:bold">$<?=number_format($item['sale_retail'],2); ?></strong>
												</td>
													<td height="100" style="border-right:1px solid #666666;padding-top:5px;padding-bottom:5px;text-align:center" title="quantity">
														<?=$item['quantity']?>
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
												<?php if ($order->credit_amount): ?>
												Credit:
													<br>
												<?php endif ?>
												Sales Tax:
												<br>
												Shipping:
												<br><br><br>
												<strong style="font-weight:bold;color:#606060">Total:</strong> 
											</td>
											<td style="padding:20px;text-align:right" valign="top">
												$<?=number_format($order->subTotal,2); ?>
												<br>
												<?php if ($order->credit_amount): ?>
													-$<?=number_format(abs($order->tax),2); ?>
													<br>
												<?php endif ?>
												$<?=number_format($order->tax,2); ?>
												<br>
												$<?=number_format($order->handling,2); ?>
												<br><br><br>
												<strong style="font-weight:bold;color:#606060">$<?=number_format($order->total,2); ?></strong>
											</td>
										</tr>
									</table>
									<table style="padding:0 0 0 20px">
										<tr>
											<td style="padding:20px;">
												Payment Info:
											</td>
											<td width="200">
												 <?=strtoupper($order->card_type)?> ending with <?=$order->card_number?>
											</td>
										</tr>
										<tr>
											<td style="padding:20px;">
												Shipping Address:
											</td>
											<td>
												<address class="shipping-address">
													<?=$order->shipping->firstname;?> <?=$order->shipping->lastname;?><br>
													<?=$order->shipping->address; ?> <?=$order->shipping->address_2; ?><br />
													<?=$order->shipping->city; ?>, <?=$order->shipping->state; ?>
													<?=$order->shipping->zip; ?>
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
<?php else: ?>
	<strong>Sorry, we cannot locate the order that you are looking for.</strong>
<?php endif ?>