<?php use lithium\storage\Session; ?>

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

	var cartExpires = new Date(<?php echo ($cartExpirationDate  * 1000)?>);	

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
<div class="cart-content" id="p-review">
	<div class="grid_11 cart-header-left">
		<div style="float:left;">
			<h2 class="page-title gray">
				<span class="cart-step-status gray" style="font-weight:bold">Review your Shipping and Payment Information</span>
				<span class="cart-step-status"><img src="<?=$img_path_prefix?>/cart_steps_completed.png"></span>
				<span class="cart-step-status"><img src="<?=$img_path_prefix?>/cart_steps_completed.png"></span>
				<span class="cart-step-status"><img src="<?=$img_path_prefix?>/cart_steps_completed.png"></span>
				<span class="cart-step-status"><img src="<?=$img_path_prefix?>/cart_steps4.png"></span>
			</h2>
		</div>
	</div>
	<div class="grid_5 cart-header-right">
		<?php echo $this->view()->render( array('element' => 'shipdateTimer'), array( 'shipDate' => $shipDate) ); ?>
	</div>	
	<div class="clear"></div>
	<hr/>
	<div class="grid_16" style="width:940px; padding-bottom:35px">
		<div class="cart-review-edit shipping">
			<div class="page-title" style="font-weight:bold; margin: 15px; font-size:15px"><span class="cart-review-edit-header">Shipping Address</span>
			<span class="cart-review-edit-change-button">
				<a href="/checkout/shipping">(Change)</a>
			</span>
			<hr>
				<div class="cart-review-edit-copy">
					<?php echo $shippingAddr['firstname']." ".$shippingAddr['lastname'];?>
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
					<?php echo $shippingAddr['city'].", ".$shippingAddr['state']." ".$shippingAddr['zip'];?>
				</div>
			</div>
		</div>
		<div class="cart-review-edit billing">
			<div class="page-title" style="font-weight:bold; margin: 15px; font-size:15px"><span class="cart-review-edit-header">Billing Address &amp; Payment Method</span>
				<span class="cart-review-edit-change-button">
					<a href="/checkout/payment">(Change)</a>
				</span>
			<hr>
			<div class="cart-review-edit-copy">
					<?php echo strtoupper($creditCard['type']);?>
				</div>
				<div class="cart-review-edit-copy">
					<?php echo "Ends in: ". substr($creditCard['number'], -4, strlen($creditCard['number']));?>
				</div>
				<div class="cart-review-edit-copy">
					<?php echo "Expires in: ". $creditCard['month'] ."/". $creditCard['year'];?>
				</div>
			</div>
		</div>
		<div class="cart-order-place-outer">
			<div class="page-title cart-order-place-inner">
				<span style="margin-bottom: 12px">
				Order Total:
				    <span style="color:#009900; text-align:center">
				    $<?php echo number_format($total,2)?> </span>
				</span>    
				<div style="text-align:center; diplay:inline-block !important">
					<input type="submit" class="button cartSubmit" form="cartForm" value="Place Your Order" />
			 	</div>
			</div>
		</div>
	</div>

<?php endif ?>
<div class="message"></div>

<?php if (!empty($subTotal)): ?>

