<?php $this->title("My Credits"); ?>

	<h1 class="p-header">My Account</h1>
	<div id="left">
		<ul class="menu main-nav">
			<li class="firstitem17"><a href="/account" title="Account Dashboard"><span>Account Dashboard</span></a></li>
			<li class="item18"><a href="/account/info" title="Account Information"><span>Account Information</span></a></li>
			<li class="item18"><a href="/account/password" title="Change Password"><span>Change Password</span></a></li>
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
		<hr/>
		
		<?php if (!empty($credit)) { ?>
		<div id="name" style="padding:10px 10px 10px 5px; color:#009900;" class="order-table">
			<strong class="fl">Total Credits: $<?=$credit?></strong>
			<div style="clear:both;"></div>
		</div>
		<table border="0" cellspacing="5" cellpadding="5" width="100%" class="order-table" style="margin-top:10px;">
			<tr>
				<th>Date</th>
				<th>Amount</th>
				
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
			
			<tr>
			<?php endforeach ?>
		</table>
		<?php } else { ?>
		<div id="name" style="padding:10px 10px 10px 5px;" class="order-table">
			<strong class="fl">Earn credits by <a href="/users/invite" title="inviting your friends and family">inviting your friends and family.</a></strong>
			<div style="clear:both;"></div>
			<br />
		</div>
		<?php } ?>
	</div>
	<div class="bl"></div>
	<div class="br"></div>
</div>
