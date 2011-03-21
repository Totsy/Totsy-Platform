<?php use lithium\net\http\Router; ?>
<!doctype html>
<html>
<head>
	<?=$this->html->charset();?>3
	<title>
		<?=$this->title() ?: 'Totsy, the private sale site for Moms'; ?>
		<?=$this->title() ? '- Totsy' : ''; ?>
	</title>
	<?=$this->html->style(array('base.css?v=012346'), array('media' => 'screen')); ?>
	<?=$this->html->script(array(
		'jquery-1.4.2.min.js?v=012346',
		'jquery-ui-1.8.2.custom.min.js?v=012346',
		'jquery.countdown.min.js?v=012346'
	)); ?>
	<?=$this->scripts(); ?>
	<?=$this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>
<body class="app">

<!-- ClickTale Top part -->
<script type="text/javascript">
var WRInitTime=(new Date()).getTime();
</script>
<!-- ClickTale end of Top part -->


<!-- 
<div id="global_site_msg"><strong>Last minute message:</strong> our last promotional campaign that was intended for a select audience of our long-time members was unintentionally exposed to the general public. <br />This promotion has now been restored and will only work for members who received an email directly from Totsy containing a promocode.</div>
-->
<div id="topper"></div>

	<div id="wrapper">

		<div id="header">

			<div id="header-lt">
				<?=$this->html->link(
					$this->html->image('logo.png', array('width'=>'155', 'height'=>'90')), '', array(
						'id' => 'main-logo', 'escape'=> false
					)
				); ?>
			</div>

			<div id="header-mid">

				<?php if (!empty($userInfo)): ?>
					<?=$this->html->link('Help Desk', 'Tickets::add', array('id' => 'cs')); ?>
				<div id="welcome">
				Hello,
					<?php if(array_key_exists('firstname',$userInfo) && !empty($userInfo['firstname'])):
					?>
						<?="{$userInfo['firstname']} {$userInfo['lastname']}"; ?>
					<?php else:?>
					    <?="{$userInfo['email']}"; ?>
					<?php endif; ?>
					(<?=$this->html->link('Sign Out', 'Users::logout', array('title' => 'Sign Out')); ?>)
				</div>

				<?php endif ?>
				<?php if (!(empty($userInfo))): ?>
					<?=$this->menu->render('main-nav'); ?>
				<?php endif ?>
			</div>
			<div id="header-rt">
				<?=$this->html->link('Invite Friends. Get $15','/users/invite',array('title'=>'Invite Friends. Get $15', 'id'=>'if'));?>
				<?php if (!empty($userInfo)): ?>
					<p class="clear">
						<span class="fr">
							(<?=$cartCount;?>)
							<?=$this->html->link('Checkout', array('Orders::add'), array(
								'id' => 'checkout', 'title' => 'checkout'
							)); ?>
			 			</span>
						<span class="fr"><?=$this->html->link('Cart', array('Cart::view'), array(
							'id' => 'cart', 'title' => 'My Cart'
						)); ?></span>
			 			<span class="fr">
							<?=$this->html->link('My Credits', array('Credits::view')); ?>
							<?php if (!empty($credit)): ?>
								($<?=$credit?>)
							<?php endif ?>
						</span>
					</p>
				<?php endif ?>
			</div>
		</div>
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
			<li class="last"><a href="/pages/contact" title="Contact Us">Contact Us</a></li>
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

	<script type="text/javascript">
	/*$("#cart").click(function() {
		$("#cart-modal").load($.base + 'cart/view').dialog({
			autoOpen: false,
			modal:true,
			width: 900,
			//height: 600,
			close: function(ev, ui) {
				location.reload();
			}
		});
		$("#cart-modal").dialog('open');
	}); */
	</script>

    <div id='toTop'>^ Back to Top</div>

    <!--affiliate pixels-->
    <?php echo $pixel; ?>
    
    
	    <!-- ClickTale Bottom part -->
	<div id="ClickTaleDiv" style="display: none;"></div>
	<script type='text/javascript'>
	document.write(unescape("%3Cscript%20src='"+
	 (document.location.protocol=='https:'?
	  'https://clicktale.pantherssl.com/':
	  'http://s.clicktale.net/')+
	 "WRb6.js'%20type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
	var ClickTaleSSL=1;
	if(typeof ClickTale=='function') ClickTale(17040,1,"www02");
	</script>
	<!-- ClickTale end of Bottom part -->
	</body>
</html>