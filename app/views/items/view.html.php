<?=$this->html->script(array('jqzoom.pack.1.0.1','jquery.equalheights'));?>
<?=$this->html->style('jquery.countdown');?>

<div id="product-detail-right">
	
	<div id="product-detail-right-top">
		
		<div id="listingCountdown" class="listingCountdown"></div>
		<?php $logo = $event->images->logo_image;?>
		<?=$this->html->image("/image/$logo.jpg", array(
			'alt' => $event->name, 'width' => "148", 'height' => "52"
		)); ?>
		
	</div>
	
	<div id="detail-top-left">
		<h1><strong><?=$event->name?></strong> <?=$item->description." ".$item->color; ?></h1>

		<div class="product-detail-attribute">

			<?php if (!empty($sizes)): ?>
				<?php if ( !($sizes[0] =='no size')): ?>
					<label for="size" class="required">Size<span>*</span></label>&nbsp;
						<select name="item_size" id="size-select">
							<?php foreach ($sizes as $value): ?>
									<option value="<?=$value?>"><?=$value?></option>
							<?php endforeach ?>
						</select>
				<?php endif ?>
			<?php endif ?>
		</div>
	</div>
	<?=$this->form->hidden("item_id", array('value' => "$item->_id", 'id'=>'item_id')); ?>

	<div id="detail-top-right" class="r-container">
   
		<div class="tl"></div>
		<div class="tr"></div>
		<div class="md-gray p-container">

			<h2 class="caps">$<?=number_format($item->sale_retail,2); ?><br />Totsy Price</h2>

			<p class="caps">
				$<?=number_format($item->msrp,2); ?><br />Original</p>
			<?php if ($item->total_quantity != 0): ?>
				<button class="buy-now" id='item-submit'>Add To Cart</button>
				<div id="all-reserved"></div>
			<?php endif ?>
		</div>
		<div class="bl"></div>
		<div class="br"></div>

	</div>

	<div class="clear"><!-- --></div>
	
	<div class="product-bottom-wrapper">
	
		<div class="sm-actions fr">
			<dl>
				<dt><strong>Share</strong></dt>
				<dd>
					<ul>
						<li><a href="http://www.facebook.com/sharer.php?u=<?=urlencode($shareurl);?>&t=<?=urlencode('Checking out the  '.$item->description.' on Totsy.com');?>" target="_blank" title="Share this item with your friends on Facebook" class="sm-facebook sm-btn">Share this sale on Facebook</a></li>
						<li><a href="http://twitter.com/home?status=Checking out the <?=$item->description; ?> at Totsy.com: <?=$shareurl;?>" target="_blank" title="Tweet this sale to your followers" class="sm-twitter sm-btn">Tweet this sale on Twitter</a></li>
					</ul>
				</dd>
			</dl>
		</div>
			
		<div id="tabs">
			<ul>
			    <li><a href="#description"><span>Description</span></a></li>
			    <li><a href="#shipping"><span>Shipping &amp; Returns</span></a></li>
			    <!--<li><a href="#video"><span>Video</span></a></li>-->
			</ul>
	
			<!-- Start Description Tab -->
			<div id="description" class="ui-tabs-hide">
			    <h3>Overview</h3>
				<?php echo $item->blurb; ?>
			</div>
			<!-- End Description Tab -->
	
			<!-- Start Shipping Tab -->
			<div id="shipping" class="ui-tabs-hide">
			
			<strong>Shipping:</strong>Totsy will ship this item via Standard UPS or Standard US Mail shipping based on your selection at the end of your <?=$this->html->link('checkout process', array('Orders::add')); ?>.
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
	
	</div>
	<!-- Started Related Products -->
	<div id="related-products">
	<?php if (!empty($related)): ?>
		<h2>You would also love:</h2><br>
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
						"width" => "93",
						"height" => "93")),
						"/items/view/$relatedItem->url", array(
							'id' => "$relatedItem->name",
							'escape'=> false
				));
			?>
		<?php endforeach ?>
	<?php endif ?>
	</div>
	<!-- End Related Products -->
</div>

