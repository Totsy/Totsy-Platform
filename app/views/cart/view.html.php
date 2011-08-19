<script type="text/javascript">	

var discountErrors = new Object();

	$(document).ready( function(){
					
			if(discountErrors.promo==true) {	
				show_code_errors("promo");
			} else if (discountErrors.credits==true)  {
				show_code_errors("credits");
			} else if(discountErrors.credits==true && discountErrors.promo==true) {
				show_code_errors("credits");
				show_code_errors("promo");
			} else {
				discountErrors.promo==false;
				discountErrors.credits==false;  
			}
		}
	);
	
	$( function () {
	    var itemExpires = new Date(<?=($cartExpirationDate  * 1000)?>);	    
		var now = new Date();
		
		$('#itemCounter').countdown( { until: itemExpires, onExpiry: refreshCart, expiryText: "<div class='over' style='color:#EB132C; padding:5px;'>no longer reserved</div>", layout: '{mnn}{sep}{snn} minutes'} );
		
		if (itemExpires < now) {
			$('#itemCounter').html("<span class='over' style='color:#EB132C; padding:5px;'>No longer reserved</span>");
		}
		
		function refreshCart() {
			window.location.reload(true);
		}
		
		//applying tooltip
		$('#shipping').tipsy({gravity: 'e'}); // nw | n | ne | w | e | sw | s | se
		$('#estimated_tax').tipsy({gravity: 'e'}); // nw | n | ne | w | e | sw | s | se
		
	}); 
	
</script>

<script type="text/javascript" src="/js/jquery.number_format.js"></script>
<script type="text/javascript" src="/js/tipsy/src/javascripts/jquery.tipsy.js"></script>
<link rel="stylesheet" type="text/css" href="/js/tipsy/src/stylesheets/tipsy.css" />

<?php  if(!empty($subTotal)): ?>
<div style="margin:10px; margin-bottom:10px">

	<div class="grid_11" style="padding-bottom:10px; margin:20px auto auto auto;">
		<div style="float:left">
			<h2 class="page-title gray">
				<span class="cart-step-status gray">Shopping Cart</span>
				<span class="cart-step-status"><img src="/img/cart_steps1.png"></span>
				<span class="cart-step-status"><img src="/img/cart_steps_remaining.png"></span>
				<span class="cart-step-status"><img src="/img/cart_steps_remaining.png"></span>
				<span class="cart-step-status"><img src="/img/cart_steps_remaining.png"></span>
			</h2>
		</div>
	</div>
	
	<div class="grid_5" style="padding-bottom:10px; margin:20px auto auto auto;">
		 <div style="float:right;">
		Item Reserved For: <br />
		<span id="itemCounter" style="color:#009900; float:right !important">Dummy cart expiration date</span>
	    </div>
	    <div style="float:left;">
		 <div>Estimated Shipping Date: </div>
	         <div style="float:right; color:#009900;">&nbsp;&nbsp;<?=date('m-d-Y', $shipDate)?></div>
	     </div>
	</div>
	<div class="clear"></div>
	<hr/>
	     <div class="cart-button fr" style="margin:10px 0px 20px 0px;">
		      <?=$this->html->link('Continue Shopping', "sale/$returnUrl", array('style'=>'float:left; margin-right:10px;', 'class' => 'button_border')); ?>
		      <?=$this->html->link('Checkout', 'Orders::shipping', array('class' => 'button', 'style'=>'float:left')); ?>
		     <div class="clear"></div>
		 </div>
<?php endif ?>

<div class="message"></div>
<?php if (!empty($subTotal)): ?>

