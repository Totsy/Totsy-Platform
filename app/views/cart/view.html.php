<?php

	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
	$test = $cart->data();
	
	$cartInfo = $cart->data();
	$cartExpirationDate =  $cartInfo[0]['expires']['sec'];
	
?>										

<div style="margin-top:10px; margin-bottom:10px">
<div class="grid_8">
	<div style="float:left">
	<h2 class="page-title gray">
	<!--<span class="red"><a href="/" title="Sales">Today's Sales</a> /</span> My Cart</h2> -->
		<span class="red">Shopping Cart</span></h2>
	</div>
	<div style="float:right; font-weight: bold">
	Item reserved for: <span id="itemCounter">Dummy cart expiration date</span>
    </div>
</div>
<div class="grid_8">
	 <div style="float:left;">
	 <span style="font-weight: bold">Estimated Shipping Date: </span>
         <span style="float:right;">&nbsp;&nbsp;<?=date('m-d-Y', $shipDate)?></span>
     </div>
     <div class="cart-button">
	     <?=$this->html->link('Checkout', 'Orders::addShipping', array('class' => 'button', 'style'=>'float:right')); ?>
	 </div>
</div>

<script type="text/javascript">

	$( function () {
	    
	    var itemExpires = new Date();
	    itemExpires = new Date(<?=$cartExpirationDate?>);
	    							
	    $("#itemCounter").countdown('change', {until: itemExpires, layout: '{mnn}{sep}{snn} minutes'});
		$('#itemCounter').countdown( {until: itemExpires, expiryText: "<div class='over' style='color:#EB132C; padding:5px;'>no longer reserved</div>", layout: '{mnn}{sep}{snn} minutes'} );
	
		var now = new Date();
		
		if (itemExpires < now ) {
		    $('#itemCounter').html("<span class='over' style='color:#EB132C; padding:5px;'>no longer reserved</span>");
		}
	
	});
	
</script>

