<div id="entire">
<?php echo $this->html->script('jquery-1.4.2');?>
<?php echo $this->html->script('jquery.maskedinput-1.2.2')?>
<?php echo $this->html->script('jquery.dataTables.js');?>
<?php echo $this->html->style(array('jquery_ui_blitzer.css', 'table'));?>
<?php
	$this->title(" - Order Confirmation");
?>
<?php if ($order): ?>
	<style type="text/css">
		td{font-family:Arial,sans-serif;color:#888888;font-size:14px;line-height:18px}
		img{border:none}
	</style>

	<div id="order_notice" style="width:100%;background-color:#DBD7D9;">
		<h4>Order Notices:</h4>
		<ul>
			<?php 
				if ($processed_count > 0) {
					echo "<li>Order has been processed and sent to DotCom</li>";
				} else {
					if ($order->auth_error) {
						echo "<li><strong>$order->auth_error</strong></li>";
						echo "<li><strong>Order has not been processed and sent to DotCom</strong></li>";
					} else {
						echo "No Notices";
					}
				}
			?>
		</ul>
	</div>
	<hr/>
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
									<div style="text-align:center;"><button id="uncancel_button" style="font-weight:bold;font-size:14px;text-align: center;"> UnCancel Order</button></div>
									<div id="uncancel_form" style="display:none">
										<?php echo $this->form->create(null ,array('id'=>'uncancelForm','enctype' => "multipart/form-data")); ?>
										<?php echo $this->form->hidden('id', array('class' => 'inputbox', 'id' => 'id', 'value' => $order["_id"])); ?>
										<?php echo $this->form->hidden('uncancel_action', array('class' => 'inputbox', 'id' => 'uncancel_action', 'value' => 1)); ?>
										<?php echo $this->form->end();?>
									</div>
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
						<?php echo $this->form->textarea("comment_text", array('style' => 'width:100%;', 'row' => '6', 'id' => "comment_text")); ?>
										</div><br />
									</div>
									<div id="normal" style="display:block">
										<p style="border:1px solid #ddd; background:#f7f7f7; padding:10px; font-size:14px; text-align:center; color:black;">
											<b>Order ID</b> : <?php echo $order['order_id'] ?><br />
											<b>AuthKey :</b> <?php echo $order['authKey'] ?><br />
											<b>Order Status :</b> <?php echo $orderStatus ?><br />
										</p>
										<p style="border:1px solid #ddd; background:#f7f7f7; padding:10px; font-size:14px; text-align:center; color:red;">
											The order is expected to ship on <?php echo date('M d, Y', $shipDate)?>
										</p>
										<p style="text-align:center;">
										<?php if(!$hasDigitalItems): ?>
											<button id="cancel_button" style="font-weight:bold;font-size:14px;"> Cancel Order</button>
										<?php endif ?>
											<!--<button id="full_order_tax_return_button" style="font-weight:bold;font-size:14px;"> Full Order TAX Return</button>-->
											<!--<button id="part_order_tax_return_button" style="font-weight:bold;font-size:14px;"> Part Order TAX Return</button>-->
										<?php if(empty($order['payment_date']) && empty($order['cancel']) && ($order['authTotal'] == $order['total'])) : ?>
											<button id="capture_button" style="font-weight:bold;font-size:14px;">Capture Full Order Amount</button>
										<?php endif; ?>
											<button id="update_shipping" style="font-weight:bold;font-size:14px;">Update Shipping</button>
											<button id="update_payment" style="font-weight:bold;font-size:14px;">Update Payment Information</button>
											<button id="refresh_total" style="font-weight:bold;font-size:14px;">Refresh & Update Total</button>
										</p>
									</div>
							
									<?php /**/ ?>
								  	<div id="full_order_tax_return_form" style="display:none">
										<?php echo $this->form->create(null ,array( 'action'=>'taxreturn', 'id'=>'fullOrderTaxReturnForm','enctype' => "multipart/form-data")); ?>
										<?php echo $this->form->hidden('id', array('class' => 'inputbox', 'id' => 'id', 'value' => $order["_id"]));?>
										<?php echo $this->form->hidden('fullordertaxreturn_action', array('class' => 'inputbox', 'id' => 'fullordertaxreturn_action', 'value' => 1)); ?>
										<?php echo $this->form->end();?>
									</div>
								  	<div id="part_order_tax_return_form" style="display:none">
										<?php echo $this->form->create(null ,array( 'action'=>'taxreturn', 'id'=>'partOrderTaxReturnForm','enctype' => "multipart/form-data")); ?>
										<?php echo $this->form->hidden('id', array('class' => 'inputbox', 'id' => 'id', 'value' => $order["_id"]));?>
										<?php echo $this->form->hidden('partordertaxreturn_action', array('class' => 'inputbox', 'id' => 'partordertaxreturn_action', 'value' => 1)); ?>
										<?php echo $this->form->end();?>
									</div>
									<?php /**/ ?>
									<div id="cancel_form" style="display:none">
										<?php echo $this->form->create(null ,array('id'=>'cancelForm','enctype' => "multipart/form-data")); ?>
										<?php echo $this->form->hidden('id', array('class' => 'inputbox', 'id' => 'id', 'value' => $order["_id"])); ?>
										<?php echo $this->form->hidden('cancel_action', array('class' => 'inputbox', 'id' => 'cancel_action', 'value' => 1)); ?>
										<?php echo $this->form->hidden('comment', array('class' => 'textarea', 'id' => 'comment')); ?>
										<?php echo $this->form->end();?>
									</div>
	
									<div id="capture_form" style="display:none">
										<?php echo $this->form->create(null ,array('id'=>'captureForm','enctype' => "multipart/form-data")); ?>
										<?php echo $this->form->hidden('capture_action', array('class' => 'inputbox', 'id' => 'cancel_action', 'value' => 1)); ?>
										<?php echo $this->form->end();?>
									</div>
									<div id="order_file" style="display:none">
										<?=$this->form->create(null ,array('id'=>'newOrderFileForm','enctype' => "multipart/form-data")); ?>
										<?=$this->form->hidden("process-as-an-exception", array('class' => 'inputbox', 'id' => "process-as-an-exception")); ?>
										<?=$this->form->submit('OK')?>
										<?=$this->form->end();?>
									</div>
									<div id="new_shipping" style="display:none">
											<h2 id="new_shipping_address">New shipping address</h2>
											<?php echo $this->form->create(null ,array('id'=>'newShippingForm','enctype' => "multipart/form-data")); ?>
												<div class="form-row">
													<?php echo $this->form->label('firstname', 'First Name', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?php echo $this->form->text('firstname', array('class' => 'inputbox')); ?>
													<?php echo $this->form->error('firstname'); ?>
												</div>
												<div class="form-row">
													<?php echo $this->form->label('lastname', 'Last Name', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?php echo $this->form->text('lastname', array('class' => 'inputbox')); ?>
													<?php echo $this->form->error('lastname'); ?>
												</div>
												<div class="form-row">
													<?php echo $this->form->label('address', 'Address', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?php echo $this->form->text('address', array('class' => 'inputbox')); ?>
													<?php echo $this->form->error('address'); ?>
												</div>
											 	<div class="form-row">
													<?php echo $this->form->label('city', 'City', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?php echo $this->form->text('city', array('class' => 'inputbox')); ?>
													<?php echo $this->form->error('city'); ?>
												</div>
												<div class="form-row">
													<?php echo $this->form->label('state', 'State', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?php echo $this->form->text('state', array('class' => 'inputbox')); ?>
													<?php echo $this->form->error('state'); ?>
												</div>
												<div class="form-row">
													<?php echo $this->form->label('zip', 'Zip', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
													<?php echo $this->form->text('zip', array('class' => 'inputbox', 'id' => 'zip')); ?>
													<?php echo $this->form->error('zip'); ?>
												</div>
												<div class="form-row">
													<?php echo $this->form->label('phone', 'Phone', array(
														'escape' => false,
														'class' => 'required'
														));
													?>
												<?php echo $this->form->text('phone', array('class' => 'inputbox', 'id' => 'phone')); ?>
													<?php echo $this->form->error('phone'); ?>
												</div>
											<?php echo $this->form->submit('Confirm new shipping details')?>
											<?php echo $this->form->end();?>
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
															<?php echo $n?>
															</td>
															<td style="padding:5px" title="type">
															<?php echo $this->shipment->link($number, array('type' => $order['shippingMethod']))?>
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
																	<?php echo $n?>
																</td>
																<td style="padding:5px" title="type">
																	<?php echo ucfirst($modification["type"])?>
																</td>
																<td style="padding:5px" title="author">
																	<?php echo $modification["author"]?>
																</td>
																<td style="padding:5px" title="date">
																	<?php echo date('Y-M-d h:i:s', $modification["date"])?>
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
													<td style="padding:5px; width:30px;"><strong></strong></td>
											</tr>
											<?php echo $this->form->create(null ,array('id'=>'itemsForm','enctype' => "multipart/form-data")); ?>
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
							<?php echo $this->form->hidden($name, array('class' => 'inputbox', 'id' => $name, 'value' => (string) $item["cancel"])); ?>
							<?php echo $this->form->hidden('id', array('class' => 'inputbox', 'id' => 'id', 'value' => $order["_id"])); ?>
												<tr class="item_line"
												<?php if($item["cancel"] == true) {
													echo "style='background-color:red;opacity:.5'";
													} ?>
													 id="<?php echo $key?>">
												<?php
													if (!empty($item['primary_image'])) {
														$image = '/image/'. $item['primary_image'] . '.jpg';
													} else {
														$image = "/img/no-image-small.jpeg";
													}
												?>
												<td style="padding:5px;" title="item">
													<?php echo $this->html->image("$image", array(
														'width' => "60",
														'height' => "60",
														'style' => "margin:2px; padding:2px; background:#fff; border:1px solid #ddd;"
														));
													?>
												</td>
												<td style="padding:5px" title="description">
													Event: <?php echo $this->html->link($item['event_name'], array(
														'Events::preview', 'args' => $item['event_id']),
														array('target' =>'_blank')
													); ?><br />
													Item: <?php echo $this->html->link($item['description'],
														array('Items::preview', 'args' => $item['url']),
														array('target' =>'_blank')
													); ?><br />
													Color: <?php echo $item['color']?><br/>
													Size: <?php echo $item['size']?><br/>
													Vendor Style: <?php echo $sku["$item[item_id]"];?><br/>
													Category: <?php echo $item['category'];?><br/>
												</td>
												<td style="padding:5px; color:#009900;" title="price">
													$<?php echo number_format($item['sale_retail'],2); ?>
												</td>
												<td style="padding:5px;" title="quantity">
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
													<?php echo $this->form->hidden("items[".$key."][initial_quantity]", array('class' => 'inputbox', 'id' => "initial_quantity", 'value' => $limit )); ?>
													 <?php if(!empty($item["digital"])) { 
															 echo $this->form->select('items['.$key.'][quantity]', $quantities, array('style' => 'float:left; width:50px; margin: 0px 20px 0px 0px;', 'id' => 'dd_qty', 'value' => $item['quantity'], 'disabled' => 'disabled'));
													} else {
															 echo $this->form->select('items['.$key.'][quantity]', $quantities, array('style' => 'float:left; width:50px; margin: 0px 20px 0px 0px;', 'id' => 'dd_qty', 'value' => $item['quantity'], 'onchange' => "change_quantity()"));
													}
													?>
													<?php if ($return_q>0){?>
													<?php echo $return_q; ?> return(s)
													<?php }?>
												</td>
												<td title="subtotal" style="padding:5px; color:#009900;">
													$<?php echo number_format(($item['quantity'] * $item['sale_retail']),2)?>
												</td>
												<?php if(empty($item["digital"])){ ?>
												<td>
													<div style="text-align:center;">
														<?php if($item["cancel"] == true){ ?>
									<a href="#" onclick="open_item('<?php echo $key?>')" id="open_button">
															<img src="/img/success-icon.png" width="20" height="20"></a>
														<?php } else {?>
									<a href="#" onclick="cancel_item('<?php echo $key?>')" id="remove_button">
										<img src="/img/error-icon.png" width="20" height="20"></a>
														<?php }//endelse?>
													</div>
												</td>
												<?php }//endelse?>
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
<?php echo $this->form->hidden("subTotal", array('class' => 'inputbox', 'id' => "subTotal", 'value' => $order->subTotal)); ?>
<?php echo $this->form->hidden("credit_used", array('class' => 'inputbox', 'id' => "credit_used", 'value' => $order->credit_used)); ?>
<?php echo $this->form->hidden("promo_discount", array('class' => 'inputbox', 'id' => "promo_discount", 'value' => $order->promo_discount)); ?>
<?php echo $this->form->hidden("discount", array('class' => 'inputbox', 'id' => "discount", 'value' => $order->discount)); ?>
<?php echo $this->form->hidden("service", array('class' => 'inputbox', 'id' => "service", 'value' => $service[0])); ?>
<?php echo $this->form->hidden("promo_code", array('class' => 'inputbox', 'id' => "promo_code", 'value' => $order->promo_code)); ?>
<?php echo $this->form->hidden("tax", array('class' => 'inputbox', 'id' => "tax", 'value' => $order->tax)); ?>
<?php echo $this->form->hidden("handling", array('class' => 'inputbox', 'id' => "handling", 'value' => $order->handling)); ?>
<?php echo $this->form->hidden("handlingDiscount", array('class' => 'inputbox', 'id' => "handlingDiscount", 'value' => $order->handlingDiscount)); ?>
<?php echo $this->form->hidden("overSizeHandling", array('class' => 'inputbox', 'id' => "overSizeHandling", 'value' => $order->overSizeHandling)); ?>
<?php echo $this->form->hidden("overSizeHandlingDiscount", array('class' => 'inputbox', 'id' => "overSizeHandlingDiscount", 'value' => $order->overSizeHandlingDiscount)); ?>
<?php echo $this->form->hidden("total", array('class' => 'inputbox', 'id' => "total", 'value' => $order->total)); ?>
<?php echo $this->form->hidden("original_credit_used", array('class' => 'inputbox', 'id' => "original_credit_used", 'value' => $order->original_credit_used)); ?>
<?php echo $this->form->hidden("user_total_credits", array('class' => 'inputbox', 'id' => "user_total_credits", 'value' => $order->user_total_credits )); ?>
<?php echo $this->form->hidden("promocode_disable", array('class' => 'inputbox', 'id' => "promocode_disable", 'value' => $order->promocode_disable )); ?>
<?php echo $this->form->hidden("isOnlyDigital", array('class' => 'inputbox', 'id' => "isOnlyDigital", 'value' => $order->isOnlyDigital )); ?>
<?php echo $this->form->hidden("payment_date", array('class' => 'inputbox', 'id' => "payment_date", 'value' => $order->payment_date )); ?>
<?php echo $this->form->hidden("auth_confirmation", array('class' => 'inputbox', 'id' => "auth_confirmation", 'value' => $order->auth_confirmation )); ?>
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
													Discount [<?php echo $order->promo_code;?>]:
													<br>
												<?php endif ?>
												<?php if ($order->discount): ?>
													Discount [<?php echo $order->service[0];?>]:
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
											$<?php echo number_format($order->subTotal,2); ?>
											<br>
											<?php if ($order->credit_used): ?>
												-$<?php echo number_format(abs($order->credit_used),2); ?>
												<br>
											<?php endif ?>
											<?php if (($order->promo_discount) && (empty($order->promocode_disable))): ?>
												-$<?php echo number_format(abs($order->promo_discount),2); ?>
												<br>
											<?php endif ?>
											<?php if ($order->discount): ?>
												-$<?php echo number_format(abs($order->discount),2); ?>
											<br>
											<?php endif ?>
											$<?php echo number_format($order->tax,2); ?>
											<br>
											$<?php echo number_format($order->handling,2); ?>
											<br>
											<?php if ($order->overSizeHandling): ?>
												$<?php echo number_format($order->overSizeHandling,2); ?>
												<br>
											<?php endif ?>
											<br><br>
											<strong style="font-weight:bold;color:#009900;">$<?php echo number_format($order->total,2); ?></strong>
											</td>
										</tr>
									</table>
									<?php endif ?>
									<table style="float:left;" valign="top">
										<tr>
											<td>
												<div style="display:block; margin-bottom:10px; width:320px;">
												  <strong>Shipping Address:</strong><br />
													<?php echo $order->shipping->firstname;?> <?php echo $order->shipping->lastname;?><br>
                                                                                                <?php echo $order->shipping->address; ?> <?php echo $order->shipping->address_2; ?><br />
                                                                                                <?php echo $order->shipping->city; ?>, <?php echo $order->shipping->state; ?>
                                                                                                <?php echo $order->shipping->zip; ?>
												<hr /></div>
													<div style="display:block; margin-bottom:10px; width:320px;">
													  <strong>Billing Address:</strong><br />												<?php echo $order->billing->firstname;?> <?php echo $order->billing->lastname;?><br>
	                                                                                                <?php echo $order->billing->address; ?> <?php echo $order->billing->address_2; ?><br />
	                                                                                                <?php echo $order->billing->city; ?>, <?php echo $order->billing->state; ?>
	                                                                                                <?php echo $order->billing->zip; ?>
																									<?php if (!empty($order->billing->telephone)): ?>
		                                                                                                <br><?php echo $order->billing->telephone; ?>
																									<?php endif ?>
													<hr /></div>
												<div style=" width:320px; display:block;"><strong>Payment Info:</strong> <br /><?php echo strtoupper($order->card_type)?> ending with <?php echo $order->card_number?></div>
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
	<?php if($itemscanceled == false): ?>
	<?php echo $this->form->hidden("save", array('class' => 'inputbox', 'id' => "save")); ?>
	Commment :
	<?php echo $this->form->textarea("comment", array('style' => 'width:100%;', 'row' => '6', 'id' => "comment")); ?>
	<div style="clear:both"></div>
	<p style="text-align:center;">
		<input type="submit" id="update_button"  onclick="update_order(); return false;" value="Update Order"/>
	</p>
	<?php endif ?>
	<?php echo $this->form->end();?>
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
	$("#uncancel_button").click(function () {
		$('#uncancelForm').submit();
	});
	$("#capture_button").click(function () {
		if (confirm('Are you sure to capture this order ?')) {
			$('#capture_action').val(true);
			$('#captureForm').submit();
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
$(document).ready(function(){
	$("#refresh_total").click(function () {
		if (confirm('Are you sure to refresh and update the order total ?')) {
			$('#save').val("false");
  			$('#itemsForm').submit();
  			$('#save').val("true");
  			$('#itemsForm').submit();
		}
	});
});
function open_comment(val) {
	// Create a regular expression to search all +s in the string
	var lsRegExp = /\+/g;
	// Return the decoded string
    val = unescape(String(val).replace(lsRegExp, " "));
	alert(val);
}
</script>