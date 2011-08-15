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
	<?php echo $this->html->style(array('admin')); ?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>

<?php
	use admin\models\Menu;

	$options = array();
	$topDoc = Menu::find('all', array('conditions' => array('location' => 'admin-top', 'active' => 'true')));
	var_dump($topDoc->data());
	$mainMenu = $this->MenuList->build($topDoc, $options);
	
?>

<body class="sec content my-account app">
	<div id="wrapper">
		<div id="header">
			<div id="header-lt">
				<?=$this->html->link($this->html->image('logo.png', array(
						'width'=>'155',
						'height'=>'90'
					)),
					'admin.totsy.com',
					array(
						'id'=>'main-logo',
						'escape'=>false
					));?>
			</div>
			<div id="header-mid">
				<div id="welcome">
					<strong>Hello! </strong><?php if(isset($userInfo['firstname'])){echo $userInfo['firstname']." ".$userInfo['lastname'];};?> (<?=$this->html->link('Sign Out',array(
							'controller' => 'users','action'=>'logout'),
							array('title'=>'Sign Out'));?>	)
				</div>
				<?php echo $mainMenu;?>
			</div>
			<div id="header-rt">
			</div>
		</div>
		<div id="content">
				<?php echo $this->content(); ?>
				<br><br>
		</div>
	</div>
	<div id="botter"></div>
	<div id="footer"></div>

	</body>
</html>