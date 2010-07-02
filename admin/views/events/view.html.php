<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery.countdown.min');?>
<?=$this->html->style('jquery.countdown');?>

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
<?=$this->html->link('Edit Event', array('Events::edit', 'args' => array($event->_id)));?>

<h1 id="event_preview">Event Preview</h1>
<div class="product-list-item middle r-container">
	<div class="tl"></div>
	<div class="tr"></div>
	<div class="md-gray p-container">
	
		<img src="/image/<?php echo $preview_image?>.jpg" width="298" height="298" title="Product Title" alt="Product Alt Text" />
		
		<div class="splash-details">
			<div class="table-cell left">
				Events End In<br />
				<strong><div id="splashCountdown"></div></strong>
			</div>
			
			<div class="table-cell right">
				<a href="#" title="View Stroller Name Now" class="flex-btn"><span>Go</span></a>
			</div>
		</div>
			
	</div>
	<div class="bl"></div>
	<div class="br"></div>
</div>

<h1 id="event_detail_preview">Event Detail Preview</h1>
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
					<h1>Fischer Price</h1>
					<strong class="red">SALE ENDS in <div id="listingCountdown"></div></strong>
					
				</div>
			</div>
			<p><?php echo $blurb; ?><p>
		</div>
		
	</div>
	<div class="bl"></div>
	<div class="br"></div>
</div>