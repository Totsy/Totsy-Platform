<?php
	$this->title($event->name);
?>
<?=$this->html->script('jquery.countdown.min');?>
<?=$this->html->style('jquery.countdown');?>

	<div id="middle" class="fullwidth">				
		
		<div id="listingCountdown" class="listingCountdown"></div>
		
		<h1 class="page-title gray"><span class="red"><?=$type?> Sales /</span> <?=$event->name; ?></h1>
	
		<div class="sm-actions fr">
			<dl>
				<dt><strong>Share</strong></dt>
				<dd>
					<ul>
						<li><a href="http://www.facebook.com/sharer.php?u=<?=urlencode($shareurl);?>&t=<?=urlencode('Checking out the '.$event->name.' event on Totsy.com');?>" target="_blank" title="Share this sale with your friends on Facebook" class="sm-facebook sm-btn">Share this sale on Facebook</a></li>
						<li><a href="http://twitter.com/home?status=Checking out the <?=$event->name; ?> event at Totsy.com: <?=$shareurl;?>" target="_blank" title="Tweet this sale to your followers" class="sm-twitter sm-btn">Tweet this sale on Twitter</a></li>
					</ul>
				</dd>
			</dl>
		</div>
		<div class="r-container clear">
			<div class="tl"></div>
			<div class="tr"></div>
			<div id="page-header" class="md-gray">
				<div style="float:left; display:block; width:300px;">
					<!-- Display Event Image -->
					<?php
						if (!empty($event->images->event_image)) {
							echo $this->html->image("/image/{$event->images->event_image}.jpg", array(
								'alt' => $event->name), array(
								'title' => $event->name, 
								'width' => "169", 
								'height'=> "193", 
								'style' => 'border:4px solid #fff;'
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
				<div style="float:left; display:block; width:590px; margin-left:5px; line-height:22px; text-align:justify;">
					<div style="width:300px;">
						<!-- Display Logo Image -->
						<?php if (!empty($event->images->logo_image)): ?>
							<img src="/image/<?=$event->images->logo_image?>.gif" alt="<?= $event->name; ?>" title="<?= $event->name; ?>" width="148" height="52" />
						<?php endif ?>
						<div class="title table-cell v-bottom">
							<!--  h1> <? //=$event->name; ?> </h1 -->
							<div id="listingCountdown"></div>
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
			<div>
			<!-- div class="sort-by" -->
			<!-- select id="by-category" name="by-category">
				<option value="">View By Category</option>
				<option value="Strollers">Strollers</option>
				<option value="Accessories">Accessories</option>
			</select>
			
			<select id="by-size" name="by-size">
				<option value="">View By Size</option>
				<option value="Small">Small</option>
				<option value="Medium">Medium</option>
				<option value="Large">Large</option>
			</select -->
		<br>
		</div>
		<?php if (!empty($items)): ?>
			<?php $y = 0; ?>
			<?php foreach ($items as $item): ?>
				<?php
					if (!empty($item->primary_image)) {
						$image = $item->primary_image;
						$productImage = "/image/$image.jpg";
					} else {
						$productImage = "/img/no-image-small.jpeg";
					}
				?>
				<!-- Start the product loop to output all products in this view -->
				<!-- Start product item -->
					<?php if (($y == 0) || ($y == 2)): ?>
						<div class="product-list-item r-container">
					<?php endif ?>
					<?php if ($y == 1): ?>
						<div class="product-list-item middle r-container">
					<?php endif ?>
					<?php if ($y == 2): ?>
						<?php $y = -1; ?>
					<?php endif ?>
					<div class="tl"></div>
					<div class="tr"></div>
					<div class="md-gray p-container">
						<?php if ($item->total_quantity == 0): ?>
								<?=$this->html->image('/img/soldout.gif', array(
									'title' => "Sold Out",
									'style' => 'z-index : 2; position : absolute; left:20%'
								)); ?>
						<?php endif ?>
						<?=$this->html->link(
							$this->html->image($productImage, array(
								'alt' => $item->name,
								'title' => $item->name,
								'width' => '298',
								'height' => '300')),
							"sale/$event->url/{$item->url}",
							array('title' => $item->name, 'escape' => false)
						); ?>
						<div class="details table-row">
							<div class="table-cell left">
								<table width="280">
									<tr>
										<td width="170" valign="top">
											<a href="<?="/sale/$event->url/$item->url"?>"><h2><?=$item->description ?></h2></a>
										</td>
										<td align="right">
											<font class="price">$<?=number_format($item->sale_retail,2);?></font><br>
											<font class="original-price">Original $<?=number_format($item->msrp,2);?></font>
										</td>
								</table>
							</div>
						</div>
					</div>
					<div class="bl"></div>
					<div class="br"></div>
				</div>
				<?php $y++ ?>
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
	$('#listingCountdown').countdown({until: saleEnd, layout: 'Closes in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
});
</script>
