<?php
	$this->html->script('application', array('inline' => false));
	$this->form->config(array('text' => array('class' => 'inputbox')));
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
	$preTotal = $subTotal + $orderCredit->credit_amount;
	$afterDiscount = $preTotal + $orderPromo->saved_amount;
	if($afterDiscount < 0){
		$afterDiscount = 0;
	}
	$total = $afterDiscount + $tax + $shippingCost + $overShippingCost;
	?>
<h1 class="page-title gray"><span class="_red"><a href="/" title="Sales">Today's Sales</a></span> / <a href="/cart/view" title="My Cart">My Cart</a> / Checkout / Process Payment</h1>

	<hr />
	
<div id="middle" class="fullwidth">

	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
<?php if ($errors = $order->errors()) { ?>

                        <?php foreach ($errors as $error): ?>

			<div class="checkout-error"><?=$error; ?></div>
                        <?php endforeach ?>

        <?php } ?>
<div style="display:none;">
	   <div class="rounded" style="color: #009900; margin:0px 10px 0px 0px; float: left; display:block; background:#ebffeb; border:1px solid #ddd; width:249px; text-align:center; padding:20px;">Shipping / Billing Info</div>
<div id="arrow-right">
  <div id="arrow-right-1"></div>
  <div id="arrow-right-2"></div>
</div><!--arrow-right-->

	      <div class="rounded" style="color: #009900; margin:0px 10px 0px 0px; float: left; display:block; background:#ebffeb; border:1px solid #ddd; width:236px; padding:20px; text-align: center;">Payment</div>
<div id="arrow-right">
  <div id="arrow-right-1"></div>
  <div id="arrow-right-2"></div>
</div><!--arrow-right-->

	      <div class="rounded" style="color:#ff0000; margin:0px 0px 0px 0px; float:left; display:block; background:#ffebeb; border:1px solid #ddd; width:246px; padding:20px; text-align:center;">Confirmation</div>
	      
	      </div>
	<ol id="checkout-process">
	<!-- End Order Details -->