<div class="roundy_cart" style="width:935px !important">
<?=$this->form->create(null ,array('id'=>'cartForm')); ?>
	<div id='message'><?php echo $message; ?></div>
		<table class="cart-table">
			<tbody>
			<?php $x = 0; ?>
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
					<td class="cart-desc" style="width:475px;">
						<?=$this->form->hidden("item$x", array('value' => $item->_id)); ?>
						<strong><?=$this->html->link($item->description,'sale/'.$item->event_url.'/'.$item->url); ?></strong><br>
						<strong>Color:</strong> <?=$item->color;?><br>
						<strong>Size:</strong> <?=$item->size;?>
					</td>
					<td style="width:150px;">
					<div id='<?php echo "itemCounter$x"; ?>_display' style="margin:5px 0px 0px 5px;" title='<?=$date?>'></div>
					</td>
					<td class="<?="price-item-$x";?>" style="width:65px;">
						<strong>$<?=number_format($item->sale_retail,2)?></strong>
					</td>
					<td class="<?="qty-$x";?>" style="width:65px; text-align:center">
					<!-- Quantity Select -->
					<?php
						if($item->available < 9) {
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
    					'id' => $item->_id, 'value' => $item->quantity, 'class'=>'quantity'
					));
					?>
					<?php 
						$date = $cartItemEventEndDates[$x] * 1000;
					?>
					</td>
					<td class="cart-actions">
						<a href="#" id="remove<?=$item->_id; ?>" title="Remove from your cart" onclick="deletechecked('Are you sure you want to remove this item?','<?=$item->_id; ?>');" style="color: red!important;"><img src="/img/trash.png" width="20" align="absmiddle" style="margin-right:20px;" /></a>
					</td>
					<td class="cart-time"><!-- <img src="/img/old_clock.png" align="absmiddle" width="23" class="fl"/>--> <div id='<?php echo "itemCounter$x"; ?>' class="counter" style="display:none;" title='<?=$date?>'></div>
					
					</td>
					<td class="<?="total-item-$x";?>" style="width:55px; text-align:right; padding-right:10px">
						<strong>$<?=number_format($item->sale_retail * $item->quantity ,2)?></strong>
					</td>
				</tr>
				<?php $x++; ?>
			<?php endforeach ?>
				<tr valign="top">
					<td colspan="2" style="padding:10px 0px 50px 10px; border-bottom: 0px">
						<div style="float: left">
							<div style="font-size: 12px; text-align:left !important;">
								<strong>Add <?php if(!empty($credit)): ?>
									<a href="#" id="credits_lnk" onclick="open_credit();" >Credits</a> /
								<?php endif ?> 
									<a href="#" id="promos_lnk" onclick="open_promo();">Optional Code</a></strong>
							</div>
							<div style="clear:both"></div>
							<div id="promos_and_credit">
							<?=$this->form->create(null); ?>
								<div id='promo' style='display:none'>
									<?=$this->view()->render( array('element' => 'promocode'), array( 'orderPromo' => $cartPromo) ); ?>
								</div>
								<div id='cred' style='display:none; text-align:left !important'>								
				   					<?=$this->view()->render(array('element' => 'credits'), array('orderCredit' => $cartCredit, 'credit' => $credit, 'userDoc' => $userDoc)); ?>
								</div>
							</div>
						</div>
					</td>
						
					<td colspan="6" style="border-bottom: 0px">	
					<div style="padding-top:10px">
							<div style="font-weight:bold" class="subtotal" >
									<span style="float:left;">Subtotal:</span>
									<span style="float:right" id="subtotal">$<?=number_format($subTotal,2)?></span>
							</div>
							<?php if (!empty($promocode['discount_amount']) && ($promocode['type'] != 'free_shipping') ):?>
							<div style="clear:both"></div>
							<div style="font-weight:bold" class="subtotal">
    								<span style="float: left;">Discount 
    								<?php echo '[' . $promocode['code'] . ']'; ?>	
    								:</span> 
    								<span style="float:right" class="fees_and_discounts">-$
    								<?=number_format(abs($promocode['discount_amount']),2)?>
    								</span>	
    						</div>
   							<?php endif ?>
   							<?php if (!empty($services['tenOffFitfy'])):?>
							<div style="clear:both"></div>
							<div style="font-weight:bold" class="subtotal">
    								<span style="float: left;">Discount [10$ Off] :</span> 
    									<span style="float:right" class="fees_and_discounts">- $<?=number_format($services['tenOffFitfy'],2)?>
    									</span>
    								</span>
    						</div>
   							<?php endif ?>
							<div style="clear:both"></div>
							<div style="font-weight:bold" class="subtotal">
									<span style="float: left;" id="shipping" original-title="Tipsy is a jQuery plugin for creating a Facebook-like tooltips effect based on an anchor tag's title attribute.">Shipping:</span> 
									<span style="float:right" class="fees_and_discounts">$7.95</span>
							</div>
							<?php if (!empty($shipping_discount)):?>
							<div style="clear:both"></div>
							<div style="font-weight:bold" class="subtotal">
    							<span style="float: left;">Free Shipping 
    								<?php 
    								if(!empty($promocode)) {
    									if($promocode['type'] === 'free_shipping')
    										echo '[' . $promocode['code'] . ']';	
    								}?>		
    								:</span> 
    								<span style="color:#707070; float:right" class="fees_and_discounts">- $<?=number_format($shipping_discount,2)?></span>
    						</div>
   							<?php endif ?>
							<div style="clear:both"></div>	
							<div style="font-weight:bold" class="subtotal">
						
									<span id="estimated_tax" original-title="Tipsy is a jQuery plugin for creating a Facebook-like tooltips effect based on an anchor tag's title attribute." style="float: left;">Estimated Tax:</span> 
									<span style="float:right" class="fees_and_discounts">$0.00</span>
							</div>
							<?php if (!empty($credits)):?>
							<div style="clear:both"></div>
							<div style="font-weight:bold" class="subtotal">
    								<span style="float:left;">Credits:</span> 
    								<span style="float:right" class="fees_and_discounts">- $<?=number_format(abs($credits),2)?></span>
    						</div>
   							<?php endif ?>
							<div style="clear:both" class="subtotal"><hr /></div>			
							<div>
								<div class="savings">Your Saving: 
									<span style="color:#ff6d1d; font-weight:bold"><?php if (!empty($savings)) : ?>
									$<?=number_format($savings,2)?>
									<?php endif ?>
									</span> 
								</div>
								<div class="subtotal">
								<span style="font-size:15px; font-weight:bold">Order Total:</span> 
									<span style="font-size:15px; color:#009900; float:right" id="ordertotal">$ <?=number_format($total,2)?> </span>
								</div>
									
						</div>	
					</div>				
					</td>
				</tr>
			</tbody>
		</table>
