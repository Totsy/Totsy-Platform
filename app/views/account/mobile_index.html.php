<h2 class="page-title gray">Account Dashboard</h2>
	<hr />
<div data-role="collapsible-set" data-theme="c" data-content-theme="d">
			<div data-role="collapsible">
				<h3>Personal Information</h3>
				<p><?php if(array_key_exists('firstname', $userInfo) &&     !empty($userInfo['firstname'])):
					?>
					<strong>Name:</strong> <?php echo $userInfo['firstname'].' '.$userInfo['lastname'] ?><br />
					<?php else: ?>
					    Totsy Member<br />
					<?php endif;?>
					<strong>Email:</strong> <?php echo $userInfo['email'];?></p>
				<p><a href="#" onclick="window.location.href='/account/info';return false;">Edit Information</a></p>
			</div>
			<div data-role="collapsible">
				<h3>Change Password</h3>
				<p><a href="#" onclick="window.location.href='/account/password';return false;">Click here to change your password</a></p>
			</div>
			<div data-role="collapsible">
				<h3>Email Preferences</h3>
				<p><a href="http://link.totsy.com/manage/optout?email=<?php echo $userInfo['email'];?>" target="_blank">Manage Email Subscriptions</a></p>
			</div>
			<div data-role="collapsible">
				<h3>Manage Addresses</h3>
				<p><strong><?php echo ('Primary Billing Address');?></strong>
					<?php if (!empty($billing)): ?>
						<br /><?php echo $this->html->link('Edit', "/addresses/edit/$billing->_id"); ?>
						<address>
							<div><?php echo $billing->address?></div>
							<div><?php echo $billing->address_2?></div>
							<div><?php echo $billing->city?>, <?php echo $billing->state?>, <?php echo $billing->zip?></div>
						<address>
					<?php else: ?>
						<br/><?php echo $this->html->link('Add Billing Address', "/addresses/add"); ?>
					<?php endif ?>
					</p>
					<hr />
					<p>
					<strong><?php echo ('Primary Shipping Address');?></strong>
					<?php if (!empty($shipping)): ?>
						<br /><?php echo $this->html->link('Edit', "/addresses/edit/$shipping->_id"); ?>
					<address>	
						<div><?php echo $shipping->address?></div>
						<div><?php echo $shipping->address_2?></div>
						<div><?php echo $shipping->city?>, <?php echo $shipping->state?>, <?php echo $shipping->zip?></div>
					</address>	
					<?php else: ?>
						<br /><?php echo $this->html->link('Add Shipping Address', "/addresses/add"); ?>
					<?php endif ?></p>
					<p><a href="#" onclick="window.location.href='/addresses/view';return false;">View All My Addresses</a></p>
			</div>
		</div>

<p></p>
<p></p>
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>