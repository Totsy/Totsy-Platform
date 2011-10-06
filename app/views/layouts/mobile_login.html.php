<style>
	body { background:#f5f5f5;}
</style>
<?php use lithium\net\http\Router; ?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?php echo $this->html->charset();?>
	<title>Totsy, the private sale site for Moms</title>
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
	<meta name = "format-detection" content = "telephone=no">
	<link media="only screen and (max-device-width: 480px) and (min-device-width: 320px)" href="css/mobile.css" type="text/css" rel="stylesheet" />
	<link media="handheld, only screen and (max-device-width: 319px)" href="css/mobile_simple.css" type="text/css" rel="stylesheet" />
	<?=$this->html->style(array('base', 'mobile.css', 'mobile_reset.css'), array('media' => 'screen')); ?>
	<meta property="fb:app_id" content="181445585225391"/>
	<meta property="og:site_name" content="Totsy"/>
	<meta name="description" content="Totsy has this super cool find available now and so much more for kids and moms! Score the best brands for your family at up to 90% off. Tons of new sales open every day. Membership is FREE, fast and easy. Start saving now!"/>

	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
	
	<script type="application/x-javascript">
		if (navigator.userAgent.indexOf('iPhone') != -1) {
		addEventListener("load", function() {
		setTimeout(hideURLbar, 0);
		}, false);
		}
		
		function hideURLbar() {
		window.scrollTo(0, 1);
		}
	</script>
</head>
<body>
	<?php echo $this->html->link($this->html->image('logo.png', array('width'=>'100', 'style' => 'text-align:center; margin:20px 0px 3px 110px;')), '/sales', array('escape'=> false)); ?>
	<?php echo $this->content(); ?>
	<!-- Affiliate Pixel -->
	<?php echo $pixel; ?>
	<script>
	    $(document).ready(function(){
    $("label.inlined + input.input-text").each(function (type) {
     
    Event.observe(window, 'load', function () {
    setTimeout(function(){
    if (!input.value.empty()) {
    input.previous().addClassName('has-text');
    }
    }, 200);
    });
     
    $(this).focus(function () {
    $(this).prev("label.inlined").addClass("focus");
    });
     
    $(this).keypress(function () {
    $(this).prev("label.inlined").addClass("has-text").removeClass("focus");
    });
     
    $(this).blur(function () {
    if($(this).val() == "") {
    $(this).prev("label.inlined").removeClass("has-text").removeClass("focus");
    }
    });
    });
    });
</script>
</body>
</html>
