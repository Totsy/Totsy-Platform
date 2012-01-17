<?php $this->title("Oops - Page Not Found - Totsy [404]"); ?>

<div id="p-status-404" class="grid_16">
	<section>
		<h2>Oops - We're sorry, we couldn't find what you were looking for.</h2>
		<p>The page you’re looking for can’t be found or no longer exists.</p>
		<p>Visit the <a href="/sales" title="Totsy's Sales">Home Page</a> or check out our latest sales:</p>
		<ul class="group">
			<li>
				<a href="#" title="Go to [EVENT NAME HERE] sale">
					<img src="http://www.totsy.com/image/4f106fd91d5ecb030600003c.jpg" alt="[EVENT NAME HERE]" />
					<em>[EVENT NAME]</em>
				</a>
			</li>
			<li>
				<a href="#" title="Go to [EVENT NAME HERE] sale">
					<img src="http://www.totsy.com/image/4f106fd91d5ecb030600003c.jpg" alt="[EVENT NAME HERE]" />
					<em>[EVENT NAME]</em>
				</a>
			</li>
			<li>
				<a href="#" title="Go to [EVENT NAME HERE] sale">
					<img src="http://www.totsy.com/image/4f106fd91d5ecb030600003c.jpg" alt="[EVENT NAME HERE]" />
					<em>[EVENT NAME That Is Reeeally Long]</em>
				</a>
			</li>
			<li>
				<a href="#" title="Go to [EVENT NAME HERE] sale">
					<img src="http://www.totsy.com/image/4f106fd91d5ecb030600003c.jpg" alt="[EVENT NAME HERE]" />
					<em>[EVENT NAME]</em>
				</a>
			</li>
		</ul>
	</section>

<?php 
/*
	@DAVID F'ING AROUND IN HERE
		* trying to call in events… 
		* just dropped in most of the code from events view, below… and updated SearchController.php… no surprise: ain't working
*/
?>
	<div>
	
		<?php $x = 0; ?>
		<?php foreach ($openEvents as $event): ?>
		
		<article class="event grid-x">
			<header class="eventDetails group">
				<h3><?php echo $event['name']; ?> <em id="<?php echo "todaysplash$x"; ?>" title="<?php echo $date = $event['end_date'] *1000 ; ?>" class="counter end"></em><!-- @TODO - use data-attribute instead of title… better, use a date/time element instead of an em --></h3>
				<p><?php echo strip_tags($event['blurb']); ?></p>
			</header><!-- /.eventDetails -->
			
			<div class="items group">
			<?php
			//print_r($event->eventItems);
			
			foreach($event['eventItems'] as $item){ ?>
				<?//php print_r($item); ?>
				<div class="item" data-prodID="<?php echo $item['_id'] ?>">
					<a href="<?php echo '/sale/' . $event['url'] . '/' . $item['url']?>" title="<?php echo $item['description'];?>">
						<img width="125" height="126" src="<?php echo "/image/" . $item['primary_image'] . ".jpg";?>" alt="<?php echo $item['description'];?>" />
						<h4><?php echo $item['description'];?></h4>
						<p>$<?php echo number_format($item['sale_retail'],2);?></p>
					</a>
				</div><!-- /.item -->
			<?php
			}
			?>
				<div class="btn viewAllEvents">
					<?php
						echo $this->html->link(
							$this->html->image("/img/btn-viewevents-bug.png", array(
								'title' => 'View all items from this sale',
								'alt' => 'View all items from this sale',
								'width' => '135',
								'height' => '203'
							)), 'sale/'.$event['url'], array('escape'=> false)
						);
					?>
				</div>
			</div><!-- /.items -->
			<footer>View all items from <?php echo $this->html->link($event['name'], 'sale/'.$event['url'], array('escape'=> false) );?></footer>
		</article><!-- /.event -->
		<?php $x++; ?>
		<?php endforeach ?>
	</div>	
<?php 
/*
	/ DAVID F'ING AROUND IN HERE
*/
?>
	
</div>