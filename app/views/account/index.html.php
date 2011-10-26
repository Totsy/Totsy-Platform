<?php $this->title("Account Dashboard"); ?>

<div class="grid_16">
	<h2 class="page-title gray">Account Dashboard</h2>
	<hr />
</div>

<div class="grid_4">
	<?php echo $this->view()->render(array('element' => 'myAccountNav')); ?>
	<?php echo $this->view()->render(array('element' => 'helpNav')); ?>
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
					<h3 class="gray fl">Contact Information</h3>&nbsp;|&nbsp;&nbsp;<?=$this->html->link('Edit', '/account/info');?>

					<div><?php if(array_key_exists('firstname', $userInfo) &&     !empty($userInfo['firstname'])):
					?>
					    <?=$userInfo['firstname'].' '.$userInfo['lastname'] ?><br />
					<?php else: ?>
					    Totsy Member
					<?php endif;?></div>
					<div><?=$userInfo['email'];?></div>
					<div><?=$this->html->link('Change Password', '/account/password');?></div>
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
		<strong>Address Book</strong> | <?=$this->html->link('Manage Addresses', '/addresses/view');?>
		<hr />
		<div class="col-2">
			<div class="r-container box-2 fl">
				<div class="tl"></div>
				<div class="tr"></div>
				<div class="r-box lt-gradient-1">
					<h3 class="gray fl"><?php echo ('Primary Billing Address');?></h3>&nbsp;|&nbsp;
					<?php if (!empty($billing)): ?>
						<?=$this->html->link('Edit', "/addresses/edit/$billing->_id"); ?>
						<address>
							<div><?=$billing->address?></div>
							<div><?=$billing->address_2?></div>
							<div><?=$billing->city?>, <?=$billing->state?>, <?=$billing->zip?></div>
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
						<?=$this->html->link('Edit', "/addresses/edit/$shipping->_id"); ?>
					<address>	
						<div><?=$shipping->address?></div>
						<div><?=$shipping->address_2?></div>
						<div><?=$shipping->city?>, <?=$shipping->state?>, <?=$shipping->zip?></div>
					</address>	
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