<div id="middle" class="fullwidth">
	<h1 class="page-title gray"><span class="red">Today's Sales</span></h1>
	<?php $x = 0; ?>
	<?php $y = 0; ?>
	<?php foreach ($openEvents as $event): ?>
		<!-- Start product item -->
		<?php if ($y == 0): ?>
			<div class="product-list-item featured r-container">
		<?php endif ?>
		<?php if ($y == 1): ?>
			<div class="product-list-item featured middle r-container">
		<?php endif ?>
		<?php if (($y == 2) || ($y == 4)): ?>
			<div class="product-list-item r-container">
		<?php endif ?>
		<?php if ($y == 3): ?>
			<div class="product-list-item middle r-container">
		<?php endif ?>
		<?php if ($y == 4): ?>
			<?php $y = 1; ?>
		<?php endif ?>
				<div class="tl"></div>
				<div class="tr"></div>
				<div class="md-gray p-container">
						<?php if ($itemCounts[ (string) $event->_id] == 0): ?>
								<?=$this->html->image('/img/soldout.gif', array(
									'title' => "Sold Out",
									'style' => 'z-index : 2; position : absolute; left:20%'
								)); ?>
					<?php endif ?>
					<?php
						if (!empty($event->images->splash_big_image)) {
							$productImage = "/image/{$event->images->splash_big_image}.jpg";
						} else {
							$productImage = ($x <= 1) ? "/img/no-image-large.jpeg" : "/img/no-image-small.jpeg";
						}
					?>
					<?php if ($x <= 1): ?>
						<?=$this->html->link(
							$this->html->image("$productImage", array(
							'title' => "Product Title",
							'alt' => "Product Alt Text",
							'width' => '355',
							'height' => '410'
							)),array('Events::view', 'args' => array($event->url)), array('escape'=> false));
						?>
					<?php else: ?>				
						<?=$this->html->link(
							$this->html->image("$productImage", array(
							'title' => "Product Title",
							'alt' => "Product Alt Text",
							'width' => '298',
							'height' => '298'
						)),array('Events::view', 'args' => array($event->url)), array('escape'=> false));
						 ?>
					<?php endif ?>
					<div class="splash-details">
						<div class="table-cell left">
							<?php echo $event->name; ?>
							<div id="<?php echo "todaysplash$x"; ?>"></div>
						</div>

						<div class="table-cell right">
							<span><?=$this->html->link('Go', array('Events::view', 'args' => array($event->url)), array('class' => 'flex-btn'));?></span>
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
			$todayJs[] = "<script type=\"text/javascript\">
				$(function () {
					var saleEnd = new Date();
					saleEnd = new Date($date);
					$(\"$splashid\").countdown({until: saleEnd, layout: 'Closes in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
				});</script>";
		?>
			<?php if ($x == 1): ?>
				<div class="invite-column r-container">

					<div class="tl"></div>
					<div class="tr"></div>
					<div class="md-gray-gradient p-container">

						<h2 class="invite-friends">Invite Friends Get $15</h2>

						<a href="/invite" class="flex-btn" title="Invite Friends Now">Invite Now</a>

						<div class="hor-div-line"><!-- --></div>

						<h2 class="socialize-us">Socialize With Us</h2>
						<br><br>
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
		<?php $y++; ?>
	<?php endforeach ?>
	<div class="coming-soon-sales">
		<h2 class="page-title gray clear"><span class="red">Coming <span class="bold caps">Soon</span></span></h2>
		<?php $x = 0; ?>
		<?php $y = 0; ?>
		<?php foreach ($pendingEvents as $event): ?>
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
						<?php
							if (!empty($event->images->splash_small_image)) {
								$productImage = "/image/{$event->images->splash_small_image}.jpg";
							} else {
								$productImage = "/img/no-image-small.jpeg";
							}
						?>
						<?=$this->html->link(
						$this->html->image("$productImage", array(
							'title' => "Product Title", 
							'alt' => "Product Alt Text", 
							'width' => '298', 
							'height' => '298'
						)),array('Events::view', 'args' => array($event->url)), array('escape'=> false));
						 ?>

						<div class="splash-details">
							<div class="table-cell left">
								Event Starts In<br>
								<strong><div id="<?php echo "futuresplash$x"; ?>"></div></strong>
							</div>

							<div class="table-cell right">
								<?=$this->html->link('Preview', array(
									'Events::view',
									'args' => array($event->url)),
									array('class' => 'flex-btn')
									);
								?>
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
				$futureJs[] = "<script type=\"text/javascript\">
					$(function () {var saleEnd = new Date();
						saleEnd = new Date($date);
						$(\"$splashid\").countdown({until: saleEnd, layout: '{dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
					});
				</script>";
			?>
			<?php $x++; ?>
			<?php $y++; ?>
	<?php endforeach ?>
	</div>
</div>
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