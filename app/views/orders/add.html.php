<?php
	use app\models\Address;
	$this->html->script('application', array('inline' => false));
	$this->form->config(array('text' => array('class' => 'inputbox')));
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
?>


<h1 class="page-title gray"><span class="_red"><a href="/" title="Sales">Today's Sales</a></span> / <a href="/cart/view" title="My Cart">My Cart</a> / Checkout / Shipping &amp; Billing</h1>

	<hr />
<div id="middle" class="fullwidth">

	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">

<?php if (!empty($error)) { ?>
                        <div class="checkout-error"><h2>Uh Oh! Please fix the errors below:</h2><hr /><?php echo $error; ?></div>

<?php } ?>
<div style="display:none;">
           <div class="rounded" style="color: #009900; margin:0px 10px 0px 0px; float: left; display:block; background:#ebffeb; border:1px solid #ddd; width:230px; text-align:center; padding:20px;">Shipping / Billing Info</div>
<div id="arrow-right">
  <div id="arrow-right-1"></div>
  <div id="arrow-right-2"></div>
</div><!--arrow-right-->

              <div class="rounded" style="color: #ff0000; margin:0px 10px 0px 0px; float: left; display:block; background:#ffebeb; border:1px solid #ddd; width:236px; padding:20px; text-align: center;">Payment</div>
<div id="arrow-right-red">
  <div id="arrow-right-1-red"></div>
  <div id="arrow-right-2-red"></div>
