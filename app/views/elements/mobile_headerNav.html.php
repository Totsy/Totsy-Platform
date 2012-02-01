<h2>My Account</h2>
<hr />
		<?php if (!(empty($userInfo))) { ?>
			<ul data-role="listview" data-inset="true">
			<li><a href="#" onclick="window.location.href='/account';return false;">Manage Account</a></li>
		
			<li><a href="#" onclick="window.location.href='/account/credits';return false;">My Credits <span class="ui-li-count" style="color:#009900;">Credit $<?php echo $credit?></span></a></li>
			
			<li><a href="#" onclick="window.location.href='/cart/view';return false;">My Cart <span class="ui-li-count"><?php echo $cartCount;?></span></a></li>
			<li><a href="#" onclick="window.location.href='/orders';return false;">My Orders</a></li>

			<li><a href="#" onclick="window.location.href='/users/invite';return false;">Invite Friends, Get $15</a></li>
			<li><a href="#"  onclick="goToLogout();">Sign Out</a></li>
			
		</ul>
		<?php } else { ?>
		<span style="text-align:right!important;">
			<a href="/" title="Sign In">Sign In</a>
			<a href="/register" title="Sign Up">Sign Up</a>
			<a href="/users/invite" title="+ Invite Friends Get $15">Invite Friends Get $15</a>
		</span><?php } ?>