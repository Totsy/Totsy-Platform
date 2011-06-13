<?php $this->title($event->name); ?>
<?=$this->html->script('jquery.countdown.min');?>
<?=$this->html->style('jquery.countdown');?>

<div class="grid_16">
		<h2 class="page-title gray"><span class="red"><a href="/" title="Sales"><?=$type?> Sales</a> /</span> <?=$event->name; ?> <div id="listingCountdown" class="listingCountdown" style="float:right;"></div></h2>
		<hr />
<div class="md-gray roundy" style="overflow:hidden;">
				<div class="grid_5 alpha omega">
					<!-- Display Event Image -->
					<?php
						if (!empty($event->images->event_image)) {
							echo $this->html->image("/image/{$event->images->event_image}.jpg", array(
								'title' => $event->name,
								'width' => "280",
							));
						} else {
							echo $this->html->image('/img/no-image-small.jpeg', array(
									'title' => "No Image Available",
									'width' => "280",
									));
						}
					?>
				</div>
				<div class="grid_11 omega">
				<?php echo $spinback_fb; ?>
					<div class="grid_3 alpha omega">
						<!-- Display Logo Image -->
						<?php if (!empty($event->images->logo_image)): ?>
							<img src="/image/<?=$event->images->logo_image?>.gif" alt="<?= $event->name; ?>" title="<?= $event->name; ?>" width="148" height="52" />
						<?php endif ?>
					</div>
					
					<div class="grid_11 alpha omega">
					<?php if (!empty($event->blurb)): ?>
						<?php echo $event->blurb ?>
					<?php endif ?>
					</div>
</div>

			</div>
		</div>
		<br />
		<?php if(!empty($filters)): ?>
		<div id='filterb' style='text-align:right'>
			<?=$this->form->create(null, array('id' => 'filterform')); ?>
			<?=$this->form->label("filterby", "View by:", array('style' => 'font-weight:bold; font-size:13px;')); ?>
			<?=$this->form->select('filterby',$filters, array('onchange' => "filter()", 'id' => 'filterby', 'value' => array($departments => $departments))); ?>
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
						<div class="grid_4">
					<?php endif ?>
					<?php if ($y == 1): ?>
						<div class="grid_4">
					<?php endif ?>
					<?php if ($y == 2): ?>
						<?php $y = -1; ?>
					<?php endif ?>
					<div class="md-gray p-container roundy_product">
						<?php if ($item->total_quantity <= 0): ?>
								<?=$this->html->image('/img/soldout.png', array(
									'title' => "Sold Out",
									'style' => 'z-index : 2; position : absolute; left:57%; margin:10px;'
								)); ?>
						<?php endif ?>
						<?=$this->html->link(
							$this->html->image($productImage, array(
								'alt' => $item->name,
								'title' => $item->name,
								'width' => '228',
								'height' => '263')),
							"sale/$event->url/{$item->url}",
							array('title' => $item->name, 'escape' => false)
						); ?>
						
						
								<table style="margin:5px;">
									<tr>
										<td width="170" valign="top">
											<a href="<?="/sale/$event->url/$item->url"?>"><h2><?=$item->description ?></h2></a>
										</td>
										<td align="right">
											<span class="price" style="text-transform:uppercase; font-weight:normal; font-size:20px; color: #009900; float:right;">$<?=number_format($item->sale_retail,2);?></span><br>
											<span class="original-price" style="font-size:10px; white-space:nowrap;">Original $<?=number_format($item->msrp,2);?></span>
										</td>
									</tr>
								</table>
								
					</div>
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
