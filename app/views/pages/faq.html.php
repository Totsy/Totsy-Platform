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
	<h2 class="gray mar-b">FAQ's</h2>
	<hr />

<ul>
  <li><a href="#1">What is Totsy?</a></li>
  <li><a href="#2">How do I become a member of Totsy?</a></li>
</ul>
<br />

<h4 class="gray mar-b">Sales and Payment</h4>
<hr />
<ul>
  <li><a href="#3">When does a Totsy sale start?</a></li>
  <li><a href="#4">How long does a Totsy sale stay open?</a></li>
  <li><a href="#5">Is there a limit to how many items I can put in my shopping cart?</a></li>
  <li><a href="#6">Can I put items from two different events in my shopping cart?</a></li>
  <li><a href="#7">How can I pay for my items?</a></li>
  <li><a href="#8">Can I cancel an order?</a></li>
  <li><a href="#9">Does Totsy charge sales tax?</a></li>
</ul>
<br />
<h4 class="gray mar-b">Shipping Information</h4>
<hr />
<ul>
  <li><a href="#10">What are my shipping options?</a></li>
  <li><a href="#11">How long will it take for me to get my order?</a></li>
  <li><a href="#12">Can I track my order?</a></li>
  <li><a href="#13">Does Totsy ship to Canada?</a></li>
  <li><a href="#14">Does Totsy ship internationally?</a></li>
</ul>
<br />
<h4 class="gray mar-b">Returns</h4>
<hr />
<ul>
  <li><a href="#15">What is Totsy's policy on returns and exchanges?</a></li>
</ul>
<br />
<h4 class="gray mar-b">Account Maintenance</h4>
<hr />
<ul>
  <li><a href="#16">What happens if I forget my password?</a></li>
  <li><a href="#17">How do I change my password, my shipping and/or billing address, email address, and/or credit card information?</a></li>
  <li><a href="#18">Can I have multiple shipping addresses on file?</a></li>
  <li><a href="#19">How do I unsubscribe to email notices?</a></li>
</ul>
<br />
<h4 class="gray mar-b">Site Issues</h4>
<hr />
<ul>
<li><a href="#20">What happened to the blog?</a></li>
<li><a href="#21">Your site says I need to use Internet Explorer 8, but that is what I have. What gives?</a></li>
</ul>
<hr />
<div class="faq_answer"><a id="1"></a><strong>Q:</strong> What is Totsy?</div>
<div class="faq_question"><strong>A: </strong>Totsy is a private shopping network of exclusive, brand-specific sales, up to 70% off retail,
  for moms on-the-go, moms-to-be, and kids, ages 0-7. Prenatal care products, baby gear, travel
  accessories, bedding and bath, children's clothing, toys, DVDs, and educational materials are
  just a sampling of a selection that promises only the best in quality and designer brands. Each
  sale lasts 48 to 72 hours.</div>
