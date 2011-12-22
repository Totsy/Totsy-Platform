<?php use lithium\net\http\Router; ?>
<?php $request = $this->request(); ?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?php echo $this->html->charset();?>
	<title>
		<?php echo $this->title() ?: 'Totsy, the private sale site for Moms'; ?>
		<?php echo $this->title() ? '- Totsy' : ''; ?>
	</title>
	<meta property="fb:app_id" content="181445585225391"/>
	<meta property="og:site_name" content="Totsy"/>
    <meta name="description"
          content="Totsy has this super cool find available now and so much more for kids and moms! Score the best brands for your family at up to 90% off. Tons of new sales open every day. Membership is FREE, fast and easy. Start saving now!"/>
	<meta id="view-lock" name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	
	<link rel="stylesheet" href="/totsyMobile/themes/totsy.css">
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0rc2/jquery.mobile.structure-1.0rc2.min.css" /> 

	<script src="https://www.google.com/jsapi"></script>
	<script> google.load("jquery", "1.6.1", {uncompressed:false});</script>
	<script> google.load("jqueryui", "1.8.13", {uncompressed:false});</script>
	<!-- end jQuery / jQuery UI -->
	
	<script src="/totsyMobile/themes/js/totsyMobile.js"></script>
	<script src="http://code.jquery.com/mobile/1.0rc2/jquery.mobile-1.0rc2.min.js"></script>
	<?php echo $this->html->script('jquery.countdown.min.js?v=007'); ?>
	<?php echo $this->scripts(); ?>
</head>
<body>
<?php if (!empty($userInfo)){ ?>
<div data-role="header" style="-moz-box-shadow: 0px 0px 4px 0px #666;
-webkit-box-shadow: 0px 0px 4px 0px #666; box-shadow:0px 0px 4px 0px #666;"> 
	<div class="logo">
		<a href="#" onclick="window.location.href='/sales';return false;"><img src="/img/logo.png" width="60" /></a>
		<div style="float:right; margin-right:10px; font-size:12px; font-weight:normal!important; color:#999;">
		<?php if(array_key_exists('firstname', $userInfo) &&     !empty($userInfo['firstname'])):
					?>
					    <a href="#" onclick="window.location.href='/account';return false;"><?=$userInfo['firstname'].' '.$userInfo['lastname'] ?></a><br />
					<?php else: ?>
					    <a href="#" onclick="window.location.href='/account';return false;">Totsy Member</a><br />
					<?php endif;?>
					Cart: <a href="#" onclick="window.location.href='/cart/view';return false;"><?php echo $cartCount;?></a>
		</div>
	</div>
<div class="clear"></div>
<div data-role="navbar">
	<ul>
		<li><a href="#" onclick="window.location.href='/sales';return false;" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales') == 0) { echo 'class="ui-btn-active"'; } ?>>All</a></li>
		<li><a href="#" onclick="window.location.href='/sales/girls';return false;" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/girls') == 0) { echo 'class="ui-btn-active"'; } ?>>Girls</a></li>
		<li><a href="#" onclick="window.location.href='/sales/boys';return false;" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/boys') == 0) { echo 'class="ui-btn-active"'; } ?>>Boys</a></li>
		<li><a href="#" onclick="window.location.href='/sales/momsdads';return false;" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/momsdads') == 0) { echo 'class="ui-btn-active"'; } ?>>Parents</a></li>
	</ul>
</div><!-- /navbar -->		
<div class="clear"></div>
</div>

<?php } else { ?>
<div class="nav_head"></div>
	<div class="mobile_ui">
	<div class="logo">
		<a href="#" onclick="window.location.href='/sales';return false;"><img src="/img/logo.png" width="80" /></a>
	</div>	
	</div>
<?php } ?>
	<div data-role="content">
	<?php echo $this->content(); ?>
	</div>
	<div class="clear"></div>

	<p class="legal">&copy;2011 Totsy, Inc. All rights reserved.</p></div>
<script>
$.mobile.fixedToolbars
   .show(true);

$.base = '<?php echo rtrim(Router::match("/", $this->_request)); ?>';
</script>

</body>
</html>
