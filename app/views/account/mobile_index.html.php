<p></p>
	<h2 class="page-title gray">Account Dashboard</h2>
	<hr />
		<p>From your My Account Dashboard you have the ability to view a snapshot of your recent account activity and update your account information. Select a link below to view or edit information.</p>

					<h3 class="gray fl">Contact Information</h3>&nbsp;|&nbsp;&nbsp;<?=$this->html->link('Edit', '/account/info');?>

					<div><?php if(array_key_exists('firstname', $userInfo) &&     !empty($userInfo['firstname'])):
					?>
					    <?=$userInfo['firstname'].' '.$userInfo['lastname'] ?><br />
					<?php else: ?>
					    Totsy Member
					<?php endif;?></div>
					<div><?=$userInfo['email'];?></div>
					<div><?=$this->html->link('Change Password', '/account/password');?></div>

					<h3 class="gray fl">Email Preferences - Coming Soon</h3>
					
		<strong>Address Book</strong> | <?=$this->html->link('Manage Addresses', '/addresses/view');?>
		<hr />
		
		
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

<div class="clear"></div>

<?php echo $this->view()->render(array('element' => 'mobile_helpNav')); ?>