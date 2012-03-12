<?php
header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"')
?>
<?php 
use lithium\net\http\Router; 
use lithium\storage\Session;
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?php echo $this->html->charset();?>
	<?php

$baseCSSPath = "";
$jQueryAllPath = "";
$googleUACode = "UA-675412-15";
$titleTag = "Totsy, the private sale site for Moms";
	
	if (Session::read("layout", array("name"=>"default"))=="mamapedia") {
			$baseCSSPath = "/css/base_mamapedia.css?" . filemtime(LITHIUM_APP_PATH . "/webroot/css/base.css");
			$jQueryAllPath = "/css/jquery_ui_custom/jquery.ui.all.mamapedia.css?" . filemtime(LITHIUM_APP_PATH . "/webroot/css/jquery_ui_custom/jquery.ui.all.mamapedia.css");	
			$googleUACode = "UA-675412-22";
			$titleTag = "Mamasource, powered by Totsy private sale";
		} else {
			$baseCSSPath = "/css/base.css?" . filemtime(LITHIUM_APP_PATH. "/webroot/css/base.css");
			$jQueryAllPath = "/css/jquery_ui_custom/jquery.ui.all.css?" . filemtime(LITHIUM_APP_PATH . "/webroot/css/jquery_ui_custom/jquery.ui.all.css");
		}
?>
<title><?php echo $titleTag ?></title>
	<meta property="fb:app_id" content="<?php echo $fbconfig['appId']; ?>"/>
	<meta property="og:site_name" content="Totsy"/>
    <meta name="description"
          content="Totsy has this super cool find available now and so much more for kids and moms! Score the best brands for your family at up to 90% off. Tons of new sales open every day. Membership is FREE, fast and easy. Start saving now!"/>
	
	<?php echo $this->html->style(array($baseCSSPath, '960.css', $jQueryAllPath), array('media' => 'screen')); ?>
		
	<script src="http://www.google.com/jsapi"></script>
	<script> google.load("jquery", "1.6.1", {uncompressed:true});</script>
	<script> google.load("jqueryui", "1.8.13", {uncompressed:true});</script>
    <!-- end jQuery / jQuery UI -->
    
    <!-- Begin Monetate tag v6. Place at start of document head. DO NOT ALTER. -->
    <?php echo $this->html->script(array('monetate.js')); ?>
<!-- End Monetate tag. -->
            
    <?php echo $this->html->script(array('jquery.backstretch.min.js', 'jquery.uniform.min.js' )); ?>
    
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
	
	
	<script type="text/javascript">
		//this is used for swapping backgrounds on registration pages that pass in affiliate codes	
		var affBgroundImage = "";
	</script>
	
	<script type="text/javascript">	
	  var googleUACode = '<?php echo $googleUACode; ?>';
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', googleUACode]);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
</head>
<body class="app login">
	<div id="fb-root"></div>
	<?php echo $this->content(); ?>
	<?php

	use \DirectoryIterator;
	use lithium\net\http\Media;
	$images = array();
	$imgDirectory = $this->_request->env('base') . '/img/login/';

	/**
	 * Get a random login image (of type jpg or png).
	 */
	foreach (new DirectoryIterator(Media::webroot(true) . '/img/login') as $file) {
		if ($file->isDot() || !preg_match('/\.(png|jpg)$/', $file->getFilename())) {
			continue;
		}
		$images[] = $file->getFilename();
	}
	$image = $images[array_rand($images)];

	?>
    <!-- Affiliate Pixel -->
    <?php echo $pixel; ?>
	<script type="text/javascript">

	    jQuery(document).ready(function($){
	    	if(affBgroundImage!=="") {
				$.backstretch(affBgroundImage);
			} else {
	    		$.backstretch("<?php echo $imgDirectory . $image;?>");
			}
	    });

	</script>
	
	<?php $logout = ($fblogout) ? $fblogout : 'Users::logout' ?>
	
	<script>
		var fbLogout = "<?php echo $logout; ?>";	
	
      	window.fbAsyncInit = function() {
        	FB.init({
        	  appId   : <?php echo $fbconfig['appId']; ?>,
        	  session : <?php echo json_encode($fbsession); ?>, // don't refetch the session when PHP already has it
        	  oauth	  : true, 
        	  status  : true, // check login status
        	  cookie  : true, // enable cookies to allow the server to access the session
        	  oauth   : true, 
        	  xfbml   : true // parse XFBML
        });

        // whenever the user logs in, we refresh the page
        FB.Event.subscribe('auth.login', function() {
          window.location.reload();
        });
        
         FB.Event.subscribe('auth.logout', function() {
          window.location.reload();
        });
        
       };

      (function() {
        var e = document.createElement('script');
        e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>
    </body>
</html>
