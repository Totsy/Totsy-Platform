<?php use lithium\net\http\Router; ?>
<!doctype html>
<html>
<head>
	<?=$this->html->charset();?>
	<title>
		<?=$this->title() ?: 'Totsy, the private sale site for Moms'; ?>
		<?=$this->title() ? '- Totsy' : ''; ?>
	</title>
	<?=$this->html->style(array('base.css?v=012345'), array('media' => 'screen')); ?>
	<?=$this->html->script(array(
		'jquery-1.4.2.min.js?v=012345',
		'jquery-ui-1.8.2.custom.min.js?v=012345',
		'jquery.countdown.min.js?v=012345'
	)); ?>
	<?=$this->scripts(); ?>
	<?=$this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>

<body class="app">
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
				<?php endif ?>
				<div id="welcome">
					<?php if(isset($userInfo['firstname'])) { ?>
						<strong>Hello!</strong>
						<?="{$userInfo['firstname']} {$userInfo['lastname']}"; ?>
						(<?=$this->html->link('Sign Out', 'Users::logout', array('title' => 'Sign Out')); ?>)
					<?php }?>
				</div>
				<?php if (!(empty($userInfo))): ?>
					<?=$this->menu->render('main-nav'); ?>
				<?php endif ?>
			</div>
			<div id="header-rt">
				<?=$this->html->link('Invite Friends. Get $15','/Users/invite',array('title'=>'Invite Friends. Get $15', 'id'=>'if'));?>
				<?php if (!empty($userInfo)): ?>
					<p class="clear">
						<span class="fr">
							(<?=$cartCount;?>)
							<?=$this->html->link('Checkout', array('Orders::add'), array(
								'id' => 'checkout', 'title' => 'checkout'
							)); ?>
			 			</span>
						<?=$this->html->link('Cart', '#', array(
							'id' => 'cart', 'title' => 'My Cart'
						)); ?>
			 			<span class="fr">
							<?=$this->html->link('My Credits', array('Pages::credits')); ?>
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
			<li><a href="/blog" title="Blog">Blog</a></li>
			<li><a href="/pages/faq" title="FAQ">FAQ</a></li>
			<li class="last"><a href="/pages/contact" title="Contact Us">Contact Us</a></li>
		</ul>
		<span id="copyright">&copy; 2010 Totsy.com. All Rights Reserved.</span>
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
	$("#cart").click(function() {
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
	});
	</script>
	
    <div id='toTop'>^ Back to Top</div>
    

	</body>	
</html>