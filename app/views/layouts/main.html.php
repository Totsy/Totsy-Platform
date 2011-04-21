<?php use lithium\net\http\Router; ?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<?=$this->html->charset();?>
	<title>
		<?=$this->title() ?: 'Totsy, the private sale site for Moms'; ?>
		<?=$this->title() ? '- Totsy' : ''; ?>
	</title>
	<?=$this->html->style(array('base.css', '960.css'), array('media' => 'screen')); ?>
	<?=$this->html->script(array(
		'jquery-1.4.2.min.js',
		'jquery-ui-1.8.2.custom.min.js',
		'jquery.countdown.min.js'
	)); ?>
	
	<?=$this->scripts(); ?>
	<?=$this->html->link('Icon', null, array('type' => 'icon')); ?>
	<meta property="og:site_name" content="Totsy"/>
	<meta property="fb:app_id" content="181445585225391"/>
    <meta property="og:description"
          content="Totsy has this super cool find available now and so much more for kids and moms! Score the best brands for your family at up to 90% off. Tons of new sales open every day. Membership is FREE, fast and easy. Start saving now!"/>
</head>
<body>
<div class="container_16" style="margin:10px auto;">
<div class="grid_2 alpha">
				<?=$this->html->link(
					$this->html->image('logo.png', array('width'=>'100')), '/sales', array(
						'escape'=> false
					)
				); ?>
</div>
<div class="grid_14">
	<?php if (!empty($userInfo)): ?>
	<div class="grid_6">
	        <ul class="nav main" id="navlist">
	            <li>
	
	                <a href="">All</a>
	
	                <ul style="background:#fff; padding:20px;">
	                    <li><a href="">Update Order Status</a></li>
	                </ul>
	            </li>
	
	            <li>
	                <a href="#">Boys</a>
	
	                <ul>
	                    <li><a href="#">Report on Credits</a></li>
	
	                    <li><a href="">Apply Credit by Event</a></li>
	                </ul>
	
	            </li>
	
	            <li>
	                <a href="">Girls</a>
	
	                <ul>
	                    <li><a href="">Add New Event</a></li>
	
	                    <li><a href="">View Events</a></li>
	
	                    <li><a href="">Search for Items</a></li>
	                </ul>
	            </li>    
	            
	            <li>
	                <a href="">Moms</a>
	
	                <ul>
	                    <li><a href="">Add New Event</a></li>
	
	                    <li><a href="">View Events</a></li>
	
	                    <li><a href="">Search for Items</a></li>
	                </ul>
	            </li>    
	                 
	        </ul>
	    </div>
		
		<div class="grid_8 alpha omega" style="text-align:right; padding:10px 0px;">
					Hello,
					<?php if(array_key_exists('firstname',$userInfo) && !empty($userInfo['firstname'])):
					?>
						<?="{$userInfo['firstname']} {$userInfo['lastname']}"; ?>
					<?php else:?>
					    <?="{$userInfo['email']}"; ?>
					<?php endif; ?>
					<?php $logout = ($fblogout) ? $fblogout : 'Users::logout' ?>
					(<?=$this->html->link('Sign Out', $logout, array('title' => 'Sign Out')); ?>) 
					<br />
					
					<?=$this->html->link('Contact Us', 'Tickets::add'); ?>
					/ 
					<?php if (!empty($userInfo)): ?>
					<?=$this->html->link('Checkout', array('Orders::add'), array(
								'id' => 'checkout', 'title' => 'checkout'
							)); ?> (<?=$cartCount;?>) 
					
					/
 
			 			<?=$this->html->link('Cart', array('Cart::view')); ?> 
					
					/
					<?=$this->html->link('My Credits', array('Credits::view')); ?>
							
							($<?=$credit?>) 
								
					/
					<?=$this->html->link('Invite Friends. Get $15', array('Users::invite'));?>
						
				<?php endif ?>
	</div>
	
				<?php endif ?>
				<?php if (!(empty($userInfo))): ?>
					<?=$this->menu->render('main-nav__'); ?>
				<?php endif ?>
			
				</div>
	</div>
	<div class="clear"></div>
	</div>
	<div class="container_16 roundy" style="background:#fff; margin:10px auto; padding:10px 0px 0px 0px; overflow:hidden;">
		<?php echo $this->content(); ?>
	</div>
	<div id="footer" class="container_16">
		<ul>
			<li class="first" style="padding-top:4px;"><a href="/pages/terms" title="Terms of Use">Terms of Use</a></li>
			<li style="padding-top:4px;"><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
			<li style="padding-top:4px;"><a href="/pages/aboutus" title="About Us">About Us</a></li>
			<li style="padding-top:4px;"><a href="http://blog.totsy.com" title="Blog" target="_blank">Blog</a></li>
			<li style="padding-top:4px;"><a href="/pages/faq" title="FAQ">FAQ</a></li>
			<li class="last" style="padding-top:4px;"><a href="/pages/contact" title="Contact Us">Contact Us</a></li>
			<li class="last" style="margin:0px 3px 0px 5px;"><a href="http://www.facebook.com/totsyfan" target="_blank"><img src="../img/icons/facebook_16.png" align="middle" /></a></li>
			<li class="last"><a href="http://twitter.com/MyTotsy" target="_blank"><img src="../img/icons/twitter_16.png" align="middle" /></a></li>
		</ul>
		<span id="copyright" style="padding-top:4px;">&copy;2011 Totsy.com. All Rights Reserved.</span>
	</div>
	<div class="container_16">
	<br />
	<!-- begin thawte seal -->
    <div id="thawteseal" title="Click to Verify - This site chose Thawte SSL for secure e-commerce and confidential communications.">
        <div style="float: left!important; width:100px; display:block;"><script type="text/javascript" src="https://seal.thawte.com/getthawteseal?host_name=www.totsy.com&amp;size=L&amp;lang=en"></script></div>

    <div class="AuthorizeNetSeal" style="float: left!important; width:100px; display:block;"> <script type="text/javascript" language="javascript">var ANS_customer_id="98c2dcdf-499f-415d-9743-ca19c7d4381d";</script> <script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js" ></script></div>
    </div>
    <!-- end thawte seal -->
    </div>
	<script type="text/javascript">
		$.base = '<?=rtrim(Router::match("/", $this->_request)); ?>';
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-675412-15']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	</script>

    <script type="text/javascript">
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
		</script>

    	<div id='cart-modal'></div>

	<script type="text/javascript">
	/*$("#cart").click(function() {
		$("#cart-modal").load($.base + 'cart/view').dialog({
			autoOpen: false,
			modal:true,
			width: 900,
			//height: 600,
			close: function(ev, ui) {
				location.reload();
			}
		});
		$("#cart-modal").dialog('open');
	}); */
	</script>

    <div id='toTop'>^ Back to Top</div>

    <!--affiliate pixels-->
    <?php echo $pixel; ?>
   
	</body>
	 
</html>