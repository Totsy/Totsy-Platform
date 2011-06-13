<?php $this->title("Account Dashboard"); ?>
<?=$this->html->script('jquery.equalheights'); ?>

<div class="grid_16">
	<h2 class="page-title gray">Account Dashboard</h2>
	<hr />
</div>

<div class="grid_4">
	<div class="roundy grey_inside">
		<h3 class="gray">My Account</h3>
		<hr />
		<ul class="menu main-nav">
		<li><a href="/account" title="Account Dashboard">Account Dashboard</a></li>
		<li><a href="/account/info" title="Account Information">Account Information</a></li>
		<li><a href="/account/password" title="Change Password">Change Password</a></li>
		<li class="active"><a href="/addresses" title="Address Book">Address Book</a></li>
		<li><a href="/orders" title="My Orders">My Orders</a></li>
		<li><a href="/Credits/view" title="My Credits">My Credits</a></li>
		<li><a href="/Users/invite" title="My Invitations">My Invitations</a></li>
		</ul>
	</div>
	<div class="clear"></div>
	<div class="roundy grey_inside">
		<h3 class="gray">Need Help?</h3>
		<hr />
		<ul class="menu main-nav">
		    <li><a href="/tickets/add" title="Contact Us">Help Desk</a></li>
			<li><a href="/pages/faq" title="Frequently Asked Questions">FAQ's</a></li>
			<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
			<li><a href="/pages/terms" title="Terms Of Use">Terms Of Use</a></li>
		</ul>
	</div>
</div>

<div class="grid_11 omega roundy grey_inside b_side">

	<h2 class="page-title gray">Account Dashboard</h2>
	<hr />

		<p>From your My Account Dashboard you have the ability to view a snapshot of your recent account activity and update your account information. Select a link below to view or edit information.</p>


		<div class="col-2">

			<div class="r-container box-2 fl">
				<div class="tl"></div>
				<div class="tr"></div>
				<div class="r-box lt-gradient-1">
					<h3 class="gray fl">Contact Information</h3>&nbsp;|&nbsp;<?=$this->html->link('Edit', '/account/info');?>
					<br />
					<br />
					<?php if(array_key_exists('firstname', $userInfo) &&     !empty($userInfo['firstname'])):
					?>
					    <?=$userInfo['firstname'].' '.$userInfo['lastname'] ?><br />
					<?php else: ?>
					    Totsy Member<br />
					<?php endif;?>
					<?=$userInfo['email'];?><br />
					<?=$this->html->link('Change Password', '/account/password');?>
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
	<br />

</div>
</div>
<div class="clear"></div>

<!-- This equals the hight of all the boxes to the same height -->
<script type="text/javascript">
	$(document).ready(function() {
		$(".r-box").equalHeights(100,300);
	});
</script>
