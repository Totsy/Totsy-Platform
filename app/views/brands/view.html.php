<?php
	if ((empty($userInfo))) { ?>
		<div id="jojoLoggedOut">
			<p class="signin">Already a member? <a href="/login" title="Sign In">Sign in here</a></p>
			<p class="signup group">
				<strong><em>Up to 90% off retail</em> for mom, baby, and child.</strong>
				<a title="Register a Totsy account" class="btn" href="/register"><strong>Join Now</strong></a>
			</p>
		</div>
		<article id="jojo" class="public">
<?php } else { ?>
	<article id="jojo">
<?php } ?>
		<h2>Totsy welcomes JoJo Maman B&eacute;b&eacute; for a special 10 day event</h2>
		<section>
			<header class="group">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam vitae nisl sit amet est ornare faucibus. Ut a tellus vitae dui posuere fermentum ut a nulla. Vivamus rhoncus imperdiet fringilla. Cras elit eros, ultrices sodales sodales non, interdum ut nisi. Nunc accumsan urna a mi sodales eu suscipit elit euismod. Donec scelerisque, tellus a luctus cursus, nulla risus auctor dui, in pulvinar ligula risus tempus augue. Pellentesque eu consectetur felis. In ut elit nisl. In tincidunt sollicitudin feugiat. Quisque nec nisi felis, id laoreet tortor.</p>
				<aside class="video">
					<?php // @TODO - replace placeholder youtube video below with actual JoJo video prior to launch
							// - use iframe embed code rather than object method for better mobile support
							/*
							<!-- old embed:
								<object width="400" height="233"><param name="movie" value="http://www.youtube.com/v/P_m7ZSo_l-s?version=3&amp;hl=en_US&amp;rel=0"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/P_m7ZSo_l-s?version=3&amp;hl=en_US&amp;rel=0" type="application/x-shockwave-flash" width="400" height="233" allowscriptaccess="always" allowfullscreen="true"></embed></object>							
							 -->
							*/
					?>
					<iframe width="400" height="233" src="http://www.youtube.com/embed/P_m7ZSo_l-s" frameborder="0" allowfullscreen></iframe>
				</aside>
			</header>
			
			<ul class="bp-events group">
				<?php $x = 0; ?>
				<?php foreach ($openEvents as $event): ?>
					<?php if($x<6){ ?>
					<li>
						<a href="<?php echo '/sale/' . $event['url'];?>" title="Go to <?php echo $event['name']; ?> sale">
							<?php // image check and store
								if($event['images']['splash_big_image'] !== null) { 
									$eventImage = "/image/" . $event['images']['splash_big_image'] . ".jpg";
								} else {
									$eventImage = '/img/no-image-small.jpeg';
								}
							?>
							<img src="<?php echo $eventImage; ?>" alt="<?php echo $event['name']; ?>" width="236" height="273" />
							<?php // remove JoJo Maman Bébé
								$eventTitle = $event['name'];
								$jojostr = '/JoJo Maman Bébé /';
								$nojojoEventTitle = preg_replace($jojostr, '', $eventTitle);
							?>
							<h3><?php echo $nojojoEventTitle; ?></h3>
						</a>
						<p><?php echo strip_tags($event['short']); ?></p>
					</li>
					<?php } ?>
				<?php $x++; ?>
				<?php endforeach; ?>
			</ul>
		</section>	
	</article>