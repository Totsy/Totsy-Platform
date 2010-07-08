<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery.countdown.min');?>


<?php $x = 0; ?>
<?php foreach ($events as $event): ?>
	<!-- Start product item -->
	<?php if ($x <= 1): ?>
		<div class="product-list-item featured r-container">
	<?php else: ?>
		<div class="product-list-item r-container">
	<?php endif ?>	
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
	<?php
		$date = $event->end_date->sec * 1000;
		$splashid = "#splash$x";
		$script[] = "<script type=\"text/javascript\">$(function () {var saleEnd = new Date();saleEnd = new Date($date);$(\"$splashid\").countdown({until: saleEnd, compact: true, description: ''});});</script>";?>
	<?php $x++; ?>
<?php endforeach ?>
<div class="invite-column r-container">

	<div class="tl"></div>
	<div class="tr"></div>
	<div class="md-gray-gradient p-container">
	
		<h2 class="invite-friends">Invite Friends Get $15</h2>
		
		<a href="#" class="flex-btn" title="Invite Friends Now">Invite Now</a>
		
		<div class="hor-div-line"><!-- --></div>
		
		<h2 class="socialize-us">Socialize With Us</h2>
		
		<div class="sm-icons">
			<a href="http://facebook.com" title="Friend us on Facebook" class="sm-btn sm-facebook-md">Friend us on Facebook</a>
			<a href="http://twitter.com" title="Follow us on Twitter" class="sm-btn sm-twitter-md">Follow us on Twitter</a>
		</div>
			
	</div>
	<div class="bl"></div>
	<div class="br"></div>

</div>
<!--Date Javascript -->
<?php foreach ($script as $value): ?>
	<?php echo $value ?>
<?php endforeach ?>