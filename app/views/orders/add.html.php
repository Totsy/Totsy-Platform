<?php $this->html->script('application', array('inline' => false)); ?>
<h1 class="p-header"><?=$this->title('Checkout'); ?></h1>

<div id="middle" class="noleft">

	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">

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

	<ol id="checkout-process">
		<?=$this->form->create($order, array('class' => 'checkout')); ?>

		<!-- Start Billing Information -->
		<li id="opc-billing">
			<div class="head"><h2>Billing Address</h2></div>

			<div id="checkout-process-billing">
				<p>Select a billing address from your address book or enter a new address.</p>

				<?=$this->form->select('billing', $billing + array('' => 'New Address...'), array(
					'id' => 'billing',
					'target' => '#billing-new-address-form',
					'value' => key($billing)
				)); ?>

				<fieldset id="billing-new-address-form">
					<legend class="no-show">New Billing Address</legend>

					<div class="form-row">
						<label for="fname" class="required">First Name<span>*</span></label>
						<input type="text" name="billing[firstName]" id="fname" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="lname" class="required">Last Name<span>*</span></label>
						<input type="text" name="billing[lastName]" id="lname" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="address" class="required">Street Address<span>*</span></label>
						<input type="text" name="billing[address]" id="address" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="address_2">Street Address 2</label>
						<input type="text" name="billing[address_2]" id="address_2" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="city" class="required">City<span>*</span></label>
						<input type="text" name="billing[city]" id="city" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="state" class="required">State/Province<span>*</span></label>
						<input type="text" name="billing[state]" id="state" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="zip" class="required">Zip/Postal Code<span>*</span></label>
						<input type="text" name="billing[zip]" id="zip" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="phone">Telephone</label>
						<input type="text" name="billing[phone]" id="phone" class="inputbox" value="" />
					</div>

					<div class="submit">
						<button class="flex-btn done">Done</button>
					</div>
				</fieldset>

				<fieldset>
					<p>
						<input type="radio" name="billing_shipping" id="billing:use_for_shipping_yes" value="1" checked="checked" />&nbsp;
						<label for="billing:use_for_shipping_yes">Ship to this address</label>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="billing_shipping" id="billing:use_for_shipping_no" value="0" />&nbsp;
						<label for="billing:use_for_shipping_no">Ship to different address</label>
					</p>
				</fieldset>

			</div>
		</li>
		<!-- End Billing Information -->

		<!-- Start Shipping Information -->
		<li id="opc-shipping" class="step" style="opacity:0.5">
			<div class="head">
				<h2>Shipping Address</h2>
			</div>

			<div id="checkout-process-shipping">

				<p>Select a shipping address from your address book or enter a new address.</p>

				<?=$this->form->select('shipping', $shipping + array('' => 'New Address...'), array(
					'id' => 'shipping',
					'target' => '#shipping-new-address-form',
					'value' => key($shipping)
				)); ?>

				<fieldset id="shipping-new-address-form">

					<legend class="no-show">New Shipping Address</legend>

					<div class="form-row">
						<label for="fname" class="required">First Name<span>*</span></label>
						<input type="text" name="shipping[firstName]" id="fname" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="lname" class="required">Last Name<span>*</span></label>
						<input type="text" name="shipping[lastName]" id="lname" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="address" class="required">Street Address<span>*</span></label>
						<input type="text" name="shipping[address]" id="address" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="address_2">Street Address 2</label>
						<input type="text" name="shipping[address_2]" id="address_2" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="city" class="required">City<span>*</span></label>
						<input type="text" name="shipping[city]" id="city" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="state" class="required">State/Province<span>*</span></label>
						<input type="text" name="shipping[state]" id="state" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="zip" class="required">Zip/Postal Code<span>*</span></label>
						<input type="text" name="shipping[zip]" id="zip" class="inputbox" value="" />
					</div>

					<div class="form-row">
						<label for="phone">Telephone</label>
						<input type="text" name="shipping[phone]" id="phone" class="inputbox" value="" />
					</div>

					<div class="submit">
						<button class="flex-btn done">Done</button>
					</div>
				</fieldset>

			</div>
		</li>
		<!-- End Shipping Information -->

		<li class="step">
			<fieldset>

				<p><a href="javascript:void(0)" id="gift" title="Want to include a gift message?">Want to include a gift message?</a></p>

				<div id="gift-message">
					<textarea name="gift-message" class="inputbox"></textarea>
				</div>

			</fieldset>

		</li>

		<li id="shipping-method" class="step">
			<div class="head">
				<h2>Shipping Method</h2>
			</div>

			<div id="shipping-method-details">
			<fieldset>

				<ul class="shipping-methods">
					<li>
						<label>
							<input type="radio" name="shipping_method" value="ups" checked="checked" />&nbsp;
							<?=$this->html->image('ups-icon.jpg', array('title' => "UPS Shipping", 'alt' => "UPS Shipping", 'width' => "26", 'height' => "32")); ?>&nbsp;
							UPS Ground
						</label>
					</li>
				</ul>
			</fieldset>

			</div>
		</li>

		<!-- Start Payment Information -->
		<li id="opc-payment" class="step">
			<div class="head">
				<h2>Payment Information</h2>
			</div>

			<div id="checkout-process-payment">
				<p>
					<label for="payment-method-select">Pay with:</label>
					<?=$this->form->select(
						'payment',
						array('' => 'New Credit Card'),
						array('id' => 'payment', 'value' => '1', 'target' => '#payment-method-form')
					); ?>
				</p>

				<fieldset id="payment-method-form">
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

			</div>

		</li>
		<!-- End Shipping Information -->

		<li class="step">
			<button class="flex-btn submit"><span>Submit Order</span></button>
			&nbsp;&nbsp;
			<span class="red">*</span> Required Fields
		</li>

		<?=$this->form->end(); ?>
	</ol>

	</div>
	<div class="bl"></div>
	<div class="br"></div>