</div>

<div class="cart-button fr" style="margin:20px 0px 20px 0px;">
		      <?=$this->html->link('Continue Shopping', "sale/$returnUrl", array('style'=>'float:left; margin-right:10px;', 'class' => 'button_border')); ?>
		      <?=$this->html->link('Checkout', 'Orders::shipping', array('class' => 'button', 'style'=>'float:left')); ?>
	<div class="clear"></div>


<?=$this->form->end(); ?>
</div>

<div id="remove_form" style="display:none">
	<?=$this->form->create(null ,array('id'=>'removeForm')); ?>
	<?=$this->form->hidden('rmv_item_id', array('class' => 'inputbox', 'id' => 'rmv_item_id')); ?>
	<?=$this->form->end();?>
</div>
	<?php if (!empty($itemCounters)): ?>
		<?php foreach ($itemCounters as $counter): ?>
			<?php //echo $counter ?>
		<?php endforeach ?>
	<?php endif ?>
	<?php if (!empty($removeButtons)): ?>
		<?php foreach ($removeButtons as $button): ?>
			<?php //echo $button ?>
		<?php endforeach ?>
	<?php endif ?>
	
<script type="text/javascript" charset="utf-8">
		
	$(".counter").each( function() {
	    				
	    var fecha  = parseInt(this.title);
	    var itemExpires = new Date();
	    var now = new Date();
	    
	    itemExpires = new Date(fecha);	
	    
	    var expireNotice = (itemExpires.valueOf() - 120000);
	    expireNotice = new Date( expireNotice );
	    
	    //show 2 minutes notice
	    if(expireNotice < now && itemExpires > now){
	    	$("#" + this.id + "_display").html('<div class=\'over\' style=\'color:#EB132C; padding:5px\'>This item will expire in 2 minutes</div>');
	    } 
	    
	   	//show item expired notice
	    if(now > itemExpires) {
	    	$("#" + this.id + "_display").html('<div class=\'over\' style=\'color:#EB132C; padding:5px\'>This item is no longer reserved</div>');
	    }
	    
	    $("#" + this.id).countdown({until: expireNotice, 
	    							expiryText: '<div class=\'over\' style=\'color:#EB132C; padding:5px\'>This item will expire in 2 minutes</div>', 
	    							layout: '{mnn}{sep}{snn} seconds',
	    							onExpiry: resetTimer
	    							});
	    
	    //call when item expires
		function notifyEnding() {
			$("#" + this.id).countdown('change', { expiryText: '<div class=\'over\' style=\'color:#EB132C; padding:5px\'>This item is no longer reserved</div>'});
		
			$("#" + this.id + "_display").html( '<div class=\'over\' style=\'color:#EB132C; padding:5px\'>This item is no longer reserved</div>' );
		}
	    
	    //call 2 minutes before the item expires							
	    function resetTimer() {	
	    	$("#" + this.id + "_display").html( $("#" + this.id).countdown('settings', 'expiryText') );
			$("#" + this.id).countdown('change', { until: itemExpires, 
												   expiryText: '<div class=\'over\' style=\'color:#EB132C; padding:5px\'>This item is no longer reserved</div>',
												    onExpiry: notifyEnding
												   });
		}							
	});		
				
</script>	
	
<div class="clear"></div>
<?php else: ?>
	<div class="grid_16" style="padding:20px 0; margin:20px 0;"><h1><center><span class="page-title gray" style="padding:0px 0px 10px 0px;">Your shopping cart is empty</span> <a href="/sales" title="Continue Shopping">Continue Shopping</a/></center></h1></div>
<?php endif ?>
</div>
<div id="modal" style="background:#fff!important; z-index:9999999999!important;"></div>

<script type="text/javascript" charset="utf-8">

//SUBMIT THE ITEM WHICH IS DELETED
function deletechecked(message, id) {
	var answer = confirm(message)
	if (answer){
		$("input[name='rmv_item_id']").val(id);
		$('#removeForm').submit();
	}
	return false;
}
//SUBMIT QUANTITY IN CASE OF DDWN CHANGE
$(function () {
	$(".quantity").live("change keyup", function () {
		if($("select").val() == 0) {
			$('input[name="rmv_item_id"]').val($(this).attr('id'));
			$('#removeForm').submit();
		} else {
			$('#cartForm').submit();
		}
	});
});
//HIDE / SHOW CREDITS INPUT
function open_credit() {
	if ($("#cred").is(":hidden")) {
		$("#cred").slideToggle("fast");
	} else {
		$("#cred").slideToggle("fast");
	}
};

//for showing promo and discount errors after the promocode form has been submitted
function show_code_errors(id) {
	$("#" + id).slideToggle("fast");
}

//HIDE / SHOW PROMOS INPUT
function open_promo() {
	if ($("#promo").is(":hidden")) {
		$("#promo").slideToggle("fast");
	} else {
		$("#promo").slideToggle("fast");
	}
};

</script>