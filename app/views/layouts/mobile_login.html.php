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
    <meta name="description"
          content="Totsy has this super cool find available now and so much more for kids and moms! Score the best brands for your family at up to 90% off. Tons of new sales open every day. Membership is FREE, fast and easy. Start saving now!"/>
          <meta name="viewport" content="width=device-width,user-scalable=no" />

	<?php echo $this->scripts(); ?>

	<link rel="stylesheet" href="/totsyMobile/themes/totsy.css">
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0rc2/jquery.mobile.structure-1.0rc2.min.css" /> 
	<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.0rc2/jquery.mobile-1.0rc2.min.js"></script>

</head>
<body>
<div style="font-weight:normal; background:#ed1c24; padding:5px; text-shadow:none; color:#fff;"></div>
	<div style="margin:15px 6px 6px 6px;">
		<?php echo $this->html->link($this->html->image('logo.png', array('width'=>'80')), '/sales', array('escape'=> false)); ?>
		
		<?php echo $this->content(); ?>
    
    <div style="text-align:center; margin:10px 0px 0px 0px;">
    	<a href="#" onclick="window.location.href='/pages/privacy';return false;"style="font-size:12px; color:#ed1c24;">Privacy</a> / <a href="#" onclick="window.location.href='/pages/terms';return false;" style="font-size:12px; color:#ed1c24;">Terms of Service</a> / <a href="#" onclick="window.location.href='/pages/contact';return false;" style="font-size:12px; color:#ed1c24;">Support</a>
    </div>
    </div><!-- Affiliate Pixel -->
    <?php echo $pixel; ?>	
</body>
</html>
