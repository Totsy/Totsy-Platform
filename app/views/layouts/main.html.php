<!doctype html>
<html>
<head>
	<?php echo $this->html->charset();?>
	<title>Totsy<?php echo $this->title(); ?></title>
	<?php echo $this->html->style(array('base')); ?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
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
<body class="app" class="sec content my-account">
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
				<p class="clear"><span class="fl"><a href="#" id="credits" title="My Credits">My Credits</a> ($1,000)</span> <a href="#" id="cart" title="My Cart">Cart</a> <span class="fl">(1) <a href="#" id="checkout" title="Checkout">Checkout</a></span></p>
			</div>
		</div>	
		<div id="content">
				<?php echo $this->content(); ?>
		</div>
	</div>
	<div id="botter"></div>
	<div id="footer"><?php echo $bottomMenu;?></div>
	</body>	
</html>