<?php $this->title("My Credits"); ?>

<div class="grid_16">
	<h2 class="page-title gray">My Credits</h2>
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

	<h2 class="page-title gray">My Credits</h2>
	<hr />
	<?php if (!empty($credit)) { ?>
		<div id="name" style="padding:10px 10px 10px 5px; color:#009900;" class="order-table">
			<strong class="fl">Total Credits: $<?=$credit?></strong>
			<div style="clear:both;"></div>
		</div>
		<table border="0" cellspacing="5" cellpadding="5" width="100%" class="order-table" style="margin-top:10px;">
			<tr>
				<th>Date</th>
				<th>Amount</th>
				<th>Description</th>
			</tr>
			<?php foreach ($credits as $credit): ?>
			<tr>
			<td><?=date('Y-m-d', $credit->_id->getTimestamp())?></td>
			<td>
			<?php if (!empty($credit->credit_amount)) { ?>
					$<?=$credit->credit_amount?>
				<?php  } else {  ?>
					$<?=$credit->amount?>
			<?php } ?>
			</td>
			<td><?=$credit->description?></td>
			<tr>
			<?php endforeach ?>
		</table>
		<?php } else { ?>
		<center><strong>Earn credits by <a href="/users/invite" title="inviting your friends and family">inviting your friends and family.</a></strong></center>
			<br />
		</div>
		<?php } ?>
	<br />

</div>
</div>
<div class="clear"></div>