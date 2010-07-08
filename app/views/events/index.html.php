<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery.countdown.min');?>


<?php $x = 0; ?>
<?php foreach ($events as $event): ?>
	<!-- Start product item -->
	<div class="product-list-item featured r-container">
		<div class="tl"></div>
		<div class="tr"></div>
		<div class="md-gray p-container">
			<img src="/image/<?php echo $event->images->preview_image?>.jpg" width="355" height="410" title="Product Title" alt="Product Alt Text" />
			
			<div class="splash-details">
				<div class="table-cell left">
					Events End In<br />
					<strong><div id="<?php echo "splash$x"; ?>"</div></strong>
				</div>
				
				<div class="table-cell right">
					<a href="#" title="View Stroller Name Now" class="flex-btn"><span>Go</span></a>
				</div>
			</div>
		</div>
		<div class="bl"></div>
		<div class="br"></div>
	</div>
	<!-- End product item -->
	<script type="text/javascript"> 
	$(function () {
		var saleEnd = new Date();
		saleEnd = new Date(<?php echo $event->end_date->sec * 1000; ?>);
		$('<?php echo "#splash$x"; ?>').countdown({until: saleEnd, compact: true, description: ''});
	});
	</script>
	<?php $x++; ?>
<?php endforeach ?>