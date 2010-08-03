<?php use lithium\net\http\Router; ?>
<!doctype html>
<html>
<head>
	<?php echo $this->html->charset();?>
	<title>Totsy<?php echo $this->title(); ?></title>
	<?php echo $this->html->style(array('base'), array('media' => 'screen')); ?>
	<?=$this->html->script(array(
		'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
		'jquery-ui-1.8.2.custom.min.js',
		'jquery.backstretch.min.js'
	)); ?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-675412-15']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
</head>
<body class="app login">
	<?php echo $this->content(); ?>
</body>


	<?php

	use \DirectoryIterator;
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

	    	$.backstretch("<?=$imgDirectory . $image;?>");

	    });

	</script>
</html>
