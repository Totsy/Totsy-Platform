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
	<meta name="viewport" content="width=device-width,user-scalable=no" />
	<?php echo $this->scripts(); ?>
	
	<link rel="stylesheet" href="/totsyMobile/themes/totsy.css">
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0rc2/jquery.mobile.structure-1.0rc2.min.css" /> 
	<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.0rc2/jquery.mobile-1.0rc2.min.js"></script>
</head>
<body>
<div data-role="header" data-position="fixed" style="-moz-box-shadow: 0px 0px 4px 0px #666;
-webkit-box-shadow: 0px 0px 4px 0px #666;
box-shadow:0px 0px 4px 0px #666;"> 
	<div class="logo">
		<a href="#" onclick="window.location.href='/sales';return false;"><img src="/img/logo.png" width="60" /></a>
		<div style="float:right; clear:both; margin-right:5px; font-size:12px; font-weight:normal!important; color:#999;"><?php if (!empty($userInfo)): ?>
		<?php if(array_key_exists('firstname',$userInfo) && !empty($userInfo['firstname'])):?>
		<?php echo "{$userInfo['firstname']} {$userInfo['lastname']}"; ?>
		<?php else:?>
		<?php if (is_array($userInfo) && array_key_exists('email', $userInfo)) { echo $userInfo['email']; } ?>
		<?php endif; ?>
		<?php $logout = ($fblogout) ? $fblogout : 'Users::logout' ?>
		<a href="#" onclick="window.location.href='/logout';return false;">Sign Out</a>
		<?php endif ?><br style="margin:2px;"/>
		Cart: <a href="#" onclick="window.location.href='/cart/view';return false;"><span class="ui-li-count"><?php echo $cartCount;?></span></a>
		</div>
	</div>
<div data-role="navbar">
	<ul>
		<li><a href="#" onclick="window.location.href='/sales';return false;" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales') == 0) { echo 'class="ui-btn-active"'; } ?>>All</a></li>
		<li><a href="#" onclick="window.location.href='/sales/girls';return false;" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/girls') == 0) { echo 'class="ui-btn-active"'; } ?>>Girls</a></li>
		<li><a href="#" onclick="window.location.href='/sales/boys';return false;" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/boys') == 0) { echo 'class="ui-btn-active"'; } ?>>Boys</a></li>
		<li><a href="#" onclick="window.location.href='/sales/momsdads';return false;" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/momsdads') == 0) { echo 'class="ui-btn-active"'; } ?>>Parents</a></li>
	</ul>
</div><!-- /navbar -->		

</div>
	<div class="mobile_ui">
		<?php echo $this->content(); ?>
	</div>
	<div class="clear"></div>
	<div style="margin-bottom:35px; clear:both;"></div>
		<h2 style="margin-left:5px;">My Account</h2>
		<hr />
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>

	<div class="footer">
		<a href="#" onclick="window.location.href='/pages/aboutus';return false;">About</a>
		<span class="splitter">/</span>
		<a href="#" onclick="window.location.href='/pages/privacy';return false;">Privacy</a>
		<span class="splitter">/</span>
		<a href="#" onclick="window.location.href='/pages/terms';return false;">Terms</a>
		<span class="splitter">/</span>
		<a href="#" onclick="window.location.href='/pages/contact';return false;">Support</a>
	</div>
	<p class="legal">&copy;2011 Totsy, Inc. All rights reserved.</p>
</div>
<script>
$.mobile.fixedToolbars
   .show(true);
</script>
</body>
</html>
