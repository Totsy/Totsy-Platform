<!-- JS for cart timer. -->
<script type="text/javascript" src="/js/cart-timer.js"></script>
<!-- JS for cart timer for individual items. -->
<script type="text/javascript" src="/js/cart-items-timer.js"></script>
<script type="text/javascript" src="/js/tipsy/src/javascripts/jquery.tipsy.js"></script>
<link rel="stylesheet" type="text/css" href="/js/tipsy/src/stylesheets/tipsy.css" />

<script type="text/javascript">	

var discountErrors = new Object();

	$(document).ready( function(){
					
		if(discountErrors.promo==true) {	
		    show_code_errors("promo");
		} else if (discountErrors.credits==true)  {
		    show_code_errors("cred");
		} else if(discountErrors.credits==true && discountErrors.promo==true) {
		    show_code_errors("cred");
		    show_code_errors("promo");
		} else {
		    discountErrors.promo=false;
		    discountErrors.credits=false;  
		}
	
	$("#cart-count").text(<?=$itemCount?>);
	var cartExpires = new Date(<?=($cartExpirationDate  * 1000)?>);	
	//set the timer
	cartTimer(cartExpires);
	//set the timer on individual cart items
	cartItemsTimer();
	//applying tooltip
	$('#shipping_tooltip').tipsy({gravity: 'e'}); // nw | n | ne | w | e | sw | s | se
	$('#tax_tooltip').tipsy({gravity: 'e'}); // nw | n | ne | w | e | sw | s | se
	$('#promocode_tooltip').tipsy({gravity: 'nw'}); // nw | n | ne | w | e | sw | s | se
	
});
			
</script>
<script type="text/javascript" src="/js/jquery.number_format.js"></script>

<?php  if(!empty($subTotal)): ?>
<h2 class="page-title gray">
	<span class="cart-step-status gray" style="font-weight:bold">Shopping Cart</span>
	<span class="cart-step-status"><img src="/img/cart_steps1.png"></span>
	<span class="cart-step-status"><img src="/img/cart_steps_remaining.png"></span>
	<span class="cart-step-status"><img src="/img/cart_steps_remaining.png"></span>
	<span class="cart-step-status"><img src="/img/cart_steps_remaining.png"></span>
</h2>


<?=$this->view()->render( array('element' => 'shipdateTimer'), array( 'shipDate' => $shipDate) ); ?>


	<?php
	if($missChristmasCount>0){
	?>
				<div style="margin-top:10px;line-height:12px;font-weight:bold; color:#990000; font-size:11px;text-align:center;">
				<img src="/img/truck_red.png">
				One or more of the items in your cart is not guaranteed to be delivered on or before 12/25*.
				</div>
	<?php
	}
	else{
	?>
				<div style="margin-top:10px;line-height:12px;font-weight:bold; color:#999999; font-size:11px;text-align:center;">
				<!-- 
				<img src="/img/truck_grey.png">
				Item will be delivered on or before 12/23.*
				-->
				</div>
	<?php
	}
	?>
<hr/>

<div class="cart-button fr" style="margin:10px 0px 20px 0px;">
	<?=$this->html->link('Continue Shopping', "sale/$returnUrl", array('style'=>'float:left; margin-right:10px;','class' => 'button_border')); ?>
	<?=$this->html->link('Checkout', 'Orders::shipping', array('data-role' => 'button', 'class' => 'button', 'style'=>'float:left')); ?>
	<div class="clear"></div>
</div>
<?php endif ?>

<div class="message"></div>
<?php if (!empty($subTotal)): ?>

