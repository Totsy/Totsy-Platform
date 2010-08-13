<?php
	use app\models\Address;
	$this->html->script('application', array('inline' => false));
	$this->form->config(array('text' => array('class' => 'inputbox')));
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
?>


<h1 class="page-title gray"><span class="red"><?=$this->title('Checkout - Delivery Method'); ?></span></h1>

<div id="middle" class="fullwidth">

	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
		<p><strong class="red"><?=$this->html->link('STEP 1 (Shipping/Billing Info)', array('Orders::add')); ?></strong> &#8658; STEP 2 (Payment) &#8658; STEP 3 (Confirmation)</p><br>
	<!-- Begin Order Details -->
	<?php if ($showCart): ?>
		<div class="head"><h2>Order Details</h2></div>
		<div class='fr'><?=$this->html->link('Edit Your Cart', '#', array('id' => 'checkout-cart')); ?></div>
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
						<td class="cart-time"><div id="<?php echo "checkout-counter-$x"; ?>"</div></td>
					</tr>
					<?php
						//Allow users three extra minutes on their items for checkout.
						$date = ($item->expires->sec * 1000);
						$checkoutCounters[] = "<script type=\"text/javascript\">
							$(function () {
								var itemCheckoutExpires = new Date($date);
								$(\"#checkout-counter-$x\").countdown('change', {until: itemCheckoutExpires, $countLayout});

							$(\"#checkout-counter-$x\").countdown({until: itemCheckoutExpires,
							    expiryText: '<div class=\"over\">This item is no longer reserved for purchase</div>', $countLayout});
							var now = new Date();
							if (itemCheckoutExpires < now) {
								$(\"#checkout-counter-$x\").html('<div class=\"over\">This item is no longer reserved for purchase</div>');
							}
							});
							</script>";
						$x++;
					?>
		<?php endforeach ?>
					<tr class="cart-total">
						<td colspan="7" id='subtotal'><strong>Subtotal: $<?=number_format($subTotal,2)?></strong></td>
					</tr>
				</tbody>
			</table>
	<?php endif ?>
	<!-- End Order Details -->
	<br>
	<ol id="checkout-process">
		<?=$this->form->create($order, array('class' => 'checkout')); ?>
		
		<!-- Start Billing Information -->
		<li id="opc-billing">
			<div id="checkout-process-billing">
			<?php if (empty($billing)): ?>
					<center><strong><?=$this->html->link('Please take a moment to add an Address', '#', array(
						'class' => 'add-address')); ?></strong></center>
				<?php else: ?>
					<div class="head"><h2>Billing Address</h2></div>
					<p>Select a billing address from your address book.</p>
						<?=$this->form->select('billing', $billing, array(
							'id' => 'billing',
							'target' => '#billing-new-address-form',
							'value' => key($billing)
						)); ?>
						<fieldset>
							<p>
								<input type="radio" name="billing_shipping" id="billing:use_for_shipping_yes" value="1" checked="checked" />&nbsp;
								<label for="billing:use_for_shipping_yes">Ship to this address</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="radio" name="billing_shipping" id="billing:use_for_shipping_no" value="0" />&nbsp;
								<label for="billing:use_for_shipping_no">Ship to different address</label>
							</p>
						</fieldset>
						<?=$this->html->link('Add a new address', '#', array(
							'class' => 'add-address')); ?>
				<?php endif ?>
			</div>
		</li>
		<!-- End Billing Information -->

		<!-- Start Shipping Information -->
		<li id="opc-shipping" class="step" style="opacity:0.5">

			<div id="checkout-process-shipping">

				<?php if (empty($shipping)): ?>

				<?php else: ?>
					<div class="head">
						<h2>Shipping Address</h2>
					</div>
					<p>Select a shipping address from your address book.
					</p>
						<?=$this->form->select('shipping', $shipping, array(
							'id' => 'shipping',
							'target' => '#shipping-new-address-form',
							'value' => key($shipping)
						)); ?><br>
						<?=$this->html->link('Add a new address', '#', array(
							'class' => 'add-address')); ?>
				<?php endif ?>

			</div>

		</li>
		<!-- End Shipping Information -->

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

		

		<li class="step">
			<button class="confirm-delivery-button submit"><span>Confirm Delivery Method</span></button>
			&nbsp;&nbsp;
		</li>

		<?=$this->form->end(); ?>
	</ol>

	</div>
	<div class="bl"></div>
	<div class="br"></div>

</div>

<div id="right">

<div id="address-modal"></div>

</div>


<script>
$(document).ready(function() {
	$('input[name=billing_shipping]').bind('change', function() {
		on = ($(this).val() != 1);
		$('#opc-shipping').css('opacity', on ? 1 : 0.5);
		$('#shipping').attr('disabled', on ? '' : 'disabled').trigger('change');
	}).first().trigger('change');
});
</script>

<?php if (!empty($checkoutCounters)): ?>
	<?php foreach ($checkoutCounters as $cc): ?>
		<?php echo $cc ?>
	<?php endforeach ?>
<?php endif ?>

<script type="text/javascript">
$(".add-address").click(function() {
	$("#address-modal").load($.base + 'addresses/add').dialog({
		autoOpen: false,
		modal:true,
		width: 500,
		height: 600,
		position: 'top',
		close: function(ev, ui) {}
	});
	$("#address-modal").dialog('open');
});

</script>
	<?php if ($cartEmpty == true): ?>
		<script>
		$(document).ready(function() {
			$("#cart-modal").load($.base + 'cart/view').dialog({
				autoOpen: false,
				modal: true,
				width: 900,
				height: 600
				close: function(ev, ui) {
					parent.location = "/events";
				}
			});
			$("#cart-modal").dialog('open');
		});
		</script>
	<?php endif ?>
	<script type="text/javascript">
	$("#checkout-cart").click(function() {
		$("#cart-modal").load($.base + 'cart/view').dialog({
			autoOpen: false,
			modal:true,
			width: 900,
			height: 600,
			close: function(ev, ui) {
				location.reload();
			}
		});
		$("#cart-modal").dialog('open');
	});
	</script>