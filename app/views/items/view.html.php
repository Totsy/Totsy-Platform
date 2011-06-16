<?=$this->html->script(array('jqzoom.pack.1.0.1','jquery.equalheights', 'cloud-zoom.1.0.2'));?>
<?=$this->html->style('jquery.countdown');?>
<div class="grid_16">
	<h2 class="page-title gray"><span class="red"><a href="/" title="Sales">Today's Sales</a> /</span> <a href="/sale/<?=$event->url?>" title="<?=$event->name?>"><?=$event->name?></a><div id="listingCountdown" class="listingCountdown" style="float:right;"></div></h2>
	<hr />
</div>

<div class="grid_6">
	<!-- Start product item -->
		<?php if ($item->total_quantity <= 0): ?>
					<?=$this->html->image('/img/soldout.png', array(
						'title' => "Sold Out",
						'style' => 'z-index : 2; position : absolute; right:0;'
					)); ?>
			<?php endif ?>
				<?php if (!empty($item->primary_image)): ?>

<div class="zoom-section">
	<div class="zoom-small-image">
    	<a href="/image/<?php echo $item->zoom_image; ?>.jpg" id="zoom1" class="cloud-zoom" rel="position: 'inside'">
    	<img src="/image/<?php echo $item->primary_image; ?>.jpg" alt="" border="0" title="" width="340"/></a>
  	</div>

  	<!-- Start additional image view thumbnails -->
	<div class="zoom-desc" style="margin-top:10px;">
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
<?=$this->form->create(null, array('url' => 'Cart::add')); ?>
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
			<strong>Shipping:</strong> Totsy will ship this item via Standard UPS or Standard US Mail shipping based on your selection at the end of your <?=$this->html->link('checkout process', array('Orders::add')); ?>.
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
		<?php $relatedData = $related->data(); ?>
		<?php if (!empty($relatedData)): ?>
		<h2 style="color:#707070;font-size:14px;">You would also love</h2>
		<hr />
		<?php foreach ($related as $relatedItem): ?>
			<?php
				if (empty($relatedItem->primary_image)) {
					$relatedImage = '/img/no-image-small.jpeg';
				} else {
					$relatedImage = "/image/{$relatedItem->primary_image}.jpg";
				}
				echo $this->html->link(
					$this->html->image("$relatedImage", array(
						"class" => "img-th",
						"style" => "border-radius:12px; overflow:hidden; -moz-border-radius: 12px; -webkit-border-radius:12px;",
						"width" => "75",
						"height" => "75")),
						"/sale/$event->url/$relatedItem->url", array(
							'id' => "$relatedItem->name",
							'escape'=> false
				));
			?>
		<?php endforeach ?>
	<?php endif ?>
	</div>
	<!-- End Related Products -->

	</div>
	
	
	<div class="grid_3"><?=$this->form->hidden("item_id", array('value' => "$item->_id", 'id'=>'item_id')); ?>

	<div id="detail-top-right" class="r-container">


		<div class="md-gray p-container roundy">

    <div id="radio">
      <input type="radio" id="radio1" name="radio" />
      <label for="radio1">Small</label>
      <input type="radio" id="radio2" name="radio"/>
      <label for="radio2">Medium</label>
    </div>
<?php if (!empty($sizes)): ?>
				<?php if ( !((string)$sizes[0] ==='no size')): ?>
						<select name="item_size" id="size-select">
									<option value="">Please Select Size</option>
							<?php foreach ($sizes as $value): ?>
									<option value="<?=$value?>"><?=$value?></option>
							<?php endforeach ?>
						</select>
						<hr />
				<?php endif ?>
			<?php endif ?>
			<h2 class="caps" style="font-size:14px; padding-top:5px">Totsy Price</h2>
			<div style="padding: 10px 0px 0px 0px; color:#009900; font-size:24px;">$<?=number_format($item->sale_retail,2); ?></div>

			<div class="original-price" style="font-size:11px; padding-bottom:10px;">Original: $<?=number_format($item->msrp,2); ?></div>
			<?php if ($item->total_quantity >= 1): ?>
				<div id="hidden-div" style="display:none; color:#eb132c; font-weight:bold;">Please Select Size!</div>
				<?=$this->form->submit('Add To Cart', array('class' => 'button')); ?>
				<div id="all-reserved"></div>
			<?php endif ?>
		</div>
		
		<?php $logo = $event->images->logo_image;?>
		<div style="padding:0px 0px 0px 13px;">
		<?=$this->html->image("/image/$logo.jpg", array(
			'alt' => $event->name, 'width' => "148"
		)); ?>
 		</div>
	</div>
<?=$this->form->end(); ?>

		<div style="padding:10px 0px; text-align:center;">
			<?php echo $spinback_fb; ?>
		</div>
		
		</div>
	<div class="clear"></div>
	
	
	
	</div>
<div id="modal" style="background:#fff!important; z-index:9999999999;"></div>
<script type="text/javascript">
$(function () {
	var saleEnd = new Date();
	saleEnd = new Date(<?php echo $event->end_date->sec * 1000?>);
	$('#listingCountdown').countdown({until: saleEnd, layout: 'Ends in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
});
</script>
<script type="text/javascript">
$(document).ready(function() {
	//create tabs
	$("#tabs").tabs();
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
            close: function(ev, ui) {}
        });
        $('#modal').dialog('open');
    });
</script>
<script type="text/javascript">
$(document).ready(function() {
	var itemCheck = function(){
		var item_id = $('#item_id').attr('value');
		var item_size = $('#size-select').attr('value');
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
        var yesFound = false;
        $("select").each(function(index, element) {
          if ( $(element).val() == "" ) {
            yesFound = true;
          }
        });
        
        if (yesFound) {
          $("#hidden-div").show();
          $("input[type=Submit]").attr("disabled","disabled");
        } else {
          $("#hidden-div").hide();
          $("input[type=Submit]").removeAttr("disabled");
        };
      }
    });
  </script> 
