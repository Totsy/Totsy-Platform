<?php var_dump($weekRevenue, $monthsRevenue, $weekRegistrations, $monthsRegistrations) ?>

<?php foreach ($weekRevenue['retval'] as $key => $day): ?>
	<?php var_dump($day, date("d", time())); ?>
	<?php if ($day['date'] == date("d", time())): ?>
		<p>Today's Revenue (as of <?php date("F j, Y, g:i a", time()); ?>)</p>: <?=$day['total']?>
	<?php else: ?>
		<?=$day['date']?> - <?=$day['total']?>
	<?php endif ?>
<?php endforeach ?>