<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */
?>
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
	if(isset($_SESSION)) {
		$array = array($_SESSION['firstname'], $_SESSION['lastname']);
		$name = implode(" ", $array);
	} 
?>
<?php
	use app\extensions\helper\Menu;
	use app\models\Navigation;

	$options = array('div' => array('id' => 'main-nav'), 'ul' => array('class' => 'menu main-nav'));
	$doc = Navigation::find('all', array('conditions' => array('location' => 'top', 'active' => 'true')));

	$mainMenu = Menu::build($doc, $options);
	
	$options = array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'));
	$doc = Navigation::find('all', array('conditions' => array('location' => 'left', 'active' => 'true')));

	$subMenu = Menu::build($doc, $options);



?>	
<body class="app" class="sec content my-account">
	<div id="topper"></div>	
	<div id="wrapper">
		<div id="header">
			<div id="header-lt">
				<?=$this->html->link($this->html->image('logo.png', array(
						'width'=>'155', 
						'height'=>'90'
					)),
					'',
					array(
						'id'=>'main-logo', 
						'escape'=>false
					));?>
			</div>
			<div id="header-mid">
				<?=$this->html->link('At Your Service','',array('id'=>'cs'));?>	
				<div id="welcome">
					<strong>Hello! </strong><?php echo $name;?> (<?=$this->html->link('Sign Out',array(
							'controller' => 'users','action'=>'logout'),
							array('title'=>'Sign Out'));?>	)				
				</div>
				<?php echo $mainMenu;?>
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
	</body>	
</html>