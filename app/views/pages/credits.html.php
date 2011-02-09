<?php $this->title("My Account Credits"); ?>
	<h1 class="p-header">My Account</h1>
	<div id="left">
		<ul class="menu main-nav">
		<li class="firstitem17"><a href="/account" title="Account Dashboard"><span>Account Dashboard</span></a></li>
	    <li class="item18"><a href="/account/info" title="Account Information"><span>Account Information</span></a></li>
	    <li class="item19"><a href="/addresses" title="Address Book"><span>Address Book</span></a></li>
	    <li class="item20"><a href="/orders" title="My Orders"><span>My Orders</span></a></li>
	    <li class="item20 active"><a href="/Credits/view" title="My Credits"><span>My Credits</span></a></li>
	    <li class="lastitem23"><a href="/Users/invite" title="My Invitations"><span>My Invitations</span></a></li>
		  <br />
		  <h3 style="color:#999;">Need Help?</h3>
		  <hr />
		  <li class="first item18"><a href="/tickets/add" title="Contact Us"><span>Help Desk</span></a></li>
		  <li class="first item19"><a href="/pages/faq" title="Frequently Asked Questions"><span>FAQ's</span></a></li>
		</ul>
	</div>

<div id="middle" class="noright">
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
		
		<h2 class="gray mar-b">My Credits</h2>
		<hr />
		<p>Credits from the old Totsy website have been moved over, and you should see that amount available at the top of the page next to the text "My Credits".</p>
		
		<p>We've taken great pains to restore your credits from the old Totsy website; if you think your credits did not make it over from your original Totsy account, please <?=$this->html->link('contact us', array('Tickets::add')); ?> and we can verify the status of your account.</p>
	
	</div>
	<div class="bl"></div>
	<div class="br"></div>
</div>
