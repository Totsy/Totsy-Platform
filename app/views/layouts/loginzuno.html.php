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
	<?php echo $this->html->style(array('formcheck')); ?>
	<?php echo $this->html->style(array('base')); ?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>

<?=$this->html->script('mootools-1.2.4-core-nc.js');?>
<?=$this->html->script('mootools-1.2.4.4-more.js');?>

<?=$this->html->script('formcheck.js');?>
<?=$this->html->script('en.js');?>
<?=$this->html->style('formcheck');?>

<script type="text/javascript">
    window.addEvent('domready', function(){
        
        new FormCheck('loginForm');
                
    });
    
</script>

<body class="app login">
	
	<div id="fullscreen">

		<div id="login-box">
			
			<div id="login-box-border">
			
				<div id="login-box-container">	
					
					<!-- iFrame to load the login / registration process -->
					<iframe src="../login" width="100%" height="100%" frameborder="0" scrolling="no"></iframe>
				
				</div>
			
			</div>
			
		</div>
	
		<div id="bg">
			<img id="bg-img" class="activeslide" src="../img/home-bg-1.jpg" alt="" />
		</div>
		
	</div>
	
	<div id="footer">
	
		<ul>
			<li class="first"><a href="#" title="Terms of Use">Terms of Use</a></li>
			<li><a href="#" title="Privacy Policy">Privacy Policy</a></li>
			<li><a href="#" title="About Us">About Us</a></li>
			<li><a href="#" title="FAQ">FAQ</a></li>
			<li class="last"><a href="#" title="Contact Us">Contact Us</a></li>
		</ul>
		
		<span id="copyright">&copy; 2010 Totsy.com. All Rights Reserved.</span>
	
	</div>

</body>
</html>