</div>

<div id="right">

	<div class="r-container order-summary">
		<div class="tl"></div>
		<div class="tr"></div>
		<div class="r-box lt-gradient-1">

			<h3>Your Order</h3>
			<table class="order-status">
				<tr>
					<td class="left"><strong>Order Subtotal:</strong></td>
					<td class="right subTotal">$<?=number_format((float) $subTotal, 2); ?></td>
				</tr>
				<tr>
					<td class="left"><strong>Shipping:</strong></td>
					<td class="right shippingCost">
						$<?=number_format((float) $shippingCost, 2); ?>
					</td>
				</tr>
				<tr>
					<td class="left"><strong>Sales Tax:</strong></td>
					<td class="right tax">$<?=number_format((float) $tax, 2); ?></td>
				</tr>
				<tr>
					<td class="left"><strong class="caps">Total:</strong></td>
					<?php $total = $subTotal + $tax + $shippingCost; ?>
					<td class="right total">
						<strong>$<?=number_format((float) $total, 2); ?></strong>
					</td>
				</tr>
			</table>

			<ol id="order-details">
				<?php if ($billingAddr) { ?>
					<li id="billing-details">
						<h4>Billing Address</h4>
						<address class="billing-address">
							<?=$billingAddr->address; ?> <?=$billingAddr->address_2; ?><br />
							<?=$billingAddr->city; ?>, <?=$billingAddr->state; ?>
							<?=$billingAddr->zip; ?>
						</address>
					</li>
				<?php } ?>
				<?php if ($shippingAddr) { ?>
					<li id="shipping-details">
						<h4>Shipping Address</h4>
						<address class="shipping-address">
							<?=$shippingAddr->address; ?> <?=$shippingAddr->address_2; ?><br />
							<?=$shippingAddr->city; ?>, <?=$shippingAddr->state; ?>
							<?=$shippingAddr->zip; ?>
						</address>

						<?php
						// <div class="shipping-detail">
						// 	<strong>Shipment 1</strong><br />
						// 	Extended Delivery Timeline<br />
						// 	<abbr title="Tuesday, April 6th, 2010">Tue 4/6/10</abbr> to
						// 	<abbr title="Thursday, April 15th, 2010">Thu 4/15/10</abbr>
						// </div>
						?>
					</li>
				<?php } ?>
			</ol>

		</div>
		<div class="bl"></div>
		<div class="br"></div>
	</div>

</div>

<script type="text/javascript">
$(document).ready(function() {
	$("#tabs").tabs();
	var initializing = true;

	var update = function() {
		var data = $('form.checkout').serialize();
		var addrFormat = "{:address} {:address_2}<br />\n{:city}, {:state} {:zip}";

		$.post(window.location + '.json', data, function(data) {
			$('.order-summary').fadeOut('fast', function() {
				if (data.shippingAddr.address) {
					$('.shipping-address').html($(data.shippingAddr).template(addrFormat));
				}
				if (data.billingAddr.address) {
					$('.billing-address').html($(data.billingAddr).template(addrFormat));
				}
				$('.shippingCost').html('$' + $.numberFormat(data.shippingCost, 2));
				$('.order-summary').fadeIn(1000);
			});
		}, 'json');
	};

	$('#billing, #shipping, #payment').bind('change', function() {
		var $this = $(this);
		var $target = $($this.attr('target'));
		var method = ($this.val() == '') ? 'show' : 'hide';
		var shippingToggle = 'input[name=billing_shipping]:checked';

		if ($this.attr('id') == 'shipping' && $(shippingToggle).val() == "1") {
			method = 'hide';
		}
		$target[method]().find('input, select').attr('disabled', (method == 'hide'));

		if (!initializing && $this.val() != '') {
			update();
		}
	}).trigger('change');

	$('#gift').bind('click', function() {
		$('#gift-message').toggle();
	});

	$('input[name=billing_shipping]').bind('change', function() {
		on = ($(this).val() != 1);
		$('#opc-shipping').css('opacity', on ? 1 : 0.5);
		$('#shipping').attr('disabled', on ? '' : 'disabled').trigger('change');
		update();
	}).first().trigger('change');

	$("button.done").bind('click', function() {
		$(this).parents('fieldset').hide();
		update();
		return false;
	});

	$('#payment-method-form').show();
	$('#payment').parent().remove();
	initializing = false;
});
</script>
