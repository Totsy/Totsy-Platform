<?=$this->html->script('jquery.countdown.min');?>
<?=$this->html->style('jquery.countdown');?>

	<div id="middle" class="fullwidth">				
		
		<h1 class="page-title gray"><span class="red">Today's <span class="bold caps">Sales</span> /</span><?=$event->name; ?></h1>
		
		<div class="sm-actions fr">
			<dl>
				<dt><strong>Share</strong></dt>
				<dd>
					<ul>
						<li><a href="http://facebook.com" title="Share this sale with your friends on Facebook" class="sm-facebook sm-btn">Share this sale on Facebook</a></li>
						<li><a href="http://twitter.com/home?status=Checking out the SALE event at Totsy.com: URL" title="Tweet this sale to your followers" class="sm-twitter sm-btn">Tweet this sale on Twitter</a></li>
					</ul>
				</dd>
			</dl>
		</div>
		<div class="r-container clear">
			<div class="tl"></div>
			<div class="tr"></div>
			<div id="page-header" class="md-gray">
				<div class="left">
					<!-- Display Event Image -->
					<?php
						if (!empty($event->images->event_image)) {
							echo $this->html->image("/image/{$event->images->event_image}.jpg", array(
								'alt' => 'altText'), array(
								'title' => "Image ALT Tag", 
								'width' => "169", 
								'height'=> "193"
							));
						} else {
							echo $this->html->image('/img/no-image-small.jpeg', array(
								'alt' => 'Totsy'), array(
									'title' => "No Image Available", 
									'width' => "169", 
									'height'=> "193"
									)); 
						}
					?>
				</div>
				<div class="right">
					<div class="details table-row">
						<!-- Display Logo Image -->
						<?php if (!empty($event->images->logo_image)): ?>
							<img src="/image/<?=$event->images->logo_image?>.gif" alt="Logo ALT Tag" title="Logo ALT Tag" width="148" height="52" />
						<?php endif ?>
						<div class="title table-cell v-bottom">
							<h1><?=$event->name?></h1>
							<strong class="red"><div id="listingCountdown"></div></strong>

						</div>
					</div>
					<p><?php if (!empty($event->blurb)): ?>
						<?php echo $event->blurb ?>
					<?php endif ?><p>
				</div>

			</div>
			<div class="bl"></div>
			<div class="br"></div>
		</div>
		
		
		
		<!--
			<div class="sort-by">
			<select id="by-category" name="by-category">
				<option value="">View By Category</option>
				<option value="Strollers">Strollers</option>
				<option value="Accessories">Accessories</option>
			</select>
			
			<select id="by-size" name="by-size">
				<option value="">View By Size</option>
				<option value="Small">Small</option>
				<option value="Medium">Medium</option>
				<option value="Large">Large</option>
			</select>
		-->
		</div>
		<?php if (!empty($items)): ?>
			<?php foreach ($items as $item): ?>
				<?php
					if (!empty($item->primary_images)) {
						$image = $item->primary_images[0];
						$productImage = "/image/$image.jpg";
					} else {
						$productImage = "/img/no-image-small.jpeg";
					}
				?>
				<!-- Start the product loop to output all products in this view -->
				<!-- Start product item -->
				<div class="product-list-item r-container">
					<div class="tl"></div>
					<div class="tr"></div>
					<div class="md-gray p-container">
						<img src="<?php echo "$productImage"; ?>" alt="<?=$item->name?>" title="<?=$item->name?>" width="298" height="300"/>
						<div class="details table-row">
							<div class="table-cell left">
								<h2><?=$item->name?></h2>
								<strong class="price">$<?=number_format($item->sale_retail,2);?> Totsy Price</strong><br />
								<strike>$<?=number_format($item->msrp,2);?> Original Price</strike>
							</div>
							<div class="table-cell right">
								<?=$this->html->link('View Now', array('Items::view', 'args' => "$item->url"), array('class' => 'flex-btn')); ?>
							</div>
						</div>

					</div>
					<div class="bl"></div>
					<div class="br"></div>
				</div>
				<!-- End product item -->
			<?php endforeach ?>
		<?php endif ?>
		
	</div>
</div>
</div>
<script type="text/javascript"> 
$(function () {
	var saleEnd = new Date();
	saleEnd = new Date(<?php echo $event->end_date->sec * 1000?>);
	$('#listingCountdown').countdown({until: saleEnd, layout:'SALE ENDS in {dn} {dl} {hn} {hl} and {mn} {ml}'});
});
</script>