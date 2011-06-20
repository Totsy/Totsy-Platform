<?php use lithium\net\http\Router; ?>
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
	<?php echo $this->html->style(array('base.css', '960.css', 'jquery_ui_custom/jquery.ui.all.css'), array('media' => 'screen')); ?>

	<script src="http://www.google.com/jsapi"></script>
	<script> google.load("jquery", "1.6.1", {uncompressed:false});</script>
	<script> google.load("jqueryui", "1.8.13", {uncompressed:false});</script>
	<!-- end jQuery / jQuery UI -->

    <?php echo $this->html->script(array('jquery.countdown.min.js','jquery.uniform.min.js')); ?>
	<?php echo $this->scripts(); ?>

	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
	<meta property="og:site_name" content="Totsy"/>
	<meta property="fb:app_id" content="181445585225391"/>
    <meta name="description"
          content="Totsy has this super cool find available now and so much more for kids and moms! Score the best brands for your family at up to 90% off. Tons of new sales open every day. Membership is FREE, fast and easy. Start saving now!"/>

</head>
<body class="app">
	<div class="container_16 roundy glow">
		<div class="grid_3 alpha" style="margin:5px 0px 0px 5px;">
				<?php echo $this->html->link($this->html->image('logo.png', array('width'=>'120')), '/sales', array('escape'=> false)); ?>
		</div>

			<div class="menu_top_left">
				<?php if (!empty($userInfo)): ?>
				Hello,
					<?php if(array_key_exists('firstname',$userInfo) && !empty($userInfo['firstname'])):?>
							<?php echo "{$userInfo['firstname']} {$userInfo['lastname']}"; ?>
					<?php else:?>
							<?php echo "{$userInfo['email']}"; ?>
					<?php endif; ?>
						<?php $logout = ($fblogout) ? $fblogout : 'Users::logout' ?>
						(<?php echo $this->html->link('Sign Out', $logout, array('title' => 'Sign Out')); ?>)
					<?php endif ?>
			</div>

			<div class="menu_top_right">
			<?php if (!(empty($userInfo))) { ?>
				<a href="/account" title="My Account">My Account</a>
				<?php if (!$credit == '0') { ?>
				<a href="/account/credits" title="My Credits $<?php echo $credit?>">My Credits $<?php echo $credit?></a>
				<?php } ?>
				<a href="/cart/view" class="cart_icon" title="My Cart (<?php echo $cartCount;?>)">My Cart (<?php echo $cartCount;?>)</a>
				<a href="/users/invite" title="+ Invite Friends Get $15">+ Invite Friends Get $15</a>
			<?php } else { ?>
				<span style="text-align:right!important;">
					<a href="/" title="Sign In">Sign In</a>
					<a href="/register" title="Sign Up">Sign Up</a>
					<a href="/users/invite" title="+ Invite Friends Get $15">+ Invite Friends Get $15</a>
				</span>
			<?php } ?>
			</div>

			<div class="menu_main_global">
			<?php if (!(empty($userInfo))): ?>
				<ul class="nav main" id="navlist">
					<li><a href="/sales" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales') == 0 || $_SERVER['REQUEST_URI'] == '/') {
		echo 'class="active"';
	} ?>>All Sales</a></li>
					<li><a href="/sales/girls" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/girls') == 0) {
	echo 'class="active"';
} ?>>Girls</a></li>
					<li><a href="/sales/boys" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/boys') == 0)  {
	echo 'class="active"';
} ?>>Boys</a></li>
					<li><a href="/sales/momsdads" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/momsdads') == 0) {
	echo 'class="active"';
} ?>>Moms &amp; Dads</a></li>
				</ul>
			<?php endif ?>
		</div>
		<!-- end header -->

		<div class="clear"></div>
		<?php echo $this->content(); ?>
		<!-- main content -->
</div>
<!-- end container_16 -->

	<div class="clear"></div>
	<div id="footer" class="container_16">
		<ul>
			<li class="first"><a href="/pages/terms" title="Terms of Use">Terms of Use</a></li>
			<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
			<li><a href="/pages/aboutus" title="About Us">About Us</a></li>
			<li><a href="http://blog.totsy.com" title="Blog" target="_blank">Blog</a></li>
			<li><a href="/pages/faq" title="FAQ">FAQ</a></li>
			<li><a href="/pages/affiliates" title="Affiliates">Affiliates</a></li>
			<?php if (empty($userInfo)){ ?>
			<li><a href="/pages/contact" title="Contact Us">Contact Us</a></li>
			<li class="last"><a href="http://nytm.org/made" title="Made in NYC" target="_blank">Made in NYC</a></li>
			<?php } else { ?>
			<li><a href="/tickets/add" title="Contact Us">Contact Us</a></li>
			<li class="last"><a href="http://nytm.org/made" title="Made in NYC" target="_blank">Made in NYC</a></li>
			<?php } ?>
			<li class="last" style="margin:0px 3px 0px 5px;"><a href="http://www.facebook.com/totsyfan" target="_blank"><img src="/img/icons/facebook_16.png" align="middle" /></a></li>
			<li class="last"><a href="http://twitter.com/MyTotsy" target="_blank"><img src="/img/icons/twitter_16.png" align="middle" /></a></li>
		</ul>
		</ul>
		<span>&copy;2011 Totsy.com. All Rights Reserved.</span>
	</div>

	<div class="container_16 clear" style="margin-top:50px;">
		<div class="prefix_7">
			<!-- begin thawte seal -->
		    <div id="thawteseal" title="Click to Verify - This site chose Thawte SSL for secure e-commerce and confidential communications.">
		    <div style="float: left!important; width:100px; display:block;"><script type="text/javascript" src="https://seal.thawte.com/getthawteseal?host_name=www.totsy.com&amp;size=L&amp;lang=en"></script></div>
		    <div style="float: left!important; width:60px; display:block;"><a href="http://www.ups.com/" target="_blank" title="Shipping provided by UPS"><img src="/img/ups_logo.png" /></a></div>
		    <div class="AuthorizeNetSeal" style="float: left!important; width:100px; display:block;"> <script type="text/javascript" language="javascript">var ANS_customer_id="98c2dcdf-499f-415d-9743-ca19c7d4381d";</script> <script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js" ></script></div>
		    </div>
		    <!-- end thawte seal -->
	    </div>
    </div>

    <div id='toTop'>^ Top</div>

     <!--affiliate pixels-->
    <?php echo $pixel; ?>

	<script type="text/javascript">
	$.base = '<?php echo rtrim(Router::match("/", $this->_request)); ?>';
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-675412-15']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	  // end google analytics

			$(function () {
				$(window).scroll(function () {
					if ($(this).scrollTop() != 0) {
						$('#toTop').fadeIn();
					} else {
						$('#toTop').fadeOut();
					}
				});
				$('#toTop').click(function () {
					$('body,html').animate({
						scrollTop: 0
					},
					800);
				});
			});
		// end back to top

	$("input:file,select").uniform();
	// end uniform inputs
	
		$(document).ready(function() {
		$("#tabs").tabs();
	});
	// end tabs
	</script>
</body>
</html>
