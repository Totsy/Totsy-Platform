<!-- JS for cart timer. -->
<script type="text/javascript" src="/js/cart-timer.js"></script>
<!-- JS for cart timer for individual items. -->
<script type="text/javascript" src="/js/cart-items-timer.js"></script>

<script type="text/javascript">	

var discountErrors = new Object();

	$(document).ready( function() {
					
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
		

	var cartExpires = new Date(<?=($cartExpirationDate  * 1000)?>);	

	//set the timer on individual items in the cart
	cartItemsTimer();
	
	//set the timer on the cart
	cartTimer(cartExpires);
	
	//applying tooltip
	$('#shipping_tooltip').tipsy({gravity: 'e'}); // nw | n | ne | w | e | sw | s | se
	$('#tax_tooltip').tipsy({gravity: 'e'}); // nw | n | ne | w | e | sw | s | se
	$('#promocode_tooltip').tipsy({gravity: 'nw'}); // nw | n | ne | w | e | sw | s | se

}); 
	
</script>

<script type="text/javascript" src="/js/jquery.number_format.js"></script>
<script type="text/javascript" src="/js/tipsy/src/javascripts/jquery.tipsy.js"></script>
<link rel="stylesheet" type="text/css" href="/js/tipsy/src/stylesheets/tipsy.css" />

<?php  if(!empty($subTotal)): ?>
<h2 class="page-title gray">
		<span class="cart-step-status gray" style="font-weight:bold">Order Review</span>
	<div style="float:right;">
		<span class="cart-step-status"><img src="/img/cart_steps_completed.png"></span>
		<span class="cart-step-status"><img src="/img/cart_steps_completed.png"></span>
		<span class="cart-step-status"><img src="/img/cart_steps_completed.png"></span>
		<span class="cart-step-status"><img src="/img/cart_steps4.png"></span>
	</div>
</h2>
<?php if (!empty($error)) { ?>
			<div class="checkout-error"><h2>Uh Oh! Please fix the errors below:</h2></div>
			<?php } ?>
<hr />


<?=$this->view()->render( array('element' => 'mobile_shipdateTimer'), array( 'shipDate' => $shipDate) ); ?>
	<hr/>
		<div class="cart-review-edit shipping">
			<div class="page-title">
			<strong style="font-size:12px;">Shipping Address <a href="#" onclick="window.location.href='/checkout/shipping';return false;">(Change)</a></strong>
	
			<hr />
				<div class="cart-review-edit-copy">
					<?=$shippingAddr['firstname']." ".$shippingAddr['lastname'];?>
				</div>
				<div class="cart-review-edit-copy">
					<?php 
					if($shippingAddr['address_2']=="") {
						echo $shippingAddr['address'];
					} else {
						echo $shippingAddr['address']."<br>".$shippingAddr['address_2'];
					}
					?>
				</div>
				<div class="cart-review-edit-copy">
					<?=$shippingAddr['city'].", ".$shippingAddr['state']." ".$shippingAddr['zip'];?>
				</div>
			</div>
			<br />
		<div class="cart-review-edit billing">
			<div class="page-title">
			<strong style="font-size:12px;">Billing Address &amp; Payment Method <a href="#" onclick="window.location.href='/checkout/payment';return false;">(Change)</a></strong>
	
			<hr/>
			<strong>Payment Method</strong>
			<div class="cart-review-edit-copy">
					<?php echo strtoupper($creditCard['type']);?>
	
					<?php echo "XXXX-XXXX-XXXX-". substr($creditCard['number'], -4, strlen($creditCard['number']));?>
				</div>
				<div class="cart-review-edit-copy">
					<?php echo "Expires in: ". $creditCard['month'] ."/". $creditCard['year'];?>
				</div>
			</div>
		</div>
		<hr />
		
	    
<?php endif ?>
	<?php
	if($missChristmasCount>0){
	?>
				<div class="holiday_message" style="text-align:center;">
				<p>One or more of the items in your cart is not guaranteed to be delivered on or before 12/25*.
				</p></div>
	
	
	<?php
	}
	elseif($notmissChristmasCount>0){
	?>
				<div class="holiday_message" style="text-align:center;">
				<p>
				Items will be delivered on or before 12/23.*
				</p></div>
	
	
	<?php
	}
	?>
	<div class="clear"></div>
<div class="message"></div>

<?php if (!empty($subTotal)): ?>