<div id="product-detail-left">

	<!-- Start product item -->
	<div class="r-container">
		<div class="tl"></div>
		<div class="tr"></div>
		<div class="md-gray p-container loading" id="img-container">
			<?php if ($item->total_quantity <= 0): ?>
					<?=$this->html->image('/img/soldout.gif', array(
						'title' => "Sold Out",
						'style' => 'z-index : 2; position : absolute; left:20%'
					)); ?>
			<?php endif ?>
				<?php if (!empty($item->primary_image)): ?>
					<div class="zoom-container show" id="full_img_1">
				<?php echo $this->html->link($this->html->image("/image/{$item->primary_image}.jpg", array(
						"width" => "298", 
						"height" => "300", 
						"title" => $item->description, 
						"alt" => $item->description)),
						"/image/{$item->zoom_image}.jpg",
						array('escape' => false, 'class' => 'zoom')); 
				?>
					</div>
				<?php endif ?>
				<?php if (!empty($item->alternate_images)): ?>
					<?php $x = 2; ?>
					<?php foreach ($item->alternate_images as $value): ?>
						<div class="zoom-container" id="full_img_<?=$x?>">
							<?php echo $this->html->link($this->html->image("/image/{$value}.jpg", array(
									"width" => "298", 
									"height" => "300", 
									"title" => $item->description, 
									"alt" => $item->description)),
									"image/$value.jpg",
									array('escape' => false));
							?>
							<?php $x++; ?>	
						</div>
					<?php endforeach ?>
				<?php endif ?>
		</div>
		<div class="bl"></div>
		<div class="br"></div>
	</div>
	<!-- End product item -->

	<!-- Start additional image view thumbnails -->
	<div id="thumbs">
		<?php
			if (!empty($item->primary_image)) {
				echo $this->html->image("/image/{$item->primary_image}.jpg", array(
					'class' => "img-th active", 
					'width' => "93", 
					'height' => "93",
					'alt' => "product-thumb-fpo",
					'rel' => "full_img_1"));
			}
		?>
		<?php if (!empty($item->alternate_images)): ?>
			<?php $x = 2; ?>
			<?php foreach ($item->alternate_images as $value): ?>
					<?=$this->html->image("/image/{$value}.jpg", array(
						'class' => "img-th", 
						'width' => "93", 
						'height' => "93",
						'rel' => "full_img_$x",
						'alt' => "product-thumb-fpo"
						)); 
					?>
					<?php $x++; ?>	
			<?php endforeach ?>
		<?php endif ?>
	</div>
	<!-- End additional image view thumbnails -->

</div>


<script type="text/javascript"> 
$(function () {
	var saleEnd = new Date();
	saleEnd = new Date(<?php echo $event->end_date->sec * 1000?>);
	$('#listingCountdown').countdown({until: saleEnd, layout: 'Closes in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
});
</script>
<script type="text/javascript">
$(document).ready(function() {

	//create tabs
	$("#tabs").tabs();

	//options of product zoom
	var options = {
		zoomWidth: 400,
		zoomHeight: 300,
		title: false,
		zoomType: 'reverse'
	};

	//intanciate zoom
	$('.zoom').jqzoom( options );

	//make product thumbnails do something
	$('.img-th').click(function(){
	
		$('.img-th').removeClass('active');
	
		$(this).addClass('active');
	
		var lg = $(this).attr('rel');
	
		$('.zoom-container').removeClass('show');
		$('#'+lg).addClass('show');

	});

});
</script>
<script type="text/javascript">

$("#item-submit").click(function(){
var item_id = $('#item_id').attr('value'); 
var item_size = $('#size-select').attr('value');

$.ajax({
	url: $.base + 'cart/add',
	data: "item_id=" + item_id + "&" + "item_size=" + item_size,
	context: document.body,
	success: function(){
		$("#cart-modal").load($.base + 'cart/view').dialog({
			autoOpen: false,
			modal:true,
			width: 900,
			//height: 600,
			overlay: {opacity: 0.5, background: "black"},
			close: function(ev, ui) { location.reload(true); }
		});
		$("#cart-modal").dialog('open');
     }
});
});

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
					$('#item-submit').hide();
					$('#all-reserved').html("<p class=\"flex-btn\">All items are reserved <br>Check back in two minutes</p>");
				} else {
					$('#item-submit').show();
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
