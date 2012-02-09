<h1>WE ARE NOT USING THIS - MOVED VIEW TO /brands</h1>


		<ul class="group">
			<?php $x = 0; ?>
			<?php foreach ($openEvents as $event): ?>
				<?php if($x<6){ ?>
				<li style="background:#000000; border:1px solid #cccccc; float:left; color#ffffff; width:300px; height:200px;">
					<a href="<?php echo '/sale/' . $event['url'];?>" title="Go to <?php echo $event['name']; ?> sale">
						<img src="/image/<?php echo $event['event_image']; ?>.jpg" alt="<?php echo $event['name']; ?>" />
						<em><?php echo $event['name']; ?></em>
					</a>
				</li>
				<?php } ?>
			<?php $x++; ?>
			<?php endforeach; ?>
		</ul>

