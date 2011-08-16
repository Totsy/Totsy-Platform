<?=$this->html->script(array('jquery.nivo.slider.pack'));?>
<script>
(function($) {
	$.fn.rotate = function() {
		var container = $(this);
		var totale = container.find("div").size();
		var current = 0;
		var i = setInterval(function() {
			if (current >= totale) current = 0;
			container.find("div").filter(":eq("+current+")").fadeIn("slow").end().not(":eq("+current+")").fadeOut("slow");
			current++;
		}, 5000);
		return container;
	};
})(jQuery);
</script>
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
		</div>
	</h2>
	<hr />
		<!--Disney -->
			<div class="disney disney_splash">
				<p><strong>SPECIAL BONUS!</strong> Included with your purchase of $45 or more is a one-year subscription to <img src="/img/Disney-FamilyFun-Logo.jpg" align="absmiddle" width="95px" /> ( a $10 value) <span id="disney">Offer &amp; Refund Details</span></p>
			</div>
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
                  
						<!-- this is where the items count was -->
						
					<?php
						if (!empty($event->images->splash_big_image)) {
							$productImage = "/image/{$event->images->splash_big_image}.jpg";
						} else {
							$productImage = ($x <= 1) ? "/img/no-image-large.jpeg" : "/img/no-image-small.jpeg";
						}
					?>
					<?php 
						if(empty($departments)) {
							$url = $event->url;
						} else {
							$url = $event->url.'/?filter='.$departments;
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
							)), "sale/$url", array('escape'=> false));
						?>
					<?php else: ?>
						<?=$this->html->link(
							$this->html->image("$productImage", array(
							'title' => $event->name,
							'alt' => $event->name,
							'width' => '298',
							'height' => '344'
						)), "sale/$url", array('escape'=> false));
						 ?>
					<?php endif ?>

                <div class="splash-details">

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
					var now = new Date();
					var diff = $date - (now.getTime());
					if((diff / 1000) < (24 * 60 * 60) ) {
						$(\"$splashid\").countdown({until: saleEnd, layout: 'Closes in {hnn}{sep}{mnn}{sep}{snn}'});
					} else {
						$(\"$splashid\").countdown({until: saleEnd, layout: 'Closes in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
					}
				});</script>";
		?>
			<?php if ($x == 1): ?>
				<div id="banner_container">
					<div><a href="/users/invite"><img src="/img/invite_girl.png" alt="" /></a></div>
					<?php if(!empty($banner["img"])): ?>
						<?php foreach($banner["img"] as $image): ?>
							<div><?php if(!empty($image["url"])):?>
								<a href="<?=$image["url"]?>"
									<?php
										if(array_key_exists('newPage', $image) && $image['newPage']) {
											echo 'target="_blank"';
										}
									?>
									>
									<img src="/image/<?=$image["_id"]?>.jpeg" alt="" />
								</a>
								<?php else: ?>
									<img src="/image/<?=$image["_id"]?>.jpeg" alt="" />
								<?php endif ?>
							</div>
						<?php endforeach ?>
					<?php endif ?>
				</div>
			<?php endif ?>
		<?php $x++; ?>
		<?php $y++; ?>
	<?php endforeach ?>


	<div style="margin-bottom:35px;" class="clear"></div>

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
					$(function () {var saleStart = new Date();
						saleStart = new Date($date);
						var now = new Date();
						var diff = $date - (now.getTime());
						if((diff / 1000) < (24 * 60 * 60) ) {
							$(\"$splashid\").countdown({until: saleStart, layout: 'Opens in {hnn}{sep}{mnn}{sep}{snn}'});
						} else {
							$(\"$splashid\").countdown({until: saleStart, layout: 'Opens in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
						}
					});
				</script>";
			?>
			<?php $x++; ?>
			<?php $y++; ?>
	<?php endforeach ?>
	</div>
</div>
<div id="modal">
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
//<!--
	$(document).ready(function() {
		$("#banner_container").rotate();
	});
//-->
</script>
<script type="text/javascript">
	$('#disney').click(function(){
		$('#modal').load('/events/disney').dialog({
			autoOpen: false,
			modal:true,
			width: 739,
			height: 700,
			position: 'top',
			close: function(ev, ui) {}
		});
		$('#modal').dialog('open');
	});
</script>

<!-- Google Code for inscrits Remarketing List -->
<script type="text/javascript">

/* <![CDATA[ */
	var google_conversion_id = 1019183989;
	var google_conversion_language = "en";
	var google_conversion_format = "3";
	var google_conversion_color = "666666";
	var google_conversion_label = "E1ZLCMH8igIQ9Yb-5QM";
	var google_conversion_value = 0;
/* ]]> */

</script>

<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js"></script>

<noscript>
	<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="https://www.googleadservices.com/pagead/conversion/1019183989/?label=E1ZLCMH8igIQ9Yb-5QM&amp;guid=ON&amp;script=0"/>
	</div>
</noscript>
<!-- END OF Google Code for inscrits Remarketing List --> 