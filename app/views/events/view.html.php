<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery.countdown.min');?>
<?=$this->html->style('jquery.countdown');?>

	<div id="middle" class="fullwidth">				
		
		<h1 class="page-title gray"><span class="red">Today's <span class="bold caps">Sales</span> /</span> Troller Roller</h1>
		
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
			$('#splashCountdown').countdown({until: saleEnd, compact: true, description: ''});
		});
		</script>

		<?php
			if(!empty($event)) {
				$banner_image = (empty($event->images)) ? null : $event->images->banner_image;
				$logo_image = (empty($event->images)) ? null : $event->images->logo_image;
				$preview_image = (empty($event->images)) ? null : $event->images->preview_image;
				$blurb = $event->blurb;
			} 
		?>

		<div class="r-container clear">
			<div class="tl"></div>
			<div class="tr"></div>
			<div id="page-header" class="md-gray">

				<div class="left">

					<img src="/image/<?php echo $banner_image?>.jpg" alt="Image ALT Tag" title="Image ALT Tag" width="169" height="193" />

				</div>

				<div class="right">
					<div class="details table-row">
						<img src="/image/<?=$logo_image?>.gif" alt="Logo ALT Tag" title="Logo ALT Tag" width="148" height="52" />
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
		
		<!-- Start the product loop to output all products in this view -->
		<!-- Start product item -->
		<div class="product-list-item r-container">
			<div class="tl"></div>
			<div class="tr"></div>
			<div class="md-gray p-container">
			
				<img src="../img/products/images/stroller-1-fpo.jpg" width="298" height="300" title="Stroller Title" alt="Stroller Alt Text" />
				
				<div class="details table-row">
					<div class="table-cell left">
						<h2>Stroller Name</h2>
						<strong class="price">$350 Totsy Price</strong><br />
						<strike>$550 Original Price</strike>
					</div>
					
					<div class="table-cell right">
						<a href="#" title="View Stroller Name Now" class="flex-btn"><span>View Now</span></a>
					</div>
				</div>
					
			</div>
			<div class="bl"></div>
			<div class="br"></div>
		</div>
		<!-- End product item -->
	
	
	</div>
	
</div>

</div>