<?=$this->html->script('jquery.countdown.min');?>

<h1 class="page-title gray"><span class="red">Today's <span class="bold caps">Sales</span></span></h1>
<?php $x = 0; ?>
<?php foreach ($openEvents as $event): ?>
	<!-- Start product item -->
	<?php if ($x <= 1): ?>
		<div class="product-list-item featured r-container">
	<?php else: ?>
		<div class="product-list-item r-container">
	<?php endif ?>	
		<div class="tl"></div>
		<div class="tr"></div>
		<div class="md-gray p-container">
			<?php
				if (!empty($event->images->splash_big_image)) {
					$productImage = "/image/{$event->images->splash_big_image}.jpg";
				} else {
					$productImage = ($x <= 1) ? "/img/no-image-large.jpeg" : "/img/no-image-small.jpeg";
				}
			?>
			<?php if ($x <= 1): ?>
				<?=$this->html->image("$productImage", array(
					'title' => "Product Title", 'alt' => "Product Alt Text", 'width' => '355', 'height' => '410'
				)); ?>
			<?php else: ?>
				<?=$this->html->image("$productImage", array(
					'title' => "Product Title", 'alt' => "Product Alt Text", 'width' => '298', 'height' => '298'
				)); ?>
			<?php endif ?>
			<div class="splash-details">
				<div class="table-cell left">
					Event Ends In<br />
					<strong><div id="<?php echo "todaysplash$x"; ?>"</div></strong>
				</div>

				<div class="table-cell right">
					<?=$this->html->link('Go', array('Events::view', 'args' => array($event->url)), array('class' => 'flex-btn'));?>
				</div>
			</div>
		</div>
		<div class="bl"></div>
		<div class="br"></div>
	</div>
	<!-- End product item -->
	<?php
		$date = $event->end_date->sec * 1000;
		$splashid = "#todaysplash$x";
		$todayJs[] = "<script type=\"text/javascript\">$(function () {var saleEnd = new Date();saleEnd = new Date($date);$(\"$splashid\").countdown({until: saleEnd, layout: '{dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});});</script>";?>
		<?php if ($x == 1): ?>
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
		<?php endif ?>
	<?php $x++; ?>
<?php endforeach ?>

<h2 class="page-title gray clear"><span class="red">Coming <span class="bold caps">Soon</span></span></h2>

<?php $x = 0; ?>
<?php foreach ($pendingEvents as $event): ?>
	<div class="product-list-item r-container">
	<div class="tl"></div>
	<div class="tr"></div>
	<div class="md-gray p-container">
		<?php
			if (!empty($event->images->splash_small_image)) {
				$productImage = "/image/{$event->images->splash_small_image}.jpg";
			} else {
				$productImage = "/img/no-image-small.jpeg";
			}
		?>
		<?=$this->html->image("$productImage", array(
			'title' => "Product Title", 'alt' => "Product Alt Text", 'width' => '298', 'height' => '298'
		)); ?>

		<div class="splash-details">
			<div class="table-cell left">
				Event Starts In<br />
				<strong><div id="<?php echo "futuresplash$x"; ?>"</div></strong>
			</div>

			<div class="table-cell right">
				<?=$this->html->link('Go', array('Events::view', 'args' => array($event->url)), array('class' => 'flex-btn'));?>
			</div>
		</div>
	</div>
	<div class="bl"></div>
	<div class="br"></div>
	</div>
	<!-- End product item -->
	<?php
		$date = $event->start_date->sec * 1000;
		$splashid = "#futuresplash$x";
		$futureJs[] = "<script type=\"text/javascript\">$(function () {var saleEnd = new Date();saleEnd = new Date($date);$(\"$splashid\").countdown({until: saleEnd, layout: '{dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});});</script>";?>
	<?php $x++; ?>
<?php endforeach ?>
<!--Javascript Output for Today's Events -->
<?php if (!empty($todayJs)): ?>
	<?php foreach ($todayJs as $value): ?>
		<?php echo $value ?>
	<?php endforeach ?>
<?php endif ?>

<!--Javascript Output for Future Events-->
<?php if (!empty($futureJs)): ?>
	<?php foreach ($futureJs as $value): ?>
		<?php echo $value ?>
	<?php endforeach ?>
<?php endif ?>
