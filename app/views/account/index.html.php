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
	<div class="grid_6">
	<h3 class="gray fl">Contact Information</h3>&nbsp;|&nbsp;<?=$this->html->link('Edit', '/account/info');?>
					<hr />
					<?php if(array_key_exists('firstname', $userInfo) &&     !empty($userInfo['firstname'])):
					?>
					    Name: <?=$userInfo['firstname'].' '.$userInfo['lastname'] ?><br />
					<?php else: ?>
					    Totsy Member<br />
					<?php endif;?>
					Email: <?=$userInfo['email'];?><br />
					<?=$this->html->link('Change Password', '/account/password');?>
	
	</div>
	
	<div class="grid_5">
	<h3 class="gray fl">Email Preferences&nbsp;|&nbsp;<?=$this->html->link('Edit', '/account/info');?></h3>
	<hr/>
	You are currently subsribed to receive Totsy Updates
	</div>
	
	
	<div class="grid_6">
	<h3 class="gray fl">Address Book</h3>&nbsp;|&nbsp;<?=$this->html->link('Manage Addresses', '/addresses/view');?>
	<hr />
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
	
	<div class="grid_5">
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



</div>
</div>
<div class="clear"></div>
