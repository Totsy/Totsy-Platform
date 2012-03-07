<?php use lithium\storage\Session; ?>
<ul>
	<li class="first"><a href="/pages/terms" title="Terms of Use">Terms of Use</a></li>
	<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
	<li><a href="/pages/aboutus" title="About Us">About Us</a></li>
	<li><a href="http://blog.totsy.com" title="Blog" target="_blank">Blog</a></li>
	<li><a href="/pages/faq" title="FAQ">FAQ</a></li>
	
	<?php if(Session::read("layout", array("name"=>"default"))!=="mamapedia"): ?>
	<li><a href="/pages/affiliates" title="Affiliates">Affiliates</a></li>
	<li><a href="/pages/careers" title="Careers">Careers</a></li>
	<?php endif ?>
	
	<?php if (empty($userInfo)){ ?>
	<li><a href="/pages/contact" title="Contact Us">Contact Us</a></li>
	<li class="last"><a href="http://nytm.org/made" title="Made in NYC" target="_blank">Made in NYC</a></li>
	<?php } else { ?>
	<li><a href="/tickets/add" title="Contact Us">Contact Us</a></li>
	<li class="last"><a href="http://nytm.org/made" title="Made in NYC" target="_blank">Made in NYC</a></li>
	<?php } ?>
	<li class="last" style="margin:-5px 3px 0px 5px;"><a href="http://www.facebook.com/totsyfan" target="_blank"><img src="/img/icons/facebook_16.png" align="middle" /></a></li>
	<li class="last" style="margin:-5px 0px 0px 0px;"><a href="http://twitter.com/MyTotsy" target="_blank"><img src="/img/icons/twitter_16.png" align="middle" /></a></li>
</ul>
<span>&copy;2012 Totsy.com. All Rights Reserved.</span>