</div><!--arrow-right-->

              <div class="rounded" style="color:#ff0000; margin:0px 0px 0px 0px; float:left; display:block; background:#ffebeb; border:1px solid #ddd; width:246px; padding:20px; text-align:center;">Confirmation</div>
              </div>


	<ol id="checkout-process">
		<?php echo $this->form->create($order, array('class' => 'checkout')); ?>

		<!-- Start Billing Information -->
		<div style="float:left; width:423px; margin:0px 10px 10px 0px;  display:block;"><li id="opc-billing">
			<div id="checkout-process-billing">
			<?php if (empty($billing)): ?>
					<center><strong><?php echo $this->html->link('Please take a moment to add an Address', '#', array(
						'class' => 'add-address')); ?></strong></center>
				<?php else: ?>
					<h2 style="color:#707070;">Billing Address</h2><hr />
					<p>Select a billing address from your address book.</p>
						<?php echo $this->form->select('billing', $billing, array(
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
						<?php echo $this->html->link('Add a new address', '#', array(
							'class' => 'add-address')); ?>
				<?php endif ?>
			</div>
		</li>
		  </div>
		<!-- End Billing Information -->

		<!-- Start Shipping Information -->
		<div style="float:left; width:445px; display:block;">
		  <li id="opc-shipping" class="step_" style="opacity:0.5">

			<div id="checkout-process-shipping">

				<?php if (empty($shipping)): ?>

				<?php else: ?>

						<h2 style="color:#707070;">Shipping Address</h2>
					<hr />
					<p>Select a shipping address from your address book.</p>
						<?php echo $this->form->select('shipping', $shipping, array(
							'id' => 'shipping',
							'target' => '#shipping-new-address-form',
							'value' => key($shipping)
						)); ?><br>
						<?php echo $this->html->link('Add a new address', '#', array(
							'class' => 'add-address')); ?>
				<?php endif ?>

			</div>

		</li>
		  </div>
<div style="clear:both;"></div>
		<!-- End Shipping Information -->

		<li id="shipping-method" class="step" style="display:none;">

				<h2 class="gray mar-b">Shipping Method</h2>
			<hr />

			<div id="shipping-method-details">
			<fieldset>

				<ul class="shipping-methods">
				<li>
						<label>
							<input type="radio" name="shipping_method" value="ups" checked="checked" />&nbsp;
							<?php echo $this->html->image('ups-icon.jpg', array('title' => "UPS Shipping", 'alt' => "UPS Shipping", 'width' => "26", 'height' => "32")); ?>&nbsp;
							UPS Ground
						</label>
				</li>
				</ul>
			</fieldset>

			</div>
		</li>


<hr />

		<li class="step">
			<?php echo $this->form->submit('Confirm & Continue', array('class' => 'button fr')); ?>
		</li>

		<?php echo $this->form->end(); ?>

			   <div style="clear:both; margin-top:90px;"></div>

		<h2 style="color:#707070;">Order Summary</h2>
		<hr />

<div style="clear:both; margin-bottom:10px;"></div>

	<!-- Begin Order Details -->
	<?php if ($cartByEvent): ?>
		<table width="100%" class="cart-table">

		<?php $x = 0; ?>
		<?php foreach ($cartByEvent as $key => $event): ?>
			<tr>
				<td colspan="3" style="vertical-align:bottom; font-weight:bold; font-size:18px;"><?php echo $orderEvents[$key]['name']?> <td>
				<td colspan="3"><div class='fr' style="padding:10px; background:#fffbd1; border-left:1px solid #D7D7D7; border-right:1px solid #D7D7D7; border-top:1px solid #D7D7D7;">Estimated Ship Date: <?php echo date('m-d-Y', $shipDate)?></div></td>
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
								<tr id="<?php echo $item['_id']?>" class="alt<?php echo $x?>" style="margin-top:10px;">
								<td class="cart-th">
									<?php
										if (!empty($item['primary_image'])) {
											$image = $item['primary_image'];
											$productImage = "/image/$image.jpg";
										} else {
											$productImage = "/img/no-image-small.jpeg";
										}
									?>
									<?php echo $this->html->link(
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
									<?php echo $this->form->hidden("item$x", array('value' => $item['_id'])); ?>
									<strong><?php echo $this->html->link($item['description'], $itemUrl);
									?></strong><br>
									<strong>Color:</strong> <?php echo $item['color'];?><br>
									<strong>Size:</strong> <?php echo $item['size'];?>
								</td>
								<td class="<?php echo "price-item-$x";?>">
									<strong style="color:#009900;">$<?php echo number_format($item['sale_retail'],2)?></strong>
								</td>
								<td class="<?php echo "qty-$x";?>">
									<?php echo $item['quantity'];?>
								</td>
								<td class="<?php echo "total-item-$x";?>">
									<strong style="color:#009900;">$<?php echo number_format($item['sale_retail'] * $item['quantity'] ,2)?></strong>
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
									    expiryText: '<div class=\"over\" style=\"color:#fff; padding:5px; background: #ff0000;\">no longer reserved</div>', $countLayout});
									var now = new Date();
									if (itemCheckoutExpires < now) {
										$(\"#checkout-counter-$x\").html('<div class=\"over\" style=\"color:#fff; padding:5px; background: #ff0000;\">no longer reserved</div>');
									}
									});
									</script>";
								$x++;
							?>
				<?php endforeach ?>
			<?php endforeach ?>

					<tr class="cart-total">
						<td colspan="7" id='subtotal'><strong>Subtotal: </strong><strong style="color:#009900;">$<?php echo number_format($subTotal,2)?></strong><br/><hr/><?php echo $this->html->link('Edit Your Cart','/cart/view' ,array('id' => 'checkout-cart', 'class' => 'button fr')); ?></td>
					</tr>
				</tbody>
			</table>
	<?php endif ?>
	<!-- End Order Details -->
	<!--Disney -->
      <div class="disney fl">
          <p><strong>SPECIAL BONUS!</strong><hr/></p>
       <p> Included with your purchase of $45 or more is a one-year subscription to <img src="/img/parents.jpg" align="absmiddle" width="95px" /> ( a $10 value ). <br/>
       <span id="disney">Offer & Refund Details</span></p>
      </div>
    <!-- begin thawte seal -->
    <div id="thawteseal" title="Click to Verify - This site chose Thawte SSL for secure e-commerce and confidential communications." style="float: right!important; width:200px;">
        <div style="float: left!important; width:100px; display:block;"><script type="text/javascript" src="https://seal.thawte.com/getthawteseal?host_name=www.totsy.com&amp;size=L&amp;lang=en"></script></div>

    <div class="AuthorizeNetSeal" style="float: left!important; width:100px; display:block;"> <script type="text/javascript" language="javascript">var ANS_customer_id="98c2dcdf-499f-415d-9743-ca19c7d4381d";</script> <script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js" ></script></div>
    </div>
    <!-- end thawte seal -->

	</ol>

	</div>

	<div class="bl"></div>
	<div class="br"></div>

</div>

<div id="right">

<div id="address-modal"></div>
<div id="modal"></div>
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
	<script type="text/javascript">
	/**
	$("#checkout-cart").click(function() {
		$("#cart-modal").load($.base + 'cart/view').dialog({
			autoOpen: false,
			modal:true,
			width: 900,
			//height: 600,
			close: function(ev, ui) {
				location.reload();
			}
		});
		$("#cart-modal").dialog('open');
	}); **/
</script>