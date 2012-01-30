<?php if(strcmp($_SERVER['REQUEST_URI'],'/category/all') == 0) { ?>
<h3 style="font-size:14px;">Shop by Category</h3>
<hr />
<?php } else { ?>
<h3 style="font-size:14px;"> <?php echo $categories; ?> <em style="float:right; font-size:12px; color:#999;">(<?php echo $eventCount; ?> Sales Found)</em></h3>
<hr />
<?php } ?>
		
		<?php // do we have events?
			if ($openEvents != null) {
			?>
				<?php $x = 0; ?>
				<?php foreach ($openEvents as $event): ?>

		<div class="content-primary">
		<ul data-role="listview" data-inset="true" data-divider-theme="e"> 
		<li data-role="list-divider" style="font-size:16px;" data-icon="false"><a href="<?php echo '/sale/' . $event['url'];?>" title="See the <?php echo $event['name']; ?> sale"><?php echo $event['name']; ?></a></li>
					<?php
					foreach($event['eventItems'] as $item){ ?>
					<?php if ($item['total_quantity'] >= 1){ ?>
					
					<li class="item" data-prodID="<?php echo $item['_id'] ?>">
							<a href="<?php echo '/sale/' . $event['url'] . '/' . $item['url']?>" title="<?php echo $item['description'];?>">
								<img width="80" src="<?php echo "/image/" . $item['primary_image'] . ".jpg";?>" alt="<?php echo $item['description'];?>" />
								<h3><?php echo $item['description'];?></h3>
								<p style="color:#999; font-size:12px;">$<?php echo number_format($item['sale_retail'],2);?></p>
							</a>
					</li>
					<?php
					}
					?>
					
					<?php
					}
					?>
					<li data-theme="d" data-icon="false" style="font-size:12px;"><?php echo $this->html->link('More from ' . $event['name'], 'sale/'.$event['url'], array('escape'=> false));?></li>
		</ul>
		</div><!-- /.item -->
		
				<?php $x++; ?>
				<?php endforeach ?>

			<?php
			}
			
			// if no events, give 'em a message / alternative
			else { 
			?>
				<?php if(!strcmp($_SERVER['REQUEST_URI'],'/category/all') == 0) { ?>
				<div class="grid_16" id="noevents">
					<section>
						<p>We're sorry, there are no events currently running in this category right now.</h2>
						<p>Visit the <a title="Totsy's Sales" href="/sales">Home Page</a> to check out our latest sales.</p>
					</section>	
				</div>
			<?php } ?>
			<?php
			}
		?>
	

<ul data-role="listview" data-inset="true">
	<li><a href="girls-apparel">Girls Apparel</a></li>
	<li><a href="boys-apparel">Boys Apparel</a></li>
	<li><a href="shoes">Shoes</a></li>
	<li><a href="accessories">Accessories</a></li>
	<li><a href="toys-books">Toys and Books</a></li>
	<li><a href="gear">Gear</a></li>
	<li><a href="home">Home</a></li>
	<li><a href="moms-dads">Moms and Dads</a></li>
</ul>

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
		    	$("#" + this.id).countdown({until: saleTime, layout: 'Ends in <strong>{hnn}{sep}{mnn}{sep}{snn}</strong>'});
		    } else {
		    	$("#" + this.id).countdown({until: saleTime, layout: 'Ends in <strong>{dn} {dl}, {hnn}{sep}{mnn}{sep}{snn}</strong>'});
		    }
		}
	 });

//-->
</script>