<?php use lithium\net\http\Router; ?>
<!doctype html>
<html>
<head>
	<?=$this->html->charset();?>
	<title>
		<?=$this->title() ?: 'Totsy, the private sale site for Moms'; ?>
		<?=$this->title() ? '- Totsy' : ''; ?>
	</title>
	<?=$this->html->style(array('base'), array('media' => 'screen')); ?>
	<?=$this->html->script(array(
		'jquery-1.4.2.min.js',
		'jquery-ui-1.8.2.custom.min.js',
		'jquery.countdown.min'
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
	<div id="footer"><?=$this->menu->render('bottom'); ?></div>
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
	</body>	
</html>