<?php $this->title("Account Dashboard"); ?>

	<h2 class="page-title gray">Account Dashboard</h2>
	<hr />
<div data-role="collapsible-set" data-theme="c" data-content-theme="d">
			<div data-role="collapsible">
				<h3>Personal Information</h3>
				<p><?php if(array_key_exists('firstname', $userInfo) &&     !empty($userInfo['firstname'])):
					?>
					<strong>Name:</strong> <?=$userInfo['firstname'].' '.$userInfo['lastname'] ?><br />
					<?php else: ?>
					    Totsy Member<br />
					<?php endif;?>
					<strong>Email:</strong> <?=$userInfo['email'];?></p>
				<p><a href="#" onclick="window.location.href='/account/info';return false;">Edit Information</a></p>
			</div>
			<div data-role="collapsible">
				<h3>Change Password</h3>
				<p><a href="#" onclick="window.location.href='/account/password';return false;">Click here to change your password</a></p>
			</div>
			<div data-role="collapsible">
				<h3>Email Preferences</h3>
				<p>Coming soon, for now you can click "unsubscribe" in the footer of the emails to stop receiving them.</p>
			</div>
			<div data-role="collapsible">
				<h3>Manage Addresses</h3>
				<p><strong><?php echo ('Primary Billing Address');?></strong>
					<?php if (!empty($billing)): ?>
						<br /><?=$this->html->link('Edit', "/addresses/edit/$billing->_id"); ?>
						<address>
							<div><?=$billing->address?></div>
							<div><?=$billing->address_2?></div>
							<div><?=$billing->city?>, <?=$billing->state?>, <?=$billing->zip?></div>
						<address>
					<?php else: ?>
						<br/><?=$this->html->link('Add Billing Address', "/addresses/add"); ?>
					<?php endif ?>
					</p>
					<hr />
					<p>
					<strong><?php echo ('Primary Shipping Address');?></strong>
					<?php if (!empty($shipping)): ?>
						<br /><?=$this->html->link('Edit', "/addresses/edit/$shipping->_id"); ?>
					<address>	
						<div><?=$shipping->address?></div>
						<div><?=$shipping->address_2?></div>
						<div><?=$shipping->city?>, <?=$shipping->state?>, <?=$shipping->zip?></div>
					</address>	
					<?php else: ?>
						<br /><?=$this->html->link('Add Shipping Address', "/addresses/add"); ?>
					<?php endif ?></p>
					<p><a href="#" onclick="window.location.href='/addresses/view';return false;">View All My Addresses</a></p>
			</div>
		</div>

<p></p>
<p></p>
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>
<?php echo $this->view()->render(array('element' => 'mobile_helpNav')); ?>