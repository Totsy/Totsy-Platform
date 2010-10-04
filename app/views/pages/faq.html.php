<script type="text/javascript">
$(document).ready(function() {
	if (hash = window.location.hash) {
		$('html, body').animate({ scrollTop: $(hash).offset().top - 100 }, 500, 'linear', function() {
			setTimeout(function() {  $(hash).animate({ backgroundColor: '#FFFFAA' }, 500)}, 250);
		});
	}
});
</script>
<h1 class="p-header"><?=$this->title('Frequently Asked Questions'); ?></h1>



<?php if (!empty($userInfo)): ?>
	<?=$this->menu->render('about'); ?>
	<div id="middle" class="noright">
<?php else: ?>
	<div id="middle" class="fullwidth">
<?php endif ?>

	<div class="tl"><!-- --></div> 
	<div class="tr"><!-- --></div> 
	
	<div id="page"> 
	 
		<p><strong>What is Totsy?</strong><br />
		Totsy is a private shopping network of exclusive, brand-specific sales, up to 70% off retail,
		for moms on-the-go, moms-to-be, and kids, ages 0-7. Prenatal care products, baby gear, travel
		accessories, bedding and bath, children's clothing, toys, DVDs, and educational materials are
		just a sampling of a selection that promises only the best in quality and designer brands. Each
		sale lasts 48 to 72 hours.</p>
	
		<p><strong>How do I become a member of Totsy?</strong><br />
		Membership is free. You can join either by accepting an invitation offer from a friend or by
		requesting your membership via Totsy's sign-in page.</p>
	
		<h2>Sales and Payment</h2>
	
		<p><strong>When does a Totsy sale start?</strong><br />
		Sales start at 10 AM (EST). Exceptions may be made for one-hour sales.</p>
	
		<p><strong>How long does a Totsy sale stay open?</strong><br />
		Unless otherwise specified, a sale lasts 48 to 72 hours.</p>
	
		<p><strong>Is there a limit to how many items I can put in my shopping cart?</strong><br />
		No, but stock is limited.</p>
	
		<p><strong>Can I put items from two different events in my shopping cart?</strong><br />
		Yes.</p>
	
		<p><strong>How can I pay for my items?</strong><br />
		Payment can be made with Visa, Mastercard, or American Express.</p>
	
		<p><strong>Can I cancel an order?</strong><br />
		Generally no, especially on final sales, but you always have the option to call customer service
		to explain the situation.</p>
	
		<p><strong>Does Totsy charge sales tax?</strong><br />
		Yes, but only for orders delivered to Delaware, Pennsylvania, and New York.</p>
	
		<h2>Shipping Information</h2>
	
		<p><strong>What are my shipping options?</strong><br />
		For standard delivery, we ship via USPS Priority Mail. For express delivery, we ship via UPS.
		</p>
	
		<p><strong>How long will it take for me to get my order?</strong><br />
		For delivery, orders will be processed, ordered to the brand, received at Totsy, quality controlled, and then shipped, and delivered within 3-5 weeks after the sale close. Your items are order to the brand after you purchase them so some brands might be a bit slow to ship those items to us. Make sure you always consider this when choosing the size when ordering an apparel item. When your order is shipped, you will be able to access a tracking number on your personal account page.</p>
	
		<p><strong>Can I track my order?</strong><br />
		Yes, you can track your order online via your account page.</p>
	
		<p><strong>Does Totsy ship to Canada?</strong><br />
		No.</p>
	
		<p><strong>Does Totsy ship internationally?</strong><br />
		We do not yet have international delivery rates set up, but if there is a specific request, you
		can contact us either via email or phone.</p>
	
		<h2>Returns</h2>
	
		<p><strong>What is Totsy's policy on returns and exchanges?</strong><br />
		Please see our Return Policy page for more information.</p>
	
		<h2>Account Maintenance</h2>
	
		<p><strong>What happens if I forget my password?</strong><br />
		If you forget your password, you can reset it via the "Forgot Your Password?" link on Totsy's
		sign-in page. You'll be directed to a new page and asked to input your email address. We will
		then email you a link to reset your password and create a new one.</p>
	
		<p><strong>How do I change my password, my shipping and/or billing address, email address,
		and/or credit card information?</strong><br />
		All of these preferences can be edited on your personal account page.</p>
	
		<p><strong>Can I have multiple shipping addresses on file?</strong><br />
		You can only store one address, but you will have the option to change the shipping address
		during checkout.</p>
	
		<p><strong>How do I unsubscribe to email notices?</strong><br />
		To unsubscribe from our email notices, just click on the unsubscribe link found at the bottom
		of every email. You'll be taken directly to a page that will ask you to confirm that you want
		to be removed from our database. Once you confirm, we'll automatically remove you from our
		lists.</p>
	 
		<h2>Site Issues</h2>
		
		<p><strong>What happened to the blog?</strong><br />
		You have probably noticed that our blog is on temporary vacation while we get the new site up 
		and running smoothly. We have big plans for the blog and will be making an announcement when 
		it comes back online.</p>
		
		<p id="IE8"><strong>Your site says I need to use Internet Explorer 8, but that is what I have. What
		gives?</strong><br />
		Internet Explorer 8 includes a feature called Compatibility Mode, which tells it to 
		pretend to be Internet Explorer 7. Not only does this fool our website, but it forces a working 
		browser to stop working properly. Make sure you are not running Internet Explorer 8 in 
		Compatibility Mode, and all should be good.</p>
	 
	</div> 
	<div class="bl"><!-- --></div> 
	<div class="br"><!-- --></div> 
</div>
