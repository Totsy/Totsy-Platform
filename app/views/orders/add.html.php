<?php
	use app\models\Address;
	$this->html->script('application', array('inline' => false));
	$this->form->config(array('text' => array('class' => 'inputbox')));
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
?>

<div class="grid_16">
	<h2 class="page-title gray">Checkout / Confirm Shipping &amp; Billing</h2>
	<hr />
	<?php if (!empty($error)) { ?>
	<div class="checkout-error"><h2>Uh Oh! Please fix the errors below:</h2><hr /><?=$error; ?></div>
<?php } ?>
</div>

<div class="grid_10 roundy grey_inside" style="width:562px!important;">

		<ol id="checkout-process">
		<?=$this->form->create($order, array('class' => 'checkout')); ?>

		<!-- Start Billing Information -->
		
		<li id="opc-billing">
			<?php if (empty($billing)): ?>
					<center><strong><?=$this->html->link('Please take a moment to add an Address', '#', array(
						'class' => 'add-address')); ?></strong></center>
				<?php else: ?>
					<h2 style="color:#707070;font-size:14px;">Billing Address <span style="float:right;"><?=$this->html->link('Add a new address', '#', array(
							'class' => 'add-address')); ?></span></h2><hr />
					<p>Select a billing address from your address book.</p>
						<?=$this->form->select('billing', $billing, array(
							'id' => 'billing',
							'target' => '#billing-new-address-form',
							'value' => key($billing)
						)); ?>
						<fieldset>
						<br>
							<p>
								<input type="radio" name="billing_shipping" id="billing:use_for_shipping_yes" value="1" checked="checked" />&nbsp;
								<label for="billing:use_for_shipping_yes">Ship to this address</label>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input type="radio" name="billing_shipping" id="billing:use_for_shipping_no" value="0" />&nbsp;
								<label for="billing:use_for_shipping_no">Ship to different address</label>
							</p>
						</fieldset>
						
				<?php endif ?>
		
		</li>
	
		<!-- End Billing Information -->

		<!-- Start Shipping Information -->
		<br>
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



<div class="grid_6 omega">
	<div class="roundy grey_inside">
		<h2 style="color:#707070;font-size:14px; font-weight:normal;">My Cart (<?=$this->html->link('edit','/cart/view'); ?>) <span style="float:right;"><?=$cartCount;?> items</span></h2>
		<hr />
		<!-- Begin Order Details -->
	<?php if ($cartByEvent): ?>

		<?php $x = 0; ?>
		<?php foreach ($cartByEvent as $key => $event): ?>
		<?php foreach ($event as $item): ?>
		<?php $itemUrl = "sale/".$orderEvents[$key]['url'].'/'.$item['url'];?>
							
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
											'width'=>'120',
									'style' => 'padding:4px; margin:2px; float:left;')),
											'',
											array( 'escape'=> false
										),
										$itemUrl
									); ?>
				<div style="min-height:113px;">
				<strong style="font-size:14px;"><?=$orderEvents[$key]['name']?></strong><br>
				
			
		
			
								
								
									<?=$this->form->hidden("item$x", array('value' => $item['_id'])); ?>
									<strong><?=$this->html->link($item['description'], $itemUrl);
									?></strong><br>
									<?php if ($item['color']) { ?>
									<strong>Color:</strong> <?=$item['color'];?><br>
									<?php } ?>
									<strong>Size:</strong> <?=$item['size'];?><br>
									<strong>Quantity:</strong> <?=$item['quantity'];?><br>
									<strong>Price:</strong> <strong style="color:#009900;">$<?=number_format($item['sale_retail'],2)?></strong> / 
									<strong>Total Price:</strong> <strong style="color:#009900;">$<?=number_format($item['sale_retail'] * $item['quantity'] ,2)?></strong><br>
								

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
							
							</div>
							<br/>
							<hr/>
				<?php endforeach ?>
			<?php endforeach ?>

						<div style="text-align:right; font-size:16px; margin-top:12px;"><strong>Subtotal: </strong><strong style="color:#009900;">$<?=number_format($subTotal,2)?></strong></div>
				
	<?php endif ?>
	<!-- End Order Details -->


		</div>

	<div class="clear"></div>
	<div class="roundy grey_inside">
		<h3 class="gray">Estimated Ship Date</h3>
		<hr />
		<span style="font-size:16px; font-weight:bold;"><?=date('m-d-Y', $shipDate)?></span>
	</div>
	<div class="clear"></div>
	<div class="roundy grey_inside">
		<h3 class="gray">Need Help?</h3>
		<hr />
		<ul class="menu main-nav">
		    <li><a href="/tickets/add" title="Contact Us">Help Desk</a></li>
			<li><a href="/pages/faq" title="Frequently Asked Questions">FAQ's</a></li>
			<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
			<li><a href="/pages/terms" title="Terms Of Use">Terms Of Use</a></li>
		</ul>
	</div>
</div>
<div class="clear"></div>



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
