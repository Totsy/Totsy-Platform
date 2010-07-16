<?=$this->html->script(array('jquery-1.4.2', 'jquery.countdown.min'));?>
<?=$this->html->style('jquery.countdown');?>
<?=$this->form->create(null, array('url' => 'cart/add')); ?>
	

<div id="product-detail-right">
	<div style="align:right" id="listingCountdown"></div>
	<p class="mar-10-b" style="padding-right:10px;text-align:right">
		
	</p>

	<div id="detail-top-left">

		<?=$this->html->image('manufacturers/logos/fp-logo.gif', array(
			'alt' => 'Logo ALT Tag', 'width' => "148", 'height' => "52"
		)); ?>
		<h1><?=$item->description." ".$item->color; ?></h1>
		
		<div class="product-detail-attribute">
			<?php if (!empty($sizes)): ?>
			<label for="size" class="required">Size<span>*</span></label>&nbsp;
				<select name="item_size" id="size-select">
					<?php foreach ($sizes as $value): ?>
						<option value="<?=$value?>"><?=$value?></option>
					<?php endforeach ?>
				</select>
			<?php endif ?>
		</div>
	</div>
	<?=$this->form->hidden("item_id", array('value' => "$item->_id")); ?>
	<div id="detail-top-right" class="r-container">

		<div class="tl"></div>
		<div class="tr"></div>
		<div class="md-gray p-container">
		
			<h2 class="caps">$<?=number_format($item->sale_retail,2); ?><br />Totsy Price</h2>
			
			<p class="caps">
				<strike>$<?=number_format($item->msrp,2); ?><br />Original Price</strike>
			</p>
			<button class="flex-btn">Buy Now</button>
		</div>
		<div class="bl"></div>
		<div class="br"></div>
		
	</div>
	
	<div class="clear"><!-- --></div>
	
	<div class="sm-actions fr">
		<dl>
			<dt><strong>Share</strong></dt>
			<dd>
				<ul>
					<li><a href="http://facebook.com" title="Share this sale with your friends on Facebook" class="sm-facebook sm-btn">Share this sale on Facebook</a></li>
					<li><a href="http://twitter.com" title="Tweet this sale to your followers" class="sm-twitter sm-btn">Tweet this sale on Twitter</a></li>
					<li><a href="#" title="Email this sale to your friends" class="sm-email sm-btn">Email this sale to your friends</a></li>
				</ul>
			</dd>
		</dl>
	</div>
				
	<div id="tabs">
		<ul>
		    <li><a href="#description"><span>Description</span></a></li>
		    <li><a href="#shipping"><span>Shipping &amp; Returns</span></a></li>
		    <li><a href="#video"><span>Video</span></a></li>
		</ul>
		
		<!-- Start Description Tab -->
		<div id="description" class="ui-tabs-hide">
		    <h3>Overview</h3>
			<?php echo $event->blurb; ?>
		</div>
		<!-- End Description Tab -->
		
		<!-- Start Shipping Tab -->
		<div id="shipping" class="ui-tabs-hide">
		    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
		    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
		</div>
		<!-- End Shipping Tab -->
		
		<!-- Start Video Tab -->
		<div id="video" class="ui-tabs-hide">
		    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
		    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
		    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
		    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
		</div>
		<!-- End Video Tab -->
		
	</div>
	<!-- Started Related Products -->
	<div id="related-products">
		<?php
			if (!empty($related)) {
				foreach ($related as $relatedItem) {
					if (empty($relatedItem->primary_images)) {
						$relatedImage = '/img/no-image-small.jpeg';
					} else {
						$relatedImage = "/image/{$relatedItem->primary_images[0]}.jpg";
					}
					echo $this->html->link(
						$this->html->image("$relatedImage", array(
							"class" => "img-th",
							"width" => "93",
							"height" => "93")),
							"/items/view/$relatedItem->url", array(
								'id' => 
								"$relatedItem->name", 
								'escape'=> false
					));
				}
			}
		?>
		
	</div>
	<!-- End Related Products -->
</div>

<div id="product-detail-left">

	<p class="mar-10-b" style="padding-left:10px">
		<?=$this->html->link('< Click here for sale page', array('Events::view', 'args' => "$event->url")); ?>
	</p>

	<!-- Start product item -->
	<div class="r-container">
		<div class="tl"></div>
		<div class="tr"></div>
		<div class="md-gray p-container">
			<?php
				if (!empty($item->primary_images)) {
					echo $this->html->image("/image/{$item->primary_images[0]}.jpg", array(
						"width" => "298", "height" => "300", "title" => $event->name, "alt" => $event->name
					));
				} else {
					echo $this->html->image('/img/no-image-small.jpeg', array(
						'alt' => 'Totsy'), array(
							'title' => "No Image Available", 
							'width' => "298", 
							'height'=> "300"
							)); 
				}
			?>
		</div>
		<div class="bl"></div>
		<div class="br"></div>
	</div>
	<!-- End product item -->
	
	<!-- Start additional image view thumbnails -->
	<div id="thumbs">
		<?php
			if (!empty($item->primary_images)) {
				echo $this->html->image("/image/{$item->primary_images[0]}.jpg", array(
					'class' => "img-th", 
					'width' => "93", 
					'height' => "93"));
			}
		?>
		<?php if (!empty($item->secondary_images)): ?>
			<?php foreach ($item->secondary_images as $value): ?>
						<?=$this->html->image("/image/{$value}.jpg", array('class' => "img-th", 'width' => "93", 'height' => "93")); ?>
			<?php endforeach ?>
		<?php endif ?>
	</div>
	<!-- End additional image view thumbnails -->

</div>
<?=$this->form->end(); ?>
<?=$this->html->script(array('jquery.equalheights', 'jquery-ui-1.8.2.custom.min')); ?>

<script type="text/javascript">
	$(document).ready(function() { $("#tabs").tabs(); });
</script>

<script type="text/javascript"> 
$(function () {
	var saleEnd = new Date();
	saleEnd = new Date(<?php echo $event->end_date->sec * 1000?>);
	$('#listingCountdown').countdown({until: saleEnd, layout:'SALE ENDS in {dn} {dl} {hn} {hl} and {mn} {ml}'});
});
</script>
