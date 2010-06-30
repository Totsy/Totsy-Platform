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
				<strong>8 Days, 12:59:49</strong>
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
					<strong class="red">SALE ENDS in </strong>
				</div>
			</div>
			<p><?php echo $blurb; ?><p>
		</div>
		
	</div>
	<div class="bl"></div>
	<div class="br"></div>
</div>
