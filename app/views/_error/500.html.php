<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Woopsies - Totsy [500 Internal Server Error]</title>
	<?php echo '<link rel="stylesheet" type="text/css" href="/css/base.css?' . filemtime(LITHIUM_APP_PATH . '/webroot/css/base.css') . '" />'; ?>
	<?php echo '<link rel="stylesheet" type="text/css" href="/css/960.css?' . filemtime(LITHIUM_APP_PATH . '/webroot/css/960.css') . '" />'; ?>
	<link href="/img/favicon.ico" title="Icon" type="image/x-icon" rel="icon" />
	<link href="/img/favicon.ico" title="Icon" type="image/x-icon" rel="shortcut icon" />
</head>

<body class="app p-status p-status-500">

	<div id="totsy" class="container_16 roundy glow">
		<header>
			<h1><em>Totsy - </em>Our best people are on this!</h1>
		</header>
		<section>
			<h2>Yo, Dude! Fix the localhost assignment in app/config/bootstra/connections and this automatic FAIL will cease...</h2>
			<p>Unfortunately, our youngster got a little rambunctious and needed a time-out, so the site is down at the moment. (Growing pains are never easy, are they?)</p>
			<p>We apologize for any inconvenience you may be experiencing. Please don't fret. We'll be back as quick as we can. We promise!</p>
			<p>In the meantime, feel free to contact <a href="mailto:support@totsy.com" title="Email Totsy Support">support@totsy.com</a> if you'd like more information, or if you just miss us.</p>
		</section>
	
	</div>

	<div id="footer">
		<span id="copyright">&copy; <?php echo date(Y) ?> Totsy.com. All Rights Reserved.</span>
	</div>

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
	</body>
</html>
