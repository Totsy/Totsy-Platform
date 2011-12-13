<style type="text/css">
	pre { display: inline; clear: none; }
	div { margin: 10px 5px; }
</style>
<h2>Something bad happened!</h2>

<p>Here's some info for the development team:</p>

<div>
	<strong><?php echo $info['type']; ?></strong>: <?php echo $info['message']; ?>
</div><div>
	Thrown in <strong><pre><?php echo $info['stack'][0] ?></pre></strong>
	at line <?php echo $info['line']; ?><br />
	(<pre><?php echo $info['file']; ?></pre>)
</div><div>
	<h4>Method parameters</h4>
	<pre><?php echo preg_replace(
		'/::__set_state\(/', '(', preg_replace(
			'/array\s+\(/', 'array(', var_export($params, true)
		)
	); ?></pre>
</div><div>
	<h4>JSON dump</h3>
	<?php echo json_encode($params); ?>
</div>