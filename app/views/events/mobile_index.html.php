<ul data-role="listview" data-filter="true" data-filter-placeholder="Filter Sales...">
<?php $x = 0; ?>
	<?php $y = 0; ?>
	<?php foreach ($openEvents as $event): ?>
		<!-- Start product item -->
		<?php if ($y == 0): ?>
		
		<?php endif ?>
		<?php if ($y == 1): ?>
		<?php endif ?>
		
		<?php if (($y == 2) || ($y == 3)): ?>
		<?php endif ?>
		<?php if ($y == 4): ?>
		<?php endif ?>
		<?php if ($y == 3): ?>
			<?php $y = 1; ?>
		<?php endif ?>
				
			<li><!-- this is where the items count was -->
			
			<?php 
					$eventHasStatus = false;
					if (!empty($event->status_update) && $event->status_update != 'none'){
						$eventHasStatus = true;
						$eventStatusClass = 'status_'.$event->status_update;
						
						switch ($event->status_update){
							case 'stock_added':
								$eventStatus = "Stock Added";
							break;
							case 'styles_added':
								$eventStatus = "Styles Added";
							break;
							case 'blowout':
								$eventStatus = "Blowout";
							break;
							case 'charity':
								$eventStatus = "Charity Event";
							break;
							case 'sold_out':
								$eventStatus = "Sold Out";
							break;
						}
					}
				?>
				
				<?php
					if (!empty($eventHasStatus)) { ?>
						<div class="p-container roundy_product_home status <?php echo $eventStatusClass; ?>">
							<span class="ui-li-count" style="margin:-3em -3em 0 0;"><?php echo $eventStatus; ?></span>
					<?php
					} else { ?>
						<div class="p-container roundy_product_home">
					<?php 
					}
				?>
				
				</div>
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
					<a href="#" onclick="window.location.href='/sale/<?php echo $url; ?>';return false;"><img src="<?php echo $productImage; ?>" width="80" alt="<?php echo $event->name; ?>" /><h3 style="margin:0.8em 0!important;"><?php echo $event->name; ?></h3><p style="color:#999; font-size:12px;"><span id="<?php echo "todaysplash$x"; ?>" title="<?php echo $date = $event->end_date->sec * 1000; ?>" class="counter end"></span></p></a>
					<?php else: ?>
					<a href="#" onclick="window.location.href='/sale/<?php echo $url; ?>';return false;"><img src="<?php echo $productImage; ?>" width="80" alt="<?php echo $event->name; ?>" /><h3 style="margin:0.8em 0!important;"><?php echo $event->name; ?></h3><p style="color:#999; font-size:12px;"><span id="<?php echo "todaysplash$x"; ?>" title="<?php echo $date = $event->end_date->sec * 1000; ?>" class="counter end"></span></p></a>
			 		<?php endif ?>
			</li>
			<?php $x++; ?>
		<?php $y++; ?>
	<?php endforeach ?>
</ul>
<div class="clear"></div>
	<div style="margin-bottom:35px; clear:both;"></div>
 
		<h2>Upcoming Sales</h2>
		<hr />
<ul data-role="listview" data-inset="true">
		<?php $x = 0; ?>
		<?php $y = 0; ?>
		<?php foreach ($pendingEvents as $event): ?>
			<?php if (($y == 0) || ($y == 2)): ?>
			<?php endif ?>
			<?php if ($y == 1): ?>
			<?php endif ?>
			<?php if ($y == 2): ?>
				<?php $y = -1; ?>
			<?php endif ?>
					<li>
					<a href="#" onclick="window.location.href='/sale/<?php echo $event->url; ?>';return false;"><h3 style="margin:0.8em 0!important;"><?php echo $event->name; ?></h3><p style="color:#999; font-size:12px;"><span id="<?php echo "futuresplash$x"; ?>" title="<?php echo $date = $event->start_date->sec * 1000; ?>" class="counter start"></span></p></a>
					</li>
			<?php $x++; ?>
			<?php $y++; ?>
	<?php endforeach ?>
	
</ul>
<!--Javascript Output for Today's Events -->
<?php if (!empty($todayJs)): ?>
	<?php foreach ($todayJs as $value): ?>
		<?php //echo $value ?>
	<?php endforeach ?>
<?php endif ?>

<!--Javascript Output for Future Events-->
<?php if (!empty($futureJs)): ?>
	<?php foreach ($futureJs as $value): ?>
		<?php //echo $value ?>
	<?php endforeach ?>
<?php endif ?>

<script type="text/javascript">
//<!--

	$(".counter").each( function () {

		var fecha  = parseInt(this.title);
		var saleTime = new Date(fecha);
		var now = new Date();
		var diff = saleTime - (now.getTime());

		//check if its and end date or start date
		if($("#" + this.id).hasClass("start"))
		{
		    if((diff / 1000) < (24 * 60 * 60) ) {
		        $("#" + this.id).countdown({until: saleTime, layout: 'Opens in {hnn}{sep}{mnn}{sep}{snn}'});
		    } else {
		        $("#" + this.id).countdown({until: saleTime, layout: 'Opens in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
		    }
		} else {
		    if((diff / 1000) < (24 * 60 * 60) ) {
		    	$("#" + this.id).countdown({until: saleTime, layout: 'Ends in {hnn}{sep}{mnn}{sep}{snn}'});
		    } else {
		    	$("#" + this.id).countdown({until: saleTime, layout: 'Ends in {dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}'});
		    }
		}
	 });

//-->
</script>

<p></p>
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>