<?php $this->title("Oops - Page Not Found - Totsy [404]"); ?>

<div id="p-status-404" class="grid_16">
	<section>
		<h2>Oops - We're sorry, we couldn't find what you were looking for.</h2>
		<p>The page you’re looking for can’t be found or no longer exists.</p>
		<p>Visit the <a href="/sales" title="Totsy's Sales">Home Page</a> or check out our latest sales:</p>
		<ul class="group">
			<?php $x = 0; ?>
			<?php foreach ($openEvents as $event): ?>
				<li>
					<a href="<?php echo '/sale/' . $event['url'];?>" title="Go to <?php echo $event['name']; ?> sale">
						<img src="/image/<?php echo $event['event_image']; ?>.jpg" alt="<?php echo $event['name']; ?>" />
						<em><?php echo $event['name']; ?></em>
					</a>
				</li>
			<?php $x++; ?>
			<?php endforeach; ?>
		</ul>
	</section>	
</div>