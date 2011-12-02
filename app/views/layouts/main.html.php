<?php use lithium\net\http\Router; ?>
<?php $request = $this->request(); ?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?php echo $this->html->charset();?>
 	<title>
		<?php echo $this->title() ?: 'Totsy, the private sale site for Moms'; ?>
		<?php echo $this->title() ? '- Totsy' : ''; ?>
	</title>
	
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
	
	<?php echo $this->html->style(array('base.css?v=007', '960.css?v=007', 'jquery_ui_custom/jquery.ui.all.css?v=007'), array('media' => 'screen')); ?>

	<script src="https://www.google.com/jsapi"></script>
	<script> google.load("jquery", "1.6.1", {uncompressed:false});</script>
	<script> google.load("jqueryui", "1.8.13", {uncompressed:false});</script>
	<!-- end jQuery / jQuery UI -->

	<?php echo $this->html->script('jquery.uniform.min.js?v=007'); ?>
	<?php echo $this->html->script('jquery.countdown.min.js?v=007'); ?>
	<?php echo $this->scripts(); ?>
	<meta property="og:site_name" content="Totsy"/>
	<meta property="fb:app_id" content="181445585225391"/>
	<meta name="description" content="Totsy has this super cool find available now and so much more for kids and moms! Score the best brands for your family at up to 90% off. Tons of new sales open every day. Membership is FREE, fast and easy. Start saving now!"/>
	<meta name="sailthru.date" content="<?=date('r')?>" /><?php
	 
		if(substr($request->url,0,5) == 'sales' || $_SERVER['REQUEST_URI'] == '/') {
			$title = "Totsy index. Evenets.";
			$tags = 'Sales'; 
			if (array_key_exists ('args',$request->params) && isset($request->params['args'][0])){
				$tags =  $request->params['args'][0]; 
			}  
		} else  {
			if (isset($event) && isset($item)) {
				$edata = $event->data();
				$idata = $item->data();
				
				if(isset($idata['departments'])) {
					$title = $edata['name'] .' - '. $idata['description'];
					$tags = $edata['name'].', '.implode(', ',$idata['departments']).', '.$idata['category'];
				}
				
				unset($edata, $idata);
			} else if (isset($event)){ 
				$edata = $event->data();
				$title = $tags = $edata['name'];
				unset($edata, $idata);
			} 
		}
	?>
	<?php if (isset($title) && isset($tags)){ ?>
	<meta name="sailthru.title" content="<?php echo strip_tags($title); ?>" />
	<meta name="sailthru.tags" content="<?php echo strip_tags($tags); ?>" />
	<?php } ?>

</head>
<body class="app">
<?php if(isset($branch)) { echo $branch; } ?>
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
<?php 
if ('/sales?req=invite' == $_SERVER['REQUEST_URI']) { 
?>
<div id="invites">
		<span class="ui-icon ui-icon-circle-check"></span>
		<?php echo $this->view()->render(array('element' => 'inviteModal')); ?>
</div>
<script>
	$(function() {
		$( "#dialog:ui-dialog" ).dialog( "destroy" );
		$( "#invites" ).dialog({
			modal: true,
			width: 760,
		});
	});
</script>
<? } ?>
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

<!-- Sailthru Horizon --> 
<script type="text/javascript">
    (function() {
        function loadHorizon() { 
            var s = document.createElement('script'); 
            s.type = 'text/javascript'; 
            s.async = true;
            s.src = ('https:' == location.protocol ? 'https://dyrkrau635c04.cloudfront.net' : 'http://cdn.sailthru.com') + '/horizon/v1.js';
            var x = document.getElementsByTagName('script')[0]; 
            x.parentNode.insertBefore(s, x);
        }
        loadHorizon();
        var oldOnLoad = window.onload;
        window.onload = function() {
            if (typeof oldOnLoad === 'function') {
                oldOnLoad();
            }
            Sailthru.setup({
                domain: 'horizon.totsy.com',
                spider: true,
                concierge: false,
            });
        };
    })();
</script>
</body>
</html>
