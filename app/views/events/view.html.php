<?php
	$this->title($event->name);
?>
<?=$this->html->script('jquery.countdown.min');?>
<?=$this->html->style('jquery.countdown');?>
	
	<div id="middle" class="fullwidth">
		<h1 class="page-title gray"><span class="red"><a href="/" title="Sales"><?=$type?> Sales</a> /</span> <?=$event->name; ?></h1>
		<hr />

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
				<div id="listingCountdown" class="listingCountdown"></div>
				<div style="clear:both;"></div><div class="sm-actions fr">
			<dl>
				<dd>
					<?php echo $spinback_fb; ?>
				</dd>
			</dl>
		</div>

					<div style="width:300px;">
						<!-- Display Logo Image -->
						<?php if (!empty($event->images->logo_image)): ?>
							<img src="/image/<?=$event->images->logo_image?>.gif" alt="<?= $event->name; ?>" title="<?= $event->name; ?>" width="148" height="52" />
						<?php endif ?>
						<div class="title table-cell v-bottom">
							<!--  h1> <? //=$event->name; ?> </h1 -->


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
		<br />
		<?php if(!empty($filters)): ?>
		<div id='filterb' style='text-align:right'>
			<?=$this->form->create(null, array('id' => 'filterform')); ?>
			<?=$this->form->label("filterby", "Display by:", array('style' => 'font-weight:bold; font-size:13px;')); ?>
			<?=$this->form->select('filterby',$filters, array('onchange' => "filter()", 'id' => 'filterby', 'style' => 'width:120px;', 'value' => array($departments => $departments))); ?>
			<?=$this->form->end(); ?>
		</div>
		<?php endif ?>
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
						<?php if ($item->total_quantity <= 0): ?>
								<?=$this->html->image('/img/soldout.png', array(
									'title' => "Sold Out",
									'style' => 'z-index : 2; position : absolute; left:69%; margin:10px;'
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
	var saleStart = new Date();
	var saleEnd = new Date();
	var now = new Date();
	saleStart = new Date(<?php echo $event->start_date->sec * 1000?>);
	saleEnd = new Date(<?php echo $event->end_date->sec * 1000?>);
	if((now.getTime()) < <?php echo $event->start_date->sec * 1000 ?>) {
		var diff = <?php echo $event->start_date->sec * 1000 ?> - (now.getTime());
		if((diff / 1000) < (24 * 60 * 60) ) {
			$('#listingCountdown').countdown({until: saleStart, layout: 'Opens in {hnn}{sep}{mnn}{sep}{snn}'});
		} else {
			$('#listingCountdown').countdown({until: saleStart, layout: 'Opens in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
		}
	} else {
			var diff = <?php echo $event->end_date->sec * 1000 ?> - (now.getTime());
			if((diff / 1000) < (24 * 60 * 60) ) {
				$('#listingCountdown').countdown({until: saleEnd, layout: 'Closes in {hnn}{sep}{mnn}{sep}{snn}'});
			} else {
				$('#listingCountdown').countdown({until: saleEnd, layout: 'Closes in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
			}
	}
});
function filter() {
	var filter = $('#filterby').val();
	_gaq.push(['_setAccount', 'UA-675412-15']);
	_gaq.push(['_trackPageview']);
	_gaq.push(['_trackEvent', 'departments', 'dropdown', filter]);
	$('#filterform').submit();
};
</script>
