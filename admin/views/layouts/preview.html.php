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
		'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js',
		'jquery-ui-1.8.2.custom.min.js',
		'jquery.countdown.min'
	)); ?>
	<?=$this->scripts(); ?>
	<?=$this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>

<body class="app">
	<center><h1 id=""><?=$preview?> Preview Page - <?=$this->html->link('CLICK TO EDIT', array("$preview::edit", 'args' => $id)); ?></h1></center>
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
				<?=$this->html->link('Help Desk', 'Tickets::add', array('id' => 'cs')); ?>
				<div id="welcome">
					<strong>Hello!</strong>
					Totsy User
					(<?=$this->html->link('Sign Out', '#', array('title' => 'Sign Out')); ?>)
				</div>

			</div>
			<div id="header-rt">
				<?=$this->html->link('Invite Friends. Get $15','#',array('title'=>'Invite Friends. Get $15', 'id'=>'if'));?>
				<p class="clear">
					<span class="fl">
						<?=$this->html->link('My Credits', '#'); ?>
						($0)
					</span>
					<?=$this->html->link('Cart', '#', array(
						'id' => 'cart', 'title' => 'My Cart'
					)); ?>
					<span class="fl">
						(0)
						<?=$this->html->link('Checkout', '#', array(
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
	</body>	
</html>