<div class="message"></div>
<?php if (!empty($test)): ?>
<?=$this->form->create(null ,array('id'=>'cartForm')); ?>
	<div class="grid_16 roundy_cart">
	<div id='message'><?php echo $message; ?></div>
		<table class="cart-table">
			<!--
			<thead>
				<tr>
					<th>Item</th>
					<th style="width:220px;">Description</th>
					<th style="width:65px;">Price</th>
					<th>Quantity</th>
					<th>Total</th>
					<th>Time Remaining</th>
					<th></th>
				</tr>
			</thead>
			-->
			<tbody>
			<?php $x = 0; ?>
			<?php $subTotal = 0; ?>
			<?php foreach ($cart as $item): ?>
				<!-- Build Product Row -->
				<tr id="<?=$item->_id?>" class="alt0">
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
								'height'=>'60',
						'style' => 'margin:2px; display:block; padding:4px;')),
							array('Items::view', 'args' => $item->url),
								array(
								'id' => 'main-logo_', 'escape'=> false
							)
						); ?>
					</td>
					<td class="cart-desc" style="width:400px;">
						<?=$this->form->hidden("item$x", array('value' => $item->_id)); ?>
						<strong><?=$this->html->link($item->description,'sale/'.$item->event_url.'/'.$item->url); ?></strong><br>
						<strong>Color:</strong> <?=$item->color;?><br>
						<strong>Size:</strong> <?=$item->size;?>
					</td>
					<td class="<?="price-item-$x";?>" style="width:65px;">
						<strong style="color:#009900;">$<?=number_format($item->sale_retail,2)?></strong>
					</td>
					<td class="<?="qty-$x";?>" style="width:65px; text-align:center">
					<!-- Quantity Select -->
					<?php
						if($item->available < 9){
							$qty = $item->available;
							if($item->quantity > $qty){
								$select = array_unique(array_merge(array('0'), range('1',(string)$item->quantity)));
							} else {
								$select = array_unique(array_merge(array('0'), range('1',(string)$qty)));
							}
						} else {
							$select = array_unique(array_merge(array('0'), range('1','9')));
						}
					?>
					<?=$this->form->select("cart[{$item->_id}]", $select, array(
    					'id' => $item->_id, 'value' => $item->quantity
					));
					?>
					</td>
					<td class="cart-actions">
						<a href="#" id="remove<?=$item->_id; ?>" title="Remove from your cart" onclick="deletechecked('Are you sure you want to remove this item?','<?=$item->_id; ?>');" style="color: red!important;"><img src="/img/trash.png" width="20" align="absmiddle" style="margin-right:20px;" /></a>
					</td>
					
					<td class="cart-time" style="width:220px;"><!-- <img src="/img/old_clock.png" align="absmiddle" width="23" class="fl"/>--> <div id='<?php echo "itemCounter$x"; ?>' class="fl" style="margin:5px 0px 0px 5px;"></div></td>
					<td class="<?="total-item-$x";?>" style="width:55px;">
						<strong style="color:#009900;">$<?=number_format($item->sale_retail * $item->quantity ,2)?></strong>
					</td>
					
				</tr>
				<?php
					
					$date = $cartItemEventEndDates[$x] * 1000;
										
					$itemCounters[] = "<script type=\"text/javascript\">
					
						var itemExpires = new Date();
						itemExpires = new Date($date);
						var now = new Date();	
						
						var expireNotice = ( itemExpires.valueOf() - 120000 ) ;
						expireNotice = new Date( expireNotice );
											
						$(\"#itemCounter$x\").countdown('change', { until: expireNotice, layout: '{mnn}{sep}{snn} minutes' });
						
						$(\"#itemCounter$x\").countdown({
						    until: expireNotice,
						    expiryText: '<div class=\"over\" style=\"color:#EB132C; padding:5px;\">This item will expire in 2 minutes</div>', 
						    layout: '{mnn}{sep}{snn} minutes',
						    onExpiry: test(itemExpires) }
						 );
						 
						 console.log('the first counter has started');
						 
						function test (exp){
						
						    $(\"#itemCounter$x\").countdown('destroy');
						    
						    console.log('the second counter has started');
						    
						    $(\"#itemCounter$x\").countdown('change', { until: exp, layout: '<div class=\"over\" style=\"color:#EB132C; padding:5px;\">This item will expire in 2 minutes</div>' });
						    						
						    $(\"#itemCounter$x\").countdown({
						        until: exp,
						        expiryText: '<div class=\"over\" style=\"color:#EB132C; padding:5px;\">This sale has ended</div>', 
						        layout: '{mnn}{sep}{snn} minutes',
						    });
						}
						
						function test2 (){
							console.log('test');
						}

						
						</script>";
					$subTotal += $item->quantity * $item->sale_retail;
					$x++;
				?>
			<?php endforeach ?>
				<tr class="cart-total">
					<td colspan="4" id='subtotal' valign='top'>
						
						<div style="float: left; ">
							
							<div style="font-size: 12px;">
								<strong>Add <?php if(!empty($credit)) { ?>
									<a href="#" id='credits_lnk' onclick="open_credit();" >Credits</a> /
								<?php } ?> 
									<a href="#" id='promos_lnk' onclick="open_promo();">Optional Code</a></strong>
							</div>
							
							<div style="clear:both"></div>
							
							<div>
							<?=$this->form->create(null); ?>
								<div id="promo" style="display:none">
									<?=$this->view()->render(array('element' => 'promocode'), array( 'orderPromo' => $cartPromo)); ?>
								</div>
								<div id="cred" style="display:none">								
				   					<?=$this->view()->render(array('element' => 'credits'), array('orderCredit' => $cartCredit, 'credit' => $credit, 'userDoc' => $userDoc)); ?>
								</div>
							</div>
							
						</div>
						
					</td>	
					<td colspan="3">	
						<div style="font-weight:bold">
								<span style="float: left;">Subtotal:</span>
								<span style="color:#009900; float:right">$<?=number_format($subTotal,2)?></span>
						</div>
						<div style="clear:both"></div>
						<div style="font-weight:bold">
								<span style="float: left;">Shipping:</span> 
								<span style="color:#009900; float:right">$7.95</span>
						</div>
						<div style="clear:both"></div>
						<div style="font-weight:bold">
								<span style="float: left;">Estimated Tax:</span> 
								<span style="color:#009900; float:right">$0.00</span>
						</div>	
						<div style="clear:both"><hr /></div>						
						<div style="font-weight:bold">
							<span style="float: left;">Your Saving 
								<?php if (!empty($savings)) : ?>
								$<?=number_format($savings,2)?>
								<?php endif ?> 
							</span>
							<span style="float:right">Order Total: 
								<span style="color:#009900;">$<?=number_format($subTotal,2)?></span>
							</span>
						</div>			
					</td>
				</tr>
				<!--
				<tr>
					<td colspan="7">
							/*$this->form->create(null); */
							<div id="promo" style="display:none">
								/*$this->view()->render(array('element' => 'promocode'), array( 'orderPromo' => $cartPromo));*/ 
							</div>
							<div id="cred" style="display:none">								
				   				/*$this->view()->render(array('element' => 'credits'), array('orderCredit' => $cartCredit, 'credit' => $credit, 'userDoc' => $userDoc));*/
							</div>
							<div class="clear"></div>
					</td>
				</tr>
				-->
				<tr class="cart-buy">
					<td colspan="2" class="cart-button">
					<?=$this->html->link('Continue Shopping', "sale/$returnUrl", array('class' => 'button', 'style'=>'float:left')); ?>
						<!--<a href='../../pages/returns'><strong style="font-size:12px; font-weight:normal;">Refund &amp; Return Policy</strong></a><br /> -->
					</td>
					<td class="cart-button" colspan="5">
						<?=$this->html->link('Checkout', 'Orders::shipping', array('class' => 'button')); ?>
						<?=$this->html->link('Continue Shopping', "sale/$returnUrl", array('style' => 'margin:7px 10px 0px 0px;')); ?>
				</td>
				</tr>
			</tbody>
		</table>
</div>
<?=$this->form->end(); ?>
<div id="remove_form" style="display:none">
	<?=$this->form->create(null ,array('id'=>'removeForm')); ?>
	<?=$this->form->hidden('rmv_item_id', array('class' => 'inputbox', 'id' => 'rmv_item_id')); ?>
	<?=$this->form->end();?>
</div>
	<?php if (!empty($itemCounters)): ?>
		<?php foreach ($itemCounters as $counter): ?>
			<?php echo $counter ?>
		<?php endforeach ?>
	<?php endif ?>
	<?php if (!empty($removeButtons)): ?>
		<?php foreach ($removeButtons as $button): ?>
			<?php echo $button ?>
		<?php endforeach ?>
	<?php endif ?>
	
<!--	
<div class="grid_4 omega">
	<div class="roundy grey_inside">
		<h3 class="gray">Your Savings <span class="fr"><?php //if (!empty($savings)) : ?>
		<span style="color:#009900; font-size:16px; float:right;">$<?php //number_format($savings,2)?></span>
		<?php //endif ?></span></h3>
	</div>
	<div class="clear"></div>
	<div class="roundy grey_inside">
		<h3 class="gray">Estimated Ship Date<span style="font-weight:bold; float:right;"><?php //date('m-d-Y', $shipDate)?></span>
		</h3>
	</div>
	<div class="clear"></div>
</div>
-->

<div class="clear"></div>
<?php else: ?>
	<div class="grid_16" style="padding:20px 0; margin:20px 0;"><h1><center><span class="page-title gray" style="padding:0px 0px 10px 0px;">Your shopping cart is empty</span> <a href="/sales" title="Continue Shopping">Continue Shopping</a/></center></h1></div>
<?php endif ?>
</div>
<div id="modal" style="background:#fff!important; z-index:9999999999!important;"></div>
<script type="text/javascript" charset="utf-8">
	$(".inputbox").bind('keyup', function() {
	var id = $(this).attr('id');
	var qty = $(this).val();
	var price = $(this).closest("tr").find("td[class^=price]").html().split("$")[1];
	var cost = parseInt(qty) * parseFloat(price);
	var itemCost = $().number_format(cost, {
		numberOfDecimals: 2,
		decimalSeparator: '.',
		thousandSeparator: ','
	});

	$(this).closest("tr").find("td[class^=total]").html("<strong>$" + itemCost + "</strong>");
	var subTotal = 0;
	$("td[class^=total]").each(function() {
	    subTotal += parseFloat($(this).html().split("$")[1]);
	});

	var subTotalProper = $().number_format(subTotal, {
		numberOfDecimals: 2,
		decimalSeparator: '.',
		thousandSeparator: ','
	});

	$.ajax({
		url: $.base + 'cart/update',
		data: "_id=" + id + "&" + "qty=" + qty,
		context: document.body,
		success: function(message) {
			$('#message').addClass("cart-message");
			$('#message').css("padding: 0pt 0.7em;");
			$('#message').html('<center>' + message + '</center>');
		}
	});
	$("#subtotal").html("<strong>Subtotal: $" + subTotalProper + "</strong>");
});
</script>
<script type="text/javascript" charset="utf-8">
//SUBMIT THE ITEM WHICH IS DELETED
function deletechecked(message, id) {
	var answer = confirm(message)
	if (answer){
		$('input[name="rmv_item_id"]').val(id);
		$('#removeForm').submit();
	}
	return false;
}
//SUBMIT QUANTITY IN CASE OF DDWN CHANGE
$(function () {
	$("select").live("change keyup", function () {
		if($("select").val() == 0) {
			$('input[name="rmv_item_id"]').val($(this).attr('id'));
			$('#removeForm').submit();
		} else {
			$("#cartForm").submit();
		}
	});
});
//HIDE / SHOW CREDITS INPUT
function open_credit() {
	if ($("#cred").is(":hidden")) {
		if(! $("#promo").is(":hidden")) {
			//$("#promo").slideToggle("fast");
		}
		$("#cred").slideToggle("fast");
	} else {
		$("#cred").slideToggle("fast");
	}
};
//HIDE / SHOW PROMOS INPUT
function open_promo() {
	if ($("#promo").is(":hidden")) {
		if(! $("#cred").is(":hidden")) {
			//$("#cred").slideToggle("fast");
		}
		$("#promo").slideToggle("fast");
	} else {
		$("#promo").slideToggle("fast");
	}
};
</script>