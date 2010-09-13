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
	<title>Totsy Admin <?php echo $this->title(); ?></title>
	
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
	
	<?php
		// add the name of the style sheets you want to include here.
		$styles = array('reset', 'debug', 'text', '960', 'ie6', 'ie', 'layout', 'nav', 'flash', 'custom');
		foreach ($styles as $css):
			if (file_exists("css/".$css.".css")) { // files are in APP/webroot
				switch ($css) {
					// to add a local browser specific css file. add them here with a case for the name of the file
					case "ie6":
						echo '<!--[if IE 6]>'.$this->html->style($css).'<![endif]-->';
					case "ie":
						echo '<!--[if IE 7]>'.$this->html->style($css).'<![endif]-->';
					default:
						echo $this->html->style($css);
				}
			} 
		endforeach; 
		// end checking for css files

		// add the name of the scripts you want to include here.
		$script = array('jquery-1.3.2.min', 'jquery-fluid16', 'jquery-ui');
		foreach ($script as $js):
			if (file_exists("js/".$js.".js")) { // files are in APP/webroot
				echo $this->html->script($js);
			} else { // files are in li3_grid
				echo $this->html->script('../../li3_grid/js/'.$js);
			}
		endforeach; 
		// end checking for script files
	?>
	
</head>
<body>
	<div class="container_16">
		<!-- HEADER -->
		<div id="header">
			<div class="grid_3">
				<?php echo $this->html->image('logo.png', array('id' => 'headerimage')); ?>
			</div>
			<div class="14">
				<h1 id="sitetitle"></h1>
			</div>
		</div>
		<!-- END HEADER -->
		
		<div class="clearfix"></div>
	
		<div id="navigation">
			<?php echo $this->view()->render(array('element' => 'navigation')); ?>
		</div>
		
		<div class="clearfix"></div>
		
		<div id="content">
			<?php //echo $this->flashMessage->output(); ?>
			<?php echo $this->content(); ?>
		</div>
		
		<div class="clearfix"></div>
		
	</div>

	<!-- END FOOTER -->

</body>
</html>