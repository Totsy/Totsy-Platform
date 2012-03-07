<?php use lithium\net\http\Router; ?>
<!doctype html>
<html>
<head>
	<?php echo $this->html->charset();?>
	<title>Totsy<?php echo $this->title(); ?></title>
	<?php echo $this->html->style(array('base')); ?>
	<?php echo $this->html->script(array(
		'jquery-1.4.2.min.js',
		'jquery-ui-1.8.2.custom.min.js',
		'jquery.backstretch.min.js'
	)); ?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>
<body class="app login">
	<?php echo $this->content(); ?>
</body>


	<?php

	use DirectoryIterator;
	use lithium\net\http\Media;
	$images = array();
	$imgDirectory = $this->_request->env('base') . '/img/login/';

	/**
	 * Get a random login image (of type jpg or png).
	 */
	foreach (new DirectoryIterator(Media::webroot(true) . '/img/login') as $file) {
		if ($file->isDot() || !preg_match('/\.(png|jpg)$/', $file->getFilename())) {
			continue;
		}
		$images[] = $file->getFilename();
	}
	$image = $images[array_rand($images)];


	?>


	<script type="text/javascript">

	    jQuery(document).ready(function($){

	    	$.backstretch("<?php echo $imgDirectory . $image;?>");

	    });

	</script>
</html>
