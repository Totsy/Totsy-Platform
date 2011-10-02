<script src="/js/jquery.tmpl.js" type="text/javascript"></script>

<!-- template used for items on cart. jquery.tmpl.js driven -->
<script id="template" type="text/html">
 <div class="cart_popup_item_wrapper">
 	<div class="cart_popup_item_thumbnail">
 		<img src="/image/4e36f764d6b0250411000257.jpg" style="width:60px; height:60px">
 	</div>
 	<div class="cart_popup_item_fields">
 		<span class="cart_popup_item_description">
 			<a target="_blank" href="sale/hot-pink-mary-janes-with-white-button-polka-dots-hot-pink">
 			 ${description} </a>
 		</span>
 		<span class="cart_popup_item_price"><strong> $${sale_retail} </strong></span>
 		<span class="cart_popup_line_qty">Qty: ${quantity} </span>		
 		</span>
 		<span class="cart_popup_line_total"> $${line_total} </span>
 		<span title="date goes here" class="counter cart-review-line-timer" id="itemCounter0" style="display: none;"></span>
 		<div style="clear:both"></div>
 		<hr>
 		<div>
 		    <span class="cart-review-color-size">Color:</span> ${color} 
 		</div>
 		<div>
 		    <span class="cart-review-color-size">Size:</span> ${size}
 		</div>
 	</div>
 </div>	
</script>

<script type="text/javascript">

var item_id = "<?=$item->_id?>";

//cart items immediately visible 
var visibleItems = new Array();

//cart items not immediately visible
var invisibleItems = new Array();

var isCollapsed = false;

//jQuery for adding items.
$(document).ready( function() {

	var showCartPopup = function(cart) {
				
		//reset all items
		if(invisibleItems.length>0){
			invisibleItems = [];
		}
		
		if(visibleItems.length>0){
			visibleItems = [];
		}
		
		var visibleItemCount = 3;
		var invisibleItemCount = 0;
				
		//convert JSON string to JS Object
		var cartData = eval('(' + cart + ')');
				
		for(i in cartData) {
			//formatting price and line totals
			cartData[i]['sale_retail'] = cartData[i]['sale_retail'].toFixed(2);	
			cartData[i]['line_total'] = (cartData[i]['quantity'] * cartData[i]['sale_retail']).toFixed(2);
		
			if(i < visibleItemCount){ 
				visibleItems.push(cartData[i]);
			} else {
				invisibleItems.push(cartData[i]);
				invisibleItemCount++;
			}
		}
		
		//unset cart_item DIV
		$("#cart_item").html("");
		
		//attach template to cart_item DIV
		$("#template").tmpl(visibleItems).appendTo("#cart_item");
		
		if (invisibleItemCount > 0 ) {
			$("#more_cart_items").css("visibility", "visible");	
		}  
		
		$("#cart_popup").slideToggle("2000");	
	};
	
	//add items to cart
	var addItem = function() {
		var item_size = "";
	
		if(typeof $('#size-select').attr('value')!='undefined') {
			item_size = $('#size-select').attr('value');
		} else {
			item_size = "no size";
		}
		
		$.ajax({
	        url: $.base + 'cart/add',
	        data: "item_id=" + item_id + "&" + "item_size=" + item_size,
	        context: document.body,
		    success: function(data) {
	        	showCartPopup(data);
	        }
	    });
	};
	
	var closeCartPopup = function() {
		$("#cart_popup").slideToggle("2000");
		//set isCollapsed to false so that the link doesn't appear on re-open
		isCollapsed = false;
		$("#more_cart_items a").html("See more...");
	}
	
	//click handler for adding items to cart
	$("#add-to-cart").click(function(){
		addItem();
	});	
	
	//toggle items for carts with more than 3 different types of items
	$("#more_cart_items a").click(function(){	
		if (isCollapsed==false) {
			isCollapsed = true;
			$("#more_cart_items a").html("...see less");
			$("#template").tmpl(invisibleItems).appendTo("#cart_item");	
		} else {
			isCollapsed = false;			
			//unset cart_item DIV
			$("#cart_item").html("");
			$("#more_cart_items a").html("See more...");
			$("#template").tmpl(visibleItems).appendTo("#cart_item");	
		}
	});

	//close cart popup
	$("#cart_popup_close_button").click(function(){
		closeCartPopup();
	});

});
</script>

