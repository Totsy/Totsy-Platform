<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>
<?=$this->html->script('jquery.dataTables.js');?>
<?=$this->html->style(array('jquery_ui_blitzer.css', 'table'));?> 
<?php
	$this->title(" - Order Confirmation");
?>

<?php if ($order): ?>
	<style type="text/css">
		td{font-family:Arial,sans-serif;color:#888888;font-size:14px;line-height:18px}
		img{border:none}
	</style>
		<table cellspacing="0" cellpadding="0" border="0" width="695">
			<tr>
				<td colspan="4">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td valign="top">
								<br />
								<?php if($order->cancel == true): ?>
									<p style="border:1px solid #ddd; background:#f7f7f7; padding:10px; font-size:14px; text-align:center; color:red;">
										The order has been canceled
									</p><br />
									<p style="text-align:center;">
										<button id="uncancel_button" style="font-weight:bold;font-size:14px;"> Uncancel Order</button>
									</p><br />
								<?php else: ?>
									<p style="border:1px solid #ddd; background:#f7f7f7; padding:10px; font-size:14px; text-align:center; color:red;">
										The order is expected to ship on <?=date('M d, Y', $shipDate)?>
									</p><br />
									<p style="text-align:center;">
									<button id="cancel_button" style="font-weight:bold;font-size:14px;"> Cancel Order</button>
									<button id="update_shipping" style="font-weight:bold;font-size:14px;">Update Shipping</button>
									</p><br />
								<?php endif ?>
									<div id="cancel_form" style="display:none">
										<?=$this->form->create(null ,array('id'=>'cancelForm','enctype' => "multipart/form-data")); ?>
										<?=$this->form->hidden('id', array('class' => 'inputbox', 'id' => 'id', 'value' => $order["_id"])); ?>
									</div>
									<div id="new_shipping" style="display:none">
											<h2 id="new_shipping_address">New shipping address</h2>
											<?=$this->form->create(null ,array('id'=>'newShippingForm','enctype' => "multipart/form-data")); ?>
												<div class="form-row">
													<?=$this->form->label('firstname', 'First Name', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('firstname', array('class' => 'inputbox')); ?>
													<?=$this->form->error('firstname'); ?>
												</div>
												<div class="form-row">
													<?=$this->form->label('lastname', 'Last Name', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('lastname', array('class' => 'inputbox')); ?>
													<?=$this->form->error('lastname'); ?>
												</div>
												<div class="form-row">
													<?=$this->form->label('address', 'Address', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('address', array('class' => 'inputbox')); ?>
													<?=$this->form->error('address'); ?>
												</div>
											 	<div class="form-row">
													<?=$this->form->label('city', 'City', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('city', array('class' => 'inputbox')); ?>
													<?=$this->form->error('city'); ?>
												</div>
												<div class="form-row">
													<?=$this->form->label('state', 'State', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('state', array('class' => 'inputbox')); ?>
													<?=$this->form->error('state'); ?>
												</div>
												<div class="form-row">
													<?=$this->form->label('zip', 'Zip', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('zip', array('class' => 'inputbox', 'id' => 'zip')); ?>
													<?=$this->form->error('zip'); ?>
												</div>
												<div class="form-row">
													<?=$this->form->label('phone', 'Phone', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('phone', array('class' => 'inputbox', 'id' => 'phone')); ?>
													<?=$this->form->error('phone'); ?>
												</div>
											<?=$this->form->submit('Confirm new shipping details')?>
										</div>
											<?php if (!empty($order->tracking_numbers)): ?>
												<div>
												<h2 class="gray mar-b">Order Tracking<span style="font-size:11px; float:right; font-weight:normal;"></h2><hr />
												<table cellspacing="0" cellpadding="0" border="0" width="695">
												<tr>
													<td valign="top">
												</tr>
												<tr>
												<td colspan="4"><!-- start order tracking table -->
													<table cellpadding="0" cellspacing="0" border="0" width="100%">
														<tr style="background:#ddd;">
															<td style="padding:5px; width:30px;"><strong>N°</strong></td>
															<td style="padding:5px; width:400px;"><strong>Tracking Number</strong></td>
														</tr>
														<?php $numbers = $order->tracking_numbers->data(); ?>
														<?php $n=0; ?>
														<?php foreach ($numbers as $number): ?>
														<tr>
															<td style="padding:5px" title="number">
															<?=$n?>
															</td>
															<td style="padding:5px" title="type">
															<?=$this->shipment->link($number, array('type' => $order['shippingMethod']))?>
															</td>
														<?php $n++; ?>
														<?php endforeach ?>
														</tr>
													</table>
												</td><!-- end order tracking table -->
												</tr>
												</table>
												</div>
											<?php endif ?>
										<?php if (!empty($order->modifications)): ?>
											<div>
											<h2 class="gray mar-b">Modifications Logs <span style="font-size:11px; float:right; font-weight:normal;"></h2><hr />
											<table cellspacing="0" cellpadding="0" border="0" width="695">
											<tr>
												<td valign="top">
											</tr>
											<tr>
											<td colspan="4"><!-- start order modifications table -->
												<table cellpadding="0" cellspacing="0" border="0" width="100%">
													<tr style="background:#ddd;">
														<td style="padding:5px; width:30px;"><strong>N°</strong></td>
														<td style="padding:5px; width:100px;"><strong>Type</strong></td>
														<td style="padding:5px; width:100px;"><strong>Author</strong></td>
														<td style="padding:5px; width:200px;"><strong>Date</strong></td>
													</tr>
													<?php $modifications = $order->modifications->data(); ?>
													<?php krsort($modifications); ?>
													<?php $n=0; ?>
													<?php foreach ($modifications as $modification): ?>
														<?php if ($n<6): ?>
															<tr>
																<td style="padding:5px" title="number">
																	<?=$n?>
																</td>
																<td style="padding:5px" title="type">
																	<?=ucfirst($modification["type"])?>
																</td>
																<td style="padding:5px" title="author">
																	<?=$modification["author"]?>
																</td>
																<td style="padding:5px" title="date">
																	<?=date('Y-M-d h:i:s', $modification["date"]["sec"])?>
																</td>
															</tr>
															<?php $n++; ?>
														<?php endif ?>
													<?php endforeach ?>
												</table>
											</td><!-- end order modifications table -->
											</tr>
											</table>
											</div>
										<?php endif ?>
										<br />
								<h2 class="gray mar-b">Order Summary <span style="font-size:11px; float:right; font-weight:normal;"><span style="font-weight:bold;">NOTE:</span> Your order will be delivered within 3-5 weeks</span></h2>
								<hr />
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
													<?=$this->html->image("$image", array(
														'width' => "60",
														'height' => "60",
														'style' => "margin:2px; padding:2px; background:#fff; border:1px solid #ddd;"
														));
													?>
												</td>
												<td style="padding:5px" title="description">
													Event: <?=$this->html->link($item['event_name'], array(
														'Events::preview', 'args' => $item['event_id']),
														array('target' =>'_blank')
													); ?><br />
													Item: <?=$this->html->link($item['description'],
														array('Items::preview', 'args' => $item['url']),
														array('target' =>'_blank')
													); ?><br />
													Color: <?=$item['color']?><br/>
													Size: <?=$item['size']?><br/>
													Vendor Style: <?=$sku["$item[item_id]"];?><br/>
													Category: <?=$item['category'];?><br/>
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
												  <strong>Shipping Address:</strong><br />
													<?=$order->shipping->firstname;?> <?=$order->shipping->lastname;?><br>
                                                                                                <?=$order->shipping->address; ?> <?=$order->shipping->address_2; ?><br />
                                                                                                <?=$order->shipping->city; ?>, <?=$order->shipping->state; ?>
                                                                                                <?=$order->shipping->zip; ?>
												<hr /></div>
													<div style="display:block; margin-bottom:10px; width:320px;">
													  <strong>Billing Address:</strong><br />												<?=$order->billing->firstname;?> <?=$order->billing->lastname;?><br>
	                                                                                                <?=$order->billing->address; ?> <?=$order->billing->address_2; ?><br />
	                                                                                                <?=$order->billing->city; ?>, <?=$order->billing->state; ?>
	                                                                                                <?=$order->billing->zip; ?>
													<hr /></div>
												<div style=" width:320px; display:block;"><strong>Payment Info:</strong> <br /><?=strtoupper($order->card_type)?> ending with <?=$order->card_number?></div>
											</td>
											<td>
											
											</td>
										</tr>
										<tr>
											<td>

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
		</table>
<?php else: ?>
	<strong>Sorry, we cannot locate the order that you are looking for.</strong>
<?php endif ?>
<script type="text/javascript">
jQuery(function($){
	$("#zip").mask("99999");
	$("#phone").mask("(999) 999-9999");
});
</script>
<script type="text/javascript" >
$(document).ready(function(){
	$("#update_shipping").click(function () {
		if ($("#new_shipping").is(":hidden")) {
			$("#new_shipping").show("slow");
		} else {
			$("#new_shipping").slideUp();
		}
	});
	$("#cancel_button").click(function () {
		if (confirm('Are you sure to cancel the order ?')) {
			$.post("../cancel", $("#cancelForm").serialize());
			window.setTimeout('location.reload()', 500);
		}
	});
	$("#uncancel_button").click(function () {
		if (confirm('Are you sure to uncancel the order ?')) {
			$.post("../cancel", $("#cancelForm").serialize());
			window.setTimeout('location.reload()', 500);
		}
	});
});
</script>