<div class="faq_answer"><a id="2"></a><strong>Q:</strong> How do I become a member of Totsy?</div>
<div class="faq_question"><strong>A: </strong> Membership is free. You can join either by accepting an invitation offer from a friend or by requesting your membership via Totsy's sign-in page.</div>
<div class="faq_answer"><a id="3"></a><strong>Q:</strong> When does a Totsy sale start?</div>
<div class="faq_question"><strong>A: </strong>Sales start at 10 AM (EST). Exceptions may be made for one-hour sales.</div>
<div class="faq_answer"><a id="4"></a><strong>Q:</strong> How long does a Totsy sale stay open?</div>
<div class="faq_question"><strong>A: </strong> Unless otherwise specified, a sale lasts 48 to 72 hours. </div>
<div class="faq_answer"><a id="5"></a><strong>Q:</strong> Is there a limit to how many items I can put in my shopping cart?</div>
<div class="faq_question"><strong>A: </strong> No, but stock is limited.</div>
<div class="faq_answer"><a id="6"></a><strong>Q:</strong> Can I put items from two different events in my shopping cart?</div>
<div class="faq_question"><strong>A: </strong>Yes.</div>
<div class="faq_answer"><a id="7"></a><strong>Q:</strong> How can I pay for my items?</div>
<div class="faq_question"><strong>A: </strong> Payment can be made with Visa, Mastercard, or American Express.</div>
<div class="faq_answer"><a id="8"></a><strong>Q:</strong> Can I cancel an order?</div>
<div class="faq_question"><strong>A: </strong> Generally no, especially on final sales, but you always have the option to call customer service to explain the situation.</div>
<div class="faq_answer"><a id="9"></a><strong>Q:</strong> Does Totsy charge sales tax?</div>
<div class="faq_question"><strong>A: </strong> Yes, but only for orders delivered to Delaware, Pennsylvania, and New York.</div>
<div class="faq_answer"><a id="10"></a><strong>Q:</strong> What are my shipping options?</div>
<div class="faq_question"><strong>A: </strong> Our shipping partner is UPS. </div>
<div class="faq_answer"><a id="11"></a><strong>Q:</strong> How long will it take for me to get my order?</div>
<div class="faq_question"><strong>A: </strong> For delivery, orders will be processed, ordered to the brand, received  at Totsy, quality controlled, and then shipped, and delivered within  3-5 weeks after the sale close. Your items are ordered to the brand  after you purchase them so some brands might be a bit slow to ship those  items to us. Make sure you always consider this when choosing the size  when ordering an apparel item. When your order is shipped, you will be  able to access a tracking number on your personal account page.</div>
<div class="faq_answer"><a id="12"></a><strong>Q:</strong> Can I track my order?</div>
<div class="faq_question"><strong>A: </strong> Yes, you can track your order online via your account page.</div>
<div class="faq_answer"><a id="13"></a><strong>Q:</strong> Does Totsy ship to Canada?</div>
<div class="faq_question"><strong>A: </strong> No.</div>
<div class="faq_answer"><a id="14"></a><strong>Q:</strong> Does Totsy ship internationally?</div>
<div class="faq_question"><strong>A: </strong> No.</div>
<div class="faq_answer"><a id="15"></a><strong>Q:</strong> What is Totsy's policy on returns and exchanges?</div>
<div class="faq_question"><strong>A: </strong> Please see our Return Policy page for more information.</div>
<div class="faq_answer"><a id="16"></a><strong>Q:</strong> What happens if I forget my password?</div>
<div class="faq_question"><strong>A: </strong> If you forget your password, you can reset it via the "Forgot Your Password?" link on Totsy's sign-in page. You'll be directed to a new page and asked to input your email address. We will then email you a link to reset your password and create a new one.</div>
<div class="faq_answer"><a id="17"></a><strong>Q:</strong> How do I change my password, my shipping and/or billing address, email address, and/or credit card information?</div>
<div class="faq_question"><strong>A: </strong> All of these preferences can be edited on your personal account page.</div>
<div class="faq_answer"><a id="18"></a><strong>Q:</strong> Can I have multiple shipping addresses on file?</div>
<div class="faq_question"><strong>A: </strong> You can only store one address, but you will have the option to change the shipping address during checkout.</div>
<div class="faq_answer"><a id="19"></a><strong>Q:</strong> How do I unsubscribe to email notices?</div>
<div class="faq_question"><strong>A: </strong> To unsubscribe from our email notices, just click on the unsubscribe link found at the bottom of every email. You'll be taken directly to a page that will ask you to confirm that you want to be removed from our database. Once you confirm, we'll automatically remove you from our lists.</div>
<div class="faq_answer"><a id="20"></a><strong>Q:</strong> What happened to the blog?</div>
<div class="faq_question"><strong>A: </strong> You have probably noticed that our blog is on temporary vacation while we get the new site up  and running smoothly. We have big plans for the blog and will be making an announcement when it comes back online.</div>
<div class="faq_answer"><a id="21"></a><strong>Q:</strong> Your site says I need to use Internet Explorer 8, but that is what I have. What gives?</div>
<div class="faq_question"><strong>A: </strong> Internet Explorer 8 includes a feature called Compatibility Mode, which tells it to  pretend to be Internet Explorer 7. Not only does this fool our website, but it forces a working  browser to stop working properly. Make sure you are not running Internet Explorer 8 in  Compatibility Mode, and all should be good.</div>

	 
	</div> 
	<div class="bl"><!-- --></div> 
	<div class="br"><!-- --></div> 
</div>
