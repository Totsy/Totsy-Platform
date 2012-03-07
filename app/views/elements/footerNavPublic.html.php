<?php use lithium\storage\Session; ?>
<style>
#socialLinks {
    margin-top: 0;
}
.socialLink {
    background: url("/img/sprite_social.png") no-repeat scroll 0 0 #FFFFFF;
    border-right: 1px solid #C5C5C5;
    display: inline;
    float: right;
    height: 30px;
    text-indent: -9999em;
    width: 30px;
}
#facebookLink {
    background-position: 0 -2px;
}
#twitterLink {
    background-position: -30px -2px;
}
#tumblrLink {
    background-position: -60px -2px;
}
</style>
<ul style="padding:5px 0px 0px 5px!important;">
	<li><a href="/pages/terms" title="Terms of Use">Terms of Use</a></li>
	<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
	<li><a href="/pages/aboutus" title="About Us">About Us</a></li>
	<li><a href="http://blog.totsy.com" title="Blog" target="_blank">Blog</a></li>
	<li><a href="/pages/faq" title="FAQ">FAQ</a></li>
	
	<?php if(Session::read('layout', array('name' => 'default'))!=='mamapedia'): ?>
	<li><a href="/pages/affiliates" title="Affiliates">Affiliates</a></li>
	<li><a href="/pages/careers" title="Careers">Careers</a></li>
	<?php endif ?>
	
	<?php if (empty($userInfo)){ ?>
	<li><a href="/pages/contact" title="Contact Us">Contact Us</a></li>
	<li><a href="http://nytm.org/made" title="Made in NYC" target="_blank">Made in NYC</a></li>
	<?php } else { ?>
	<li><a href="/tickets/add" title="Contact Us">Contact Us</a></li> 
	<li><a href="http://nytm.org/made" title="Made in NYC" target="_blank">Made in NYC</a></li>
	<?php } ?>
	<span style="float:left;">&copy; 2012 Totsy.com. All Rights Reserved.</span>
</ul>


<footer id="loginFooter" class="block">
        <div id="socialLinks">
            <a href="http://www.facebook.com/totsyfan" id="facebookLink" class="socialLink" target="_blank" title="Totsy.com on Facebook">F</a>
            <a href="http://blog.totsy.com/" id="tumblrLink" class="socialLink" target="_blank" title="Totsy on Tumblr">T</a>
            <a href="http://twitter.com/MyTotsy" id="twitterLink" class="socialLink" target="_blank" title="Totsy on Twitter">T</a>

            <div class="clear"></div>
        </div>
</footer>