<style>

div.list-row a {
    clear: both;
    color: #F4CF53;
    display: block;
    float: left;
    font-weight: bold;
    overflow: visible;
    border-top: 1px solid #ddd; padding:5px 5px 5px 0px;
    text-align: left;
    text-decoration: none;
    width: 100%;
    
}
.list-row a:hover {
    background:#f7f7f7;
}
</style>

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
		<div class="list-row">
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
						<a href="#" onclick="window.location.href='sale/<?php echo $url; ?>';return false;"><img src="<?php echo $productImage; ?>" width="50" style="margin:0px 0px 0px 5px; float:left;" alt="<?php echo $event->name; ?>" title="<?php echo $event->name; ?>" /><br /><span style="color:#ed1c25; font-size:16px; margin:0px 0px 0px 10px;"><?php echo $event->name; ?></span></a>
					<?php else: ?>
						<a href="#" onclick="window.location.href='sale/<?php echo $url; ?>';return false;"><img src="<?php echo $productImage; ?>" width="50" style="margin:0px 0px 0px 5px; float:left;" alt="<?php echo $event->name; ?>" title="<?php echo $event->name; ?>" /><br/><span style="color:#ed1c25; font-size:16px; margin:0px 0px 0px 10px;"><?php echo $event->name; ?></span></a>
			 		<?php endif ?>


						
					</div>
				</div>
			
			</div>

			
			<?php $x++; ?>
		<?php $y++; ?>
	<?php endforeach ?>

	<div style="margin-bottom:35px;" class="clear"></div>

	<div class="container_16">
	<div class="grid_16">
		<h2 class="page-title gray">Upcoming Sales</h2>
		<hr />
	</div>
		<?php $x = 0; ?>
		<?php $y = 0; ?>
		<?php foreach ($pendingEvents as $event): ?>
			<?php if (($y == 0) || ($y == 2)): ?>
				<div class="grid_4">
			<?php endif ?>
			<?php if ($y == 1): ?>
				<div class="grid_4">
			<?php endif ?>
			<?php if ($y == 2): ?>
				<?php $y = -1; ?>
			<?php endif ?>
					<div class="p-container roundy_product_home">
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
							'width' => '228',
							'height' => '266'
						)), 'sale/'.$event->url, array('escape'=> false));
						 ?>				
				<div class="splash-details">
						<div class="table-cell left" style="display:block; padding:5px 5px 5px 10px;">
						 <p style="padding:0px; margin:0px; font-size:15px; color:#fff; font-weight:normal; text-transform:none;"> <?php echo $event->name; ?></p>
						 <p style="padding:0px; margin:-3px 0px 0px 0px; font-size:12px; color:#c7c7c7; font-weight:normal; font-style:italic; text-transform:none;">
						 <span id="<?php echo "futuresplash$x"; ?>" title="<?php echo $date = $event->start_date->sec * 1000; ?>" class="counter start"></span>
						</div>
				
			
			<?php $x++; ?>
			<?php $y++; ?>
	<?php endforeach ?>
	</div>
</div>
</div>
</div>
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
