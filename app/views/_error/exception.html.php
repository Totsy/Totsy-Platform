<style type="text/css">
	pre { display: inline; clear: none; }
	div { margin: 10px 5px; }
</style>
<h2>Something bad happened!</h2>

<p>Here's some info for the development team:</p>

<div>
	<strong><?=$info['type']; ?></strong>: <?=$info['message']; ?>
</div><div>
	Thrown in <strong><pre><?=$info['stack'][0] ?></pre></strong>
	at line <?=$info['line']; ?><br />
	(<pre><?=$info['file']; ?></pre>)
</div><div>
	<h4>Method parameters</h4>
	<pre><?=preg_replace(
		'/::__set_state\(/', '(', preg_replace(
			'/array\s+\(/', 'array(', var_export($params, true)
		)
	); ?></pre>
</div><div>
	<h4>JSON dump</h3>
	<?=json_encode($params); ?>
</div>