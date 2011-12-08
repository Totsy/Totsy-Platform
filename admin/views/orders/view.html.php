<div id="entire">
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
								<?php else: ?>

									<div id='confirm_cancel_div' style="display:none">
									<p style="border:1px solid #ddd; background:#f7f7f7; padding:10px; font-size:14px; text-align:center; color:red;">
										Are you really sure you want to cancel this order ? You won't be able to go back.
									</p>
									<div style="text-align:center;">
										<button id="confirm_cancel" style="font-weight:bold;font-size:14px;">Confirm Cancel</button>
										<button id="abort_cancel" style="font-weight:bold;font-size:14px;">Abort</button>
										<div style="clear:both"></div><br \>
										Commment :
						<?=$this->form->textarea("comment_text", array('style' => 'width:100%;', 'row' => '6', 'id' => "comment_text")); ?>
										</div><br />
									</div>
									<div id="normal" style="display:block">
										<p style="border:1px solid #ddd; background:#f7f7f7; padding:10px; font-size:14px; text-align:center; color:red;">
										The order is expected to ship on <?=date('M d, Y', $shipDate)?>
										</p>
									<?php if($edit_mode): ?>
									<p style="text-align:center;">
<button id="full_order_tax_return_button" style="font-weight:bold;font-size:14px;"> Full Order TAX Return</button>
									<button id="part_order_tax_return_button" style="font-weight:bold;font-size:14px;"> Part Order TAX Return</button>
									<button id="cancel_button" style="font-weight:bold;font-size:14px;"> Cancel Order</button>
									<button id="update_shipping" style="font-weight:bold;font-size:14px;">Update Shipping</button>
									<button id="update_payment" style="font-weight:bold;font-size:14px;">Update Payment Information</button>
									<button id="generate_order_file" style="font-weight:bold;font-size:14px;">Generate Order File</button>
									</p></div>
								<?php endif ?>
									<?php /**/ ?>
								  	<div id="full_order_tax_return_form" style="display:none">
										<?=$this->form->create(null ,array( 'action'=>'taxreturn', 'id'=>'fullOrderTaxReturnForm','enctype' => "multipart/form-data")); ?>
										<?=$this->form->hidden('id', array('class' => 'inputbox', 'id' => 'id', 'value' => $order["_id"]));?>
										<?=$this->form->hidden('fullordertaxreturn_action', array('class' => 'inputbox', 'id' => 'fullordertaxreturn_action', 'value' => 1)); ?>
										<?=$this->form->end();?>
									</div>
								  	<div id="part_order_tax_return_form" style="display:none">
										<?=$this->form->create(null ,array( 'action'=>'taxreturn', 'id'=>'partOrderTaxReturnForm','enctype' => "multipart/form-data")); ?>
										<?=$this->form->hidden('id', array('class' => 'inputbox', 'id' => 'id', 'value' => $order["_id"]));?>
										<?=$this->form->hidden('partordertaxreturn_action', array('class' => 'inputbox', 'id' => 'partordertaxreturn_action', 'value' => 1)); ?>
										<?=$this->form->end();?>
									</div>
									<?php /**/ ?>
									<div id="cancel_form" style="display:none">
										<?=$this->form->create(null ,array('id'=>'cancelForm','enctype' => "multipart/form-data")); ?>
										<?=$this->form->hidden('id', array('class' => 'inputbox', 'id' => 'id', 'value' => $order["_id"])); ?>
										<?=$this->form->hidden('cancel_action', array('class' => 'inputbox', 'id' => 'cancel_action', 'value' => 1)); ?>
										<?=$this->form->hidden('comment', array('class' => 'textarea', 'id' => 'comment')); ?>
										<?=$this->form->end();?>
									</div>
									<div id="order_file" style="display:none">
										<?=$this->form->create(null ,array('id'=>'newOrderFileForm','enctype' => "multipart/form-data")); ?>
										<?=$this->form->hidden("process-as-an-exception", array('class' => 'inputbox', 'id' => "process-as-an-exception")); ?>
										<?=$this->form->submit('OK')?>
										<?=$this->form->end();?>
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
											<?=$this->form->end();?>
										</div>
										<div id="new_payment" style="display:none">
											<?=$this->form->create(null ,array('id'=>'newPaymentForm','enctype' => "multipart/form-data")); ?>
												<h2 id="new_credit_card_infos">Credit Card Informations</h2>
												<div class="form-row">
													<?=$this->form->label('type', 'Type', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->select('creditcard[type]', array('visa' => 'Visa', 'mc' => 'MasterCard','amex' => 'American Express'), array('id' => 'card_type', 'class'=>'inputbox')); ?>
													<?=$this->form->error('creditcard[type]'); ?>
												</div>												
												<div class="form-row">
													<?=$this->form->label('number', 'Number', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('creditcard[number]', 
														array('class' => 'inputbox', 'id' => 'creditcard[number]')); ?>
													<?=$this->form->error('number'); ?>
												</div>
												<div class="form-row">
													<?=$this->form->label('month', 'Month', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
												<?=$this->form->text('creditcard[month]', 
													array('class' => 'inputbox', 'id' => 'creditcard[month]')); ?>
													<?=$this->form->error('month'); ?>
												</div>
												<div class="form-row">
													<?=$this->form->label('year', 'Year', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('creditcard[year]', array('class' => 'inputbox', 'id' => 'creditcard[year]')); ?>
													<?=$this->form->error('year'); ?>
												</div>
												<div class="form-row">
													<?=$this->form->label('code', 'Code', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('creditcard[code]', array('class' => 'inputbox', 'id' => 'creditcard[code]')); ?>
													<?=$this->form->error('code'); ?>
												</div>
												<h2 id="new_billing_infos">Billing Informations</h2>
												<div class="form-row">
													<?=$this->form->label('firstname', 'First Name', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('billing[firstname]', array('class' => 'inputbox')); ?>
													<?=$this->form->error('firstname'); ?>
												</div>
												<div class="form-row">
													<?=$this->form->label('lastname', 'Last Name', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('billing[lastname]', array('class' => 'inputbox')); ?>
													<?=$this->form->error('lastname'); ?>
												</div>
												<div class="form-row">
													<?=$this->form->label('address', 'Address', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('billing[address]', array('class' => 'inputbox')); ?>
													<?=$this->form->error('address'); ?>
												</div>
											 	<div class="form-row">
													<?=$this->form->label('city', 'City', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('billing[city]', array('class' => 'inputbox')); ?>
													<?=$this->form->error('city'); ?>
												</div>
												<div class="form-row">
													<?=$this->form->label('state', 'State', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('billing[state]', array('class' => 'inputbox')); ?>
													<?=$this->form->error('state'); ?>
												</div>
												<div class="form-row">
													<?=$this->form->label('zip', 'Zip', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?=$this->form->text('billing[zip]', array('class' => 'inputbox', 'id' => 'zip')); ?>
													<?=$this->form->error('zip'); ?>
												</div>
												<div class="form-row">
													<?=$this->form->label('phone', 'Phone', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
												<?=$this->form->text('billing[phone]', array('class' => 'inputbox', 'id' => 'phone')); ?>
													<?=$this->form->error('phone'); ?>
												</div>
											<?=$this->form->submit('Create new Authorization')?>
											<?=$this->form->end();?>
										</div>
										<?php endif ?>
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
														<td style="padding:5px; width:30px;"><strong>Comment</strong></td>
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
																<?php if(!empty($modification["comment"])) :?>
																<td style="padding:5px" title="comment">
							<a href="#" onclick='open_comment("<?php echo(urlencode($modification["comment"])) ;?>")' re >See</a>
																</td>
																<?php endif ?>
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
										<?php if($itemscanceled == false): ?>
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
												<td style="padding:5px; width:80px;"><strong>Subtotal</strong></td>
												<?php if($edit_mode): ?>
													<td style="padding:5px; width:30px;"><strong></strong></td>
												<?php endif ?>
											</tr>
											<?=$this->form->create(null ,array('id'=>'itemsForm','enctype' => "multipart/form-data")); ?>
											<?php $items = $order->items; ?>
											<?php foreach ($items as $key => $item): ?>
											<?php $name = "items[".strval($key)."][cancel]"; ?>
											<?php $itm = $item->data();
												  if (array_key_exists('return',$itm)){ 
														$return_q = array_sum($itm['return']); 
												  } else { 
												  		$return_q = 0; 
												  }
												  unset($itm);

											?>
							<?=$this->form->hidden($name, array('class' => 'inputbox', 'id' => $name, 'value' => (string) $item["cancel"])); ?>
							<?=$this->form->hidden('id', array('class' => 'inputbox', 'id' => 'id', 'value' => $order["_id"])); ?>
												<tr class="item_line"
												<?php if($item["cancel"] == true) {
													echo "style='background-color:red;opacity:.5'";
													} ?>
													 id="<?=$key?>">
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
												<?php if($edit_mode): ?>
												<?php  
													if(!empty($item['initial_quantity'])) {
														$limit = $item['initial_quantity'];
													} else {
														$limit = $item['quantity'];
													}
													$i = 0;
													$quantities = array();
													do {
														$quantities[$i] = $i;
														$i++;
													} while ($i <= $limit)
													?>
													<?=$this->form->hidden("items[".$key."][initial_quantity]", array('class' => 'inputbox', 'id' => "initial_quantity", 'value' => $limit )); ?>
													<?=$this->form->select('items['.$key.'][quantity]', $quantities, array('style' => 'float:left; width:50px; margin: 0px 20px 0px 0px;', 'id' => 'dd_qty', 'value' => $item['quantity'], 'onchange' => "change_quantity()"));
													?>
													<?php else :?>
														<?=$item['quantity'] ?>
													<?php endif ?>
													<?php if ($return_q>0){?>
													<?php echo $return_q; ?> return(s)
													<?php }?>
												</td>
												<td title="subtotal" style="padding:5px; color:#009900;">
													$<?php echo number_format(($item['quantity'] * $item['sale_retail']),2)?>
												</td>
												<?php if($edit_mode): ?>
												<td>
													<div style="text-align:center;">
														<?php if($item["cancel"] == true){ ?>
									<a href="#" onclick="open_item('<?=$key?>')" id="open_button">
															<img src="/img/success-icon.png" width="20" height="20"></a>
														<?php } else {?>
									<a href="#" onclick="cancel_item('<?=$key?>')" id="remove_button">
										<img src="/img/error-icon.png" width="20" height="20"></a>
														<?php }//endelse?>
													</div>
												</td>
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
							<tr>
								<td style="padding:0px 0px 5px 0px; margin:0px;"><hr /></td>
							</tr>
							<tr>
								<td colspan="4">
<!-- HIDDEN DATAS - ITEMS -->
<?=$this->form->hidden("subTotal", array('class' => 'inputbox', 'id' => "subTotal", 'value' => $order->subTotal)); ?>
<?=$this->form->hidden("credit_used", array('class' => 'inputbox', 'id' => "credit_used", 'value' => $order->credit_used)); ?>
<?=$this->form->hidden("promo_discount", array('class' => 'inputbox', 'id' => "promo_discount", 'value' => $order->promo_discount)); ?>
<?=$this->form->hidden("discount", array('class' => 'inputbox', 'id' => "discount", 'value' => $order->discount)); ?>
<?=$this->form->hidden("service", array('class' => 'inputbox', 'id' => "service", 'value' => $service[0])); ?>
<?=$this->form->hidden("promo_code", array('class' => 'inputbox', 'id' => "promo_code", 'value' => $order->promo_code)); ?>
<?=$this->form->hidden("tax", array('class' => 'inputbox', 'id' => "tax", 'value' => $order->tax)); ?>
<?=$this->form->hidden("handling", array('class' => 'inputbox', 'id' => "handling", 'value' => $order->handling)); ?>
<?=$this->form->hidden("handlingDiscount", array('class' => 'inputbox', 'id' => "handlingDiscount", 'value' => $order->handlingDiscount)); ?>
<?=$this->form->hidden("overSizeHandling", array('class' => 'inputbox', 'id' => "overSizeHandling", 'value' => $order->overSizeHandling)); ?>
<?=$this->form->hidden("overSizeHandlingDiscount", array('class' => 'inputbox', 'id' => "overSizeHandlingDiscount", 'value' => $order->overSizeHandlingDiscount)); ?>
<?=$this->form->hidden("total", array('class' => 'inputbox', 'id' => "total", 'value' => $order->total)); ?>
<?=$this->form->hidden("original_credit_used", array('class' => 'inputbox', 'id' => "original_credit_used", 'value' => $order->original_credit_used)); ?>
<?=$this->form->hidden("user_total_credits", array('class' => 'inputbox', 'id' => "user_total_credits", 'value' => $order->user_total_credits )); ?>
<?=$this->form->hidden("promocode_disable", array('class' => 'inputbox', 'id' => "promocode_disable", 'value' => $order->promocode_disable )); ?>
									<!--- END HIDDEN DATAS - ITEMS -->
									<?php if(empty($order->cancel)): ?>
									<table style="width:250px; float: right;">
										<tr>
											<td valign="top">
												Order Subtotal:
												<br>
												<?php if ($order->credit_used): ?>
												Credit Applied:
												<br>
												<?php endif ?>
												<?php if ((($order->promo_discount) && (empty($order->promocode_disable)))): ?>
													Discount [<?=$order->promo_code;?>]:
													<br>
												<?php endif ?>
												<?php if ($order->discount): ?>
													Discount [<?=$order->service[0];?>]:
													<br>
												<?php endif ?>
												Sales Tax:
												<br>
												Shipping:
												<br>
												<?php if ($order->overSizeHandling): ?>
												Oversize Shipping:
												<br>
												<?php endif ?>
												<br><br>
												<strong style="font-weight:bold;color:#606060">Total:</strong>
											</td>
											<td style="padding-left:15px; text-align:right;" valign="top">
											$<?=number_format($order->subTotal,2); ?>
											<br>
											<?php if ($order->credit_used): ?>
												-$<?=number_format(abs($order->credit_used),2); ?>
												<br>
											<?php endif ?>
											<?php if (($order->promo_discount) && (empty($order->promocode_disable))): ?>
												-$<?=number_format(abs($order->promo_discount),2); ?>
												<br>
											<?php endif ?>
											<?php if ($order->discount): ?>
												-$<?=number_format(abs($order->discount),2); ?>
											<br>
											<?php endif ?>
											$<?=number_format($order->tax,2); ?>
											<br>
											$<?=number_format($order->handling,2); ?>
											<br>
											<?php if ($order->overSizeHandling): ?>
												$<?=number_format($order->overSizeHandling,2); ?>
												<br>
											<?php endif ?>
											<br><br>
											<strong style="font-weight:bold;color:#009900;">$<?=number_format($order->total,2); ?></strong>
											</td>
										</tr>
									</table>
									<?php endif ?>
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
									<?php endif ?>
								</td>
							</tr>
		<tr>
			<td style="padding:0px 0px 5px 0px;"><hr></td>
		</tr>
	</table>
	<?php if($edit_mode): ?>
	<?php if($itemscanceled == false): ?>
	<?=$this->form->hidden("save", array('class' => 'inputbox', 'id' => "save")); ?>
	Commment :
	<?=$this->form->textarea("comment", array('style' => 'width:100%;', 'row' => '6', 'id' => "comment")); ?>
	<div style="clear:both"></div>
	<p style="text-align:center;">
		<input type="submit" id="update_button"  onclick="update_order(); return false;" value="Update Order"/>
	</p>
	<?php endif ?>
	<?=$this->form->end();?>
	<?php endif ?>
<?php else: ?>
	<strong>Sorry, we cannot locate the order that you are looking for.</strong>
<?php endif ?>
</div>
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
	$("#generate_order_file").click(function () {
		if (confirm('Are you sure to send this order as exception to Dotcom ?')) {
			$('input[name="process-as-an-exception"]').val("true");
			$('#newOrderFileForm').submit();
		}
	});
	$("#update_payment").click(function () {
		if ($("#new_payment").is(":hidden")) {
			$("#new_payment").show("slow");
		} else {
			$("#new_payment").slideUp();
		}
	});
	$("#confirm_cancel").click(function () {
		var test = $('textarea#comment_text').val();
		if(test == "") {
			alert("Please fill the comment section before updating");
			return false;
		} else {
			$('#comment').val(test);
			$('#cancelForm').submit();
		}
	});
	$("#abort_cancel").click(function () {
		if ($("#normal").is(":hidden")) {
			$("#normal").show("slow");
			$("#confirm_cancel_div").slideUp();
		}
	});
	$("#cancel_button").click(function () {
		if ($("#confirm_cancel_div").is(":hidden")) {
			$("#confirm_cancel_div").show("slow");
			$("#normal").slideUp();
		}
	});
	$('#full_order_tax_return_button').click(function(){
		if (confirm('Are you sure to make full order TAX return? You won\'t be able to go back.')) {
	  		$('#fullOrderTaxReturnForm').submit();
		}
	});
	$('#part_order_tax_return_button').click(function(){
	  	$('#partOrderTaxReturnForm').submit();
	});
});
function change_quantity() {
	if (confirm('Are you sure to change the quantity of this item ?')) {
		$('#save').val("false");
  		$('#itemsForm').submit();
	}
};
function cancel_item(val) {
	if (confirm('Are you sure to remove this item ?')) {
		$('input[name="items['+val+'][cancel]"]').val("true");
		$('#save').val("false");
		$('#itemsForm').submit();
	}
};
function open_item(val) {
	if (confirm('Are you sure to reopen this item ?')) {
		$('input[name="items['+val+'][cancel]"]').val("false");
		$('#save').val("false");
		$('#itemsForm').submit();
	}
};

function update_order() {
	var test = $('textarea#comment').val();
	if(test == "") {
		alert("Please fill the comment section before updating");
		return false;
	} else {
		if(confirm('Are you sure to apply the changes to the order?')) {
			$('#save').val("true");
			$('#itemsForm').submit();
		}
	}
};
function open_comment(val) {
	// Create a regular expression to search all +s in the string
	var lsRegExp = /\+/g;
	// Return the decoded string
    val = unescape(String(val).replace(lsRegExp, " "));
	alert(val);
}
</script>