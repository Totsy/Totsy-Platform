<?php use lithium\net\http\Router; ?>
<!doctype html>
<html>
<head>
	<?php echo $this->html->charset();?>
	<title>Totsy<?php echo $this->title(); ?></title>
	<?php echo $this->html->script(array('jquery-1.4.2','jquery-ui-1.8.2.custom.min.js')); ?>
	<script type="text/javascript">
		$.base = '<?php echo rtrim(Router::match("/", $this->_request)); ?>';
	</script>
</head>
<body class="app">		
	<div>
		<div id="content">
			<?php echo $this->content(); ?>
		</div>
	</div>
</body>
</html>