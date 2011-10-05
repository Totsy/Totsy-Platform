<script id="template" type="text/html">
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
 			<a target="_blank" href="#" >
 			 ${description} </a>
 			 </span>
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