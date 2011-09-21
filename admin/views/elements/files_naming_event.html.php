<?php

extract(array(
	'item' => null
), EXTR_SKIP);

?>
<div class="box files naming">
	<h2>Event Image Naming Conventions</h2>
	<div class="block">
		<?php if ($item): ?>
			<p>
				The following names are <strong>actual names</strong> and can be copied and pasted.
			</p>
			<?php $names = $item->uploadNames(); ?>
			<dl>
				<?php foreach ($names['form'] as $type => $name): ?>
					<dt><?=$type; ?></dt>
					<dd><?=$name; ?></dd>
				<?php endforeach; ?>
			</dl>
		<?php else: ?>
			<p>
				An event with the title <em>Pink T-Shirt</em>
				will have <em>pink-t-shirt</em> as the value for <em>URL</em>.
				Values for <em>URL</em> are always lowercased and contain no spaces.
			</p>
			<dl>
				<dt>Image</dt>
				<dd>events_URL.jpg <em>or</em></dd>
				<dd>events_URL_image.jpg</dd>

				<dt>Logo</dt>
				<dd>events_URL_logo.jpg</dd>

				<dt>Small Splash</dt>
				<dd>events_URL_small_splash.jpg <em>or</em></dd>
				<dd>events_URL_splash_small.jpg</dd>

				<dt>Big Splash</dt>
				<dd>events_URL_big_splash.jpg <em>or</em></dd>
				<dd>events_URL_splash_big.jpg</dd>
			</dl>
		<?php endif; ?>
	</div>
</div>