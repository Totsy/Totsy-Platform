<?php $this->title($event->name); ?>
<?php echo $this->html->script('jquery.countdown.min');?>
<?php echo $this->html->style('jquery.countdown');?>

<div class="item_intro_box">
<?php if (!empty($event->images->event_image)) {
	echo $this->html->image("/image/{$event->images->event_image}.jpg", array(
		'title' => $event->name,
		'width' => "278",
	));

	} else {
	
	echo $this->html->image('/img/no-image-small.jpeg', array(
		'title' => "No Image Available",
		'width' => "278",
	));
} ?>

<div class="item_title_bar">
<div class="item_title" style="float:left;"><?php echo $event->name; ?></div>
<!-- <a href="#items" class="item_shop_btn" style="float:left; margin-left:5px;">Shop</a> -->
</div>
<div class="blurb">
	<?php echo $event->blurb ?>
</div>
</div>
<div id="listingCountdown" class="listingCountdown" style="text-align:center; margin:10px 0px;"></div>

<div class="holiday_message">
	<?php if($missChristmasCount>0){ ?>
	<p>Items in this sale are not guaranteed to be delivered <br />on or before 12/25.*</p>
	<?php } else{ ?>
	<p>Items in this sale will be delivered on or before 12/23.*
	</p>
	<?php } ?>
</div>

<div style="clear:both;"></div>

<div>
<?php if(!empty($filters)): ?>
<hr />
				
				
		<div id='filterb'>
			<?php echo $this->form->create(null, array('id' => 'filterform')); ?>
			<option value="">Sort By:</option>
			<?php echo $this->form->select('filterby',$filters, array('onchange' => "filter()", 'id' => 'filterby', 'data-overlay-theme' => 'a', 'data-native-menu' => 'false', 'value' => array($departments => $departments))); ?>
			<?php echo $this->form->end(); ?>
		</div>
<hr />
		<?php endif ?>
		</div>
<div style="clear:both;"></div>
	<div data-role="content">
		<div class="content-primary">
		<ul data-role="listview">	
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
				<!-- Start product item -->
					<?php if (($y == 0) || ($y == 2)): ?>
						<li>
					<?php endif ?>
					<?php if ($y == 1): ?>
						<li>
					<?php endif ?>
					<?php if ($y == 2): ?>
						<?php $y = -1; ?>
					<?php endif ?>
				<a href="#" onclick="window.location.href='/sale/<?php echo $event->url; ?>/<?php echo $item->url; ?>';return false;">
				<?php if ($item->total_quantity <= 0): ?>
								<?php echo $this->html->image('/img/soldout.png', array(
									'title' => "Sold Out",
									'style' => 'z-index : 99999; position : absolute; right:0;'
								)); ?>
						<?php endif ?>
						<img src="<?php echo $productImage; ?>" alt="<?php echo $item->description ?>" title="<?php echo $item->description ?>" width="80" />
				<h3 style="font-size:14px;"><?php echo $item->description ?></h3>
				<span class="price" style="text-transform:uppercase; font-weight:normal;font-size:12px; color: #009900; float:left;">$<?php echo number_format($item->sale_retail,2);?></span>
				<span class="original-price" style="font-size:12px; white-space:nowrap; color:#999; float:left; margin:0px 0 0 7px; text-decoration: line-through;">$<?php echo number_format($item->msrp,2);?></span>
				</a><?php $y++ ?>
			</li>
				<!-- End product item -->
			<?php endforeach ?>
		<?php endif ?>
		</ul>
		</div>
	</div>
<div class="clear"></div>
<div style="color:#707070; font-size:12px; font-weight:bold; padding:10px;">
				<?php
				if($missChristmasCount>0&&$notmissChristmasCount>0){
				?>
				* Totsy ships all items together. If you would like the designated items in your cart delivered on or before 12/23, please ensure that any items that are not guaranteed to ship on or before 12/25 are removed from your cart and purchased separately. Our delivery guarantee does not apply when transportation networks are affected by weather. Please contact our Customer Service department at 888-247-9444 or email <a href="mailto:support@totsy.com">support@totsy.com</a> with any questions. 
				
				<?php
				}
				elseif($missChristmasCount>0){
				?>
				* Your items will arrive safely, but after 12/25.				
				<?php
				}
				else{
				?>
				
				* Our delivery guarantee does not apply when transportation networks are affected by weather.
				
				<?php
				}
				?>
				
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
			$('#listingCountdown').countdown({until: saleStart, layout: 'Sale opens in {hnn}{sep}{mnn}{sep}{snn}'});
		} else {
			$('#listingCountdown').countdown({until: saleStart, layout: 'Sale opens in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
		}
	} else {
			var diff = <?php echo $event->end_date->sec * 1000 ?> - (now.getTime());
			if((diff / 1000) < (24 * 60 * 60) ) {
				$('#listingCountdown').countdown({until: saleEnd, layout: 'Hurry sale ends in {hnn}{sep}{mnn}{sep}{snn}'});
			} else {
				$('#listingCountdown').countdown({until: saleEnd, layout: 'Hurry sale ends in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
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

<script type="text/javascript">
var cto_params = [];
cto_params["kw"] = "<?php echo $event->name?>"; //REMOVE LINE IF NOT APPLICABLE
<?php if (!empty($items)): ?>
<?php $iCounter = 1; ?>
<?php foreach ($items as $item): ?>
cto_params["i<?php echo $iCounter;?>"] = "<?php echo (string) $item->_id; ?>";
<?php if($iCounter==5) break; ?>
<?php $iCounter++; ?>
<?php endforeach ?>
<?php endif ?>
</script>

<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>