<div>
<?=$this->form->create(null ,array('id'=>'cartForm')); ?>
	<div id='message'><?php echo $message; ?></div>
		<table class="cart-table">
			<tbody>
			<?php $x = 0; ?>
			<?php foreach ($cart as $item): ?>
			
			<!--temporary miss christmas check -->
			<?php
			if($item->miss_christmas){
				$tableclass = "alt0a";
				if($notmissChristmasCount>0){
					$shipmsg = "<span class=\"shippingalert\">This item is not guaranteed to be delivered on or before 12/25.<br>Please remove this item from your cart and order separately to receive your other items on or before 12/23.*</span>";
				}
				else{
					$shipmsg = "<span class=\"shippingalert\">This item is not guaranteed to be delivered on or before 12/25.*</span>";
				}
			}
			else{
				$tableclass = "alt0";
				$shipmsg = "Item will be delivered on or before 12/23.*";
			}			
			?>
			<!-- end xmas -->
				<!-- Build Product Row -->
				<tr id="<?=$item->_id?>" class="<?=$tableclass?>">
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
								'id' => 'main-logo_', 'style' => 'color:#0000ff', 'escape'=> false
							)
						); ?>
					</td>
					<td class="cart-desc">
						<?=$this->form->hidden("item$x", array('value' => $item->_id)); ?>
						<strong><?=$this->html->link($item->description,'sale/'.$item->event_url.'/'.$item->url); ?></strong><br />
						<?php if($item->color) : ?>
						<strong>Color:</strong> <?=$item->color;?><br />
						<?php endif ?>
						<?php if($item->size!=="no size") : ?>
						<strong>Size:</strong> <?=$item->size;?>
						<?php endif ?>
						<br><?=$shipmsg?>
					</td>
					<?php
						$date = $cartItemEventEndDates[$x] * 1000;
					?>
					<td class="cart-item-timer-td">
					<div id='<?php echo "itemCounter$x"; ?>_display' class="cart-item-timer" title='<?=$date?>'></div>
					</td>
					<td class="<?="price-item-$x";?>" class="cart-item-price">
						<strong>$<?=number_format($item->sale_retail,2)?></strong>
					</td>
					<td class="<?="qty-$x";?> cart-item-qty">
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
										</td>
					<td class="cart-actions">
						<a href="#" id="remove<?=$item->_id; ?>" title="Remove from your cart" onclick="deletechecked('Are you sure you want to remove this item?','<?=$item->_id; ?>');" style="color: red!important;"><img src="/img/trash.png" width="20" align="absmiddle" style="margin-right:20px;" /></a>
					</td>
					<td class="cart-time">
						<div id='<?php echo "itemCounter$x"; ?>' class="counter" style="display:none;" title='<?=$date?>'></div>
					</td>
					<td class="<?="total-item-$x";?> cart-line-total">
						<strong>$<?=number_format($item->sale_retail * $item->quantity ,2)?></strong>
					</td>
				</tr>
				<?php $x++; ?>
			<?php endforeach ?>
			</tbody>
			</table>
</div>
<?=$this->form->end(); ?>

<div class="clear"></div>

<div class="cart-code-buttons">
	<?php if(!empty($credit)): ?>
	<strong>Add <a href="#" id="credits_lnk" onclick="open_credit();" >Credits</a></strong> /
	<?php endif ?>
	<span id="promocode_tooltip" original-title="Promo codes cannot be combined and can be applied once to an order per member." class="cart-tooltip">
	<img src="/img/tooltip_icon.png">
	</span>
	
	<strong>Add <a href="#" id="promos_lnk" onclick="open_promo();">Promo Code</a></strong>
</div>

<div style="clear:both"></div>

<div id="promos_and_credit">
	<div id="promo" style="display:none">
	<?=$this->view()->render( array('element' => 'promocode'), array( 'orderPromo' => $cartPromo, 'promocode_disable' => $promocode_disable)); ?>
	</div>
	
	<div id="cred" style="display:none; text-align:left !important">
	<?=$this->view()->render(array('element' => 'credits'), array('orderCredit' => $cartCredit, 'credit' => $credit, 'user' => $user)); ?>
	</div>
</div>
			
<div class="cart-subtotal-content">
	<div class="subtotal" >
	<span style="float:left;">Subtotal:</span>
	<span style="float:right" id="subtotal">$<?=number_format($subTotal,2)?></span>
</div>

