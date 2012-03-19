<?php echo $this->html->script(array('cloud-zoom.1.0.2'));?>
<script src="/js/jquery.tmpl.js" type="text/javascript"></script>

<script type="text/javascript">
	var item_id = "<?php echo $item->_id?>";
</script>

<?php echo $this->html->script(array('cart-timer.js?v='.filemtime(LITHIUM_APP_PATH . '/webroot/js/cart-timer.js'), 'cart-items-timer.js?v='. filemtime(LITHIUM_APP_PATH . '/webroot/js/cart-items-timer.js'), 'cart-popup.js?v=' . filemtime(LITHIUM_APP_PATH . '/webroot/js/cart-popup.js')));?>

<!-- template used for items on cart. jquery.tmpl.js driven -->
<?php echo $this->view()->render( array('element' => 'popupCartItems') ); ?>

<div style="position:relative">
<div id="cart_popup" class="grid_16 roundy glow" style="display:none">
	<div id="cart_popup_header">
	    <div id="cart_popup_timer">
	    	<span style="float:right; margin-left: 30px">Item Reserved For:<br>
	    		<span style="color:#009900; font-weight:bold;font-size:14px" id="itemCounter"></span>
	    	</span>
	    	<span style="float:right">Estimated Shipping Date: <br>
	    		 <span id="ship_date" style="font-weight:bold; color:#009900; font-size:14px"></span>
	    	</span>
	    </div>
	    <div id="cart_popup_close_button">
	    	<a href="#">
	    	<img src="<?=$img_path_prefix?>popup_cart_close.jpg" style="width:20px; height:20px"></a>
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
</div>
</div>

<div class="grid_16">
	<h2 class="page-title gray"><span class="red"><a href="/sales" title="Sales">Today's Sales</a> /</span> <a href="/sale/<?php echo $event->url?>" title="<?php echo $event->name?>"><?php echo $event->name?></a><div id="listingCountdown" class="listingCountdown" style="float:right;"></div></h2>
	<hr />
</div>

<div class="grid_6">
	<!-- Start product item -->
		<?php if ($item->total_quantity <= 0): ?>
					<?php echo $this->html->image('/img/soldout.png', array(
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
					<?php echo $this->html->link(
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
		<h1><?php echo $item->description." ".$item->color; ?></h1>
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
			<strong>Shipping:</strong> Totsy will ship this item via Standard UPS or Standard US Mail shipping based on your selection at the end of your <?php echo $this->html->link('checkout process', array('Cart::view')); ?>.
			Complete shipping details are available at <?php echo $this->html->link('shipping terms', array('Pages::shipping')); ?>.

			<p><strong>Returns:</strong> Totsy accept returns on selected items only. You will get a merchandise credit and free shipping (AK &amp; HI: air shipping rates apply). Simply be sure that we receive the merchandise you wish to return within 30 days from the date you originally received it in its original condition with all the packaging intact. Please note: Final Sale items cannot be returned. Want to learn more? Read more in our <?php echo $this->html->link('returns section', array('Pages::returns')); ?>.</p>
			</div>
		</div>
	<!--Disney -->
		<!-- @DG-2011.12.09
			- removed per Micah's request
			- consider replacing content-specific class name "disney" with semantic/functional i.e. "promo"
		
<div class="disney">
          <strong>SPECIAL BONUS!</strong><hr/></p>
       <p> Included with your purchase of $45 or more is a one-year subscription to <img src="/img/parents.png" align="absmiddle" width="95px" /> ( a $10 value )
       <span id="disney">Offer & Refund Details</span>
      </div>
		-->
	<br><!-- Started Related Products -->
	<div id="related-products">
		<?php $relatedData = $related; ?>
		<?php if (!empty($relatedData)): ?>
		<h2 style="color:#707070;font-size:14px;">You would also love</h2>
		<hr />
		<?php foreach ($related as $relatedItem) {
			if ($relatedItem['total_quantity'] >= 1){
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
			}
		} ?>
	<?php endif ?>
	</div>
	<!-- End Related Products -->
	</div>
	<div class="grid_3">
	<div id="detail-top-right" class="r-container">

		<div class="md-gray p-container roundy">
			<h2 class="caps" style="font-size:14px; padding-top:5px">Totsy Price</h2>
			<div style="padding: 10px 0px 0px 0px; color:#009900; font-size:24px;">$<?php echo number_format($item->sale_retail,2); ?></div>

			<div class="original-price" style="font-size:11px; padding-bottom:10px;">Original: $<?php echo number_format($item->msrp,2); ?></div>

<?php if (!empty($sizes)): ?>
				<?php if ( !((string)strtolower($sizes[0]) ==='no size')): ?>
						<select name="size-select" id="size-select">
									<option value="">Please Select Size</option>
							<?php foreach ($sizes as $value): ?>
									<option value="<?php echo $value?>"><?php echo $value?></option>
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
	</div>
	<div class="clear"></div>
		
	<?php
	
		$item_blurb = htmlspecialchars_decode($item->blurb);
		$item_blurb = html_entity_decode($item_blurb);
		$item_blurb = strip_tags($item_blurb); 
		$item_blurb = iconv("UTF-8", "ISO-8859-1//IGNORE", $item_blurb); 	
	
	?>
		
	<ul class="echoShare" data-productname='<?php echo $item->description ?>' data-productcap="" data-productdesc='<?php echo $item_blurb; ?>' data-imageclass="" data-producturl='<?php echo $_SERVER['HTTP_HOST'].'/image/' . $item->primary_image . 'jpg' ?>'></ul> 
	
	<div class="grid_3" style="padding-left:11px;">
		
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
	            success: function(data) {
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
cto_params["i"] = item_id;
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
