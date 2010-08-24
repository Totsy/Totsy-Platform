<?php
	$this->html->script('application', array('inline' => false));
	$this->form->config(array('text' => array('class' => 'inputbox')));
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
?>


<h1 class="page-title gray"><span class="red"><?=$this->title('Checkout - Payment Method'); ?></span></h1>

<div id="middle" class="fullwidth">

	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
		<p>
			<strong class="red"><?=$this->html->link('STEP 1 (Shipping/Billing Info)', array('Orders::add'));?></strong>
			&raquo;
			<strong class="red">STEP 2 (Payment)</strong>
			&raquo; STEP 3 (Confirmation)
		</p>
	<?php if ($errors = $order->errors()) { ?>
		<p>
			<strong>
				There were some errors processing your order.
				Please correct them before resubmitting.
			</strong>
			<?php foreach ($errors as $error): ?>
				<div class="checkout-error"><?=$error; ?></div>
			<?php endforeach ?>
			<br />
		</p>
	<?php } ?>

	<!-- Begin Order Details -->
	<?php if ($showCart): ?>
		<div class="head"><h2>Items in Your Cart</h2></div><br>
		<table width="100%" class="cart-table">
			<thead>
				<tr>
					<th>Item</th>
					<th>Description</th>
					<th>QTY</th>
					<th>Price</th>
					<th>Total Cost</th>
					<th>Time Remaining</th>
				</tr>
			</thead>
			<tbody>
		<?php $x = 0; ?>
		<?php foreach ($showCart as $item): ?>
			<!-- Build Product Row -->
						<tr id="<?=$item->_id?>" class="alt<?=$x?>">
						<td class="cart-th">
							<?php
								if (!empty($item->primary_image)) {
									$image = $item->primary_image;
									$productImage = "/image/$image.jpg";
								} else {
									$productImage = "/img/no-image-small.jpeg";
								}
							?>
							<?=$this->html->link(
								$this->html->image("$productImage", array(
									'width'=>'60',
									'height'=>'60')),
									'',
									array(
									'id' => 'main-logo', 'escape'=> false
								)
							); ?>
						</td>
						<td class="cart-desc">
							<?=$this->form->hidden("item$x", array('value' => $item->_id)); ?>
							<strong><?=$this->html->link($item->description, array(
								'Items::view',
								'args' => $item->url
								));
							?></strong><br>
							<strong>Color:</strong> <?=$item->color;?><br>
							<strong>Size:</strong><?=$item->size;?>
						</td>
						<td class="<?="qty-$x";?>">
							<?=$item->quantity;?>
						</td>
						<td class="<?="price-item-$x";?>">
							<strong>$<?=number_format($item->sale_retail,2)?></strong>
						</td>
						<td class="<?="total-item-$x";?>">
							<strong>$<?=number_format($item->sale_retail * $item->quantity ,2)?></strong>
						</td>
						<td class="cart-time"><div id="<?php echo "itemCounter-$x"; ?>"</div></td>
					</tr>
					<?php
						//Allow users three extra minutes on their items for checkout.
						$date = ($item->expires->sec * 1000);
						$itemCounters[] = "<script type=\"text/javascript\">
							$(function () {
								var itemProcessExpires = new Date($date);
								$(\"#itemCounter-$x\").countdown('change', {until: itemProcessExpires, $countLayout});

							$(\"#itemCounter-$x\").countdown({until: itemProcessExpires,
							    expiryText: '<div class=\"over\">This item is no longer reserved for purchase</div>', $countLayout});
							var now = new Date();
							if (itemProcessExpires < now) {
								$(\"#itemCounter-$x\").html('<div class=\"over\">This item is no longer reserved for purchase</div>');
							}
							});
							</script>";
						$x++;
					?>
		<?php endforeach ?>
				</tbody>
			</table>
	<?php endif ?>
	<?php $total = ($subTotal + $orderCredit->credit_amount) + $tax + $shippingCost; ?>

	<ol id="checkout-process">
	<!-- End Order Details -->
		<li id="order-summary" class="step">
			<table class="order-status" width="100%">
				<tr>
					<td style="text-align:right"><strong>Order Subtotal:</strong> </td>
					<td style="text-align:center">$<?=number_format((float) $subTotal, 2);?></td>
				</tr>
				<tr>
					<td style="text-align:right"><strong>Shipping:</strong> </td>
					<td style="text-align:center">$<?=number_format((float) $shippingCost, 2);?></td>
				</tr>
				<tr>
					<td style="text-align:right"><strong>Sales Tax:</strong> </td>
					<td style="text-align:center">$<?=number_format((float) $tax, 2);?></td>
				</tr>
				<tr>
					<?php if ($credit): ?>
						<?php $orderCredit->credit_amount = abs($orderCredit->credit_amount); ?>
						<?=$this->form->create($orderCredit); ?>
						<div class="form-row">
						<?=$this->form->error('amount'); ?>
						</div>
							<td style="text-align:right"><strong>Credit:</strong> </td>
							<td style="text-align:center">-$<?=number_format((float) $orderCredit->credit_amount, 2);?></td>
							<td style="text-align:center">
								$<?=$this->form->text('credit_amount', array('size' => 4, 'maxlength' => '6')); ?>
								<?=$this->form->submit('Apply Credit'); ?>
							</td>
						<?=$this->form->end(); ?>
					<?php endif ?>
				</tr>
				<tr>
					<td style="text-align:right"><strong>Total:</strong> </td>
					<td style="text-align:center">$<?=number_format((float) $total, 2);?></td>
				</tr>
			</table>
		</li>
<?=$this->form->create(); ?>
	<!-- Start Payment Information -->    
	<li id="opc-payment" class="step">
		<div id="checkout-process-payment">
		<table width="100%">
			<tr>
				<td>
					<div class="head">
						<h2>Payment Information</h2>
					</div>
					<fieldset>
						<legend class="no-show">New Payment Method</legend>

						<div class="form-row">
							<label for="cc-type" class="required">Credit Card Type<span>*</span></label>
							<?=$this->form->select('card[type]', array(
								'visa' => 'Visa',
								'mc' => 'MasterCard',
								'amex' => 'American Express'
							)); ?>
						</div>

						<div class="form-row">
							<label for="cc" class="required">Card Number<span>*</span></label>
							<?=$this->form->text('card[number]', array('id' => 'cc', 'class' => 'inputbox')); ?>
						</div>

						<div class="form-row">
							<label for="cc-exp" class="required">Expiration Date<span>*</span></label>
							<?=$this->form->select('card[month]', array(
								'' => 'Month',
								1 => 'January',
								2 => 'February',
								3 => 'March',
								4 => 'April',
								5 => 'May',
								6 => 'June',
								7 => 'July',
								8 => 'August',
								9 => 'September',
								10 => 'October',
								11 => 'November',
								12 => 'December'
							)); ?>
							<?php
								$now = intval(date('Y'));
								$years = array_combine(range($now, $now + 15), range($now, $now + 15));
							?>
							<?=$this->form->select('card[year]', array('' => 'Year') + $years); ?>
						</div>

						<div class="form-row">
							<label for="cc-ccv" class="required">CVV2 Code<span>*</span></label>
							<?=$this->form->text('card[code]', array('class' => 'inputbox')); ?>
						</div>
					</fieldset>
				</td>
				<td>
					<?php if ($billingAddr) { ?>
						<li>
							<h4>Billing Address</h4>
							<address class="billing-address">
								<?=$billingAddr->address; ?> <?=$billingAddr->address_2; ?><br />
								<?=$billingAddr->city; ?>, <?=$billingAddr->state; ?>
								<?=$billingAddr->zip; ?>
							</address>
					<?php } ?>
				</td>
				<td>
					<?php if ($shippingAddr) { ?>
							<h4>Shipping Address</h4>
							<address class="shipping-address">
								<?=$shippingAddr->address; ?> <?=$shippingAddr->address_2; ?><br />
								<?=$shippingAddr->city; ?>, <?=$shippingAddr->state; ?>
								<?=$shippingAddr->zip; ?>
							</address>
						</li>
					<?php } ?>
				</td>
			</tr>
		</table>
		
			<!-- <p>
				<label for="payment-method-select">Pay with:</label>
				<?=$this->form->select(
					'payment',
					array('' => 'New Credit Card'),
					array('id' => 'payment', 'value' => '1', 'target' => '#payment-method-form')
				); ?>
			</p> -->
			<li class="step">
				<fieldset>

					<p><a href="javascript:void(0)" id="gift" title="Want to include a gift message?">Want to include a gift message?</a></p>

					<div id="gift-message">
						<textarea name="gift-message" class="inputbox"></textarea>
					</div>

				</fieldset>

			</li>
			<li class="step">
				<?=$this->form->submit('Place Your Order', array('class' => 'place-order-button submit')); ?>
				&nbsp;&nbsp;
				<span class="red">*</span> Required Fields
			</li>
			<?=$this->form->hidden('credit_amount', array('value' => $orderCredit->credit_amount)); ?>
			<?=$this->form->end(); ?>
	</ol>
	
	<div class="bl"></div>
	<div class="br"></div>
	
	</div>

</div>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$('#gift').bind('click', function() {
			$('#gift-message').toggle();
		});
	});
</script>
<?php if (!empty($itemCounters)): ?>
	<?php foreach ($itemCounters as $counter): ?>
		<?php echo $counter ?>
	<?php endforeach ?>
<?php endif ?>
<?php if ($cartEmpty == true): ?>
	<script>
	$(document).ready(function() {
		$("#cart-modal").load($.base + 'cart/view').dialog({
			autoOpen: false,
			modal:true,
			width: 900,
			height: 600,
			close: function(ev, ui) {
				parent.location = "/events";
			}
		});
		$("#cart-modal").dialog('open');
	});
	</script>
<?php endif ?>