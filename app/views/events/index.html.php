<?=$this->html->script(array('jquery.nivo.slider.pack'));?>

	<h2 class="page-title gray"><span class="_red">Today's Sales</span>
    <div class="sm-actions fr" style="font-size:12px; margin:7px 0px 0px 0px;">
			<dl>
				<dt><strong>Socialize With Us</strong></dt>
				<dd>
					<ul>
						<li><a href="http://www.facebook.com/pages/Totsy/141535723466" target="_blank" title="Friend us on Facebook" class="sm-facebook sm-btn">Friend us on Facebook</a></li>
						<li><a href="http://twitter.com/MyTotsy" target="_blank" title="Follow us on Twitter" class="sm-twitter sm-btn">Follow us on Twitter</a></li>
					</ul>
				</dd>
			</dl>
		</div>   </h2>
	<hr />



<div class="fullwidth">

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
							'title' => $event->name,
							'alt' => $event->name,
							'width' => '355',
							'height' => '410',
					'style' => 'margin:0px 0px -6px 0px;'
							)), "sale/$event->url", array('escape'=> false));
						?>
					<?php else: ?>
						<?=$this->html->link(
							$this->html->image("$productImage", array(
							'title' => $event->name,
							'alt' => $event->name,
							'width' => '298',
							'height' => '344'
						)), "sale/$event->url", array('escape'=> false));

						 ?>

					<?php endif ?>


                <div class="splash-details">

                <!-- Begin Holiday Banner Notifications -->
                <?php if ($event->tags): ?>
					<?php foreach ($event->tags as $key => $value): ?>
                        <div style="background:#f0efef; font-size:12px; color:#a8a8a8; font-weight:normal; padding:3px 5px; margin:0px 0px -22px 0px;"> <?=$value?></div>
                    	<div class="clear"></div>
                    <?php endforeach ?>
          		<?php endif ?>
                <!-- End Holiday Banner Notifications -->

						<div class="table-cell left" style="display:block; padding:5px 5px 5px 10px;">
						 <p style="padding:0px; margin:0px; font-size:16px; color:#fff; font-weight:normal; text-transform:none;"> <?php echo $event->name; ?></p>
						 <p style="padding:0px; margin:-3px 0px 0px 0px; font-size:13px; color:#c7c7c7; font-weight:normal; font-style:italic; text-transform:none;"><span id="<?php echo "todaysplash$x"; ?>"></span>
						</div>

						<div class="table-cell right" style="width:55px; display:block; padding:5px; margin:7px 0px 0px 0px; ">
							<span><?=$this->html->link('Shop', 'sale/'.$event->url, array('class' => 'go-btn'));?></span>
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

			<?=$this->html->link($this->html->image("/img/invite_girl.png", array(
					'title' => "Invite Friends. Get $15",
					'alt' => "Invite Friends. Get $15",
					'width' => '181',
					'height' => '413'
					)),'/Users/invite', array('escape'=> false));
			?>

			<?php endif ?>
		<?php $x++; ?>
		<?php $y++; ?>
	<?php endforeach ?>


        <div style="margin-bottom:35px;" class="clear"></div>

		<h2 class="page-title gray clear"><span class="_red">Monthly Sales / Home Decor Month</span></h2>
		<hr />

 <div id="slider" class="nivoSlider">
		<a href="/sale/baby-mod"><img src="/img/home_img-1.jpg"  alt="" title="Baby Mod - Up to 47% Off" /></a>
                <a href="/sale/woombie"><img src="/img/home_img-2.jpg"  alt="" title="Woombie - Up to 40% Off" /></a>
                <a href="/sale/cozibug"><img src="/img/home_img-3.jpg" alt="" title="Cozibug - Up to 60% Off" /></a>
                <a href="/sale/kids-line"><img src="/img/home_img-4.jpg"  alt="" title="Kids Line - Up to 72% Off" /></a>
                <a href="/sale/all-pop-art"><img src="/img/home_img-5.jpg" alt=""  title="All Pop Art - Up to 46% Off" /></a>



            </div>

	<br style="margin-bottom:10px;"/>

	<div class="coming-soon-sales">

		<h2 class="page-title gray clear"><span class="_red">Upcoming Sales</span></h2>
		<hr />

         <div class="clear"></div>
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
							'title' => $event->name,
							'alt' => $event->name,
							'width' => '298',
							'height' => '344'
						)), 'sale/'.$event->url, array('escape'=> false));
						 ?>

						<div class="splash-details">

<!-- Begin Holiday Banner Notifications -->
                <?php if ($event->tags): ?>
                                        <?php foreach ($event->tags as $key => $value): ?>
                        <div style="background:#f0efef; font-size:12px; color:#a8a8a8; font-weight:normal; padding:3px 5px; margin:0px 0px 0px 0px;"> <?=$value?></div>
                        <div class="clear"></div>
                    <?php endforeach ?>
                        <?php endif ?>
                <!-- End Holiday Banner Notifications -->

<div class="table-cell left" style="display:block; padding:5px 5px 5px 10px;">
							<p style="padding:0px; margin:0px; font-size:16px; color:#fff; font-weight:normal; text-transform:none;"> <?php echo $event->name; ?></p>
							<p style="padding:0px; margin:-3px 0px 0px 0px; font-size:13px; color:#c7c7c7; font-weight:normal; font-style:italic; text-transform:none;">
<span id="<?php echo "futuresplash$x"; ?>"></span>
							</div>

							<div class="table-cell right" style="width:55px; display:block; padding:5px; margin:7px 0px 0px 0px;">
								<?=$this->html->link('View', 'sale/'.$event->url, array('class' => 'preview-btn')
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

    <script type="text/javascript">
    $(window).load(function() {
        $('#slider').nivoSlider();
    });
    </script>
