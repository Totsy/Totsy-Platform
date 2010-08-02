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
	<title>Totsy V2 <?php echo $this->title(); ?></title>
	<?php echo $this->html->style(array('base'), array('media' => 'screen')); ?>
	<?php echo $this->html->style(array('modal'), array('media' => 'screen')); ?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>
<body class="app">		
	<div>
		<div id="content">
			<?php echo $this->content(); ?>
		</div>
	</div>
</body>
</html>