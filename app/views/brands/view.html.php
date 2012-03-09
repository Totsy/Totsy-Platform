<?php
	if ((empty($userInfo))) { ?>
		<div id="jojoLoggedOut">
			<p class="signin">Already a member? <a href="/login" title="Sign In">Sign in here</a></p>
			<p class="signup group">
				<strong><em>Up to 90% off retail</em> for mom, baby, and child.</strong>
				<a title="Register a Totsy account" class="btn" href="<?php echo $registerlink ?>"><strong>Join Now</strong></a>
			</p>
		</div>
		<article id="jojo" class="public">
<?php } else { ?>
	<article id="jojo">
<?php } ?>
		<h2>Totsy welcomes JoJo Maman B&eacute;b&eacute; for a special 10 day event</h2>
		<section>
			<header class="group">
				<aside class="video">
					<?php // use iframe embed code rather than object method for better mobile support ?>
					<iframe width="400" height="233" src="http://www.youtube.com/embed/ChiuuSVFRNo?rel=0" frameborder="0" allowfullscreen></iframe>
				</aside>
				<section>
					<p>Totsy is proud to be the exclusive flash site to host the U.S. debut of JoJo Maman B&eacute;b&eacute;’s Spring 2012 collection. JoJo Maman B&eacute;b&eacute; is a London-based brand of baby and children’s apparel and chic fashions for pregnant and nursing moms. JoJo’s dedication to exquisite quality, high-minded style and thoughtful innovation has earned them distinction as one of the most celebrated companies in the UK.</p>
					<p>In 1993, years before becoming a Maman herself, fashion maven and JoJo founder Laura Tenison observed how few high-quality clothing options there were for growing families. Why should stylish pregnant and nursing women be forced to wear something frumpy? And why were children’s clothes so impractical and just plain boring?</p>
					<p>Nineteen years and 45 stores later, JoJo Maman B&eacute;b&eacute; is the premiere destination for smart and savvy families. Pregnant and nursing women will find sophisticated pieces such as the bestselling wrap dress with discreet nursing functions, sporty sweaters with smoothing and supportive stretch, plus breezy linen trousers and slimming shorts, just in time for spring. For babies and children, JoJo offers adorable playwear, dress-up duds, beach gear and more, all constructed from only high-quality, breathable fabrics, designed with such practical elements as reversible sides, extra zippers for easy changing, laser-cut tags, flat seams and fun patterns. </p>
				</section>

			</header>
			
			<ul class="bp-events group">
				<?php $x = 0; ?>
				<?php foreach ($openEvents as $event): ?>
					<?php if($x<6){ ?>
					<li>
<?php if ((empty($userInfo))) { ?>
						<a href="<?php echo $registerlink ?>" title="Go to <?php echo $event['name']; ?> sale">
<?php } else { ?>
						<a href="<?php echo '/sale/' . $event['url'];?>" title="Go to <?php echo $event['name']; ?> sale">
<?php } ?>
							<?php // image check and store
								if($event['images']['splash_small_image'] !== null) { 
									$eventImage = "/image/" . $event['images']['splash_small_image'] . ".jpg";
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
						<p><?php echo $event['short']; ?></p>
					</li>
					<?php } ?>
				<?php $x++; ?>
				<?php endforeach; ?>
			</ul>
		</section>	
	</article>