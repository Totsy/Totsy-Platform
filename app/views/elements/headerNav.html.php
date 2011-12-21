<header class="group">

	<h1 id="logo">
		<?php //echo $this->html->link($this->html->image('logo.png', array('width'=>'120')), '/sales', array('escape'=> false)); ?>
		<a href="/sales" title="Totsy">totsy</a>
	</h1>
		
	<div id="userUtils">
		<?php
			if (!empty($userInfo)) { ?>
				<div id="userinfo">
					<strong>Hi
					<?php
						if (array_key_exists('firstname',$userInfo) && !empty($userInfo['firstname'])) :
							echo "{$userInfo['firstname']}";
						else :
							if (is_array($userInfo) && array_key_exists('email', $userInfo)) : echo $userInfo['email']; endif;
						endif;
					?></strong>
					<?php 
						$logout = ($fblogout) ? $fblogout : 'Users::logout';
						echo $this->html->link('Sign Out', $logout, array('title' => 'Sign Out')) . ' | <a href="/account" title="My Account">My Account</a>';
					?>
				</div>
				<div id="usercart">
					<a href="/cart/view" class="icon cart" title="My Cart (<?php echo $cartCount;?>)">$<?php echo $cartCount;?></a>
					<a href="/cart/view" class="btn checkout" title="My Cart (<?php echo $cartCount;?>)">CHECKOUT</a>
					<?php
						// credits logic - retained but commented out for possible re-inclusion
						//if (!(empty($credit))) : echo '<a href="/account/credits" title="My Credits $' . $credit . '>My Credits $' . $credit . '</a>';
					?>
				</div><?php
			} else { ?>
				<div id="loggedOut">
					<a href="/login" title="Sign In">Sign In</a> | <strong>Not a member?</strong> <a href="/register" title="Sign Up">Join Now</a>
				</div><?php
			}
		?>
	</div>

	<?php
		if (!(empty($userInfo))) { ?>
			<nav>
				<ul class="group">
					<li id="newsales"><a href="/sales" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales') == 0 || $_SERVER['REQUEST_URI'] == '/') {
					echo 'class="active"';
					} ?>><em>New<br/> Sales</em></a></li>
					<li id="ending"><a href="/sales/girls" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/girls') == 0) {
					echo 'class="active"';
					} ?>><em>Ending<br/> Soon</em></a></li>
					<li id="bycat" class="haschild"><a href="/sales/boys" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/boys') == 0)  {
					echo 'class="active"';
					} ?>><em>Shop By<br/> Category</em></a>
						<ul>
							<li><a href="#" title="click here">link</a></li>
							<li><a href="#" title="click here">link</a></li>
							<li><a href="#" title="click here">link</a></li>
							<li><a href="#" title="click here">link</a></li>
						</ul>
					</li>
					<li id="byage" class="haschild"><a href="/sales/momsdads" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/momsdads') == 0) {
					echo 'class="active"';
					} ?>><em>Shop<br/> By Age</em></a>
											<ul>
							<li><a href="#" title="click here">link</a></li>
							<li><a href="#" title="click here">link</a></li>
							<li><a href="#" title="click here">link</a></li>
							<li><a href="#" title="click here">link</a></li>
						</ul>
					</li>
				</ul>
			</nav>
			<div id="invite">
				<a href="/users/invite" title="Invite Friends, Get $15">Invite Your Friends, <strong>Get $15</strong></a>
			</div>
	<?php } ?>
	
</header>