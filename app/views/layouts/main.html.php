<?php 

header("p3p: CP=\"ALL DSP COR PSAa PSDa OUR NOR ONL UNI COM NAV\"");

use lithium\net\http\Router; 
use lithium\storage\Session;
use app\models\Event;
?>
<?php $request = $this->request(); ?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?php echo $this->html->charset();?>
	
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
	<?php 
		$baseCSSPath = "";
		$jQueryAllPath = "";
		$titleTag = "Totsy, the private sale site for Moms";
		$googleUACode = "UA-675412-15";
		$isWhiteLabel =  false;
		
		//pick CSS for Mamasource vs Totsy based on session variable
		if (Session::read("layout", array("name"=>"default"))=="mamapedia") {
			$baseCSSPath = "/css/base_mamapedia.css?" . filemtime(LITHIUM_APP_PATH . "/webroot/css/base.css");
			$jQueryAllPath = "/css/jquery_ui_custom/jquery.ui.all.mamapedia.css?" . filemtime(LITHIUM_APP_PATH . "/webroot/css/jquery_ui_custom/jquery.ui.all.mamapedia.css");	
			$googleUACode = "UA-675412-22";
			$titleTag = "Mamasource, powered by Totsy private sale";
			$isWhiteLabel = true;
		} else {
			$baseCSSPath = "/css/base.css?" . filemtime(LITHIUM_APP_PATH. "/webroot/css/base.css");
			$jQueryAllPath = "/css/jquery_ui_custom/jquery.ui.all.css?" . filemtime(LITHIUM_APP_PATH . "/webroot/css/jquery_ui_custom/jquery.ui.all.css");
		}
		
	 ?>
	 <title>
	<?php echo $titleTag; ?>
	</title>
	 <?php 
	 	echo $this->html->style(Array( $baseCSSPath , "/css/960.css?" . filemtime(LITHIUM_APP_PATH . "/webroot/css/960.css"), $jQueryAllPath));
	 ?>
	<script src="https://www.google.com/jsapi"></script>
	<script> google.load("jquery", "1.6.1", {uncompressed:false});</script>
	<script> google.load("jqueryui", "1.8.13", {uncompressed:false});</script>
	<!-- end jQuery / jQuery UI -->
	
 <!-- Begin Monetate tag v6. Place at start of document head. DO NOT ALTER. -->
    <?php echo $this->html->script(array('monetate.js')); ?>
<!-- End Monetate tag. -->
	
	<?php echo '<script src="/js/jquery.uniform.min.js?' . filemtime(LITHIUM_APP_PATH . '/webroot/js/jquery.uniform.min.js') . '" /></script>'; ?>
	<?php echo '<script src="/js/jquery.countdown.min.js?' . filemtime(LITHIUM_APP_PATH . '/webroot/js/jquery.countdown.min.js') . '" /></script>'; ?>
	<script>$('html').addClass('js'); /* for js-enabled - avoid FOUC */</script>
	<!-- Kick in the pants for <=IE8 to enable HTML5 semantic elements and CSS3 selectors support -->
	<!--[if lt IE 9]><script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<!--[if lte IE 8]><script type="text/javascript" src="/js/selectivizr-min.js"></script><![endif]-->	
	<?php echo $this->scripts(); ?>
	<meta http-equiv="Expires" content="<?php echo date('D, d M Y h:i T', strtotime('tomorrow')); ?>"/>
	<meta property="og:site_name" content="Totsy"/>
	<meta property="fb:app_id" content="<?php echo $fbconfig['appId']; ?>"/>
	<meta name="description" content="Totsy has this super cool find available now and so much more for kids and moms! Score the best brands for your family at up to 90% off. Tons of new sales open every day. Membership is FREE, fast and easy. Start saving now!"/>
	<meta name="sailthru.date" content="<?php echo date('r')?>" /><?php

		if(substr($request->url,0,5) == 'sales' || $_SERVER['REQUEST_URI'] == '/') {

			$title = 'Totsy Sales';
			$tags = 'Sales';
			if (array_key_exists ('args',$request->params) && isset($request->params['args'][0])){
				$tags =  $request->params['args'][0];
			}
		} else if (substr($request->url,0,8) == 'category' || substr($request->url,0,3) == 'age') {
			$title = $tags = $categories;

			$ts = Event::mapCat2Url('age',$tags);
			if (!empty($ts)>0){ $tags = $ts; }
			
			$ts = Event::mapCat2Url('category',$tags);
			if (!empty($ts)>0){
				$tags = $ts;
			}
		} else if (isset($item)) {
			$itemData = $item->data();
			$title = $tags = $itemData['description'];

			if (count($itemData['departments'])) {
				$tags .= ', ' . implode(', ', $itemData['departments']);
			}
			if (count($itemData['categories'])) {
				$tags .= ', ' . implode(', ', $itemData['categories']);
			}
			if (count($itemData['ages'])) {
				$ts = array();
				foreach ($itemData['ages'] as $a){
					$ts[] = Event::mapCat2Url('age',$a);
				}
				if (sizeof($ts)>0){
					$tags .= ', ' . implode(', ', $ts);
				}
			}
		} else if (isset($event)) {
			$eventData = $event->data();
			$title = $tags = $eventData['name'];

			if (count($eventData['departments'])) {
				$tags .= ', ' . implode(', ', $eventData['departments']);
			}
		}

	?>
	<?php if (isset($title) && isset($tags)){ ?>
	<meta name="sailthru.title" content="<?php echo strip_tags($title); ?>" />
	<meta name="sailthru.tags" content="<?php echo strip_tags($tags); ?>" />
	<?php } ?>