<div style="position:relative"> 
<div id="cart_popup" class="grid_16 roundy glow" style="display:none">
	<div id="cart_popup_header">
	    <div id="cart_popup_timer">
	    	<span style="float:right">Item Reserved For:<br>
	    		<span style="color:#009900; font-weight:bold;font-size:14px" id="itemCounter" class="hasCountdown">14:05 minutes</span>
	    	</span>
	    	<span style="float:right">Estimated Shipping Date: <br>
	    		 <span style="font-weight:bold; color:#009900; font-size:14px">01-25-2012</span>
	    	</span>		
	    </div>
	    <div id="cart_popup_close_button">
	    	<a href="#"><img src="/img/popup_cart_close.jpg" style="width:20px; height:20px"></a>
	    </div>
	</div>
	<div style="clear:both"></div>
	 
	<div id="cart_item"></div>
	<div id="more_cart_items" style="font-style:italic !important; visibility:hidden; text-align:center">
		<a href="#">See more...</a>
	</div>
	
	<hr>	
	 <div style="clear:both"></div>
	 <div id="cart_popup_breakdown">
	 	<div class="cart-savings">Your Savings: $22.49</div>
	 	<div id="cart_popup_order_total">
	 		<span class="cart-order-total">Order Total:</span> 
	 	    <span id="ordertotal">$22.45 </span>
	 	</div>						    	
	 </div>
	 <div style="clear:both"></div>
	 <div class="cart-button fr cart-nav-buttons">
	 	<a id="cart_popup_cont_shop" class="button_border" href="/sale/mom-co-maternity">Continue Shopping</a>		      
	 	<a id="cart_popup_checkout" class="button" href="/checkout/shipping">Checkout</a>		 
	 </div>
</div>
</div>

<div class="grid_16">
	<h2 class="page-title gray"><span class="red"><a href="/sales" title="Sales">Today's Sales</a> /</span> <a href="/sale/<?=$event->url?>" title="<?=$event->name?>"><?=$event->name?></a><div id="listingCountdown" class="listingCountdown" style="float:right;"></div></h2>
	<hr />
</div>

<div class="grid_6">
	<!-- Start product item -->
		<?php if ($item->total_quantity <= 0): ?>
					<?=$this->html->image('/img/soldout.png', array(
						'title' => "Sold Out",
						'style' => 'z-index : 99999; position : absolute; right:0;',
						'id'=>'sold_out_img'
					)); ?>
			<?php endif ?>
				<?php if (!empty($item->primary_image)): ?>
<div class="zoom-section">
	<div class="zoom-small-image">
    	<a href="/image/<?php echo $item->zoom_image; ?>.jpg" id="zoom1" class="cloud-zoom" rel="position: 'inside'" style="overflow:hidden;">
    	<img src="/image/<?php echo $item->primary_image; ?>.jpg" alt="" border="0" title="" width="348"/></a>
  	</div>

  	<!-- Start additional image view thumbnails -->
	<div class="zoom-desc" style="margin-top:6px;">
		<?php
			if (!empty($item->primary_image)) {
				echo $this->html->link(
				$this->html->image("/image/{$item->primary_image}.jpg", array(
					'class' => "zoom-tiny-image",
					'width' => "75",
					'height' => "75",
					'alt' => "product-thumb-fpo",
					'rel' => "full_img_1")),
					"/image/{$item->primary_image}.jpg", array(
							'class' => "cloud-zoom-gallery",
							'rel' => "useZoom: 'zoom1', smallImage: '/image/{$item->primary_image}.jpg'",
							'escape'=> false
				));
			}
		?>
		<?php if (!empty($item->alternate_images)): ?>
			<?php $x = 2; ?>
			<?php foreach ($item->alternate_images as $value): ?>
					<?=$this->html->link(
					$this->html->image("/image/{$value}.jpg", array(
						'class' => "zoom-tiny-image",
						'width' => "75",
						'height' => "75",
						'alt' => "full_img_$x"
						)),
						"/image/$item->zoom_image.jpg", array(
							'class' => "cloud-zoom-gallery",
							'rel' => "useZoom: 'zoom1', smallImage: '/image/{$value}.jpg'",
							'escape'=> false
				));

					?>

					<?php $x++; ?>
			<?php endforeach ?>
		<?php endif ?>

	<!-- End additional image view thumbnails -->

	</div>
		<?php endif ?>
		<?php if (!empty($item->alternate_images)): ?>
		    <?php $x = 2; ?>
		<?php endif ?>
	</div>
	<!-- End product item -->
