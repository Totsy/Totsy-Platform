<?php
	$this->html->script('application', array('inline' => false));
	$this->form->config(array('text' => array('class' => 'inputbox')));
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
	$preTotal = $subTotal + $orderCredit->credit_amount + $orderServiceCredit;
	$afterDiscount = $preTotal + $orderPromo->saved_amount;
	if ($afterDiscount < 0) {
		$afterDiscount = 0;
	}
	$total = $afterDiscount + $tax + $shippingCost + $overShippingCost;
?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>
<div class="grid_16">
	<h2 class="page-title gray">Checkout / Process Payment</h2>
	<hr />
</div>

<div class="grid_10 roundy grey_inside" style="width:562px!important;">



<?php if ($errors = $order->errors()): ?>
	<?php foreach ($errors as $error): ?>
	    <?php if (is_array($error)): ?>
	        <?php foreach($error as $msg): ?>
	            <div class="checkout-error"><?=$msg; ?></div>
	        <?php endforeach; ?>
	    <?php else: ?>
		    <div class="checkout-error"><?=$error; ?></div>
		<?php endif; ?>
	<?php endforeach ?>
<?php endif ?>

<table style="width:100%;">
	<tr>
		<td valign="top">
			<table>
				<tr>
					<td>
						<?=$this->form->create(); ?>
							<h1 style="color:#707070; font-size:14px;">Payment Information <span class="fr" style="font-size:12px; font-weight:normal;"><span class="red ">*</span> Required</span></h1>
							<hr />
								
								<p>
									<label for="cc-type" class="required">Credit Card Type<span>*</span></label>
									<?=$this->form->select('card[type]', array(
										'visa' => 'Visa',
										'mc' => 'MasterCard',
										'amex' => 'American Express'
									)); ?>
								</p>
								<p>
									<label for="cc" class="required">Card Number<span>*</span></label>
									<?=$this->form->text('card[number]', array('id' => 'cc', 'class' => 'inputbox', 'size' => '16', 'maxlength' => '16')); ?>
								</p>
								<p>
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
								</p>
								<p>
									<label for="cc-ccv" class="required">CVV2 Code<span>*</span></label>
									<?=$this->form->text('card[code]', array('id' => 'CVV2','class' => 'inputbox', 'maxlength' => '4', 'size' => '4')); ?>
								</p>
							<?=$this->form->submit('Place Your Order', array('class' => 'button submit')); ?>
							<?=$this->form->hidden('credit_amount', array('value' => $orderCredit->credit_amount)); ?>
						<?=$this->form->end(); ?>
											</td>
				</tr>
			</table>
		</td>
    	<td valign="top">
			<table style=" margin:0 10px;">
				<tr>
					<h1 style="color:#707070; font-size:14px;"><?php if (!$credit == '0') { ?>Credits &amp; <?php } ?>Promotional Codes</h1>
					<hr />
				</tr>
				<tr>
					<td><strong>Order Subtotal:</strong> </td>
					<td style="text-align:left; padding-left:10px;">$<?=number_format((float) $subTotal, 2);?></td>
				</tr>
				
				<tr>
					<td>
							<strong>Promo Savings:</strong></td>
							<td style="text-align:left; padding-left:10px;"><?php if (!empty($orderPromo)): ?>
								-$<?=number_format((float) abs($orderPromo->saved_amount), 2);?>
							<?php else: ?>
								-$<?=number_format((float) 0, 2);?>
							<?php endif ?>
				</tr>
				
				<?php
					if ($orderServiceCredit): ?>
						<tr>
							<td>You qualify for $10 off your purchase!</td><td>- $10.00</td>
						</tr>
				<?php endif; ?>
				<tr>
					<td><strong>Shipping:</strong> </td>
					<td style="text-align:left; padding-left:10px;">$<?=number_format((float) $shippingCost, 2);?></td>
				</tr>
				<?php if ($overShippingCost !=0): ?>
					<tr>
						<td><strong>Oversize Shipping:</strong> </td>
						<td style="text-align:left; padding-left:10px;">$<?=number_format((float) $overShippingCost, 2);?></td>
					</tr>
				<?php endif ?>
				<?php
					if ($freeshipping): ?>
						<tr>
							<td>You qualify for free shipping!</td>
						</tr>
				<?php endif; ?>
				<tr>
					<td><strong>Sales Tax:</strong></td>
					<td style="text-align:left; padding-left:10px;">$<?=number_format((float) $tax, 2);?>
				</tr>
					<?php if ($discountExempt): ?>
						<tr>
							<p>**Your order contains an item where promotions or credits cannot be applied.<br>
								If you need to use credits or promotion codes, please do so on a separate order.<br>
								We apologize for any inconvenience this may cause.
							</p>
						</tr>
					<?php endif ?>
				<tr>
					<?php if ($credit): ?>
						<div style="padding:10px; background:#eee;"><?php $orderCredit->credit_amount = abs($orderCredit->credit_amount); ?>
							<?=$this->form->create($orderCredit); ?>
							<?=$this->form->error('amount'); ?>
							You have $<?=number_format((float) $userDoc->total_credit, 2);?> in credits
							<hr />
							<?=$this->form->text('credit_amount', array('size' => 6, 'maxlength' => '6')); ?>
									<?=$this->form->submit('Apply Credit', array('class' => 'button')); ?>
									<hr />
										<strong>Credit:</strong>
								-$<?=number_format((float) $orderCredit->credit_amount, 2);?>
							<?=$this->form->end(); ?>
							<div style="clear:both"></div>
						</div>
					<?php else : ?>
						<?php if ($credit = '0') { ?>
						<div style="padding:10px; background:#eee;"><h1 style="color:#707070; font-size:14px;">Credits: <span style="color:#009900; float:right;">$0.00</span></h1></div>
						<?php } ?>
					<?php endif ?>
				</tr>
				<tr>
					<div style="padding:10px; background:#eee; margin:10px 0">
						<?=$this->form->create($orderPromo); ?>
							<?php if (is_array($this->form->error('promo'))): ?>
                                <?php foreach($this->form->error('promo') as $msg) :?>
                                    <?php echo $msg ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?=$this->form->error('promo'); ?>
							<?php endif; ?>
							<?=$this->form->text('code', array('size' => 6)); ?>
							<?=$this->form->submit('Apply Promo Code', array('class' => 'button')); ?>
							
							<?=$this->form->end(); ?></td>
						<div style="clear:both"></div>
					</div>
				</tr>
				<tr>
					<td style="text-align:left; color:#707070; font-size:22px;"><hr /><strong>Order Total:</td>
					<td style="text-align:right; color:#009900; font-size:22px; padding-left:10px"><hr />$<?=number_format((float) $total, 2);?></td>
				</tr>
				
			</table>
		</td>
	</tr>
