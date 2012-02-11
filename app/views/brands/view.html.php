<article id="jojo">
	<h2>Totsy welcomes JoJo Maman B&eacute;b&eacute; for a special 10 day event</h2>
	<section>
		<header class="group">
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam vitae nisl sit amet est ornare faucibus. Ut a tellus vitae dui posuere fermentum ut a nulla. Vivamus rhoncus imperdiet fringilla. Cras elit eros, ultrices sodales sodales non, interdum ut nisi. Nunc accumsan urna a mi sodales eu suscipit elit euismod. Donec scelerisque, tellus a luctus cursus, nulla risus auctor dui, in pulvinar ligula risus tempus augue. Pellentesque eu consectetur felis. In ut elit nisl. In tincidunt sollicitudin feugiat. Quisque nec nisi felis, id laoreet tortor.</p>
			<aside class="video">
				<?php // placeholder youtube video below ?>
				<iframe width="400" height="233" src="http://www.youtube.com/embed/P_m7ZSo_l-s?rel=0" frameborder="0" allowfullscreen></iframe>		
			</aside>
		</header>
		
		<ul class="bp-events group">
			<?php $x = 0; ?>
			<?php foreach ($openEvents as $event): ?>
				<?php if($x<6){ ?>
				<li>
					<a href="<?php echo '/sale/' . $event['url'];?>" title="Go to <?php echo $event['name']; ?> sale">
					<?php
						/* 
							@TODO: add the redirect for non-logged-in view… 
							<?php echo '/sale/' . $event['url'] . '?redirect=/sale/' . $event['url']; ?>
						*/
					?>
						<?php // image check and store
							if($event['images']['splash_big_image'] !== null) { 
								$eventImage = "/image/" . $event['images']['splash_big_image'] . ".jpg";
							} else {
								$eventImage = '/img/no-image-small.jpeg';
							}
						?>
						<img src="<?php echo $eventImage; ?>" alt="<?php echo $event['name']; ?>" width="246" height="284" />
						<em><?php echo $event['name']; ?></em>
					</a>
				</li>
				<?php } ?>
			<?php $x++; ?>
			<?php endforeach; ?>
		</ul>
	</section>	
</article>