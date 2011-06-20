<div class="menu_top_left">
		<?php if (!empty($userInfo)): ?>
		Hello,
		<?php if(array_key_exists('firstname',$userInfo) && !empty($userInfo['firstname'])):?>
		<?php echo "{$userInfo['firstname']} {$userInfo['lastname']}"; ?>
		<?php else:?>
		<?php echo "{$userInfo['email']}"; ?>
		<?php endif; ?>
		<?php $logout = ($fblogout) ? $fblogout : 'Users::logout' ?>
		(<?php echo $this->html->link('Sign Out', $logout, array('title' => 'Sign Out')); ?>)
		<?php endif ?>
	</div>
	
	<div class="menu_top_right">
		<?php if (!(empty($userInfo))) { ?>
		<a href="/account" title="My Account">My Account</a>
		<?php if (!(empty($credit))) { ?>
		<a href="/account/credits" title="My Credits $<?php echo $credit?>">My Credits $<?php echo $credit?></a>
		<?php } ?>
		<a href="/cart/view" class="cart_icon" title="My Cart (<?php echo $cartCount;?>)">My Cart (<?php echo $cartCount;?>)</a>
		<a href="/users/invite" title="+ Invite Friends Get $15">+ Invite Friends Get $15</a>
		<?php } else { ?>
		<span style="text-align:right!important;">
			<a href="/" title="Sign In">Sign In</a>
			<a href="/register" title="Sign Up">Sign Up</a>
			<a href="/users/invite" title="+ Invite Friends Get $15">+ Invite Friends Get $15</a>
		</span>
		<?php } ?>
	</div>
	
	<div class="menu_main_global">
		<?php if (!(empty($userInfo))): ?>
		<ul class="nav main" id="navlist">
			<li><a href="/sales" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales') == 0 || $_SERVER['REQUEST_URI'] == '/') {
			echo 'class="active"';
			} ?>>All Sales</a></li>
			<li><a href="/sales/girls" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/girls') == 0) {
			echo 'class="active"';
			} ?>>Girls</a></li>
			<li><a href="/sales/boys" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/boys') == 0)  {
			echo 'class="active"';
			} ?>>Boys</a></li>
			<li><a href="/sales/momsdads" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/momsdads') == 0) {
			echo 'class="active"';
			} ?>>Moms &amp; Dads</a></li>
		</ul>
		<?php endif ?>
</div>
<!-- end header -->