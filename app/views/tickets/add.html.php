<?php $this->title("My Help Desk"); ?>
	<h1 class="p-header">My Account</h1>
	<div id="left">
		<ul class="menu main-nav">
		<li class="firstitem17"><a href="/account" title="Account Dashboard"><span>Account Dashboard</span></a></li>
	    <li class="item18"><a href="/account/info" title="Account Information"><span>Account Information</span></a></li>
	    <li class="item19"><a href="/addresses" title="Address Book"><span>Address Book</span></a></li>
	    <li class="item20"><a href="/orders" title="My Orders"><span>My Orders</span></a></li>
	    <li class="item20"><a href="/Credits/view" title="My Credits"><span>My Credits</span></a></li>
	    <li class="lastitem23"><a href="/Users/invite" title="My Invitations"><span>My Invitations</span></a></li>
		  <br />
		  <h3 style="color:#999;">Need Help?</h3>
		  <hr />
		  <li class="first item18 active"><a href="/tickets/add" title="Contact Us"><span>Help Desk</span></a></li>
		  <li class="first item19"><a href="/pages/faq" title="Frequently Asked Questions"><span>FAQ's</span></a></li>
		</ul>
	</div>

<!-- Start Main Page Content -->
<div id="middle" class="noright">
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
		<h2 class="gray mar-b">Help Desk</h2>
		<hr />
		<?php if (!empty($message)): ?>
			<div class="standard-message"><?=$message;?></div>
		<?php endif ?>
		
			<div id="message">
				<p>Hello <?=$userInfo['firstname']?>, <br><br>We hope to quickly resolve any issue you may have.</p> 
				<p>Please <a href="mailto:support@totsy.com" title="click to send us an email at support@totsy.com">send us a message</a> with as much detail as possible for us to assist you.</p>
				<p>You can also contact Totsy at:<br>
					Corporate Address: 10 West 18th Street, 4th Floor, New York, NY 10011<br>
					<!-- Phone Number: 1-888-59TOTSY (1-888-598-6879) --> </p>
			</div>
	</div>
	
	<div class="bl"></div>
	<div class="br"></div>
</div>
<script type="text/javascript" src="../js/jquery.equalheights.js"></script>
<!-- This equals the hight of all the boxes to the same height -->
<script type="text/javascript">
	$(document).ready(function() {
		$(".r-box").equalHeights(100,300);
	});
</script>
