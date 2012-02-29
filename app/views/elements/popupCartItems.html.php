<script id="template" type="text/x-jquery-tmpl">
 <div class="cart_popup_item_wrapper">
 	{{if primary_image}}
 	<div class="cart_popup_item_thumbnail">
 		<img src="/image/${primary_image}.jpg" style="width:60px; height:60px">
 	</div>
 	{{else}}
 	<div class="cart_popup_item_thumbnail">
 		<img src="/img/no-image-small.jpeg" style="width:60px; height:60px">
 	</div>
 	{{/if}}
 	<div class="cart_popup_item_fields">
 		<span class="cart_popup_item_description">
 			<a href="#" onclick="window.location.replace('${url}')">
 			 	${description}
 			 </a>
 		</span>
 		<span class="cart_popup_item_price"><strong> $${sale_retail} </strong></span>
 		<span class="cart_popup_line_qty">Qty: ${quantity} </span>
 		</span>
 		<span class="cart_popup_line_total"> $${line_total} </span>
 		<div style="clear:both"></div>
 		<hr>
 		{{if color}}
 		<div>
 		    <span class="cart-review-color-size">Color:</span> ${color}
 		</div>
 		<span id="itemCounter${_id}_display" style="float:right"></span>
 		{{/if}}
 		{{if size}}
 		<div>
 		    <span class="cart-review-color-size">Size:</span> ${size}
 		</div>
 		{{/if}}

 	</div>
 </div>
 <div style="clear:both"></div>
</script>
<div id="cart_popup_wrap">
	<div id="cart_popup" class="grid_16 roundy glow" style="display:none">
		<div id="cart_popup_header">
		    <div id="cart_popup_timer">
		    	<span style="float:right; margin-left: 30px">Item Reserved For:<br>
		    		<span style="color:#009900; font-weight:bold;font-size:14px" id="itemCounter"></span>
		    	</span>
		    	<span style="float:right">Estimated Ship Date: <br>
		    		 <span id="ship_date" style="font-weight:bold; color:#009900; font-size:14px"></span>
		    	</span>
		    </div>
		    <div id="cart_popup_close_button">
		    	<a href="#">
		    		<img src="/img/popup_cart_close.jpg" style="width:20px; height:20px">
		    	</a>
		    </div>
		</div>
		<div style="clear:both"></div>
		<div id="cart_item"></div>
		<div style="clear:both"></div>
		<div style="clear:both"></div>
		<div><hr></div>
		<div id="cart_popup_breakdown">
		   <div class="cart-savings">Your Savings: $<span id="savings"></span></div>
		   <div id="cart_popup_order_total">
		   	<span class="cart-order-total">Subtotal: </span>
		       <span id="order_total_num" style="font-weight:bold !important; color:#009900 !important; font-size:14px !important"></span>
		   </div>
		</div>
		<div style="clear:both"></div>
		<div id="cart_popup_checkout_buttons" class="cart-button fr">
		   <a id="cart_popup_cont_shop" class="button_border" href="#">Continue Shopping</a>
		   <a id="cart_popup_checkout" class="button" href="/checkout/view">Checkout</a>
		</div>
	</div><!-- /#cart_popup -->
</div>
