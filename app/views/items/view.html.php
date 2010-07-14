<?=$this->html->script(array('jquery-1.4.2', 'jquery.countdown.min'));?>
<?=$this->html->style('jquery.countdown');?>

<div id="product-detail-right">
	<div style="align:right" id="listingCountdown"></div>
	<p class="mar-10-b" style="padding-right:10px;text-align:right">
		
	</p>

	<div id="detail-top-left">

		<?=$this->html->image('manufacturers/logos/fp-logo.gif', array(
			'alt' => 'Logo ALT Tag', 'width' => "148", 'height' => "52"
		)); ?>
		<h1><?=$event->name; ?></h1>
		
		<div class="product-detail-attribute">
			<label for="size" class="required">Size<span>*</span></label>&nbsp;
			<select name="size" id="size-select">
				<option value="xsmall">x-small</option>
				<option value="small">small</option>
				<option value="medium">medium</option>
				<option value="large">large</option>
			</select>
		</div>
		
	
	</div>

	<div id="detail-top-right" class="r-container">

		<div class="tl"></div>
		<div class="tr"></div>
		<div class="md-gray p-container">
		
			<h2 class="caps"><?=$item->sale_retail; ?><br />Totsy Price</h2>
			
			<p class="caps">
				<strike><?=$item->msrp; ?><br />Original Price</strike>
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
	
	<div id="related-products">
		<h4>You would also love:</h4>
		<a href="#" title="Product Name">
			<?=$this->html->image('products/th/product-thumb-fpo.jpg', array(
				"class" => "img-th", "width" => "93", "height" => "93"
			)); ?>
		</a>
		<a href="#" title="Product Name">
			<?=$this->html->image('products/th/product-thumb-2-fpo.jpg', array(
				"class" => "img-th", "width" => "93", "height" => "93"
			)); ?>
		</a>
		<a href="#" title="Product Name">
			<?=$this->html->image('products/th/product-thumb-3-fpo.jpg', array(
				"class" => "img-th", "width" => "93", "height" => "93"
			)); ?>
		</a>
	</div>

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
		
			<?=$this->html->image("/image/{$event->images->preview_image}.jpg", array(
				"width" => "298", "height" => "300", "title" => $event->name, "alt" => $event->name
			)); ?>

		</div>
		<div class="bl"></div>
		<div class="br"></div>
	</div>
	<!-- End product item -->
	
	<!-- Start additional image view thumbnails -->
	<div id="thumbs">

		<?=$this->html->image('products/th/product-thumb-fpo.jpg', array('class' => "img-th", 'width' => "93", 'height' => "93")); ?>
		<?=$this->html->image('products/th/product-thumb-2-fpo.jpg', array('class' => "img-th active", 'width' => "93", 'height' => "93")); ?>
		<?=$this->html->image('products/th/product-thumb-3-fpo.jpg', array('class' => "img-th", 'width' => "93", 'height' => "93")); ?>

	</div>
	<!-- End additional image view thumbnails -->

</div>

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
