<?php
	$this->title("Order Confirmation");
?>

<?php if ($order): ?>

<?php
   $new = ($order->date_created->sec > (time() - 120)) ? true : false;
?>
	<style type="text/css">

		td{font-family:Arial,sans-serif;color:#888888;font-size:14px;line-height:18px}
		img{border:none}

	</style>
		<table cellspacing="0" cellpadding="0" border="0" width="695">
				<tr >
					<td colspan="4">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td valign="top">
<?php if ($new) { ?>
<div class="rounded" style="color:#009900; margin:0px 10px 0px 0px; float: left; display:block; background:#ebffeb; border:1px solid #ddd; width:180px; text-align:center; padding:20px;">Shipping / Billing Info</div>
<div id="arrow-right">
  <div id="arrow-right-1"></div>
  <div id="arrow-right-2"></div>
</div><!--arrow-right-->

              <div class="rounded" style="color:#009900; margin:0px 10px 0px 0px; float: left; display:block; background:#ebffeb; border:1px solid #ddd; width:180px; padding:20px; text-align: center;"
>Payment</div>
<div id="arrow-right">
  <div id="arrow-right-1"></div>
  <div id="arrow-right-2"></div>
</div><!--arrow-right-->

              <div class="rounded" style="color:#009900; margin:0px 0px 0px 0px; float:left; display:block; background:#ebffeb; border:1px solid #ddd; width:188px; padding:20px; text-align:center;">Confirmation</div>
              <div style="clear:both; margin-bottom:15px;"></div>
 

								<div style="background:#f7f7f7; padding:10px; border:1px solid #ddd;"><h2>Thank you! Your order has been successfully placed! <span style="float:right;">Order #<?=$order->order_id;?></span></h2></div>	

<?php } else { ?>
								<br />

<h2 class="gray mar-b">Tracking System <span style="float:right; font-weight:normal; font-size:11px;"><span style="font-weight:bold;">NOTE: </span>Orders may be split into multiple shipments with different tracking numbers.</span></h2>
<hr />

<p style="border:1px solid #ddd; background:#f7f7f7; padding:10px; font-size:14px; text-align:center; color:red;">
	The order is expected to ship on <?=date('M d, Y', $shipDate)?>
</p>
<br />

								<h2 class="gray mar-b">Order Summary <span style="font-size:11px; float:right; font-weight:normal;"><span style="font-weight:bold;">NOTE:</span> Your order will be delivered within 3-5 weeks</span></h2>
								<hr />

 <?php } ?>

						        </td>
							</tr>
							<tr>
								<td valign="top">
							</tr>
							<tr>
								<td colspan="4"><!-- start order detail table -->
										<table cellpadding="0" cellspacing="0" border="0" width="100%">
											<tr style="background:#ddd;">
												<td style="padding:5px; width:70px;"><strong>Item</strong></td>
												<td style="padding:5px; width:340px;"><strong>Description</strong></td>
												<td style="padding:5px; width:100px;"><strong>Price</strong></td>
												<td style="padding:5px; width: 50px;"><strong>Qty</strong></td>
												<td style="padding:5px; width:100px;"><strong>Subtotal</strong></td>
											</tr>
											
											<?php $items = $order->items->data(); ?>

											<?php foreach ($items as $item): ?>
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
												$<?=number_format($order->handling,2); ?>
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