</head>
<body class="app">

<style>
	#global_site_msg select { background:#fff!important;}
	#global_site_msg span { background:#fff!important;}
	#global_site_msg .selector { background:#fff!important;}
</style>
	<?php if(isset($branch)) { ?> 
	<div id='global_site_msg'>
	<div style="font-size:16px; margin:0px 0px 0px 300px; width:230px; float:left; padding:7px 0px 0px 0px;">	<?php echo $branch; ?></div>
	<div style="margin:0px auto; width:230px; float:left;">
		<select name="jumpMenu" id="jumpMenu">
			<option>Choose Developer Box</option>
			<option value="http://eric.totsy.com">eric.totsy.com</option>
			<option value="http://evan.totsy.com">evan.totsy.com</option>
			<option value="http://chris.totsy.com">chris.totsy.com</option>
			<option value="http://david.totsy.com">david.totsy.com</option>
			<option value="http://deepen.totsy.com">deepen.totsy.com</option>
			<option value="http://froggygeek.totsy.com">froggygeek.totsy.com</option>
			<option value="http://gene.totsy.com">gene.totsy.com</option>
			<option value="http://hara.totsy.com">hara.totsy.com</option>
			<option value="http://jonathan.totsy.com">jonathan.totsy.com</option>
			<option value="http://josh.totsy.com">josh.totsy.com</option>
			<option value="http://kkim.totsy.com">kkim.totsy.com</option>
			<option value="http://lawren.totsy.com">lawren.totsy.com</option>
			<option value="http://micah.totsy.com">micah.totsy.com</option>
			<option value="http://release.totsy.com">release.totsy.com</option>
			<option value="http://rockmongo.totsy.com">rockmongo.totsy.com</option>
			<option value="http://slavik.totsy.com">slavik.totsy.com</option>
			<option value="http://tharsan.totsy.com">tharsan.totsy.com</option>
			<option value="http://tom.totsy.com">tom.totsy.com</option>
			<option value="http://track.totsy.com">track.totsy.com</option>
			<option value="http://www.totsy.com">www.totsy.com</option>
		</select>
	</div>
	<script type="text/javascript">
    $(function (){  
        $("#jumpMenu").change(function(e) {
            window.location.href = $(this).val();
        });
    });
	</script>
	<div class="clear"></div>
	</div>
	<?php } ?>
	 
	<?php 
		/*
			Brand Promo banner - remove (comment out) when promo is over
		*/
		// don't show banner when in /brands directory
		$query = $_SERVER['REQUEST_URI'];
		if(!preg_match("/brands/", $query)) { ?>
		
			<div class="container_16 brandpromo">
				<a href="/brands/jojo" title="JoJo Maman B&eacute;b&eacute; Special 10-day Event"><img src="/img/jojo-banner.png" alt="JoJo Maman B&eacute;b&eacute; Special 10-day Event" width="816" height="125" /></a>
			</div>
	<?php 
		} ?>
	
	<div id="totsy" class="container_16 roundy glow">
		
		<?php echo $this->view()->render(array('element' => 'headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout, 'cartSubTotal' =>$cartSubTotal)); ?>
				
		<div id="contentMain" class="container_16 group">
			<div id="noscript">Unfortunately, JavaScript is currently disabled or not supported by your browser. Please enable JavaScript for full functionality.</div>
			<?php echo $this->content(); ?>
		</div>
		<!-- /#contentMain -->

	</div><!-- /#totsy -->

	<div id="footer" class="container_16 group">
		<?php echo $this->view()->render(array('element' => 'footerNav'), array('userInfo' => $userInfo)); ?>
	</div>
	<!-- end footer nav -->

	<div class="container_16 group">
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
	var googleUACode = "<?php echo $googleUACode; ?>";
	
	$.base = '<?php echo rtrim(Router::match("/", $this->_request)); ?>';
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', googleUACode]);	  
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
</script>
	<?php 
	use li3_facebook\extension\FacebookProxy; 
	$fbconfig = FacebookProxy::config();
	$appId = $fbconfig['appId'];	
?>
<?php $logout = ($fblogout) ? $fblogout : 'Users::logout' ?>
<script type="text/javascript">
	var fbCookie = 'fbsr_<?php echo $appId; ?>';
	var mobilefbCookie = 'fbm_<?php echo $appId; ?>';
	var logoutURL = '<?php echo $logout; ?>';
	
		function deleteFBCookies() {
		    //all posible FB cookies
		    try {
		    	document.cookie = fbCookie + '=; domain=.totsy.com; expires=Thu, 01-Jan-70 00:00:01 GMT; path=/';
				document.cookie = mobilefbCookie + '=; base_domain=.totsy.com; expires=Thu, 01-Jan-70 00:00:01 GMT; path=/';
		    	document.cookie = 'datr=; expires=Thu, 01-Jan-70 00:00:01 GMT;path=/';
		    	document.cookie = 'locale=; expires=Thu, 01-Jan-70 00:00:01 GMT;path=/';
		    	document.cookie = 'lu=; expires=Thu, 01-Jan-70 00:00:01 GMT;path=/';
		    	document.cookie = 'reg_fb_gate=; expires=Thu, 01-Jan-70 00:00:01 GMT;path=/';
		    	document.cookie = 'reg_fb_ref=; expires=Thu, 01-Jan-70 00:00:01 GMT;path=/';
		    	document.cookie = 'lsd=; expires=Thu, 01-Jan-70 00:00:01 GMT;path=/';
		    	document.cookie = 'L=; expires=Thu, 01-Jan-70 00:00:01 GMT;path=/';
		    	document.cookie = 'act=; expires=Thu, 01-Jan-70 00:00:01 GMT;path=/';
		    	document.cookie = 'openid_p=; expires=Thu, 01-Jan-70 00:00:01 GMT;path=/';
		    	return true;
		    } catch(err) {
		    	return false;
		    }
		}
		
		function goToLogout() {
			if (deleteFBCookies()==true) {
		    	window.location = logoutURL;
		    }
		}	
</script>

<?php if(Session::read('layout', array('name' => 'default'))!=='mamapedia'): ?>
<script language="javascript"> 
	document.write('<sc'+'ript src="http'+ (document.location.protocol=='https:'?'s://www':'://www')+ '.upsellit.com/upsellitJS4.jsp?qs=237268202226312324343293280329277309292309329331334326345325&siteID=6605"><\/sc'+'ript>')
</script>
<?php endif ?>

<script type="text/javascript">
	<?php // global functions here (although all js *really* should be externalized and view-specific… Magento Magento Magento we'll make it happen) ?>
	$(document).ready(function() {
		$("input:file, select").not('.uniform-hidden').uniform().each(function(i,elt) {
			// find any elements processed that were hidden, and hide the
			// new container for the element created by uniform.js
			if (this.style.display == 'none') {
				this.parentNode.style.display = 'none';
			}
		});
		$("#tabs").tabs();

		// back to top
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
	});
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
<?php
$currentURI  = $_SERVER['REQUEST_URI'];
					
$URIArray = explode("/", $currentURI);
$controllerName = $URIArray[1];			

//disable for checkout pages except order/view, and for Mamasource		    		
if( $controllerName=="checkout" || ($controllerName=="orders" && $URIArray[2]=="view") || $controllerName=="cart" || $isWhiteLabel==true || $controllerName=="pages") { 
?>
<script type="text/javascript">
//code for disabling echoSahre
var ECHO_SYSTEM_SETTINGS = { socialCenter: { enabled: false }};
</script>
<?php }
	//code for including echoShare
	echo $this->html->script(array('echoshare_flyout.js')); 
?>

<!-- Server Name: <?php echo $_SERVER['SERVER_NAME']; ?> -->
<!-- Host Name: <?php echo php_uname('n'); ?> -->
<?php if(isset($version)) { echo $version; } ?>

</body>
</html>
