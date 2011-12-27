<header class="group">

	<h1 id="logo">
		<?php //echo $this->html->link($this->html->image('logo.png', array('width'=>'120')), '/sales', array('escape'=> false)); ?>
		<a href="/sales" title="Totsy">totsy</a>
	</h1>
		
	<?php 
		use li3_facebook\extension\FacebookProxy; 
		$fbconfig = FacebookProxy::config();
		$appId = $fbconfig['appId'];	
	?>	
		

	<?php
		if (!(empty($userInfo))) { ?>
			<nav class="group">
				
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
								<em>
								<?php
									echo $this->html->link('Sign Out', "#", array('title' => 'Sign Out', 'onClick'=>'goToLogout()')) . ' | <a href="/account" title="My Account">My Account</a>';
								?>
								</em>
							</div>
							<div id="usercart">
							
								<a href="/cart/view" class="icon cart cart_icon" title="My Cart total: $<?php echo number_format($cartSubTotal,2)?>">$<?php echo number_format($cartSubTotal,2)?></a>
								<a href="/cart/view" class="btn checkout" title="My Cart (<?php echo $cartCount;?>)"><strong>CHECKOUT</strong></a>
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
				<!-- /#userUtils -->

				<ul>
					<li id="newsales"><a href="/sales" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales') == 0 || $_SERVER['REQUEST_URI'] == '/') {
					echo 'class="active"';
					} ?>><em>New<br/> Sales</em></a></li>
					<li id="ending"><a href="/sales#upcoming" <?php if(strcmp($_SERVER['REQUEST_URI'],'/sales/girls') == 0) {
					echo 'class="active"';
					} ?>><em>Upcoming<br/> Sales</em></a></li>
					<li id="bycat" class="haschild"><a><em>Shop By<br/> Category</em></a>
						<ul>
							<li><a href="/category/girls-apparel " title="View Girls Apparel">Girls Apparel</a></li>
							<li><a href="/category/boys-apparel " title="View Boys Apparel">Boys Apparel</a></li>
							<li><a href="/category/shoes " title="View Shoes">Shoes</a></li>
							<li><a href="/category/accessories" title="View Accessories">Accessories</a></li>
							<li><a href="/category/toys-books" title="View Toys &amp; Books">Toys &amp; Books</a></li>
							<li><a href="/category/gear" title="View Gear">Gear</a></li>
							<li><a href="/category/home" title="View Home">Home</a></li>
							<li><a href="/category/moms-dads" title="View Moms &amp; Dads">Moms &amp; Dads</a></li>
						</ul>
					</li>
					<li id="byage" class="haschild"><a><em>Shop<br/> By Age</em></a>
						<ul>
							<li><a href="/age/newborn" title="View items for Newborns">Newborn</a></li>
							<li><a href="/age/infant" title="View items for Infants">Infant 0 -12M</a></li>
							<li><a href="/age/toddler" title="View items for Toddlers">Toddler 1-3Y</a></li>
							<li><a href="/age/preschool" title="View items for Preschoolers">Preschool 4-5Y</a></li>
							<li><a href="/age/school" title="View items for School Age">School Age 5+</a></li>
						</ul>
					</li>
				</ul>
				
			</nav>
			<div id="invite">
				<a href="/users/invite" title="Invite Friends, Get $15">Invite Your Friends, <strong>Get $15</strong></a>
			</div>
	<?php } ?>
	
</header>