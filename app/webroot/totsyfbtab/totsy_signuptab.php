<?php 
header("p3p: CP=\"ALL DSP COR PSAa PSDa OUR NOR ONL UNI COM NAV\"");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" 
xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>

<?php 
	include("../../libraries/li3_facebook/libraries/facebook-sdk/src/facebook.php"); 
	
	$signedRequest = "";
	$appId = "130085027045086";
	
	$facebook = new Facebook( Array(
  		'appId' => $appId,
		'secret' => '33a18cebb0ac415c6bddf28cebb48e96'
	));
	
	//if($_SERVER['HTTP_HOST']=="facebook.com"){
	$signedRequest = $facebook->getSignedRequest();
	//}
	
	$appData = Array();
	$affiliateCode = "";
	
	if (!empty($signedRequest) && !empty($signedRequest['app_data'])) {
  		$appData = json_decode($signedRequest['app_data'], true);
  		
  		if(!empty($appData['a'])) {	
  			$affiliateCode = $appData['a'];
  		}
	}
?>


<title>Totsy Registration</title>
    <script src="http://connect.facebook.net/en_US/all.js" language="Javascript" type="text/javascript"></script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta property="fb:app_id" content="<?php echo $appId; ?>"/>

<style type="text/css">
.form:focus {
	border: 2px solid #ef4739;
	outline: none;
}

h2 {
color: #999;
font-size: 18px;
font-weight: normal;
margin: 0;
padding: 0;
}
</style>

	<script src="http://www.google.com/jsapi"></script>
	<script> google.load("jquery", "1.6.1", { uncompressed:true });</script>
	<script> google.load("jqueryui", "1.8.13", { uncompressed:true });</script>

<!-- ETIQUETTE GA TRACKING CODE -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-27138066-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>

<body>
<div id="fb-root"></div>

<script>	
	
	var submitted = false;
	var appId = "<?php echo $appId; ?>";
	var affiliateCode = "<?php echo $affiliateCode; ?>";
	
    window.fbAsyncInit = function() {
        FB.init({
        	appId   : appId,
        	oauth	: true, 
        	status  : true, // check login status
        	cookie  : true, // enable cookies to allow the server to access the session
        	xfbml	: true 
    	});	
    };	 

    (function() {
        var e = document.createElement('script');
        e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
    }());
       
</script>

<script type="text/javascript">

	function fbLogin() {
		FB.login(function(response) {
	    	if (response.authResponse) {
	    		if (affiliateCode) {
	    			<?php
	    			header("P3P","CP=\"IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT\"");	
	    			 ?>
    				window.parent.location = "/a/" + affiliateCode + "/sales";
    			} else {
    				<?php 
    				header("P3P","CP=\"IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT\"");
    				?>
    				window.parent.location = "/sales";
    			}
	    	}	
		}, 		 
			{ scope:'email' } 
		);
		submitted = true;
	}
	
</script>

<div style="width:520px; -moz-border-radius: 5px;-webkit-border-radius: 5px; overflow:hidden;">
<div>
	<img src="http://www.etiquettecreative.com/totsy/totsy_signuptab_01.png" />
</div>
<div style="background-image:url(http://www.etiquettecreative.com/totsy/signuptab.jpg); background-repeat:no-repeat; text-align:left; min-height:350px; padding:160px 0px 0px 0px; -moz-border-radius: 5px;-webkit-border-radius: 5px;">
<!-- TOTSY SIGN UP FORM -->
<div style="width:520px; padding:5px 5px 5px 0px;">

   <h2 style="margin-bottom:20px;">Register with Facebook</h2>
	<a href="javascript:;" onclick="fbLogin();return false;"><img src="/img/sign_in_fb.png" class="fr"></a>
<!-- </form>-->
</div>
<!-- END TOTSY SIGN UP FORM -->
</div>
</div>

</body>
</html>