<?php if (!empty($cartPromo['saved_amount']) && ($cartPromo['type'] != 'free_shipping') ):?>
				<div style="clear:both"></div>
				<div class="subtotal">
    			    	<span style="float: left;">Discount
    			    	<?php echo '[' . $cartPromo['code'] . ']'; ?>:
    			    	</span>
    			    	<span style="float:right">-
    			    	$<?=number_format(abs($cartPromo['saved_amount']),2)?>
    			    	</span>
    			</div>
   				<?php endif ?>
   				<?php if (!empty($services['tenOffFitfy'])):?>
				<div style="clear:both"></div>
				<div class="subtotal">
    			    	<span style="float: left;">Discount [10$ Off] :</span>
    			    		<span style="float:right">- $<?=number_format($services['tenOffFitfy'],2)?>
    			    		</span>
    			    	</span>
    			</div>
   				<?php endif ?>
   				<?php if (!empty($credits)):?>
				<div style="clear:both"></div>
				<div class="subtotal">
    			    	<span style="float:left;">Credits:</span>
    			    	<span style="float:right">- $<?=number_format(abs($credits),2)?></span>
    			</div>
   				<?php endif ?>
				<div style="clear:both"></div>
			
				<div class="subtotal">
					<?php if (!empty($shipping)):?>
						<span id="shipping_tooltip" class="cart-tooltip" original-title="Shipping charges may vary depending on item type.">
							<img src="/img/tooltip_icon.png">
						</span>
						<span style="float:left;" id="shipping">
				    		Shipping:
				    	</span>
				    	<span style="float:right">$<?=number_format(abs($shipping),2)?></span>
					<?php endif ?>
				</div>
				
				<?php if (!empty($shipping_discount)):?>
				<div style="clear:both"></div>
				<div class="subtotal">
    			    <span style="float:left;">Free Shipping
    			    	<?php
    			    	if(!empty($promocode)) {
    			    		if($promocode['type'] === 'free_shipping')
    			    			echo '[' . $promocode['code'] . ']';
    			    	}?>
    			    	:</span>
    			    	<span style="color:#707070; float:right">- $<?=number_format($shipping_discount,2)?></span>
    			</div>
   				<?php endif ?>
				<div style="clear:both"></div>
		
				<div class="subtotal">
				    <span id="tax_tooltip" class="cart-tooltip" original-title="Sales tax will be calculated once we collect the shipping address for this order. If you are shipping to NY or NJ, tax will be charged on the order subtotal, shipping and handling at the applicable county rate. Tax rates within counties vary"><img src="/img/tooltip_icon.png">
</span>
					<span id="estimated_tax" style="float:left;">Sales Tax:</span>
				    <span style="float:right">$0.00</span>
				</div>
		

				<div style="clear:both" class="subtotal"><hr /></div>
				<div>
				    <div class="cart-savings">
				    <?php if (!empty($savings)) : ?>
				    Your Savings:
				    $<?=number_format($savings,2)?>
				    	<?php endif ?>
				    </div>
				    <div class="subtotal">
				    <span class="cart-order-total">Order Total:</span>
				    	<span id="ordertotal">$<?=number_format($total,2)?> </span>
				    </div>

<div class="cart-button fr cart-nav-buttons">
		      <?=$this->html->link('Continue Shopping', "sale/$returnUrl", array('style'=>'float:left; margin-right:10px;', 'class' => 'button_border')); ?>
		      <?=$this->html->link('Checkout', 'Orders::shipping', array('data-role' => 'button', 'class' => 'button', 'style'=>'float:left')); ?>
		      <div class="clear"></div>

<?=$this->form->end(); ?>

</div>

<div class="clear"></div>
				<?php
				if($missChristmasCount>0&&$notmissChristmasCount>0){
				?>
				* Totsy ships all items together. If you would like the designated items in your cart delivered on or before 12/23, please ensure that any items that are not guaranteed to ship on or before 12/25 are removed from your cart and purchased separately. Our delivery guarantee does not apply when transportation networks are affected by weather. Please contact our Customer Service department at 888-247-9444 or email <a href="mailto:support@totsy.com">support@totsy.com</a> with any questions. 
								
				<?php
				}
				elseif($missChristmasCount>0){
				?>
				* Your items will arrive safely, but after 12/25.
				
				<?php
				}
				else{
				?>
				
				* Our delivery guarantee does not apply when transportation networks are affected by weather.
				
				<?php
				}
				?>
				

<div id="remove_form" style="display:none">
	<?=$this->form->create(null ,array('id'=>'removeForm')); ?>
	<?=$this->form->hidden('rmv_item_id', array('class' => 'inputbox', 'id' => 'rmv_item_id')); ?>
	<?=$this->form->end();?>
</div>

<div class="clear"></div>
<?php else: ?>
<div class="holiday_message">
		<h3 style="text-align:center; font-size:16px;">Your shopping cart is empty 
			<a href="/sales">Continue Shopping</a/>
		</h3>
</div>
<?php endif ?>


<p></p>
<?php echo $this->view()->render(array('element' => 'mobile_aboutUsNav')); ?>
<?php echo $this->view()->render(array('element' => 'mobile_helpNav')); ?>
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>


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
	$(document).ready( function(){

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
	});

	//HIDE / SHOW CREDITS INPUT
	function open_credit() {
		if ($("#cred").is(":hidden")) {
			$("#cred").slideToggle("fast");
			if (!$("#promo").is(":hidden")) {
				$("#promo").slideToggle("fast");
			}
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
			if (!$("#cred").is(":hidden")) {
				$("#cred").slideToggle("fast");
			}
		} else {
			$("#promo").slideToggle("fast");
		}
	};
</script>
