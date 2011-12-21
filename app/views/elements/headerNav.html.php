<?php 
	use li3_facebook\extension\FacebookProxy; 
	$fbconfig = FacebookProxy::config();
	$appId = $fbconfig['appId'];	
?>

<script type="text/javascript">
		function deleteFBCookies() {
			//all posible FB cookies
			var fbCookie = 'fbsr_<?php echo $appId; ?>';	
			
			document.cookie = fbCookie + '=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
			document.cookie = 'datr=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
			document.cookie = 'locale=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
			document.cookie = 'lu=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
			document.cookie = 'reg_fb_gate=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
			document.cookie = 'reg_fb_ref=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
			document.cookie = 'lsd=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
			document.cookie = 'L=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
			document.cookie = 'act=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
			document.cookie = 'openid_p=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
		}
		
</script>
<div class="menu_top_left">
		<?php if (!empty($userInfo)): ?>
		Hello,
		<?php if(array_key_exists('firstname',$userInfo) && !empty($userInfo['firstname'])):?>
		<?php echo "{$userInfo['firstname']} {$userInfo['lastname']}"; ?>
		<?php else:?>
		<?php if (is_array($userInfo) && array_key_exists('email', $userInfo)) { echo $userInfo['email']; } ?>
		<?php endif; ?>		
		<?php $logout = ($fblogout) ? $fblogout : 'Users::logout' ?>
		(<?php echo $this->html->link('Sign Out', $logout, array('title' => 'Sign Out', 'onClick'=>'deleteFBCookies()')); ?>)
		<?php endif ?>
		
	</div>

	<div class="menu_top_right">
		<?php if (!(empty($userInfo))) { ?>
		<a href="/account" title="My Account">My Account</a>
		<?php if (!(empty($credit))) { ?>
		&nbsp;
		<a href="/account/credits" title="My Credits $<?php echo $credit?>">My Credits $<?php echo $credit?></a>
		<?php } ?>
		<a href="/cart/view" class="cart_icon" alt="My Cart (<?php echo $cartCount;?>)" title="My Cart (<?php echo $cartCount;?>)">My Cart (<span id="cart-count"><?php echo $cartCount;?></span>)</a>
		<a href="/users/invite" title="+ Invite Friends Get $15">+ Invite Friends Get $15</a>
		<?php } else { ?>
		<span style="text-align:right!important;">
			<a href="/" title="Sign In">Sign In</a>
			<a href="/register" title="Sign Up">Sign Up</a>
			<a href="/users/invite" title="+ Invite Friends Get $15">+ Invite Friends Get $15</a>
		</span>
		<?php } ?>
	</div>