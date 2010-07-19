<!doctype html>
<html>
<head>
	<?php echo $this->html->charset();?>
	<title>Totsy<?php echo $this->title(); ?></title>
	<?=$this->html->script(array('jquery-1.4.2','jquery-ui-1.8.2.custom.min.js')); ?>
</head>
<body class="app">		
	<div>
		<div id="content">
			<?php echo $this->content(); ?>
		</div>
	</div>
</body>
</html>