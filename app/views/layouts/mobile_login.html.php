<?php use lithium\net\http\Router; ?>
<!doctype html>
<?php
  // Copyright 2010 Google Inc. All Rights Reserved.
  function googleAnalyticsGetImageUrl() {
    $GA_ACCOUNT = "MO-675412-20";
    $GA_PIXEL = "/ga.php";
    $url = "";
    $url .= $GA_PIXEL . "?";
    $url .= "utmac=" . $GA_ACCOUNT;
    $url .= "&utmn=" . rand(0, 0x7fffffff);
    $referer = $_SERVER["HTTP_REFERER"];
    $query = $_SERVER["QUERY_STRING"];
    $path = $_SERVER["REQUEST_URI"];
    if (empty($referer)) {
      $referer = "-";
    }
    $url .= "&utmr=" . urlencode($referer);
    if (!empty($path)) {
      $url .= "&utmp=" . urlencode($path);
    }
    $url .= "&guid=ON";
    return str_replace("&", "&amp;", $url);
  }
?>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?php echo $this->html->charset();?>
	<title>Totsy</title>
	
	<meta name="description"
	content="Totsy has this super cool find available now and so much more for kids and moms! Score the best brands for your family at up to 90% off. Tons of new sales open every day. Membership is FREE, fast and easy. Start saving now!"/>
	<meta id="view-lock" name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta content="yes" name="apple-mobile-web-app-capable" />
	
	<link rel="stylesheet" href="/totsyMobile/themes/totsy.css">
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0rc2/jquery.mobile.structure-1.0rc2.min.css" /> 

	<script src="https://www.google.com/jsapi"></script>
	<script> google.load("jquery", "1.6.1", {uncompressed:false});</script>
	<script> google.load("jqueryui", "1.8.13", {uncompressed:false});</script>
	<!-- end jQuery / jQuery UI -->
	<script src="http://code.jquery.com/mobile/1.0rc2/jquery.mobile-1.0rc2.min.js"></script>
	
	<link rel="apple-touch-startup-image" href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/totsyMobile/themes/images/totsy_startup.png" />
<link rel="apple-touch-icon" href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/totsyMobile/themes/images/icon.png"/> 
	
	<?php echo $this->html->script('jquery.countdown.min.js?v=007'); ?>
	<?php echo $this->scripts(); ?>
</head>
<body>
<div id="fb-root"></div>
<div class="nav_head"></div>
	<div class="mobile_ui">
	<div class="logo">
		<a href="#" onclick="window.location.href='/sales';return false;"><img src="/img/logo.png" width="80" /></a>
	</div>	
	<div data-role="content">
	<?php echo $this->content(); ?>
	</div>
	<p class="legal">&copy;2012 Totsy, Inc. All rights reserved.</p>
</div>
	<script>
      window.fbAsyncInit = function() {
        FB.init({
          appId   : <?php echo $fbconfig['appId']; ?>,
          session : <?php echo json_encode($fbsession); ?>, // don't refetch the session when PHP already has it
          oauth	  : true, 
          status  : true, // check login status
          cookie  : true, // enable cookies to allow the server to access the session
          xfbml   : true // parse XFBML
        });

        // whenever the user logs in, we refresh the page
        FB.Event.subscribe('auth.login', function() {
          window.location.reload();
        });
        
        /*
        FB.Event.subscribe('auth.logout', function(response) {
		    window.location.href='/logout';
 		});
 		*/
        
      };

      (function() {
        var e = document.createElement('script');
        e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
<script>
//your fb login function
function fblogin() {
	FB.login(function(response) {
		if (response.authResponse) {
			window.location.reload();    
  		}	
	}, 		 {scope:'email'});
}
</script>
<?php
  $googleAnalyticsImageUrl = googleAnalyticsGetImageUrl();
  echo '<img src="' . $googleAnalyticsImageUrl . '" />';?>
</body>
</html>


