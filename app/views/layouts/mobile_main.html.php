<?php 
use lithium\net\http\Router; 
use lithium\storage\Session;
use app\models\Event;
?>
<?php $request = $this->request(); ?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?php echo $this->html->charset();?>
	<title>
		<?php echo $this->title() ?: 'Totsy'; ?>
	</title>
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
				$ts = array();
				$ts[] = Event::mapCat2Url('age',$tags);
				if (sizeof($ts)>0){
					$tags = implode(', ', $ts);
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
			if (count($eventData['tags'])) {
				$tags .= ', ' . implode(', ', $eventData['tags']);
			}
		}

	?>
	<?php if (isset($title) && isset($tags)){ ?>
	<meta name="sailthru.title" content="<?php echo strip_tags($title); ?>" />
	<meta name="sailthru.tags" content="<?php echo strip_tags($tags); ?>" />
	<?php } ?>
	<meta id="view-lock" name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta content="yes" name="apple-mobile-web-app-capable" />
	
	<link rel="stylesheet" href="/totsyMobile/themes/totsy.css">
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0rc2/jquery.mobile.structure-1.0rc2.min.css" /> 

	<script src="https://www.google.com/jsapi"></script>
	<script> google.load("jquery", "1.6.1", {uncompressed:false});</script>
	<script> google.load("jqueryui", "1.8.13", {uncompressed:false});</script>
	<!-- end jQuery / jQuery UI -->
	
	<script src="/totsyMobile/themes/js/totsyMobile.js"></script>
	<script src="http://code.jquery.com/mobile/1.0rc2/jquery.mobile-1.0rc2.min.js"></script>
	
	<link rel="apple-touch-startup-image" href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/totsyMobile/themes/images/totsy_startup.png" />
<link rel="apple-touch-icon" href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/totsyMobile/themes/images/icon.png"/> 
	
	<?php echo $this->html->script('jquery.countdown.min.js?v=007'); ?>
	<?php echo $this->scripts(); ?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-675412-20']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>
<script>
if (!navigator.cookieEnabled) {
	document.write("<div style='margin:10px 0px; background:#f8f57d; padding:10px; border:4px solid #333; text-align:center;'>You do not have cookies enabled. <br />Please enable to continue.</div>");
}
</script>
<script>
if (!navigator.cookieEnabled) { 
	 var txt=document.getElementById("no_cookies")
  		 document.write("<div style='margin:10px 0px; background:#f8f57d; padding:10px; border:4px solid #333; text-align:center;'>You do not have cookies enabled. Please enable to continue.</div>");
}
</script>
<div data-role="page" id="main">
<?php if (!empty($userInfo)){ ?>
	<div data-role="header" style="-moz-box-shadow: 0px 0px 4px 0px #666;
	-webkit-box-shadow: 0px 0px 4px 0px #666; box-shadow:0px 0px 4px 0px #666;"> 
		<div class="logo">
			<a href="#" onclick="window.location.href='/sales';return false;"><img src="/img/logo.png" width="60" /></a>
			<div style="float:right; margin-right:10px; font-size:12px; font-weight:normal!important; color:#999;">
			<?php if(array_key_exists('firstname', $userInfo) &&     !empty($userInfo['firstname'])):
						?>
						    <a href="#" onclick="window.location.href='/account';return false;"><?php echo $userInfo['firstname'].' '.$userInfo['lastname'] ?></a><br />
						<?php else: ?>
						    <a href="#" onclick="window.location.href='/account';return false;">Totsy Member</a><br />
						<?php endif;?>
						Cart: <a href="#" onclick="window.location.href='/cart/view';return false;"><?php echo $cartCount;?></a>
			</div>
		</div>
		<div class="clear"></div>

		<div data-role="navbar">
			<ul>
				<li><a href="#" onclick="window.location.href='/sales';return false;" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales') == 0) { echo 'class="ui-btn-active"'; } ?>>Shop<br />by Date</a></li>
				<li><a href="#" onclick="window.location.href='/age/all';return false;" <?php if(strcmp($_SERVER['REQUEST_URI'],'/age/all') == 0) { echo 'class="ui-btn-active"'; } ?>>Shop<br />by Age</a></li>
				<li><a href="#" onclick="window.location.href='/category/all';return false;" <?php if(strcmp($_SERVER['REQUEST_URI'],'/categories/all') == 0) { echo 'class="ui-btn-active"'; } ?>>Shop<br />by Category</a></li>
			</ul>
		</div><!-- /navbar -->

		<div class="clear"></div>
	</div>
<?php } else { ?>
	<div class="nav_head"></div>
	<div class="mobile_ui">
		<div class="logo">
			<a href="#" onclick="window.location.href='/sales';return false;"><img src="/img/logo.png" width="80" /></a>
		</div>	
	</div>
<?php } ?>
	<div data-role="content" data-role="page" class="type-interior">
	<?php echo $this->content(); ?>
	</div>
	<div class="clear"></div>

	<p class="legal">&copy;2012 Totsy, Inc. All rights reserved.</p>
</div>
<?= $this->view()->render(array('element' => 'modal/mobile_password'), array('user' => $user)); ?>
<script>
$.mobile.fixedToolbars
   .show(true);

$.base = '<?php echo rtrim(Router::match("/", $this->_request)); ?>';
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