</div>

<div class="grid_7">

	<div id="product-detail-right-top"  style="width:405px;">

		<div id="listingCountdown" class="listingCountdown"></div>

	</div>

	<div id="detail-top-left"  style="width:405px;">
		<h1><strong><?=$event->name?></strong> <?=$item->description." ".$item->color; ?></h1>
	</div>
		<div class="clear"></div>

		<div id="tabs">
			<ul>
			    <li><a href="#description">Description</a></li>
			    <li><a href="#shipping">Shipping &amp; Returns</a></li>
			    <!--<li><a href="#video"><span>Video</span></a></li>-->
			</ul>

			<!-- Start Description Tab -->
			<div id="description" class="ui-tabs-hide">
				<?php echo $item->blurb; ?>
			</div>
			<!-- End Description Tab -->

			<!-- Start Shipping Tab -->
			<div id="shipping" class="ui-tabs-hide">
			<strong>Shipping:</strong> Totsy will ship this item via Standard UPS or Standard US Mail shipping based on your selection at the end of your <?=$this->html->link('checkout process', array('Cart::view')); ?>.
			Complete shipping details are available at <?=$this->html->link('shipping terms', array('Pages::shipping')); ?>.

			<p><strong>Returns:</strong> Totsy accept returns on selected items only. You will get a merchandise credit and free shipping (AK &amp; HI: air shipping rates apply). Simply be sure that we receive the merchandise you wish to return within 30 days from the date you originally received it in its original condition with all the packaging intact. Please note: Final Sale items cannot be returned. Want to learn more? Read more in our <?=$this->html->link('returns section', array('Pages::returns')); ?>.</p>
			</div>
			<!-- End Shipping Tab -->

			<!-- Start Video Tab -->
			<!--
			<div id="video" class="ui-tabs-hide">
			</div>
			-->
			<!-- End Video Tab -->

		</div>

<!--Disney -->
      <div class="disney">
          <strong>SPECIAL BONUS!</strong><hr/></p>
       <p> Included with your purchase of $45 or more is a one-year subscription to <img src="/img/Disney-FamilyFun-Logo.jpg" align="absmiddle" width="95px" /> ( a $10 value )
       <span id="disney">Offer & Refund Details</span>
      </div>
	<br><!-- Started Related Products -->
	<div id="related-products">
		<?php $relatedData = $related; ?>		
		<?php if (!empty($relatedData)): ?>
		<h2 style="color:#707070;font-size:14px;">You would also love</h2>
		<hr />
		<?php foreach ($related as $relatedItem): ?>
			<?php
				if (empty($relatedItem['primary_image'])) {
					$relatedImage = '/img/no-image-small.jpeg';
				} else {
					$relatedImage = "/image/".$relatedItem['primary_image'].".jpg";
				}
				
				echo $this->html->link(
					$this->html->image($relatedImage, array(
						"class" => "img-th",
						"width" => "93",
						"height" => "93")),
						"/sale/$event->url/".$relatedItem['url'], array(
							'id' => $relatedItem['description'],
							'escape'=> false
				));
			?>
		<?php endforeach ?>
	<?php endif ?>
	</div>
	<!-- End Related Products -->
	</div>

	<div class="grid_3">
	<div id="detail-top-right" class="r-container">

		<div class="md-gray p-container roundy">
			<h2 class="caps" style="font-size:14px; padding-top:5px">Totsy Price</h2>
			<div style="padding: 10px 0px 0px 0px; color:#009900; font-size:24px;">$<?=number_format($item->sale_retail,2); ?></div>

			<div class="original-price" style="font-size:11px; padding-bottom:10px;">Original: $<?=number_format($item->msrp,2); ?></div>

<?php if (!empty($sizes)): ?>
				<?php if ( !((string)strtolower($sizes[0]) ==='no size')): ?>
						<select name="size-select" id="size-select">
									<option value="">Please Select Size</option>
							<?php foreach ($sizes as $value): ?>
									<option value="<?=$value?>"><?=$value?></option>
							<?php endforeach ?>
						</select>
						<hr />
				<?php endif ?>
			<?php endif ?>
			<?php if ($item->total_quantity >= 1): ?>
				<div id="hidden-div" style="display:none; color:#eb132c; font-weight:bold;">Please Select Size!</div>
				<span style="display: inline-block;">
				<input type="button" value="Add to Cart" id="add-to-cart" class="button">	
				</span>
				<div id="all-reserved"></div>
			<?php endif ?>
		</div>

		<?php $logo = $event->images->logo_image;?>
		<div style="padding:0px 0px 0px 7px;">
		<?=$this->html->image("/image/$logo.jpg", array(
			'alt' => $event->name, 'width' => "148"
		)); ?>

 		</div>
	</div>

		<div style="padding:10px 0px; text-align:center;">
			<?php echo $spinback_fb; ?>
		</div>

		</div>
	<div class="clear"></div>
	</div>
