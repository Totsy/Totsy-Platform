<?php $this->title("Account Dashboard for " . $userInfo['firstname']); ?>
<?=$this->html->script('jquery.equalheights'); ?>
<h1 class="p-header">My Account</h1>

	<div id="left">
	  <ul class="menu main-nav">
	    <li class="firstitem17 active"><a href="/account" title="Account Dashboard"><span>Account Dashboard</span></a></li>
	    <li class="item18"><a href="/account/info" title="Account Information"><span>Account Information</span></a></li>
	    <li class="item19"><a href="/addresses" title="Address Book"><span>Address Book</span></a></li>
	    <li class="item20"><a href="/orders" title="My Orders"><span>My Orders</span></a></li>
	    <li class="item20"><a href="/Credits/view" title="My Credits"><span>My Credits</span></a></li>
	    <li class="item22"><a href="/tickets/add" title="Help Desk"><span>Help Desk</span></a></li>
	    <li class="lastitem23"><a href="/Users/invite" title="My Invitations"><span>My Invitations</span></a></li>
	  </ul>
	</div>
	
<div id="middle" class="noright">
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
		<!-- Replace with user's name -->
		<h2 class="gray mar-b">Hello <?=$userInfo['firstname']?>!</h2>
		<hr />
	
		<!-- Replace with account welcome message -->
		<p>From your My Account Dashboard you have the ability to view a snapshot of your recent account activity and update your account information. Select a link below to view or edit information.</p>
		</br><br>
		<h2 class="gray mar-b">Account Information</h2>
		<hr />
	
		<div class="col-2">
	
			<div class="r-container box-2 fl">
				<div class="tl"></div>
				<div class="tr"></div>
				<div class="r-box lt-gradient-1">
					<h3 class="gray fl">Contact Information</h3>&nbsp;|&nbsp;<?=$this->html->link('Edit', '/account/info');?>
					<br />
					<br />
					<?=$userInfo['firstname'].' '.$userInfo['lastname'] ?><br />
					<?=$userInfo['email']?><br />
					<?=$this->html->link('Change Password', '/account/info');?>
				</div>
				<div class="bl"></div>
				<div class="br"></div>
			</div>

			<div class="r-container box-2 fr">
				<div class="tl"></div>
				<div class="tr"></div>
				<div class="r-box lt-gradient-1">
					<h3 class="gray fl">Email Preferences - Coming Soon</h3><!--&nbsp;|&nbsp;<a href="" title="Edit">Edit</a>
					<br />
					<br />
					<dl>
						<dt>You are currently subscribed to:</dt>
						<dd>
							<ul>
								<li>General Subscription</li>
							</ul>
						</dd>
					</dl>-->
				
				</div>
				<div class="bl"></div>
				<div class="br"></div>
			</div>
	
		</div>
	
		<h2 class="gray fl">Address Book</h2>&nbsp;|&nbsp;<?=$this->html->link('Manage Addresses', '/addresses/view');?>
		<hr />
		<div class="col-2">
	
			<div class="r-container box-2 fl">
				<div class="tl"></div>
				<div class="tr"></div>
				<div class="r-box lt-gradient-1">
					<h3 class="gray fl"><?php echo ('Primary Billing Address');?></h3>&nbsp;|&nbsp;
					<?php if (!empty($billing)): ?>
						<?=$this->html->link('Edit', "/addresses/edit/$billing->_id"); ?><br><br>
						<address>
							<?=$billing->address?><br>
							<?=$billing->address_2?><br>
							<?=$billing->city?>, <?=$billing->state?>, <?=$billing->zip?>
						<address>
					<?php else: ?>
						<?=$this->html->link('Add', "/addresses/add"); ?>
					<?php endif ?>
				</div>
				<div class="bl"></div>
				<div class="br"></div>
			</div>
		
			<div class="r-container box-2 fr">
				<div class="tl"></div>
				<div class="tr"></div>
				<div class="r-box lt-gradient-1">
					<h3 class="gray fl"><?php echo ('Primary Shipping Address');?></h3>&nbsp;|&nbsp;
					<?php if (!empty($shipping)): ?>
						<?=$this->html->link('Edit', "/addresses/edit/$shipping->_id"); ?><br><br>
						<address>
							<?=$shipping->address?><br>
							<?=$shipping->address_2?><br>
							<?=$shipping->city?>, <?=$shipping->state?>, <?=$shipping->zip?>
						<address>
					<?php else: ?>
						<?=$this->html->link('Add', "/addresses/add"); ?>
					<?php endif ?>
				</div>
				<div class="bl"></div>
				<div class="br"></div>
			</div>
	
		</div>

	</div>
	<div class="bl"></div>
	<div class="br"></div>
</div>
<!-- This equals the hight of all the boxes to the same height -->
<script type="text/javascript">
	$(document).ready(function() {
		$(".r-box").equalHeights(100,300);
	});
</script>
