<?php use lithium\net\http\Router; ?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?php echo $this->html->charset();?>
	<title>
		<?php echo $this->title() ?: 'Totsy, the private sale site for Moms'; ?>
		<?php echo $this->title() ? '- Totsy' : ''; ?>
	</title>
	
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
	
	<?php echo $this->html->style(array('base.css', '960.css', 'jquery_ui_custom/jquery.ui.all.css'), array('media' => 'screen')); ?>

	<script src="http://www.google.com/jsapi"></script>
	<script> google.load("jquery", "1.6.1", {uncompressed:false});</script>
	<script> google.load("jqueryui", "1.8.13", {uncompressed:false});</script>
	<!-- end jQuery / jQuery UI -->

	<?php echo $this->html->script('jquery.uniform.min.js'); ?>
	<?php echo $this->html->script('jquery.countdown.min.js'); ?>
	<?php echo $this->scripts(); ?>
	<meta property="og:site_name" content="Totsy"/>
	<meta property="fb:app_id" content="181445585225391"/>
	<meta name="description" content="Totsy has this super cool find available now and so much more for kids and moms! Score the best brands for your family at up to 90% off. Tons of new sales open every day. Membership is FREE, fast and easy. Start saving now!"/>

</head>
<body class="app">
<?php echo $branch; ?>
<div class="container_16 roundy glow">
	<div class="grid_3 alpha" style="margin:5px 0px 0px 5px;">
		<?php echo $this->html->link($this->html->image('logo.png', array('width'=>'120')), '/sales', array('escape'=> false)); ?>
	</div>
	<?php echo $this->view()->render(array('element' => 'headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>
	
		<div class="menu_main_global">
		<?php if (!(empty($userInfo))): ?>
		<ul class="nav main" id="navlist">
			<li><a href="/sales" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales') == 0 || $_SERVER['REQUEST_URI'] == '/') {
			echo 'class="active"';
			} ?>>All Sales</a></li>
			<li><a href="/sales/girls" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/girls') == 0) {
			echo 'class="active"';
			} ?>>Girls</a></li>
			<li><a href="/sales/boys" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/boys') == 0)  {
			echo 'class="active"';
			} ?>>Boys</a></li>
			<li><a href="/sales/momsdads" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/momsdads') == 0) {
			echo 'class="active"';
			} ?>>Moms &amp; Dads</a></li>
		</ul>
		<?php endif ?>
	</div>
	<!-- end header nav -->
	
	<div class="container_16">
		<?php echo $this->content(); ?>
	</div>
	<!-- main content -->
</div>
<!-- end container_16 -->
	
	<div id="footer" class="container_16">
		<?php echo $this->view()->render(array('element' => 'footerNav'), array('userInfo' => $userInfo)); ?>
	</div>
	<!-- end footer nav -->

	<div class="container_16 clear" style="margin-top:50px;">
		<?php echo $this->view()->render(array('element' => 'footerIcons')); ?>
	</div>
	<!-- end footer icons -->

	<div id='toTop'>^ Top</div>

	<!--affiliate pixels-->
	<?php echo $pixel; ?>

<script type="text/javascript">
	$.base = '<?php echo rtrim(Router::match("/", $this->_request)); ?>';
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-675412-15']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
</script>
	
	<script language="javascript">
	document.write('<sc'+'ript src="http'+ (document.location.protocol=='https:'?'s://www':'://www')+ '.upsellit.com/upsellitJS4.jsp?qs=237268202226312324343293280329277309292309329331334326345325&siteID=6605"><\/sc'+'ript>')
	</script>
	<script type="text/javascript">
		// end uniform inputs
		$(document).ready(function() {
			$("input:file, select").uniform();
			$("#tabs").tabs();
		});
	
			$(function () {
				$(window).scroll(function () {
					if ($(this).scrollTop() != 0) {
						$('#toTop').fadeIn();
					} else {
						$('#toTop').fadeOut();
					}
				});
				$('#toTop').click(function () {
					$('body,html').animate({
						scrollTop: 0
					},
					800);
				});
			});
		// end back to top	
	
	// end tabs
</script>

</body>
</html>
