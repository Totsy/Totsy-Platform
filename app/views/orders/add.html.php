<?php
	use app\models\Address;
	$this->html->script('application', array('inline' => false));
	$this->form->config(array('text' => array('class' => 'inputbox')));
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
?>

<div class="grid_16">
	<h2 class="page-title gray"><span class="_red"><a href="/" title="Sales">Today's Sales</a></span> / <a href="/cart/view" title="My Cart">My Cart</a> / Checkout / Shipping &amp; Billing</h2>
	<hr />
</div>

<div class="grid_12">

		<h2 style="color:#707070; font-size:14px;">Order Summary <span style="float:right"><?php if(!empty($savings)) : ?>
						<strong>You're Saving : <span style="color:#009900;">$<?=number_format($savings,2)?></span></strong>
							<?php endif ?></span></h2>
							
		<hr />


	<!-- Begin Order Details -->
	<?php if ($cartByEvent): ?>
		<table width="100%" class="cart-table">

		<?php $x = 0; ?>
		<?php foreach ($cartByEvent as $key => $event): ?>
			<tr style="margin-top:10px;">
				<td colspan="2" style="vertical-align:bottom; font-weight:bold; font-size:18px;"><?=$orderEvents[$key]['name']?> <td>
				<td colspan="4"><div class='fr' style="padding:10px; background:#fffbd1; border-left:1px solid #D7D7D7; border-right:1px solid #D7D7D7; border-top:1px solid #D7D7D7;">Estimated Ship Date: <?=date('m-d-Y', $shipDate)?></div></td>
			</tr>
			<tr>
				<th>Item</th>
				<th>Description</th>
				<th>Price</th>
				<th>Qty</th>
				<th>Total Cost</th>
				<th>Time Remaining</th>
			</tr>
			<?php foreach ($event as $item): ?>
				<tbody>
					<!-- Build Product Row -->
								<?php $itemUrl = "sale/".$orderEvents[$key]['url'].'/'.$item['url'];?>
								<tr id="<?=$item['_id']?>" class="alt<?=$x?>" style="margin-top:10px;">
								<td class="cart-th">
									<?php
										if (!empty($item['primary_image'])) {
											$image = $item['primary_image'];
											$productImage = "/image/$image.jpg";
										} else {
											$productImage = "/img/no-image-small.jpeg";
										}
									?>
									<?=$this->html->link(
										$this->html->image("$productImage", array(
											'width'=>'60',
											'height'=>'60',
									'style' => 'padding:4px; margin:2px;')),
											'',
											array(
											'id' => 'main-logo_', 'escape'=> false
										),
										$itemUrl
									); ?>
								</td>
								<td class="cart-desc">
									<?=$this->form->hidden("item$x", array('value' => $item['_id'])); ?>
									<strong><?=$this->html->link($item['description'], $itemUrl);
									?></strong><br>
									<strong>Color:</strong> <?=$item['color'];?><br>
									<strong>Size:</strong> <?=$item['size'];?>
								</td>
								<td class="<?="price-item-$x";?>">
									<strong style="color:#009900;">$<?=number_format($item['sale_retail'],2)?></strong>
								</td>
								<td class="<?="qty-$x";?>">
									<?=$item['quantity'];?>
								</td>
								<td class="<?="total-item-$x";?>">
									<strong style="color:#009900;">$<?=number_format($item['sale_retail'] * $item['quantity'] ,2)?></strong>
								</td>
								<td class="cart-time" style="border-right:1px solid #d7d7d7;"><img src="/img/clock_icon.gif" class="fl"/><div id='<?php echo "checkout-counter-$x"; ?>' class="fl" style="margin-left:5px;"></div></td>

							</tr>
							<?php
								//Allow users three extra minutes on their items for checkout.
								$date = ($item['expires']['sec'] * 1000);
								$checkoutCounters[] = "<script type=\"text/javascript\">
									$(function () {
										var itemCheckoutExpires = new Date($date);
										$(\"#checkout-counter-$x\").countdown('change', {until: itemCheckoutExpires, $countLayout});

									$(\"#checkout-counter-$x\").countdown({until: itemCheckoutExpires,
									    expiryText: '<div class=\"over\" style=\"color:#fff; padding:5px; background: #EB132C;\">no longer reserved</div>', $countLayout});
									var now = new Date();
									if (itemCheckoutExpires < now) {
										$(\"#checkout-counter-$x\").html('<div class=\"over\" style=\"color:#fff; padding:5px; background: #EB132C;\">no longer reserved</div>');
									}
									});
									</script>";
								$x++;
							?>
				<?php endforeach ?>
			<?php endforeach ?>

					<tr class="cart-total">
						<td colspan="7" id='subtotal'><strong>Subtotal: </strong><strong style="color:#009900;">$<?=number_format($subTotal,2)?></strong>
							
							<br/><hr/><?=$this->html->link('Edit Your Cart','/cart/view' ,array('id' => 'checkout-cart', 'class' => 'button fr')); ?></td>
					</tr>
				</tbody>
			</table>
	<?php endif ?>
	<!-- End Order Details -->


	</ol>
</div>

	<div class="grid_4 omega">
<?php if (!empty($error)) { ?>
	<div class="checkout-error"><h2>Uh Oh! Please fix the errors below:</h2><hr /><?=$error; ?></div>
<?php } ?>
              
	<ol id="checkout-process">
		<?=$this->form->create($order, array('class' => 'checkout')); ?>

		<!-- Start Billing Information -->
		
		<li id="opc-billing">
			<?php if (empty($billing)): ?>
					<center><strong><?=$this->html->link('Please take a moment to add an Address', '#', array(
						'class' => 'add-address')); ?></strong></center>
				<?php else: ?>
					<h2 style="color:#707070;font-size:14px;">Billing Address</h2><hr />
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
		
		</li>
	
		<!-- End Billing Information -->

		<!-- Start Shipping Information -->
		
		  <li id="opc-shipping" class="step_" style="opacity:0.5">

			<div id="checkout-process-shipping">

				<?php if (empty($shipping)): ?>

				<?php else: ?>

						<h2 style="color:#707070;font-size:14px;">Shipping Address</h2>
					<hr />
					<p>Select a shipping address from your address book.</p>
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

		<li id="shipping-method" class="step" style="display:none;">

				<h2 style="color:#707070;">Shipping Method</h2>
			<hr />

			
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

			
		</li>


<hr />

		<li class="step">
			<?=$this->form->submit('Confirm & Continue', array('class' => 'button fr')); ?>
		</li>

		<?=$this->form->end(); ?>

</div>

</div>

<div id="address-modal"></div>
<div id="modal"></div>

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
<script type="text/javascript">
    $('#disney').click(function(){
        $('#modal').load('/events/disney').dialog({
            autoOpen: false,
            modal:true,
            width: 739,
            height: 700,
            position: 'top',
            close: function(ev, ui) {}
        });
        $('#modal').dialog('open');
    });
</script>
	<?php if ($cartEmpty == true):
	?>
		<script>
			window.location.replace('/cart/view');
		</script>
	<?php endif ?>
