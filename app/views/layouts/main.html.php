<!doctype html>
<html>
<head>
	<?=$this->html->charset();?>
	<title>
		<?=$this->title() ?: 'Totsy, the private sale site for Moms'; ?>
		<?=$this->title() ? '- Totsy' : ''; ?>
	</title>
	<?=$this->html->style(array('base')); ?>
	<?=$this->html->script(array('jquery-1.4.2','jquery-ui-1.8.2.custom.min.js', 'jquery.countdown.min')); ?>
	<?=$this->scripts(); ?>
	<?=$this->html->link('Icon', null, array('type' => 'icon')); ?>
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
</head>

<?php
	use app\models\Menu;

	$options = array('div' => array('id' => 'main-nav'), 'ul' => array('class' => 'menu main-nav'));
	$topDoc = Menu::find('all', array('conditions' => array('location' => 'top', 'active' => 'true')));
	
	$mainMenu = $this->MenuList->build($topDoc, $options);
	$bottomOptions = array('ul' => array('class' => 'menu'));
	$bottomDoc = Menu::find('all', array('conditions' => array('location' => 'bottom', 'active' => 'true')));
	$bottomMenu = $this->MenuList->build($bottomDoc, $bottomOptions);
?>	

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
				<?=$this->html->link('At Your Service', '', array('id' => 'cs')); ?>
				<div id="welcome">
					<strong>Hello!</strong>
					<?php if(isset($userInfo['firstname'])) { ?>
						<?="{$userInfo['firstname']} {$userInfo['lastname']}"; ?>
					<?php }?>
					(<?=$this->html->link('Sign Out', 'Users::logout', array('title' => 'Sign Out')); ?>)
				</div>
				<?php echo $mainMenu; ?>
			</div>
			<div id="header-rt">
				<?=$this->html->link('Invite Friends. Get $15','',array('title'=>'Invite Friends. Get $15', 'id'=>'if'));?>	
				<p class="clear">
					<span class="fl">
						<a href="#" id="credits" title="My Credits">My Credits</a>
						($100)
					</span>
					<?=$this->html->link('Cart', '#', array(
						'id' => 'cart', 'title' => 'My Cart'
					)); ?>
					<span class="fl">
						(<?=$cartCount;?>)
						<?=$this->html->link('Checkout', array('Transactions::add'), array(
							'id' => 'checkout', 'title' => 'checkout'
						)); ?>
					</span>
				</p>
			</div>
		</div>	
		<div id="content">
			<?php echo $this->content(); ?>
		</div>
	</div>
	<div id="botter"></div>
	<div id="footer"><?php echo $bottomMenu; ?></div>
	<div id='cart-modal'></div>
	<script type="text/javascript">
	$("#cart").click(function() {
		$("#cart-modal").load('/cart/view').dialog({
			autoOpen: false,
			modal:true,
			width: 900,
			height: 600,
			close: function(ev, ui) { location.reload(true); }
		});
		$("#cart-modal").dialog('open');

	});
	</script>
	</body>	
</html>