<div class="grid_16" style="width:935px">
<?php echo $this->form->create(null ,array('id'=>'cartForm')); ?>
	<div id='message'><?php echo $message; ?></div>
		<table class="cart-table">
			<tbody>
			<?php echo $this->form->hidden("process", array('id'=>'process')); ?>
			<?php $x = 0; ?>
			<?php foreach ($cart as $item): ?>


				<!-- Build Product Row -->
				<tr id="<?php echo $item->_id?>">
					<td colspan="1" class="cart-th">
						<span class="cart-review-thumbnail">
						<?php
								if (!empty($item->primary_image)) {
									$image = $item->primary_image;
									$productImage = "/image/$image.jpg";
								} else {
									$productImage = "/img/no-image-small.jpeg";
								}
							?>
							<?php echo $this->html->link(
								$this->html->image("$productImage", array(
									'width'=>'107',
									'height'=>'107',
							'style' => 'margin:2px; padding:4px;')),
								'sale/'.$item->event_url.'/'.$item->url,
									array(
									'id' => 'main-logo_', 'escape'=> false
								), $item->description,'sale/'.$item->event_url.'/'.$item->url); ?>
						</span>
					</td>
					<td colspan="8">
						<div class="cart-review-line-content">
							<span>
								<span class="cart-review-desc">

									<?php echo $this->form->hidden("item$x", array('value' => $item->_id)); ?>
									<?php echo $this->html->link($item->description,'sale/'.$item->event_url.'/'.$item->url, array("target"=>"_blank")); ?>
				
								<span style="display:none" id='<?php echo "itemCounter$x"; ?>' class="counter cart-review-line-timer" title='<?php echo $date?>'></span>
							</span>
							
							<span class="<?php echo "price-item-$x";?> cart-review-line-price">													
								<strong>$<?php echo number_format($item->sale_retail,2)?></strong>
							</span>
							<span class="<?php echo "qty-$x";?> cart-review-line-qty">Qty: <?php echo $item->quantity;?></span>						
							<span class="<?php echo "total-item-$x";?> cart-review-line-total" style="padding-right:10px;">$<?php echo number_format($item->sale_retail * $item->quantity ,2)?>
							</span>
						</div>
							<hr />
						<div>
						<?php if($item->color) : ?>
							<div><span class="cart-review-color-size">Color:</span> <?php echo $item->color;?></div>
							<?php endif ?>
							<?php if($item->size!=="no size") : ?>						
							<div><span class="cart-review-color-size">Size:</span> <?php echo $item->size;?></div>
							<?php endif ?>
							<br><?php echo $shipmsg?>

						</div>	

					</td>
				</tr>
				<?php $x++; ?>
			<?php endforeach ?>
			</tbody>
		</table>
		<?php echo $this->form->end(); ?>
		</div>

		<div class="clear"></div>
		<div class="grid_16" style="width:935px; padding-top:30px">
		<div class="cart-codes">
		
<!-- no promocodes for Mama users begin -->
				<div class="cart-code-buttons">
				     <?php if(!empty($credit)): ?>
				    	<strong>Add <a href="#" id="credits_lnk" onclick="open_credit();" >Credits</a></strong> /
			         <?php endif ?>
				<?php if(Session::read("layout", array("name"=>"default"))!=="mamapedia") : ?>
					<span id="promocode_tooltip" original-title="Promo codes cannot be combined and can be applied once to an order per member." class="cart-tooltip">
			        	<img src="/img/tooltip_icon.png">
			        </span>
					<strong>Add <a href="#" id="promos_lnk" onclick="open_promo();">Promo Code</a></strong>
					 <?php if($serviceAvailable) : ?>
				    	/ <strong><a href="#" id="reservices_lnk" onclick="reaplyService();">Re-Apply <?php echo $serviceAvailable; ?></a></strong>
				    <?php endif ?>
				<?php endif ?>
				</div>
				<!-- no promocodes for Mama users ending -->
				
				<div style="clear:both"></div>
				<div id="promos_and_credit">
				    <div id="promo" style="display:none">
				    	<?php echo $this->view()->render(array('element' => 'promocode'), array( 'orderPromo' => $cartPromo, 'promocode_disable' => $promocode_disable)); ?>
				    </div>
				    <div id="cred" style="display:none; text-align:left !important">		
				    	<?php echo $this->view()->render(array('element' => 'credits'), array('orderCredit' => $cartCredit, 'credit' => $credit, 'user' => $user)); ?>
				    </div>
				</div>
			</div>
			<div class="cart-subtotal-content">
			    <div class="subtotal" >
			        	<span style="float:left;">Subtotal:</span>
			        	<span style="float:right" id="subtotal">$<?php echo number_format($subTotal,2)?></span>
			    </div>
			    <?php if (!empty($cartPromo['saved_amount']) && ($cartPromo['type'] != 'free_shipping') ):?>
			    <div style="clear:both"></div>
			    <div class="subtotal">
    		        	<span style="float: left;">Discount 
    		        	<?php echo '[' . $cartPromo['code'] . ']'; ?>	
    		        	:</span> 
    		        	<span style="float:right">-$<?php echo number_format(abs($cartPromo['saved_amount']),2)?>
    		        	</span>	
    		    </div>
   			    <?php endif ?>
   			    <?php if (!empty($services['tenOffFitfy'])):?>
			    <div style="clear:both"></div>
			    <div class="subtotal">
    		        	<span style="float: left;">Discount [10$ Off] :</span> 
    		        		<span style="float:right">-$<?php echo number_format($services['tenOffFitfy'],2)?>
    		        		</span>
    		        	</span>
    		    </div>
   			    <?php endif ?>
   			    <?php if (!empty($credits)):?>
			    <div style="clear:both"></div>
			    <div class="subtotal">
    		        	<span style="float:left;">Credits:</span> 
    		        	<span style="float:right">- $<?php echo number_format(abs($credits),2)?></span>
    		    </div>
   			    <?php endif ?>
			    <div style="clear:both"></div>
			    <div style="font-weight:bold;" >
			    <div class="subtotal">
			    <span id="shipping_tooltip" class="cart-tooltip" original-title="Shipping charges may vary depending on item type."><img src="/img/tooltip_icon.png">
			        	</span>
			        <span style="float: left;" id="shipping">
			        Shipping:</span> 
			        <span style="float:right">$<?php echo number_format($shippingCost,2)?></span>
			    </div>
			    </div>
			    <?php if (!empty($overShippingCost)):?>
			    <div style="clear:both"></div>
			    <div class="subtotal">
    		        <span style="float: left;">Oversize Shipping:</span> 
    		        <span style="float:right">$<?php echo number_format($overShippingCost,2)?></span>
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
    		        	<span style="color:#707070; float:right" class="fees_and_discounts">- $<?php echo number_format($shipping_discount,2)?></span>
    		    </div>
   			    <?php endif ?>
			    <div style="clear:both"></div>
			    <div>
			    <div class="subtotal">

			        <span id="tax_tooltip" original-title="Sales tax will be calculated once we collect the shipping address for this order. If you are shipping to NY or NJ, tax will be charged on the order subtotal, shipping and handling at the applicable county rate. Tax rates within counties vary." class="cart-tooltip"><img src="/img/tooltip_icon.png"></span>		
			    <span id="estimated_tax" style="float: left;">Estimated Tax:</span> 
			        	<span style="float:right">$<?php echo number_format($tax,2)?></span>
			    </div>
			    </div>
			    <div style="clear:both" class="subtotal"><hr /></div>
			    <div>
			        <div class="cart-savings">
			        	<?php if (!empty($savings)) : ?>
			        	Your Savings:
			        	$<?php echo number_format($savings,2)?>
			        	<?php endif ?>
			        </div>
			        <div class="subtotal">
			        <span style="font-size:15px; font-weight:bold">Order Total:</span> 
			        	<span style="font-size:15px; color:#009900; float:right" id="ordertotal">$<?php echo number_format($total,2)?> </span>
			        </div>
			    </div>
		</div>
