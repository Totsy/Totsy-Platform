<h1>JOJO MAMAN BEBE SPLASH TEST FRAMEWORK</h1>


		<ul class="bp-events group">
<!-- @TODO: there is NO data being returned??? will this be static or dynamic?? -->
			<?php $x = 0; ?>
			<?php foreach ($openEvents as $event): ?>
				<?php if($x<6){ ?>
				<li class="even">
					<a href="<?php echo '/sale/' . $event['url'];?>" title="Go to <?php echo $event['name']; ?> sale">
						<img src="/image/<?php echo $event['event_image']; ?>.jpg" alt="<?php echo $event['name']; ?>" />
						<em><?php echo $event['name']; ?></em>
					</a>
				</li>
				<?php } ?>
			<?php $x++; ?>
			<?php endforeach; ?>

<!--
				<li>
					<a href="/sale/" title="Go to [EVENT NAME HERE] sale">
						<img src="/image/<?php echo $event['event_image']; ?>.jpg" alt="<?php echo $event['name']; ?>" />
						<em><?php echo $event['name']; ?></em>
					</a>
				</li>
-->

		</ul>

