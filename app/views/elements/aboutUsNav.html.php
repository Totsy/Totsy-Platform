<?php use lithium\storage\Session; ?>
<div class="roundy grey_inside">
	<h3 class="gray">About Us</h3>
	<hr />
	<ul class="menu main-nav">
		<li><a href="/pages/aboutus" title="About Totsy">How Totsy Works</a></li>
		<li><a href="/pages/moms" title="Meet The Moms">Meet The Moms</a></li>
		<li><a href="/pages/press" title="Press">Totsy in the Press</a></li>
		<li><a href="/pages/testimonials" title="Video Testimonials">Video Testimonials</a></li>
		<li><a href="/pages/being_green" title="Being Green">Being Green</a></li>
		<li><a href="http://blog.totsy.com" target="_blank" title="Blog">Totsy Blog</a></li>
	<?php if(Session::read("layout", array("name"=>"default"))!=="mamapedia"): ?>
		<li><a href="/pages/affiliates" title="Affiliates">Affiliate Program</a></li>
		<li><a href="/pages/careers" title="Careers">Careers</a></li>
	<?php endif ?>
	</ul>
</div>