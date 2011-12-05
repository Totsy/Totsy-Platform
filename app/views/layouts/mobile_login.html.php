<?php use lithium\net\http\Router; ?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?php echo $this->html->charset();?>
	<title>Totsy, the private sale site for Moms</title>
	
	<meta name="description"
	content="Totsy has this super cool find available now and so much more for kids and moms! Score the best brands for your family at up to 90% off. Tons of new sales open every day. Membership is FREE, fast and easy. Start saving now!"/>
	<meta name="viewport" content="width=device-width,user-scalable=no" />

	<?php echo $this->scripts(); ?>

	<link rel="stylesheet" href="/totsyMobile/themes/totsy.css">
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0rc2/jquery.mobile.structure-1.0rc2.min.css" /> 
	<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.0rc2/jquery.mobile-1.0rc2.min.js"></script>

</head>
<body>
<div class="nav_head"></div>
	<div class="mobile_ui">
	<div class="logo">
		<a href="#" onclick="window.location.href='/sales';return false;"><img src="/img/logo.png" width="80" /></a>
	</div>	
	<div data-role="content">
	<?php echo $this->content(); ?>
	</div>
	<?php if (!empty($userInfo)){ ?>
	<div class="footer">
		<a href="#" onclick="window.location.href='/pages/aboutus';return false;">About</a>
		<span class="splitter">/</span>
		<a href="#" onclick="window.location.href='/pages/privacy';return false;">Privacy</a>
		<span class="splitter">/</span>
		<a href="#" onclick="window.location.href='/pages/terms';return false;">Terms</a>
		<span class="splitter">/</span>
		<a href="#" onclick="window.location.href='/pages/contact';return false;">Support</a>
	</div>
	<?php } ?>

	<p class="legal">&copy;2011 Totsy, Inc. All rights reserved.</p>
</div>
</body>
</html>
