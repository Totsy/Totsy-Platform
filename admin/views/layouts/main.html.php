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
	use totsy_common\models\Menu;

	$options = array('div' => array('id' => 'main-nav'), 'ul' => array('class' => 'menu main-nav'));
	$topDoc = Menu::find('all', array('conditions' => array('location' => 'top', 'active' => 'true')));

	$mainMenu = $this->MenuList->build($topDoc, $options);
	$bottomOptions = array('ul' => array('class' => 'menu'));
	$bottomDoc = Menu::find('all', array('conditions' => array('location' => 'bottom', 'active' => 'true')));
	$bottomMenu = $this->MenuList->build($bottomDoc, $bottomOptions);


?>
<body class="sec content my-account app">
	<div id="topper"></div>
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

			</div>
			<div id="header-rt">

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