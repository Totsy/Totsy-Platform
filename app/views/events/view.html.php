<?=$this->html->script('jquery-1.4.2');?>
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
						<li><a href="http://twitter.com" title="Tweet this sale to your followers" class="sm-twitter sm-btn">Tweet this sale on Twitter</a></li>
						<li><a href="#" title="Email this sale to your friends" class="sm-email sm-btn">Email this sale to your friends</a></li>
					</ul>
				</dd>
			</dl>
		</div>
		
		


		<script type="text/javascript"> 
		$(function () {
			var saleEnd = new Date();
			saleEnd = new Date(<?php echo $event->end_date->sec * 1000?>);
			$('#listingCountdown').countdown({until: saleEnd, format:'dHM'});
		});
		</script>

		<?php
			if(!empty($event)) {
				$banner = (empty($event->images)) ? null : $event->images->banner_image;
				$logo = (empty($event->images)) ? null : $event->images->logo_image;
				$blurb = $event->blurb;
			} 
		?>

		<div class="r-container clear">
			<div class="tl"></div>
			<div class="tr"></div>
			<div id="page-header" class="md-gray">

				<div class="left">
					<?php
						if (!empty($banner)) {
							echo $this->html->image("/image/$banner.jpg", array(
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
						<?php if (!empty($logo)): ?>
							<img src="/image/<?=$logo?>.gif" alt="Logo ALT Tag" title="Logo ALT Tag" width="148" height="52" />
						<?php endif ?>
						<div class="title table-cell v-bottom">
							<h1><?=$event->name?></h1>
							<strong class="red">SALE ENDS in <div id="listingCountdown"></div></strong>

						</div>
					</div>
					<p><?php echo $blurb; ?><p>
				</div>

			</div>
			<div class="bl"></div>
			<div class="br"></div>
		</div>
		
		
		
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
								<strong class="price"><?=$item->sale_retail;?> Totsy Price</strong><br />
								<strike><?=$item->msrp;?> Original Price</strike>
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