<div id="modal" style="background:#fff!important; z-index:999!important;"></div>
<script type="text/javascript">
$(function () {
	var saleEnd = new Date();
	saleEnd = new Date(<?php echo $event->end_date->sec * 1000?>);
	$('#listingCountdown').countdown({until: saleEnd, layout: 'Ends in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
});
</script>

<script type="text/javascript">
    $('#disney').click(function(){
		$('#modal').load('/events/disney').dialog({
			autoOpen: false,
			modal:true,
			width: 739,
			height: 750,
			position: 'top',
			close: function(ev, ui) { $(this).close(); }

		});
		$("#sold_out_img").css("z-index", 999);
		$('#modal').dialog('open');
	});
</script>

<script type="text/javascript">
$(document).ready(function() {
	var itemCheck = function(){
		var item_size = $('#size-select').attr('value');
		if(item_size != '') {
		    $.ajax({
	            url: $.base + 'items/available',
	            data: "item_id=" + item_id + "&" + "item_size=" + item_size,
	            context: document.body,
	            success: function(data){
	                if (data == 'false') {
	                    $('#all-reserved').show();
	                    $('.button').hide();
	                    $('#all-reserved').html("<p style='background:#EB132C;padding:5px;text-align:center;color:#fff;border-radius:6px;'>All items are reserved <br>Check back in two minutes</p>");
	                } else {
	                    $('.button').show();
	                    $('#all-reserved').hide();
	                }
	             }
	        });
		}
	};
	itemCheck();

	$("#size-select").change(function(){
		itemCheck();
	});
});
</script>

<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
      checkOptions();
      $("select").change(checkOptions);

      function checkOptions() {
        var getSize = false;
        $("select").each(function(index, element) {
          if ( $(element).val() == "" ) {
            getSize = true;
          }
        });

        if (getSize) {
          $("#hidden-div").show();
          $("#add-to-cart").attr("disabled","disabled");
        } else {
          $("#hidden-div").hide();
          $("#add-to-cart").removeAttr("disabled");
        };
      }
    });
</script>
<script type="text/javascript">
//cto product tag
var cto_params = [];
cto_params["i"] = $('#item_id').attr('value');
var cto_conf = 't1=sendEvent&c=2&p=3290';
var cto_conf_event = 'v=2&wi=7714287&pt1=2';
var CRITEO=function(){var b={Load:function(d){var c=window.onload;window.onload=function(){if(c){c()}d()}}};function a(e){if(document.createElement){
var c=document.createElement((typeof(cto_container)!='undefined'&&cto_container=='img')?'img':'iframe');if(c){c.width='1px';c.height='1px';c.style.display='none';
c.src=e;var d=document.getElementById('cto_mg_div');if(d!=null&&d.appendChild){d.appendChild(c)}}}}return{Load:function(c){
document.write("<div id='cto_mg_div' style='display:none;'></div>");c+='&'+cto_conf;var f='';if(typeof(cto_conf_event)!='undefined')f=cto_conf_event;
if(typeof(cto_container)!='undefined'){if(cto_container=='img')c+='&resptype=gif';}if(typeof(cto_params)!='undefined'){for(var key in cto_params){if(key!='kw')
f+='&'+key+'='+encodeURIComponent(cto_params[key]);}if(cto_params['kw']!=undefined)c+='&kw='+encodeURIComponent(cto_params['kw']);}c+='&p1='+encodeURIComponent(f);
c+='&cb='+Math.floor(Math.random()*99999999999);try{c+='&ref='+encodeURIComponent(document.referrer);}catch(e){}try{
c+='&sc_r='+encodeURIComponent(screen.width+'x'+screen.height);}catch(e){}try{c+='&sc_d='+encodeURIComponent(screen.colorDepth);}catch(e){}b.Load(function(){
a(c.substring(0,2000))})}}}();CRITEO.Load(document.location.protocol+'//dis.us.criteo.com/dis/dis.aspx?');
</script>
