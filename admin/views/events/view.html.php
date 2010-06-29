<?php

	if(!empty($event)) {
		$preview_image = (empty($event->images)) ? null : $event->images->preview_image;
		$logo_image = (empty($event->images)) ? null : $event->images->logo_image;
		$blurb = $event->blurb;
	} 
?>
<?=$this->html->link('Edit Event', array('Events::edit', 'args' => array($event->_id)));?>

<div class="r-container clear">
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page-header" class="md-gray">
	
		<div class="left">
	
			<img src="/image/<?php echo $preview_image?>.jpg" alt="Image ALT Tag" title="Image ALT Tag" width="169" height="193" />
		
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