<?=$this->form->create(null ,array('id'=>'cartForm')); ?>
	<div id='message'><?php echo $message; ?></div>
		<table class="cart-table">
			<tbody>
			<?=$this->form->hidden("process", array('id'=>'process')); ?>
			<?php $x = 0; ?>
			<?php foreach ($cart as $item): ?>


			<?php
			if($item['miss_christmas']){
				$classadd = "background:#fde5e5;";
				if($notmissChristmasCount>0){
					$shipmsg = "<span class=\"shippingalert\">This item is not guaranteed to be delivered on or before 12/25.<br>Please remove this item from your cart and order separately to receive your other items on or before 12/23*.</span>";
				}
				else{
					$shipmsg = "<span class=\"shippingalert\">This item is not guaranteed to be delivered on or before 12/25.*</span>";
				}
			}
			else{
				$shipmsg = "Item will be delivered on or before December 23.*";
				$classadd = "";
			}
			?>


	<div class="clear"></div>
<br />
<strong>Your Items</strong>
<hr />
				<!-- Build Product Row -->
				<tr id="<?=$item->_id?>" style="<?=$classadd?>">

					<td colspan="8">	
					
									<?=$this->form->hidden("item$x", array('value' => $item->_id)); ?>
									<?=$this->html->link($item->description,'sale/'.$item->event_url.'/'.$item->url, array("target"=>"_blank")); ?>
									<span style="font-size:9px;"><?=$shipmsg?></span>
				
								<span style="display:none" id='<?php echo "itemCounter$x"; ?>' class="counter cart-review-line-timer" title='<?=$date?>'></span>
							
							<br />
							<span class="<?="price-item-$x";?> cart-review-line-price">													
								<strong>$<?=number_format($item->sale_retail,2)?></strong>
							</span>
							<br />
							<span class="<?="qty-$x";?> cart-review-line-qty">Qty: <?=$item->quantity;?></span>						
							<span class="<?="total-item-$x";?> cart-review-line-total" style="padding-right:10px;">$<?=number_format($item->sale_retail * $item->quantity ,2)?>
							</span>
							<br />
						<?php if($item->color) : ?>
							<span class="cart-review-color-size">Color:</span> <?=$item->color;?>
							<?php endif ?>
							<br />
							<?php if($item->size!=="no size") : ?>						
							<span class="cart-review-color-size">Size:</span> <?=$item->size;?>
							<?php endif ?>
						
						
							
					</td>
					
				</tr>
				<?php $x++; ?>
			<?php endforeach ?>
			</tbody>
		</table>
		<?=$this->form->end(); ?>
<hr />
		<div class="cart-codes">
				<div class="cart-code-buttons">
				     <?php if(!empty($credit)): ?>
				    	<strong style="font-size:11px;"><a href="#" id="credits_lnk" onclick="open_credit();" >Use Credits</a></strong> /
				    <?php endif ?> 

					<strong style="font-size:11px;"><a href="#" id="promos_lnk" onclick="open_promo();">Add Promo Code</a></strong>
				</div>
				<div style="clear:both"></div>
				<div id="promos_and_credit">
				    <div id="promo" style="display:none">
				    	<?=$this->view()->render(array('element' => 'promocode'), array( 'orderPromo' => $cartPromo, 'promocode_disable' => $promocode_disable)); ?>
				    </div>
				    <div id="cred" style="display:none; text-align:left !important">		
				    	<?=$this->view()->render(array('element' => 'credits'), array('orderCredit' => $cartCredit, 'credit' => $credit, 'user' => $user)); ?>
				    </div>
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
    		        	<?php echo '[' . $cartPromo['code'] . ']'; ?>	
    		        	:</span> 
    		        	<span style="float:right">-$<?=number_format(abs($cartPromo['saved_amount']),2)?>
    		        	</span>	
    		    </div>
   			    <?php endif ?>
   			    <?php if (!empty($services['tenOffFitfy'])):?>
			    <div style="clear:both"></div>
			    <div class="subtotal">
    		        	<span style="float: left;">Discount [10$ Off] :</span> 
    		        		<span style="float:right">-$<?=number_format($services['tenOffFitfy'],2)?>
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
			    <div style="font-weight:bold;" >
			    <div class="subtotal">	
			    <span id="shipping_tooltip" class="cart-tooltip" original-title="Shipping charges may vary depending on item type."><img src="/img/tooltip_icon.png">
			        	</span>
			        <span style="float: left;" id="shipping">
			        Shipping:</span> 
			        <span style="float:right">$<?=number_format($shippingCost,2)?></span>
			    </div>
			    </div>
			    <?php if (!empty($overShippingCost)):?>
			    <div style="clear:both"></div>
			    <div class="subtotal">
    		        <span style="float: left;">Oversize Shipping:</span> 
    		        <span style="float:right">$<?=number_format($overShippingCost,2)?></span>
    		    </div>
   			    <?php endif ?>
			    <?php if (!empty($shipping_discount)):?>
			    <div style="clear:both"></div>
			    <div class="subtotal">
    		        <span style="float: left;">Free Shipping 
    		        	<?php 
    		        	if(!empty($cartPromo)) {
    		        		if($cartPromo['type'] === 'free_shipping')
    		        			echo '[' . $cartPromo['code'] . ']';	
    		        	}?>		
    		        	:</span> 
    		        	<span style="color:#707070; float:right" class="fees_and_discounts">- $<?=number_format($shipping_discount,2)?></span>
    		
   			    <?php endif ?>
   			    
			    <div style="clear:both"></div>	
			    <div>
			    <div class="subtotal">
			        <span id="tax_tooltip" original-title="Sales tax will be calculated once we collect the shipping address for this order. If you are shipping to NY or NJ, tax will be charged on the order subtotal, shipping and handling at the applicable county rate. Tax rates within counties vary." class="cart-tooltip"><img src="/img/tooltip_icon.png"></span>		
			    <span id="estimated_tax" style="float: left;">Estimated Tax:</span> 
			        	<span style="float:right">$<?=number_format($tax,2)?></span>
			    </div>
			    </div>
			    <div style="clear:both" class="subtotal"><hr /></div>			
			    <div>
			        
			        <?php if (!empty($savings)) : ?>
			        <div class="subtotal">
			        <span style="font-size:15px; font-weight:bold">Your Savings:</span> 
			        	<span style="font-size:15px;float:right" id="ordertotal">$<?=number_format($savings,2)?></span>
			        </div>
			        <?php endif ?>
			        
			        
			        <div class="subtotal">
			        <span style="font-size:15px; font-weight:bold">Order Total:</span> 
			        	<span style="font-size:15px; color:#009900; float:right" id="ordertotal">$<?=number_format($total,2)?> </span>
			        </div>
			    </div>	
		</div>				
