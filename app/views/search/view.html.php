<h1>Sorry, we couldn't find what you were looking for</h1>

<?php if (!count($events)) { ?>
	<p>Furthermore, we have no clue what you're talking about.
	<?php return; ?>
<?php } ?>
<?php var_dump($events->data()); ?>