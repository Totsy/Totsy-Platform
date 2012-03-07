<?php
	// @DG-2012.01.18 - this is the current/original woopsies/maintenance page… 
	// replaced with separate 500 (error) and 503 (maintenance) pages, which now live in /webroot/error
?>
<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Site Down For Maintenance - Totsy	</title>
	<link rel="stylesheet" type="text/css" href="/error/css/base.css" media="screen" />
	<link href="/img/favicon.ico" title="Icon" type="image/x-icon" rel="icon" />
	<link href="/img/favicon.ico" title="Icon" type="image/x-icon" rel="shortcut icon" />
</head>

<body class="app">
<div id="topper"></div>
	<div id="wrapper">
		<div id="header">
			<div id="header-lt">
				<a href="/" id="main-logo"><img src="/img/logo.png" width="155" height="90" alt="" /></a></div>
			</div>

	<div id="content">
		<h1>There was an error processing your request.</h1>
		<hr/>
		<p>Our engineers have been alerted and will work quickly to correct any issues.</p>
		<p>We're incredibly sorry for the inconvenience.</p>
		<p>Please feel free to contact support@totsy.com if the problem persists.</p>
		<br/>
	</div>

	<div align="center">
		<img src="/img/error-img.jpg" alt="" />
	</div>

	</div>
</div>

	<div id="botter"></div>

	<div id="footer">
		<span id="copyright">&copy; 2011 Totsy.com. All Rights Reserved.</span>
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