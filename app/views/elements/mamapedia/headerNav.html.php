<div class="menu_top_right">
	<?php if (!(empty($userInfo))) { ?>
	<a href="/account" title="My Account">My Account</a>
	<?php if (!(empty($credit))) { ?>
	&nbsp;
	<a href="/account/credits" title="My Credits $<?php echo $credit?>">My Credits $<?php echo $credit?></a>
	<?php } ?>
	<a href="/cart/view" class="cart_icon" title="My Cart (<?php echo $cartCount;?>)">My Cart (<span id="cart-count"><?php echo $cartCount;?></span>)</a>
	<a href="/users/invite" title="+ Invite Friends Get $15">+ Invite Friends Get $15</a>
	<?php } else { ?>
	<span style="text-align:right!important;">
	    <a href="/" title="Sign In">Sign In</a>
	    <a href="/register" title="Sign Up">Sign Up</a>
	    <a href="/users/invite" title="+ Invite Friends Get $15">+ Invite Friends Get $15</a>
	</span>
	<?php } ?>
</div>

<div class="menu_top_left" style="width:auto !important"> 
	<?php if (!empty($userInfo)): ?>
	Hello,
	<?php if(array_key_exists('firstname',$userInfo) && !empty($userInfo['firstname'])):?>
	<?php echo "{$userInfo['firstname']} {$userInfo['lastname']}"; ?>
	<?php else:?>
	<?php if (is_array($userInfo) && array_key_exists('email', $userInfo)) { echo $userInfo['email']; } ?>
	<?php endif; ?>
	<?php $logout = ($fblogout) ? $fblogout : 'Users::logout' ?>
	(<?php echo $this->html->link('Sign Out', $logout, array('title' => 'Sign Out')); ?>)
	<?php endif ?>
</div>