<?=$this->form->create(); ?>
	<!-- Start Payment Information -->
	<li id="opc-payment" class="step">
		<div id="checkout-process-payment">
		<table>
			<tr>
				<td style="vertical-align:top; padding-right:10px;">

					<h1 style="color:#707070; font-size:22px;">Payment Information <span style="font-size:12px; font-weight:normal;"><span class="red">*</span> Required Fields</span></h1>
					<hr />
					<div>
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
					<li class="step">
						<?=$this->form->submit('Place Your Order', array('class' => 'button submit fr')); ?>
					</li>
					<?=$this->form->hidden('credit_amount', array('value' => $orderCredit->credit_amount)); ?>
					<?=$this->form->end(); ?>
					  </div>
				</td>
				<td style="vertical-align:top; width:300px;">
				<h1 style="color:#707070; font-size:22px;">Credits & Promotional Codes</h1>
				<hr />
						<li id="order-summary" class="step">
			<table class="order-status" width="100%">
				<tr>
					<td><strong>Order Subtotal:</strong> </td>
					<td style="text-align:left; padding-left:10px;">$<?=number_format((float) $subTotal, 2);?></td>
				</tr>
				<tr>
					<td><strong>Shipping:</strong> </td>
					<td style="text-align:left; padding-left:10px;">$<?=number_format((float) $shippingCost, 2);?></td>
				</tr>
				<?php if ($overShippingCost !=0): ?>
					<tr>
						<td><strong>Oversize Shipping:</strong> </td>
						<td style="text-align:left; padding-left:10px;">$<?=number_format((float) $overShippingCost, 2);?></td>
					</tr>
				<?php endif; ?>
				<tr>
					<td><strong>Sales Tax:</strong> </td>
					<td style="text-align:left; padding-left:10px;">$<?=number_format((float) $tax, 2);?></td>

				<?php if ($discountExempt): ?>
						<p>**Your order contains an item where promotions or credits cannot be applied.<br>
							If you need to use credits or promotion codes, please do so on a separate order.<br>
							We apologize for any inconvenience this may cause.
						</p>
				<?php else: ?>

						<?php if ($credit): ?>
							<div style="padding:10px; background:#eee;"><?php $orderCredit->credit_amount = abs($orderCredit->credit_amount); ?>
							<?=$this->form->create($orderCredit); ?>
							<div class="form-row">
							<?=$this->form->error('amount'); ?>
							</div>
							You have $<?=number_format((float) $userDoc->total_credit, 2);?> in credits
							<hr />
							<?=$this->form->text('credit_amount', array('size' => 6, 'maxlength' => '6')); ?>
									<?=$this->form->submit('Apply Credit'); ?>
									<hr />
										<strong>Credit:</strong>
								-$<?=number_format((float) $orderCredit->credit_amount, 2);?>
							<?=$this->form->end(); ?>
							</div>
							</div></div>
						<?php else : ?>
						<div style="padding:10px; background:#eee;"><h1 style="color:#707070; font-size:22px;">Credits: <span style="color:#009900; float:right;">$0.00</span></h1></div>
						<?php endif ?>
					<div style="padding:10px; background:#eee; margin:10px 0">
							<?=$this->form->create($orderPromo); ?>
							<div class="form-row">
								<?=$this->form->error('promo'); ?>
							</div>
									<?=$this->form->text('code', array('size' => 6)); ?>
									
									<?=$this->form->submit('Apply Promo Code'); ?>
								<hr />
								<strong>Promo Savings:</strong>
								<?php if (!empty($orderPromo)): ?>
									-$<?=number_format((float) abs($orderPromo->saved_amount), 2);?>
								<?php else: ?>
									-$<?=number_format((float) 0, 2);?>
								<?php endif ?>
							<?=$this->form->end(); ?>
							</div>
					</tr>
				<?php endif ?>
				<tr>
					<td style="text-align:left; color:#707070; font-size:22px;"><hr /><strong>Order Total:</strong> </td>
					<td style="text-align:right; color:#009900; font-size:22px;"><hr />$<?=number_format((float) $total, 2);?></td>
				</tr>
			</table>
		</li>
		
				</td>
				<td style="vertical-align:top; padding-left:10px; width:250px">
		
					<?php if ($billingAddr) { ?>

							<h1 style="color:#707070; font-size:22px">Billing Address</h1>
							<hr />
							<address class="billing-address">
								<?=$billingAddr->address; ?> <?=$billingAddr->address_2; ?><br />
								<?=$billingAddr->city; ?>, <?=$billingAddr->state; ?>
								<?=$billingAddr->zip; ?>
							</address>
					<?php } ?>
					<br />
					<?php if ($shippingAddr) { ?>
							<h1 style="color:#707070; font-size:22px">Shipping Address</h1>
							<hr />
							<address class="shipping-address">
								<?=$shippingAddr->address; ?> <?=$shippingAddr->address_2; ?><br />
								<?=$shippingAddr->city; ?>, <?=$shippingAddr->state; ?>
								<?=$shippingAddr->zip; ?>
							</address>

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
			<!--
			     <li class="step">
				<fieldset>

					<p><a href="javascript:void(0)" id="gift" title="Want to include a gift message?">Want to include a gift message?</a></p>

					<div id="gift-message">
						<textarea name="gift-message" class="inputbox"></textarea>
					</div>

				</fieldset>

			     </li>
			-->
			<hr/>

			
			   
			   
			   <div style="clear:both; margin-top:90px;"></div>

		<h1 style="color:#707070;">Order Summary</h2>
		<hr />

<div style="clear:both; margin-bottom:10px;"></div>

   
	<!-- Begin Order Details -->
	<?php if ($cartByEvent): ?>
		<?php $x = 0; ?>
		<?php foreach ($cartByEvent as $key => $event): ?>
		<table width="100%" class="cart-table">
			<thead>
				<tr >
					<td colspan='3' style="vertical-align:bottom; font-weight:bold; font-size:18px;"><?=$orderEvents[$key]['name']?><td>
					<td></td>
					<td colspan="3"><div class="fr" style="padding:10px; background:#fffbd1; border-left:1px solid #D7D7D7; border-right:1px solid #D7D7D7; border-top:1px solid #D7D7D7;">Estimated Ship Date: <?=date('M d, Y', $shipDate)?></div></td>
				</tr>
				<tr>
					<th>Item</th>
					<th>Description</th>
					<th>Price</th>
					<th>Qty</th>
					<th>Total Cost</th>
					<th>Time Remaining</th>
				</tr>
			</thead>
			<tbody>
		<?php foreach ($event as $item): ?>
			<!-- Build Product Row -->
						<tr id="<?=$item['_id']?>" class="alt<?=$x?>" style="margin-top:10px;">
						<td class="cart-th">
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
									'width'=>'60',
									'height'=>'60',
									'style' => 'margin:2px; padding:4px;',)),
									'',
									array(
									'id' => 'main-logo_', 'escape'=> false
								)
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
						<td class="cart-time"><img src="/img/clock_icon.gif" class="fl"/><div id='<?php echo "itemCounter-$x"; ?>' class="fl" style="margin-left:5px;"></div></td>
					</tr>
					<?php
						//Allow users three extra minutes on their items for checkout.
						$date = ($item['expires']['sec'] * 1000);
						$itemCounters[] = "<script type=\"text/javascript\">
							$(function () {
								var itemProcessExpires = new Date($date);
								$(\"#itemCounter-$x\").countdown('change', {until: itemProcessExpires, $countLayout});

							$(\"#itemCounter-$x\").countdown({until: itemProcessExpires,
							    expiryText: '<div class=\"over\" style=\"color:#fff; padding:5px; background: #ff0000;\">no longer reserved</div>', $countLayout});
							var now = new Date();
							if (itemProcessExpires < now) {
								$(\"#itemCounter-$x\").html('<div class=\"over\" style=\"color:#fff; padding:5px; background: #ff0000;\">no longer reserved</div>');
							}
							});
							</script>";
						$x++;
					?>
			<?php endforeach ?>
		<?php endforeach ?>
				</tbody>
			</table>
	<?php endif ?>


	

    <!-- begin thawte seal -->
    <div id="thawteseal" title="Click to Verify - This site chose Thawte SSL for secure e-commerce and confidential communications." style="float: right!important; width:200px;">
        <div style="float: left!important; width:100px; display:block;"><script type="text/javascript" src="https://seal.thawte.com/getthawteseal?host_name=www.totsy.com&amp;size=L&amp;lang=en"></script></div>

    <div class="AuthorizeNetSeal" style="float: left!important; width:100px; display:block;"> <script type="text/javascript" language="javascript">var ANS_customer_id="98c2dcdf-499f-415d-9743-ca19c7d4381d";</script> <script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js" ></script></div>
    </div>

    <!-- end thawte seal -->

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
		window.location.replace('/cart/view');
	</script>
<?php endif ?>
