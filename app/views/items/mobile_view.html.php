<?=$this->html->script(array('cloud-zoom.1.0.2'));?>
<script type="text/javascript">
	var item_id = "<?=$item->_id?>";
</script>

<?=$this->html->script(array('cart-timer.js?v=007', 'cart-items-timer.js?v=007'));?>

<h2 style="font-size:12px;">
	<a href="/sales">Today's Sales</a> <span class="splitter">/</span> <a href="/sale/<?=$event->url?>"><?=$event->name?></a>	<div style="color:#009900; margin:-3px 0px 0px 0px; font-size:14px; float:right; text-align:right;">$<?=number_format($item->sale_retail,2); ?><br /><div class="original-price" style="font-size:9px; white-space:nowrap; color:#999; margin:0px 0 0 7px;">Original $<?=number_format($item->msrp,2); ?></div></div>
	<br />
	<div id="listingCountdown" class="listingCountdown" style="font-size:9px; color:#999;"></div> 
	<hr />
</h2>
	<h2 style="font-size:10px;margin-bottom:8px; color:#444;"><?=$item->description." ".$item->color; ?></h2>
<form action="" method="get" class="" style="border:0;">
<div class="item_intro_box">
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
    	<a href="/image/<?php echo $item->zoom_image; ?>.jpg" id="zoom1" class="cloud-zoom" rel="position: 'inside'" style="overflow:hidden; display:inline!important;">
    	<img src="/image/<?php echo $item->primary_image; ?>.jpg" alt="" border="0" title="" width="278"/></a>
  	</div>
</div>
</div>
<div class="clear"></div>
  	<!-- Start additional image view thumbnails -->
	<div class="zoom-desc" style="margin-top:6px;">
		<?php
			if (!empty($item->primary_image)) {
				echo $this->html->link(
				$this->html->image("/image/{$item->primary_image}.jpg", array(
					'class' => "zoom-tiny-image item_intro_box",
					'width' => "50",
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
						'class' => "zoom-tiny-image item_intro_box",
						'width' => "50",
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
		<?php endif ?>
		<?php if (!empty($item->alternate_images)): ?>
		    <?php $x = 2; ?>
		<?php endif ?>
	<!-- End product item -->
</div>
<div data-role="collapsible-set" data-theme="c" data-content-theme="d">
			<div data-role="collapsible" data-collapsed="false">
				<h3>Description</h3>
				<span class="blurb"><?php echo $item->blurb; ?></span>
			</div>
			<div data-role="collapsible">
				<h3>Shipping &amp; Returns</h3>
				<span class="blurb">
				<p><strong>Shipping:</strong> Totsy will ship this item via Standard UPS or Standard US Mail shipping based on your selection at the end of your <?=$this->html->link('checkout process', array('Cart::view')); ?>.
			Complete shipping details are available at <?=$this->html->link('shipping terms', array('Pages::shipping')); ?>.</p>
			</span>
			<span class="blurb">
			<p><strong>Returns:</strong> Totsy accept returns on selected items only. You will get a merchandise credit and free shipping (AK &amp; HI: air shipping rates apply). Simply be sure that we receive the merchandise you wish to return within 30 days from the date you originally received it in its original condition with all the packaging intact. Please note: Final Sale items cannot be returned. Want to learn more? Read more in our <?=$this->html->link('returns section', array('Pages::returns')); ?>.</p>
			</span>
			</div>
		<?php $relatedData = $related; ?>
				<?php if (!empty($relatedData)): ?>
				<div data-role="collapsible">
				<h3>You would also love</h3>
		
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
						"width" => "50")),
						"/sale/$event->url/".$relatedItem['url'], array(
							'id' => $relatedItem['description'],
							'escape'=> false
				));
			}
		} ?>
	
			</div>
			<?php endif ?>
		</div>
<?php
				if($item->miss_christmas){
				?>
				<p></p>
				<div class="holiday_message error_flash" style="text-align:center;">
				<p>NOTE: This item will ship AFTER xmas</p></div>
		<?php
				}
				?>
				<p></p>
<form id="callAjaxForm">

<?php if (!empty($sizes)): ?>
				<?php if ( !((string)strtolower($sizes[0]) ==='no size')): ?>
						<select name="size-select" id="size-select" data-theme="b" data-overlay-theme="d" data-native-menu="false">
									<option value="">Please Select Size</option>
							<?php foreach ($sizes as $value): ?>
									<option value="<?=$value?>"><?=$value?></option>
							<?php endforeach ?>
						</select>
						
						<hr />
				<?php endif ?>
			<?php endif ?>
			<?php if ($item->total_quantity >= 1): ?>
					
				<input type="button" value="Add to Cart" id="add-to-cart" class="button">
				</form>
				
				
				<div id="all-reserved"></div>
				
				<?php
				if($item->miss_christmas){
				?>
				<p></p>
				<div class="holiday_message">
				<p>This item is not guaranteed to be delivered on or before 12/25.*</p></div>
				<?php
				}
				else{
				?>
				<p></p>
				<div class="holiday_message">
				<p>This item will be delivered on or before 12/23*</p></div>
				
				
				<?php
				}
				?>
				
			<?php endif ?>

				<?php
				if($item->miss_christmas){
				?>
				<p></p>
				<div class="holiday_message">
				<p>* Totsy ships all items together. If you would like the designated items in your cart delivered on or before 12/23, please ensure that any items that are not guaranteed to ship on or before 12/25 are removed from your cart and purchased separately. Our delivery guarantee does not apply when transportation networks are affected by weather. Please contact our Customer Service department at 888-247-9444 or email <a href="mailto:support@totsy.com">support@totsy.com</a> with any questions. 
				<?php
				}
				else{
				?>
				
				<div class="holiday_message">
				<p>* Our delivery guarantee does not apply when transportation networks are affected by weather.
				
				<?php
				}
				?>
				
</div>
</div>

<script type="text/javascript">
$(function () {
	var saleEnd = new Date();
	saleEnd = new Date(<?php echo $event->end_date->sec * 1000?>);
	$('#listingCountdown').countdown({until: saleEnd, layout: 'Hurry Sale Ends in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
});
</script>


<script type="text/javascript">
$(document).ready(function() {
	var itemCheck = function(){
		var item_size = $('#size-select').attr('value');
		if(item_size != '') {
		    $.ajax({
	            url: '/items/available',
	            data: "item_id=" + item_id + "&" + "item_size=" + item_size,
	            context: document.body,
	            success: function(data){
	                if (data == 'false') {
	                    $('#all-reserved').show();
	                    $('#add-to-cart').hide();
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
    
    



     
        $(document).ready(function() {
            $(".button").click(function(){
               
                 if ($('#size-select option:selected').val() == "no size" ) {
                 var size = 'no%20size';
                 } else {
				var size = $('#size-select option:selected').val();
				 };
                $.ajax({
  url: "/cart/add?item_id=<?=$item->_id; ?>&item_size=" + size,
  	}).done(function() {
  $(location).attr('href','/cart/view')
});
 
                return false;
               
            });
        });
        </script>