<hr />
		      <a href="#" data-role="button" data-ajax="true" onclick="updateOrder();return false;">Place Your Order</a>

	<div class="clear"></div>

<?=$this->form->end(); ?>
</div>
<div class="clear"></div>
<div style="color:#707070; font-size:12px; font-weight:bold; padding:10px;">
				<?php
				if($missChristmasCount>0&&$notmissChristmasCount>0){
				?>
				<div class="holiday_message" style="text-align:center;">
				<p>* Totsy ships all items together. If you would like the designated items in your cart delivered on or before 12/23, please ensure that any items that are not guaranteed to ship on or before 12/25 are removed from your cart and purchased separately. Our delivery guarantee does not apply when transportation networks are affected by weather. Please contact our Customer Service department at 888-247-9444 or email <a href="mailto:support@totsy.com">support@totsy.com</a> with any questions. </p></div>
				
				<?php
				}
				elseif($missChristmasCount>0){
				?>
				<div class="holiday_message" style="text-align:center;">
				<p>* Your items will arrive safely, but after 12/25.</p></div>
				
				<?php
				}
				else{
				?>
				
				<div class="holiday_message" style="text-align:center;">
				<p>* Our delivery guarantee does not apply when transportation networks are affected by weather.</p></div>
				
				<?php
				}
				?>
				
</div>

<div id="remove_form" style="display:none">
	<?=$this->form->create(null ,array('id'=>'removeForm')); ?>
	<?=$this->form->hidden('rmv_item_id', array('class' => 'inputbox', 'id' => 'rmv_item_id')); ?>
	<?=$this->form->end();?>
</div>
		
<script type="text/javascript" src="/js/cart-items-timer.js" charset="utf-8"></script>	
	
<div class="clear"></div>
<?php else: ?>
<div class="holiday_message">
		<p>Your shopping cart is empty <a href="/sales">Continue Shopping</a/></p>
</div>
<?php endif ?>
</div>
<div id="modal" style="background:#fff!important; z-index:9999999999!important;">

<?php if(number_format((float) $total, 2) >= 35 && number_format((float) $total, 2) <= 44.99){ ?>
<script type=\"text/javascript\">
	var total = "<?=(float)$total?>";
	var itemUrl = "<?=$itemUrl?>";

    $.post('/cart/modal',{modal: 'disney'},function(data){
      //  alert(data);
        if(data == 'false'){
            $('#modal').load('/cart/upsell?subtotal=' + total + '&redirect=' + itemUrl).dialog({
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
</script>;
<?php } ?>

</div>

<script type="text/javascript" charset="utf-8">

function updateOrder() {
	$('#process').val("true");
	$('#cartForm').submit();	    
}

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