</table>
</div>


<div class="grid_6 omega">
<div class="roundy grey_inside">
		<h3 class="gray">Your Savings <span class="fr"><?php if (!empty($savings)) : ?>
		<span style="color:#009900; font-size:16px; float:right;">$<?=number_format((float) $savings, 2);?></span>
		<?php endif ?></span></h3>
	</div>
	<div class="roundy grey_inside">
		<h3 class="gray">Estimated Ship Date<span style="font-weight:bold; float:right;"><?=date('m-d-Y', $shipDate)?></span></h3>
		
	</div>
	
	<div class="roundy grey_inside">
		<?php if ($billingAddr): ?>
								<h3 class="gray">Billing Address <span class="fr">(<a href="#" class="add-address">edit</a>)</span></h3>
								<hr />
								<address class="billing-address">
									<?=$billingAddr->address; ?> <?=$billingAddr->address_2; ?><br />
									<?=$billingAddr->city; ?>, <?=$billingAddr->state; ?>
									<?=$billingAddr->zip; ?>
								</address>
						<?php endif ?>
						<br />
						<?php if ($shippingAddr): ?>
								<h3 class="gray">Shipping Address <span class="fr">(<a href="#" class="add-address">edit</a>)</span></h3>
								<hr />
								<address class="shipping-address">
									<?=$shippingAddr->address; ?> <?=$shippingAddr->address_2; ?><br />
									<?=$shippingAddr->city; ?>, <?=$shippingAddr->state; ?>
									<?=$shippingAddr->zip; ?>
								</address>
						<?php endif ?>

		
	</div>
	
	<div class="roundy grey_inside">
		<h2 style="color:#707070;font-size:14px; font-weight:normal;">My Cart (<?=$this->html->link('edit','/cart/view'); ?>) <span style="float:right;"><?=$cartCount;?> items</span></h2>
		<hr />
		<!-- Begin Order Details -->
	<?php if ($cartByEvent): ?>

		<?php $x = 0; ?>
		<?php foreach ($cartByEvent as $key => $event): ?>
		<?php foreach ($event as $item): ?>
		<?php $itemUrl = "sale/".$orderEvents[$key]['url'].'/'.$item['url'];?>
		<div style="float:left; width:85px;">					
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
											'width'=>'75',
									'style' => 'margin:2px;')),
											'',
											array( 'escape'=> false
										),
										$itemUrl
									); ?>
				</div>
				<div style="float:left; width:236px;">
				<?=$orderEvents[$key]['name']?><br>

									<?=$this->form->hidden("item$x", array('value' => $item['_id'])); ?>
									<?=$item['description'];?><br>
									<?php if ($item['color']) { ?>
									Color: <?=$item['color'];?><br>
									<?php } ?>
									<?php if (!$item['size'] == 'no size') { ?>
									Size: <?=$item['size'];?><br>
									<?php } else { ?>
									<?php } ?>
									Quantity: <?=$item['quantity'];?> (<strong style="color:#009900;">$<?=number_format($item['sale_retail'],2)?></strong>)<br>
																	

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
							<div class="clear"></div>
							<hr/>
				<?php endforeach ?>
			<?php endforeach ?>

							<?php endif ?>
	<!-- End Order Details -->


		</div>

	<div class="clear"></div>
	
</div>
<div class="clear"></div>
<div id="modal"></div>
<div id="address-modal" style="z-index:9999999999!important;"></div>
<script type="text/javascript">
$(".add-address").click(function() {
	$("#address-modal").load($.base + 'addresses/add').dialog({
		autoOpen: false,
		modal:true,
		width: 415,
		height: 497,
		position: 'top',
		close: function(ev, ui) {}
	});
	$("#address-modal").dialog('open');
});

</script>
<?php
    if(number_format((float) $total, 2) >= 35 && number_format((float) $total, 2) <= 44.99){
        echo "<script type=\"text/javascript\">
            $.post('/cart/modal',{modal: 'disney'},function(data){
              //  alert(data);
                if(data == 'false'){
                    $('#modal').load('/cart/upsell?subtotal=" . (float)$total ."&redirect=".$itemUrl."').dialog({
                        autoOpen: false,
                        modal:true,
                        width: 550,
                        height: 320,
                        position: 'top',
                        close: function(ev, ui) {}
                    });
                    $('#modal').dialog('open');
                }
            });
            </script>";
    }
?>
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


<script type="text/javascript">
jQuery(function($){
   $("#cc").mask("9999999999999999");
   $("#CVV2").mask("9999");
});
</script>