<?=$this->html->script('jquery-1.4.2');?>
<?=$this->html->script('jquery.countdown.min');?>

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
<h1 class="page-title gray"><span class="red">Today's <span class="bold caps">Sales</span></span></h1>
<?php $x = 0; ?>
<?php foreach ($eventsToday as $event): ?>
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
					<strong><div id="<?php echo "todaysplash$x"; ?>"</div></strong>
				</div>

				<div class="table-cell right">
					<?=$this->html->link('Go', array('Events::view', 'args' => array($event->name)), array('class' => 'flex-btn'));?>
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
		$todayJs[] = "<script type=\"text/javascript\">$(function () {var saleEnd = new Date();saleEnd = new Date($date);$(\"$splashid\").countdown({until: saleEnd, compact: true, description: ''});});</script>";?>
	<?php $x++; ?>
<?php endforeach ?>

<?php $x = 0; ?>
<?php foreach ($currentEvents as $event): ?>
	
	<!-- Start product item -->
	<?php if ($x <= 1): ?>
		<div class="product-list-item featured r-container">
	<?php else: ?>
		<div class="product-list-item r-container">
	<?php endif ?>	
		<div class="tl"></div>
		<div class="tr"></div>
		<div class="md-gray p-container">
			<img src="/image/<?php echo $event->images->preview_image?>.jpg" width="298" height="298" title="Product Title" alt="Product Alt Text" />

			<div class="splash-details">
				<div class="table-cell left">
					Events End In<br />
					<strong><div id="<?php echo "currentsplash$x"; ?>"</div></strong>
				</div>

				<div class="table-cell right">
					
					<?=$this->html->link('Go', array('Events::view', 'args' => array($event->name)), array('class' => 'flex-btn'));?>
				</div>
			</div>
		</div>
		<div class="bl"></div>
		<div class="br"></div>
	</div>
	<!-- End product item -->
	<?php
		$date = $event->end_date->sec * 1000;
		$splashid = "#currentsplash$x";
		$currentJs[] = "<script type=\"text/javascript\">$(function () {var saleEnd = new Date();saleEnd = new Date($date);$(\"$splashid\").countdown({until: saleEnd, compact: true, description: ''});});</script>";?>
	<?php $x++; ?>
<?php endforeach ?>

<h2 class="page-title gray clear"><span class="red">Coming <span class="bold caps">Soon</span></span></h2>

<?php $x = 0; ?>
<?php foreach ($futureEvents as $event): ?>
	<div class="product-list-item r-container">
	<div class="tl"></div>
	<div class="tr"></div>
	<div class="md-gray p-container">
		<img src="/image/<?php echo $event->images->preview_image?>.jpg" width="298" height="298" title="Product Title" alt="Product Alt Text" />

		<div class="splash-details">
			<div class="table-cell left">
				Events End In<br />
				<strong><div id="<?php echo "futuresplash$x"; ?>"</div></strong>
			</div>

			<div class="table-cell right">
				<?=$this->html->link('Go', array('Events::view', 'args' => array($event->name)), array('class' => 'flex-btn'));?>
			</div>
		</div>
	</div>
	<div class="bl"></div>
	<div class="br"></div>
	</div>
	<!-- End product item -->
	<?php
		$date = $event->end_date->sec * 1000;
		$splashid = "#futuresplash$x";
		$futureJs[] = "<script type=\"text/javascript\">$(function () {var saleEnd = new Date();saleEnd = new Date($date);$(\"$splashid\").countdown({until: saleEnd, compact: true, description: ''});});</script>";?>
	<?php $x++; ?>
<?php endforeach ?>
<!--Javascript Output for Today's Events -->
<?php if (!empty($todayJs)): ?>
	<?php foreach ($todayJs as $value): ?>
		<?php echo $value ?>
	<?php endforeach ?>
<?php endif ?>

<!--Javascript Output for Current Events-->
<?php if (!empty($currentJs)): ?>
	<?php foreach ($currentJs as $value): ?>
		<?php echo $value ?>
	<?php endforeach ?>
<?php endif ?>

<!--Javascript Output for Future Events-->
<?php if (!empty($futureJs)): ?>
	<?php foreach ($futureJs as $value): ?>
		<?php echo $value ?>
	<?php endforeach ?>
<?php endif ?>
