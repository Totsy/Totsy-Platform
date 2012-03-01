<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" 
xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title>Totsy Registration</title>
    <script src="http://connect.facebook.net/en_US/all.js" language="Javascript" type="text/javascript"></script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta property="fb:app_id" content="130085027045086"/>

<style type="text/css">
.form:focus {
	border: 2px solid #ef4739;
	outline: none;
}

h2{
color: #999;
font-size: 18px;
font-weight: normal;
margin: 0;
padding: 0;
}
</style>

	<script src="http://www.google.com/jsapi"></script>
	<script> google.load("jquery", "1.6.1", {uncompressed:true});</script>
	<script> google.load("jqueryui", "1.8.13", {uncompressed:true});</script>

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
	
    window.fbAsyncInit = function() {
        FB.init({
        	appId   : '130085027045086',
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

	function fblogin() {
		FB.login(function(response) {
	    	if (response.authResponse) {
    			window.open("evan.totsy.com");
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

<iframe name="hidden_iframe" id="hidden_iframe" style="display:none;" onload="if(submitted) {window.location='<?php echo $_SERVER['HTTP_HOST']; ?>/totsyfbtab/totsy_confirmtab.html';}"></iframe>

   <h2 style="margin-bottom:20px;">Register with Facebook</h2>
	<a href="javascript:;" onclick="fblogin();return false;"><img src="/img/sign_in_fb.png" class="fr"></a>
<!-- </form>-->
</div>
<!-- END TOTSY SIGN UP FORM -->
</div>
</div>

</body>
</html>
