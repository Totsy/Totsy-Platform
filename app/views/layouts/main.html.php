<?php use lithium\net\http\Router; ?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?=$this->html->charset();?>
	<title>
		<?=$this->title() ?: 'Totsy, the private sale site for Moms'; ?>
		<?=$this->title() ? '- Totsy' : ''; ?>
	</title>
	<?=$this->html->style(array('base.css'), array('media' => 'screen')); ?>
	<?=$this->html->script(array(
		'jquery-1.4.2.min.js',
		'jquery-ui-1.8.2.custom.min.js',
		'jquery.countdown.min.js'
	)); ?>
	<?=$this->scripts(); ?>
	<?=$this->html->link('Icon', null, array('type' => 'icon')); ?>
	<meta property="og:site_name" content="Totsy"/>
	<meta property="fb:app_id" content="181445585225391"/>
    <meta name="description"
          content="Totsy has this super cool find available now and so much more for kids and moms! Score the best brands for your family at up to 90% off. Tons of new sales open every day. Membership is FREE, fast and easy. Start saving now!"/>
</head>
<body class="app">
<!--
<div id="global_site_msg"><strong>Last minute message:</strong> our last promotional campaign that was intended for a select audience of our long-time members was unintentionally exposed to the general public. <br />This promotion has now been restored and will only work for members who received an email directly from Totsy containing a promocode.</div>
-->
<?php echo $branch; ?>
<div id="topper"></div>
	<div id="wrapper">
		<div id="header">
			<div id="header-lt">
				<?=$this->html->link(
					$this->html->image('logo.png', array('width'=>'155', 'height'=>'90')), '/sales', array(
						'id' => 'main-logo', 'escape'=> false
					)
				); ?>
			</div>

			<div class="menu_top_left">
			<?php if (!empty($userInfo)): ?>
			Hello, <?php if(array_key_exists('firstname',$userInfo) && !empty($userInfo['firstname'])):
					?>
						<?="{$userInfo['firstname']} {$userInfo['lastname']}"; ?>
					<?php else:?>
						<?="{$userInfo['email']}"; ?>
					<?php endif; ?>
					<?php $logout = ($fblogout) ? $fblogout : 'Users::logout' ?>
					(<?=$this->html->link('Sign Out', $logout, array('title' => 'Sign Out')); ?>)
			<?php endif ?>
			</div>

			<div class="menu_top_right">
			<?php if (!(empty($userInfo))) { ?>
				<a href="/account" title="My Account">My Account</a>
				<a href="/account/credits" title="My Credits $<?=$credit?>">My Credits $<?=$credit?></a>
				<a href="/cart/view" class="cart_icon" title="My Cart (<?=$cartCount;?>)">My Cart (<?=$cartCount;?>)</a>
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
		</div>
		<div style="clear:both;"></div>

		<div id="content">
			<?php echo $this->content(); ?>
		</div>
	</div>
	<div id="botter"></div>
	<div id="footer">
		<ul>
			<li class="first"><a href="/pages/terms" title="Terms of Use">Terms of Use</a></li>
			<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
			<li><a href="/pages/aboutus" title="About Us">About Us</a></li>
			<li><a href="http://blog.totsy.com" title="Blog" target="_blank">Blog</a></li>
			<li><a href="/pages/faq" title="FAQ">FAQ</a></li>
			<li><a href="/pages/affiliates" title="Affiliates">Affiliates</a></li>

			<! -- switch where this link points depending on whether they're logged in or not -->
		<?php if (empty($userInfo)){ ?>
		<li><a href="/pages/contact" title="Contact Us">Contact Us</a></li>
		<li class="last"><a href="http://nytm.org/made" title="Made in NYC" target="_blank">Made in NYC</a></li>
		<?php } else { ?>
		<li><a href="/tickets/add" title="Contact Us">Contact Us</a></li>
		<li class="last"><a href="http://nytm.org/made" title="Made in NYC" target="_blank">Made in NYC</a></li>
		<?php } ?>
		</ul>
		<span id="copyright">&copy; 2011 Totsy.com. All Rights Reserved.</span>
	</div>
	<script type="text/javascript">
		$.base = '<?=rtrim(Router::match("/", $this->_request)); ?>';
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-675412-15']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	</script>

    <script type="text/javascript">
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
		</script>

    	<div id='cart-modal'></div>
    <div id='toTop'>^ Back to Top</div>

    <!--affiliate pixels-->
    <?php echo $pixel; ?>
	</body>
</html>