</div>

<div class="cart-button fr cart-nav-buttons">
	<input type="submit" class="button cartSubmit" form="cartForm" value="Place Your Order" />
	<div class="clear"></div>

<?php echo $this->form->end(); ?>
</div>
<div class="clear"></div>

<div id="remove_form" style="display:none">
	<?php echo $this->form->create(null ,array('id'=>'removeForm')); ?>
	<?php echo $this->form->hidden('rmv_item_id', array('class' => 'inputbox', 'id' => 'rmv_item_id')); ?>
	<?php echo $this->form->end();?>
</div>


<div id="reappServiceF" style="display:none">
	<?php echo $this->form->create(null ,array('id'=>'reappServiceForm')); ?>
	<?php echo $this->form->hidden('reapplyService', array('class' => 'inputbox', 'id' => 'reapplyService')); ?>
	<?php echo $this->form->end();?>
</div>

<script type="text/javascript" src="/js/cart-items-timer.js" charset="utf-8"></script>	
<div class="clear"></div>
<?php else: ?>
	<div class="grid_16 cart-empty">
		<h1>
			<span class="page-title gray" style="padding:0px 0px 10px 0px;">Your shopping cart is empty</span>
			<a href="/sales" title="Continue Shopping">Continue Shopping</a/></h1>
	</div>
<?php endif ?>
</div>
<div id="modal" style="background:#fff!important; z-index:9999999999!important;">
	<?php
		/* @DG-2011.12.09
			- commented out per Micah's request
		if(number_format((float) $total, 2) >= 35 && number_format((float) $total, 2) <= 44.99){ ?>
	<script type="text/javascript">
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
	</script>
	<?php }*/ ?>

</div>

<script type="text/javascript" charset="utf-8">

// submit cart - bind click event to .cartSubmit buttons, prevent multiple clicks/submissions
$(document).ready(function(){
	$('.cartSubmit').click(function(e){
		e.preventDefault(); // if JS is enabled, we can disable default submit behavior
		$('#process').val('true');
		$('.cartSubmit').attr('disabled', 'disabled').val('Please wait…').css('cursor', 'default');
		$('#cartForm').submit();
	});
});

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
	//Submit Reapply Old Service
	function reaplyService() {
		$('input[name="reapplyService"]').val('true');
		$('#reappServiceForm').submit();
	}
</script>